<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information (name, email, role-specific fields, photo).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $data['profile_photo'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }

        // Update users table
        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        // Update role-specific records
        if ($user->isStudent() && $user->student) {
            $studentData = [];
            foreach (['phone', 'gender', 'date_of_birth', 'address'] as $field) {
                if (array_key_exists($field, $data)) {
                    $studentData[$field] = $data[$field];
                }
            }
            // Handle student profile photo
            if ($request->hasFile('profile_photo')) {
                if ($user->student->profile_photo) {
                    Storage::disk('public')->delete($user->student->profile_photo);
                }
                $studentData['profile_photo'] = $data['profile_photo'];
            }
            if (!empty($studentData)) {
                $user->student()->update($studentData);
            }
        }

        if ($user->isInstructor() && $user->instructor) {
            $instructorData = [];
            foreach (['specialization', 'biography'] as $field) {
                if (array_key_exists($field, $data)) {
                    $instructorData[$field] = $data[$field];
                }
            }
            // Handle instructor profile photo
            if ($request->hasFile('profile_photo')) {
                if ($user->instructor->profile_photo) {
                    Storage::disk('public')->delete($user->instructor->profile_photo);
                }
                $instructorData['profile_photo'] = $data['profile_photo'];
            }
            if (!empty($instructorData)) {
                $user->instructor()->update($instructorData);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
