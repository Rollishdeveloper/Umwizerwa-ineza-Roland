<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Category;
use App\Models\Enrollment;
use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateTest extends TestCase
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
        ]);
    }

    public function test_certificate_generated_after_course_completion()
    {
        $this->actingAs($this->studentUser);

        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
            'completion_percentage' => 100,
        ]);

        $response = $this->post(route('certificates.generate', $enrollment));

        $response->assertStatus(302);
        $this->assertDatabaseHas('certificates', [
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
        ]);
    }

    public function test_certificate_not_generated_for_incomplete_course()
    {
        $this->actingAs($this->studentUser);

        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
            'completion_percentage' => 50,
        ]);

        $response = $this->post(route('certificates.generate', $enrollment));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('certificates', [
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
        ]);
    }

    public function test_duplicate_certificate_not_created()
    {
        $this->actingAs($this->studentUser);

        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
            'completion_percentage' => 100,
        ]);

        Certificate::factory()->create([
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
        ]);

        $response = $this->post(route('certificates.generate', $enrollment));

        $response->assertStatus(302);
        $this->assertDatabaseCount('certificates', 1);
    }

    public function test_certificate_has_unique_number()
    {
        $cert1 = Certificate::factory()->create();
        $cert2 = Certificate::factory()->create();

        $this->assertNotEquals($cert1->certificate_number, $cert2->certificate_number);
    }

    public function test_certificate_can_be_verified()
    {
        $this->actingAs($this->studentUser);

        $certificate = Certificate::factory()->create();

        $response = $this->get(route('certificates.verify', [
            'certificate_number' => $certificate->certificate_number,
        ]));

        $response->assertStatus(200);
        $response->assertSee($certificate->certificate_number);
    }

    public function test_invalid_certificate_shows_error()
    {
        $this->actingAs($this->studentUser);

        $response = $this->get(route('certificates.verify', [
            'certificate_number' => 'INVALID-123',
        ]));

        $response->assertStatus(302);
        $response->assertSessionHas('error');
    }

    public function test_student_can_view_their_certificates()
    {
        $this->actingAs($this->studentUser);

        Certificate::factory(2)->create([
            'student_id' => $this->student->student_id,
        ]);

        $response = $this->get(route('certificates.index'));

        $response->assertStatus(200);
    }

    public function test_completed_enrollment_status_on_certificate_generation()
    {
        $this->actingAs($this->studentUser);

        $enrollment = Enrollment::factory()->create([
            'student_id' => $this->student->student_id,
            'course_id' => $this->course->course_id,
            'completion_percentage' => 100,
            'status' => 'active',
        ]);

        $this->post(route('certificates.generate', $enrollment));

        $this->assertDatabaseHas('enrollments', [
            'enroll_id' => $enrollment->enroll_id,
            'status' => 'completed',
        ]);
    }
}
