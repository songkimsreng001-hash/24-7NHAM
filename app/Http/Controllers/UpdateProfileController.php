<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function editProfile(User $user)
    {
        $user = Auth::user();

        return view('auth.editprofile', compact('user'));
    }

    public function updateProfile(Request $request, User $user)
    {
        $authUser = Auth::user();

        // Ensure user can only update their own profile unless admin
        if ($authUser->id !== $user->id && ! $authUser->isAdmin()) {
            abort(403);
        }

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'phone' => ['required', 'string', 'min:8', 'max:25', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'unique:users,phone,'.$user->id],
            'cropped_avatar_data' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if (empty($value)) {
                        return;
                    }
                    if (! str_starts_with($value, 'data:image/')) {
                        $fail('The cropped photo must be a valid image data string.');

                        return;
                    }
                    if (! preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,/i', $value)) {
                        $fail('The cropped photo format must be PNG, JPG, JPEG, or WEBP.');

                        return;
                    }
                },
            ],
            'avatar' => [
                'nullable',
                'file',
                'max:5120', // 5MB
                function ($attribute, $value, $fail) {
                    if (! $value || ! $value->isValid()) {
                        return;
                    }

                    // Strict extension check (case-insensitive, supporting PNG, JPG, JPEG, WEBP and .PNG, .JPG, .JPEG, .WEBP)
                    $extension = strtolower($value->getClientOriginalExtension());
                    $allowedExtensions = ['png', 'jpg', 'jpeg', 'webp'];
                    if (! in_array($extension, $allowedExtensions, true)) {
                        $fail('The profile photo must be a file of type: PNG, JPG, JPEG, or WEBP.');

                        return;
                    }

                    // Strict MIME type check
                    $mime = strtolower((string) $value->getMimeType());
                    $allowedMimes = ['image/png', 'image/jpeg', 'image/pjpeg', 'image/webp', 'image/x-png'];
                    if (! in_array($mime, $allowedMimes, true)) {
                        $fail('The uploaded profile photo must be a valid PNG, JPG, JPEG, or WEBP image.');

                        return;
                    }
                },
            ],
            'remove_avatar' => ['nullable', 'boolean'],
        ];

        if ($user->isDriver()) {
            $rules['vehicle_type'] = ['nullable', 'string', 'in:motorbike,bicycle,car,scooter'];
            $rules['license_plate'] = ['nullable', 'string', 'max:50'];
        }

        $validated = $request->validate($rules);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'];

        if ($user->isDriver()) {
            $user->vehicle_type = $validated['vehicle_type'] ?? $user->vehicle_type;
            $user->license_plate = $validated['license_plate'] ?? $user->license_plate;
        }

        // Handle profile photo upload (cropped data takes priority, then raw avatar file, then removal)
        if ($request->filled('cropped_avatar_data') && str_starts_with($request->cropped_avatar_data, 'data:image/')) {
            $data = $request->cropped_avatar_data;
            if (preg_match('/^data:image\/(\w+);base64,/i', $data, $matches)) {
                $ext = strtolower($matches[1]);
                if ($ext === 'jpeg') {
                    $ext = 'jpg';
                }
                if (in_array($ext, ['png', 'jpg', 'webp'], true)) {
                    $base64 = substr($data, strpos($data, ',') + 1);
                    $decoded = base64_decode($base64);
                    if ($decoded !== false) {
                        $destDir = public_path('img/avatars');
                        if (! file_exists($destDir)) {
                            @mkdir($destDir, 0755, true);
                        }
                        if ($user->avatar && file_exists(public_path('img/avatars/'.$user->avatar))) {
                            @unlink(public_path('img/avatars/'.$user->avatar));
                        }
                        $filename = 'avatar_'.$user->id.'_'.time().'_'.uniqid().'.'.$ext;
                        file_put_contents($destDir.'/'.$filename, $decoded);
                        $user->avatar = $filename;
                    }
                }
            }
        } elseif ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $destDir = public_path('img/avatars');
            if (! file_exists($destDir)) {
                @mkdir($destDir, 0755, true);
            }

            // Clean up old local avatar if exists
            if ($user->avatar && file_exists(public_path('img/avatars/'.$user->avatar))) {
                @unlink(public_path('img/avatars/'.$user->avatar));
            }

            $ext = strtolower($file->getClientOriginalExtension()) ?: 'png';
            $filename = 'avatar_'.$user->id.'_'.time().'_'.uniqid().'.'.$ext;
            $file->move($destDir, $filename);

            $user->avatar = $filename;
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar && file_exists(public_path('img/avatars/'.$user->avatar))) {
                @unlink(public_path('img/avatars/'.$user->avatar));
            }
            $user->avatar = null;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
