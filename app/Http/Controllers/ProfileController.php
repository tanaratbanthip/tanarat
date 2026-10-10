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
use Cloudinary\Cloudinary;

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

            // ดึง CLOUDINARY_URL จาก Environment หรือ Config
            $cloudinaryUrl = env('CLOUDINARY_URL') ?: config('cloudinary.cloud_url');

            if (!$cloudinaryUrl) {
                return back()->with('error', 'ไม่สามารถอัปโหลดได้: ยังไม่ได้ตั้งค่า CLOUDINARY_URL ในระบบ');
            }

            try {
                // เรียกใช้ Cloudinary SDK หลักโดยตรง (ข้าม Service Provider ที่มีปัญหา)
                $cloudinary = new Cloudinary($cloudinaryUrl);

                $response = $cloudinary->uploadApi()->upload(
                    $request->file('avatar')->getRealPath(),
                    [
                        'folder' => 'avatars',
                        'resource_type' => 'image',
                    ]
                );

                // ได้ URL ที่ปลอดภัย (HTTPS)
                $user->avatar = $response['secure_url'];
            } catch (\Exception $e) {
                return back()->with('error', 'ไม่สามารถอัปโหลดรูปภาพได้: ' . $e->getMessage());
            }
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
