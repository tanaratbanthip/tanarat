<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    /**
     * Update the user's profile information.
     */




public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();
    $data = $request->validated();

    if ($request->hasFile('avatar')) {
        $request->validate([
            'avatar' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // อัปโหลดขึ้น Cloudinary โฟลเดอร์ avatars
        $uploaded = $request->file('avatar')->storeOnCloudinary('avatars');
        $data['avatar'] = $uploaded->getSecurePath();
    }

    $user->fill($data);

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->save();

    return Redirect::route('profile.edit')->with('success', 'อัปเดตข้อมูลโปรไฟล์และรูปภาพเรียบร้อยแล้ว');
}
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
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
