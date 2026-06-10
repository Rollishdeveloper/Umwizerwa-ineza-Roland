<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Student;
use App\Models\Instructor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user_with_role_student()
    {
        $user = User::factory()->create([
            'role' => 'student',
        ]);

        $this->assertTrue($user->isStudent());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isInstructor());
        $this->assertEquals('student', $user->role);
    }

    public function test_can_create_user_with_role_admin()
    {
        $user = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isStudent());
        $this->assertFalse($user->isInstructor());
    }

    public function test_can_create_user_with_role_instructor()
    {
        $user = User::factory()->create([
            'role' => 'instructor',
        ]);

        $this->assertTrue($user->isInstructor());
        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isStudent());
    }

    public function test_user_has_student_relationship()
    {
        $user = User::factory()->create(['role' => 'student']);
        $student = Student::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Student::class, $user->student);
        $this->assertEquals($student->student_id, $user->student->student_id);
    }

    public function test_user_has_instructor_relationship()
    {
        $user = User::factory()->create(['role' => 'instructor']);
        $instructor = Instructor::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(Instructor::class, $user->instructor);
        $this->assertEquals($instructor->instructor_id, $user->instructor->instructor_id);
    }

    public function test_active_scope_filters_active_users()
    {
        User::factory()->create(['status' => 'active']);
        User::factory()->create(['status' => 'inactive']);

        $activeUsers = User::active()->get();

        $this->assertCount(1, $activeUsers);
        $this->assertEquals('active', $activeUsers->first()->status);
    }

    public function test_user_default_status_is_active()
    {
        $user = User::factory()->create();

        $this->assertEquals('active', $user->status);
    }

    public function test_user_can_be_suspended()
    {
        $user = User::factory()->create(['status' => 'active']);

        $user->update(['status' => 'suspended']);

        $this->assertEquals('suspended', $user->fresh()->status);
    }
}
