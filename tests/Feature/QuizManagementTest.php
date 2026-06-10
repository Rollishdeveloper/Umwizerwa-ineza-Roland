<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Category;
use App\Models\Quiz;
use App\Models\Question;
use App\Services\GamificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizManagementTest extends TestCase
{
    use RefreshDatabase;

    private $instructorUser;
    private $instructor;
    private $course;
    private $studentUser;
    private $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instructorUser = User::factory()->create(['role' => 'instructor']);
        $this->instructor = Instructor::factory()->create([
            'user_id' => $this->instructorUser->id,
        ]);

        $category = Category::factory()->create();
        $this->course = Course::factory()->create([
            'instructor_id' => $this->instructor->instructor_id,
            'category_id' => $category->category_id,
        ]);

        $this->studentUser = User::factory()->create(['role' => 'student']);
        $this->student = Student::factory()->create([
            'user_id' => $this->studentUser->id,
        ]);
    }

    public function test_instructor_can_create_quiz()
    {
        $this->actingAs($this->instructorUser);

        $quizData = [
            'title' => 'Week 1 Quiz',
            'description' => 'Test your knowledge',
            'total_marks' => 100,
            'passing_marks' => 60,
            'duration_minutes' => 30,
        ];

        $response = $this->post(route('quizzes.store', $this->course), $quizData);

        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'course_id' => $this->course->course_id,
            'title' => 'Week 1 Quiz',
            'total_marks' => 100,
            'passing_marks' => 60,
        ]);
    }

    public function test_instructor_can_add_question_to_quiz()
    {
        $this->actingAs($this->instructorUser);
        $quiz = Quiz::factory()->create(['course_id' => $this->course->course_id]);

        $response = $this->post(route('quizzes.questions.store', [$this->course, $quiz]), [
            'question_text' => 'What is 2 + 2?',
            'option_a' => '3',
            'option_b' => '4',
            'option_c' => '5',
            'option_d' => '6',
            'correct_answer' => 'b',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('questions', [
            'quiz_id' => $quiz->quiz_id,
            'question_text' => 'What is 2 + 2?',
            'correct_answer' => 'b',
        ]);
    }

    public function test_student_can_take_quiz()
    {
        $this->actingAs($this->studentUser);

        $quiz = Quiz::factory()->create([
            'course_id' => $this->course->course_id,
            'total_marks' => 100,
            'passing_marks' => 50,
        ]);

        $questions = Question::factory(4)->create([
            'quiz_id' => $quiz->quiz_id,
        ]);

        $answers = [];
        foreach ($questions as $q) {
            $answers[$q->question_id] = $q->correct_answer;
        }

        $response = $this->post(route('quizzes.submit', [$this->course, $quiz]), [
            'answers' => $answers,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_results', [
            'quiz_id' => $quiz->quiz_id,
            'student_id' => $this->student->student_id,
            'status' => 'passed',
        ]);
    }

    public function test_student_fails_quiz_with_wrong_answers()
    {
        $this->actingAs($this->studentUser);

        $quiz = Quiz::factory()->create([
            'course_id' => $this->course->course_id,
            'total_marks' => 100,
            'passing_marks' => 80,
        ]);

        $questions = Question::factory(4)->create([
            'quiz_id' => $quiz->quiz_id,
        ]);

        $wrongAnswers = [];
        foreach ($questions as $q) {
            $wrongAnswers[$q->question_id] = $q->correct_answer === 'a' ? 'b' : 'a';
        }

        $response = $this->post(route('quizzes.submit', [$this->course, $quiz]), [
            'answers' => $wrongAnswers,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_results', [
            'quiz_id' => $quiz->quiz_id,
            'student_id' => $this->student->student_id,
            'status' => 'failed',
        ]);
    }

    public function test_quiz_requires_all_questions_answered()
    {
        $this->actingAs($this->studentUser);

        $quiz = Quiz::factory()->create(['course_id' => $this->course->course_id]);
        Question::factory(2)->create(['quiz_id' => $quiz->quiz_id]);

        $response = $this->post(route('quizzes.submit', [$this->course, $quiz]), [
            'answers' => [],
        ]);

        $response->assertSessionHasErrors('answers');
    }

    public function test_instructor_can_view_quiz_results()
    {
        $this->actingAs($this->instructorUser);
        $quiz = Quiz::factory()->create(['course_id' => $this->course->course_id]);

        $response = $this->get(route('quizzes.show', [$this->course, $quiz]));

        $response->assertStatus(200);
        $response->assertSee($quiz->title);
    }

    public function test_instructor_can_delete_question()
    {
        $this->actingAs($this->instructorUser);
        $quiz = Quiz::factory()->create(['course_id' => $this->course->course_id]);
        $question = Question::factory()->create(['quiz_id' => $quiz->quiz_id]);

        $response = $this->delete(
            route('quizzes.questions.destroy', [$this->course, $quiz, $question])
        );

        $response->assertStatus(302);
        $this->assertDatabaseMissing('questions', [
            'question_id' => $question->question_id,
        ]);
    }

    public function test_quiz_result_shows_exact_score()
    {
        $this->actingAs($this->studentUser);

        $quiz = Quiz::factory()->create([
            'course_id' => $this->course->course_id,
            'total_marks' => 40,
            'passing_marks' => 20,
        ]);

        $q1 = Question::factory()->create([
            'quiz_id' => $quiz->quiz_id,
            'correct_answer' => 'a',
        ]);
        $q2 = Question::factory()->create([
            'quiz_id' => $quiz->quiz_id,
            'correct_answer' => 'b',
        ]);

        $response = $this->post(route('quizzes.submit', [$this->course, $quiz]), [
            'answers' => [
                $q1->question_id => 'a',
                $q2->question_id => 'a',
            ],
        ]);

        $this->assertDatabaseHas('quiz_results', [
            'quiz_id' => $quiz->quiz_id,
            'student_id' => $this->student->student_id,
            'score' => 20,
            'percentage' => 50,
        ]);
    }
}
