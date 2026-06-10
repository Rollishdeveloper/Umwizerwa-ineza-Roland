<?php

namespace Tests\Unit\Models;

use App\Models\Course;
use App\Models\Category;
use App\Models\Instructor;
use App\Models\Module;
use App\Models\Enrollment;
use App\Models\Quiz;
use App\Models\Assignment;
use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_course_with_valid_data()
    {
        $instructor = Instructor::factory()->create();
        $category = Category::factory()->create();

        $course = Course::create([
            'instructor_id' => $instructor->instructor_id,
            'title' => 'Introduction to PHP',
            'category_id' => $category->category_id,
            'level' => 'beginner',
            'status' => 'draft',
        ]);

        $this->assertNotNull($course);
        $this->assertEquals('Introduction to PHP', $course->title);
        $this->assertEquals('beginner', $course->level);
        $this->assertEquals('draft', $course->status);
    }

    public function test_course_auto_generates_slug_on_create()
    {
        $course = Course::factory()->create([
            'title' => 'My Test Course',
            'slug' => '',
        ]);

        $this->assertNotEmpty($course->slug);
        $this->assertStringStartsWith('my-test-course', $course->slug);
    }

    public function test_course_belongs_to_instructor()
    {
        $instructor = Instructor::factory()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->instructor_id]);

        $this->assertInstanceOf(Instructor::class, $course->instructor);
        $this->assertEquals($instructor->instructor_id, $course->instructor->instructor_id);
    }

    public function test_course_belongs_to_category()
    {
        $category = Category::factory()->create();
        $course = Course::factory()->create(['category_id' => $category->category_id]);

        $this->assertInstanceOf(Category::class, $course->category);
        $this->assertEquals($category->category_id, $course->category->category_id);
    }

    public function test_course_has_many_modules()
    {
        $course = Course::factory()->create();
        $module = Module::factory()->create(['course_id' => $course->course_id]);

        $this->assertTrue($course->modules->contains($module));
        $this->assertCount(1, $course->modules);
    }

    public function test_course_has_many_enrollments()
    {
        $course = Course::factory()->create();
        $enrollment = Enrollment::factory()->create(['course_id' => $course->course_id]);

        $this->assertTrue($course->enrollments->contains($enrollment));
        $this->assertCount(1, $course->enrollments);
    }

    public function test_course_has_many_quizzes()
    {
        $course = Course::factory()->create();
        $quiz = Quiz::factory()->create(['course_id' => $course->course_id]);

        $this->assertTrue($course->quizzes->contains($quiz));
        $this->assertCount(1, $course->quizzes);
    }

    public function test_course_has_many_assignments()
    {
        $course = Course::factory()->create();
        $assignment = Assignment::factory()->create(['course_id' => $course->course_id]);

        $this->assertTrue($course->assignments->contains($assignment));
        $this->assertCount(1, $course->assignments);
    }

    public function test_published_scope_only_returns_published_courses()
    {
        Course::factory()->create(['status' => 'published']);
        Course::factory()->create(['status' => 'draft']);
        Course::factory()->create(['status' => 'archived']);

        $published = Course::published()->get();

        $this->assertCount(1, $published);
        $this->assertEquals('published', $published->first()->status);
    }

    public function test_by_level_scope_filters_by_level()
    {
        Course::factory()->create(['level' => 'beginner']);
        Course::factory()->create(['level' => 'intermediate']);
        Course::factory()->create(['level' => 'advanced']);

        $beginners = Course::byLevel('beginner')->get();

        $this->assertCount(1, $beginners);
        $this->assertEquals('beginner', $beginners->first()->level);
    }

    public function test_course_price_defaults_to_zero()
    {
        $course = Course::factory()->create(['price' => 0]);

        $this->assertEquals(0, $course->price);
    }

    public function test_course_can_have_certificates()
    {
        $course = Course::factory()->create();
        $certificate = Certificate::factory()->create(['course_id' => $course->course_id]);

        $this->assertTrue($course->certificates->contains($certificate));
    }
}
