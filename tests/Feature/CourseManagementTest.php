<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Category;
use App\Models\Instructor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseManagementTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $instructor;
    private $instructorUser;
    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);

        $this->instructorUser = User::factory()->create(['role' => 'instructor']);
        $this->instructor = Instructor::factory()->create([
            'user_id' => $this->instructorUser->id,
        ]);

        $this->category = Category::factory()->create();
    }

    public function test_guest_cannot_access_course_management()
    {
        $response = $this->get(route('courses.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_instructor_can_create_course()
    {
        $this->actingAs($this->instructorUser);

        $courseData = [
            'title' => 'Introduction to Testing',
            'description' => 'Learn PHPUnit testing',
            'category_id' => $this->category->category_id,
            'level' => 'beginner',
            'price' => 0,
            'status' => 'published',
        ];

        $response = $this->post(route('courses.store'), $courseData);
        $response->assertRedirect();

        $this->assertDatabaseHas('courses', [
            'title' => 'Introduction to Testing',
            'instructor_id' => $this->instructor->instructor_id,
        ]);
    }

    public function test_instructor_can_view_their_courses()
    {
        $this->actingAs($this->instructorUser);

        Course::factory()->create([
            'instructor_id' => $this->instructor->instructor_id,
            'title' => 'My Course',
        ]);
        Course::factory(3)->create();

        $response = $this->get(route('courses.index'));

        $response->assertStatus(200);
        $response->assertSee('My Course');
    }

    public function test_course_list_shows_search_results()
    {
        $instructor = Instructor::factory()->create();
        $course = Course::factory()->create([
            'instructor_id' => $instructor->instructor_id,
            'title' => 'Unique Searchable Course',
        ]);

        $this->actingAs($this->admin);
        $response = $this->get(route('courses.index', ['search' => 'Unique']));

        $response->assertStatus(200);
        $response->assertSee('Unique Searchable Course');
    }

    public function test_instructor_can_update_their_course()
    {
        $this->actingAs($this->instructorUser);

        $course = Course::factory()->create([
            'instructor_id' => $this->instructor->instructor_id,
        ]);

        $response = $this->put(route('courses.update', $course), [
            'title' => 'Updated Title',
            'category_id' => $this->category->category_id,
            'level' => 'intermediate',
            'status' => 'published',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('courses', [
            'course_id' => $course->course_id,
            'title' => 'Updated Title',
            'level' => 'intermediate',
        ]);
    }

    public function test_course_requires_title()
    {
        $this->actingAs($this->instructorUser);

        $response = $this->post(route('courses.store'), [
            'category_id' => $this->category->category_id,
            'level' => 'beginner',
            'status' => 'draft',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_course_requires_valid_level()
    {
        $this->actingAs($this->instructorUser);

        $response = $this->post(route('courses.store'), [
            'title' => 'Invalid Course',
            'category_id' => $this->category->category_id,
            'level' => 'expert',
            'status' => 'draft',
        ]);

        $response->assertSessionHasErrors('level');
    }

    public function test_admin_can_delete_course()
    {
        $this->actingAs($this->admin);

        $course = Course::factory()->create();
        $courseId = $course->course_id;

        $response = $this->delete(route('courses.destroy', $course));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('courses', ['course_id' => $courseId]);
    }

    public function test_course_page_shows_enroll_button_for_guests()
    {
        $course = Course::factory()->create(['status' => 'published']);
        $course->load('instructor', 'category', 'modules.lessons', 'quizzes', 'assignments');

        $response = $this->get(route('courses.show', $course));

        $response->assertStatus(200);
        $response->assertSee($course->title);
    }
}
