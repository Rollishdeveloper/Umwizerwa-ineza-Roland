<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Category;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AssignmentManagementTest extends TestCase
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

    public function test_instructor_can_create_assignment()
    {
        $this->actingAs($this->instructorUser);

        $response = $this->post(route('assignments.store', $this->course), [
            'title' => 'Final Project',
            'description' => 'Build something amazing',
            'total_marks' => 100,
            'due_date' => now()->addDays(7)->format('Y-m-d\TH:i'),
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('assignments', [
            'course_id' => $this->course->course_id,
            'title' => 'Final Project',
            'total_marks' => 100,
        ]);
    }

    public function test_instructor_can_view_assignment_submissions()
    {
        $this->actingAs($this->instructorUser);
        $assignment = Assignment::factory()->create([
            'course_id' => $this->course->course_id,
        ]);

        $response = $this->get(route('assignments.show', [$this->course, $assignment]));

        $response->assertStatus(200);
        $response->assertSee($assignment->title);
    }

    public function test_guest_cannot_submit_assignment()
    {
        $assignment = Assignment::factory()->create([
            'course_id' => $this->course->course_id,
        ]);

        $response = $this->post(route('assignments.submit', [$this->course, $assignment]), [
            'file' => UploadedFile::fake()->create('document.pdf', 100),
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_assignment_requires_title()
    {
        $this->actingAs($this->instructorUser);

        $response = $this->post(route('assignments.store', $this->course), [
            'total_marks' => 100,
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_assignment_total_marks_must_be_positive()
    {
        $this->actingAs($this->instructorUser);

        $response = $this->post(route('assignments.store', $this->course), [
            'title' => 'Test',
            'total_marks' => 0,
        ]);

        $response->assertSessionHasErrors('total_marks');
    }

    public function test_instructor_can_grade_submission()
    {
        $this->actingAs($this->instructorUser);
        $assignment = Assignment::factory()->create([
            'course_id' => $this->course->course_id,
            'total_marks' => 100,
        ]);
        $submission = AssignmentSubmission::factory()->create([
            'assignment_id' => $assignment->assignment_id,
            'student_id' => $this->student->student_id,
        ]);

        $response = $this->post(
            route('assignments.grade', [$this->course, $assignment, $submission]),
            [
                'marks' => 85,
                'feedback' => 'Great work!',
            ]
        );

        $response->assertStatus(302);
        $this->assertDatabaseHas('assignment_submissions', [
            'submission_id' => $submission->submission_id,
            'marks' => 85,
            'feedback' => 'Great work!',
        ]);
    }

    public function test_grade_cannot_exceed_total_marks()
    {
        $this->actingAs($this->instructorUser);
        $assignment = Assignment::factory()->create([
            'course_id' => $this->course->course_id,
            'total_marks' => 100,
        ]);
        $submission = AssignmentSubmission::factory()->create([
            'assignment_id' => $assignment->assignment_id,
            'student_id' => $this->student->student_id,
        ]);

        $response = $this->post(
            route('assignments.grade', [$this->course, $assignment, $submission]),
            ['marks' => 150]
        );

        $response->assertSessionHasErrors('marks');
    }
}
