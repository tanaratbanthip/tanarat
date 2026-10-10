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

            $cloudinaryUrl = env('CLOUDINARY_URL') ?: config('cloudinary.cloud_url');

            if (!$cloudinaryUrl) {
                return back()->with('error', 'ไม่สามารถอัปโหลดได้: ยังไม่ได้ตั้งค่า CLOUDINARY_URL ในระบบ');
            }

            try {
                $cloudinary = new Cloudinary($cloudinaryUrl);

                // 1. ลบรูป Avatar เดิมออกจาก Cloudinary (ถ้ามี)
                if ($user->avatar && str_starts_with($user->avatar, 'http')) {
                    $this->deleteCloudinaryImage($cloudinary, $user->avatar);
                }

                // 2. อัปโหลดรูปใหม่พร้อมบีบอัดและปรับขนาด 300x300 px
                $response = $cloudinary->uploadApi()->upload(
                    $request->file('avatar')->getRealPath(),
                    [
                        'folder' => 'avatars',
                        'resource_type' => 'image',
                        'transformation' => [
                            [
                                'width' => 300,
                                'height' => 300,
                                'crop' => 'fill',
                                'gravity' => 'face', // โฟกัสใบหน้าอัตโนมัติ
                                'quality' => 'auto',
                                'fetch_format' => 'auto',
                            ],
                        ],
                    ]
                );

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

        // ลบรูปภาพ Avatar ออกจาก Cloudinary เมื่อลบบัญชี
        if ($user->avatar && str_starts_with($user->avatar, 'http')) {
            $cloudinaryUrl = env('CLOUDINARY_URL') ?: config('cloudinary.cloud_url');
            if ($cloudinaryUrl) {
                try {
                    $cloudinary = new Cloudinary($cloudinaryUrl);
                    $this->deleteCloudinaryImage($cloudinary, $user->avatar);
                } catch (\Exception $e) {
                    // ข้ามข้อผิดพลาดเพื่อให้กระบวนการลบบัญชีดำเนินการต่อได้
                }
            }
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    /**
     * ดึง public_id และลบรูปภาพจาก Cloudinary
     */
    private function deleteCloudinaryImage(Cloudinary $cloudinary, string $imageUrl): void
    {
        // แกะเอา public_id ออกจาก URL เช่น /avatars/xyz
        if (preg_match('#/upload/(?:(?:[a-zA-Z]_[^/,]+,?)+/)?(?:v\d+/)?(.+?)(?:\.[a-zA-Z0-9]+)?$#', $imageUrl, $matches)) {
            $publicId = $matches[1];
            $cloudinary->uploadApi()->destroy($publicId);
        }
    }
}
