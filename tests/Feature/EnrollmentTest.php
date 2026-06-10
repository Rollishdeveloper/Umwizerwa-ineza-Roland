<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private $studentUser;
    private $student;
    private $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->studentUser = User::factory()->create(['role' => 'student']);
        $this->student = Student::factory()->create([
            'user_id' => $this->studentUser->id,
        ]);

        $instructor = Instructor::factory()->create();
        $category = Category::factory()->create();
        $this->course = Course::factory()->create([
            'instructor_id' => $instructor->instructor_id,
            'category_id' => $category->category_id,
            'status' => 'published',
        ]);
    }

    public function test_student_can_enroll_in_course()
    {
        $this->actingAs($this->studentUser);

        $response = $this->post(route('enrollments.store', $this->course));

        $response->assertStatus(302);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
            'status' => 'active',
        ]);
    }

    public function test_student_cannot_enroll_twice()
    {
        $this->actingAs($this->studentUser);

        Enrollment::factory()->create([
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
        ]);

        $response = $this->post(route('enrollments.store', $this->course));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_guest_cannot_enroll()
    {
        $response = $this->post(route('enrollments.store', $this->course));

        $response->assertRedirect(route('login'));
    }

    public function test_student_can_view_their_enrollments()
    {
        $this->actingAs($this->studentUser);

        Enrollment::factory(3)->create([
            'student_id' => $this->student->student_id,
        ]);

        $response = $this->get(route('enrollments.index'));

        $response->assertStatus(200);
    }

    public function test_enrollment_progress_can_be_updated()
    {
        $this->actingAs($this->studentUser);

        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
        ]);

        $response = $this->post(route('enrollments.progress', $enrollment), [
            'completion_percentage' => 50,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('enrollments', [
            'enroll_id' => $enrollment->enroll_id,
            'completion_percentage' => 50,
        ]);
    }

    public function test_enrollment_marked_completed_at_100_percent()
    {
        $this->actingAs($this->studentUser);

        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
        ]);

        $this->post(route('enrollments.progress', $enrollment), [
            'completion_percentage' => 100,
        ]);

        $this->assertDatabaseHas('enrollments', [
            'enroll_id' => $enrollment->enroll_id,
            'completion_percentage' => 100,
            'status' => 'completed',
        ]);
    }

    public function test_student_can_drop_enrollment()
    {
        $this->actingAs($this->studentUser);

        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
        ]);

        $response = $this->delete(route('enrollments.destroy', $enrollment));

        $response->assertStatus(302);
        $this->assertDatabaseHas('enrollments', [
            'enroll_id' => $enrollment->enroll_id,
            'status' => 'dropped',
        ]);
    }
}
