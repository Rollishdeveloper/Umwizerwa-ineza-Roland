<?php

namespace Tests\Unit\Models;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuizResult;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_quiz()
    {
        $course = Course::factory()->create();

        $quiz = Quiz::create([
            'course_id' => $course->course_id,
            'title' => 'PHP Basics Quiz',
            'total_marks' => 100,
            'passing_marks' => 60,
        ]);

        $this->assertNotNull($quiz);
        $this->assertEquals('PHP Basics Quiz', $quiz->title);
        $this->assertEquals(100, $quiz->total_marks);
        $this->assertEquals(60, $quiz->passing_marks);
    }

    public function test_quiz_belongs_to_course()
    {
        $course = Course::factory()->create();
        $quiz = Quiz::factory()->create(['course_id' => $course->course_id]);

        $this->assertInstanceOf(Course::class, $quiz->course);
        $this->assertEquals($course->course_id, $quiz->course->course_id);
    }

    public function test_quiz_has_many_questions()
    {
        $quiz = Quiz::factory()->create();
        $questions = Question::factory(3)->create(['quiz_id' => $quiz->quiz_id]);

        $this->assertCount(3, $quiz->questions);
        $this->assertTrue($quiz->questions->contains($questions->first()));
    }

    public function test_quiz_has_many_results()
    {
        $quiz = Quiz::factory()->create();
        $results = QuizResult::factory(2)->create(['quiz_id' => $quiz->quiz_id]);

        $this->assertCount(2, $quiz->results);
    }

    public function test_quiz_duration_can_be_null()
    {
        $quiz = Quiz::factory()->create(['duration_minutes' => null]);

        $this->assertNull($quiz->duration_minutes);
    }

}
