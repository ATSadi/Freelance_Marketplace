<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\UpdateWorkProfileRequest;
use App\Models\User;
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
        $user = $request->user();
        $profile = $user->profile()->firstOrCreate([]);

        return view('profile.edit', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    /**
     * Update the user's account information (name, email).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's WorkVault work profile (bio, skills, photo, etc.).
     */
    public function updateWorkProfile(UpdateWorkProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $user->profile()->firstOrCreate([]);

        $data = $request->safe()->except(['profile_photo']);

        // Clear role-specific fields that do not apply.
        if ($user->role === User::ROLE_CLIENT) {
            $data['skills'] = null;
            $data['hourly_rate'] = null;
        }

        if ($user->role === User::ROLE_FREELANCER) {
            $data['company_name'] = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($profile->profile_photo_path) {
                Storage::disk('public')->delete($profile->profile_photo_path);
            }

            $data['profile_photo_path'] = $request->file('profile_photo')->store('profiles', 'public');
        }

        $profile->update($data);

        return Redirect::route('profile.edit')->with('status', 'work-profile-updated');
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
