<?php

namespace Tests\Unit\Models;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_assignment()
    {
        $course = Course::factory()->create();

        $assignment = Assignment::create([
            'course_id' => $course->course_id,
            'title' => 'Final Project',
            'description' => 'Build a web application',
            'total_marks' => 100,
        ]);

        $this->assertNotNull($assignment);
        $this->assertEquals('Final Project', $assignment->title);
        $this->assertEquals(100, $assignment->total_marks);
    }

    public function test_assignment_belongs_to_course()
    {
        $course = Course::factory()->create();
        $assignment = Assignment::factory()->create(['course_id' => $course->course_id]);

        $this->assertInstanceOf(Course::class, $assignment->course);
        $this->assertEquals($course->course_id, $assignment->course->course_id);
    }

    public function test_assignment_has_many_submissions()
    {
        $assignment = Assignment::factory()->create();
        $submissions = AssignmentSubmission::factory(3)->create([
            'assignment_id' => $assignment->assignment_id,
        ]);

        $this->assertCount(3, $assignment->submissions);
    }

    public function test_assignment_due_date_can_be_set()
    {
        $dueDate = now()->addDays(7);
        $assignment = Assignment::factory()->create([
            'due_date' => $dueDate,
        ]);

        $this->assertNotNull($assignment->due_date);
        $this->assertTrue($assignment->due_date->isFuture());
    }

    public function test_assignment_marks_are_decimal()
    {
        $assignment = Assignment::factory()->create([
            'total_marks' => 99.99,
        ]);

        $this->assertEquals(99.99, $assignment->total_marks);
    }
}
