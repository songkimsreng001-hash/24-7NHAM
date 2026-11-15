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

        if ($request->has('phone')) {
            $cleanPhone = preg_replace('/[^0-9]/', '', (string) $request->phone);
            $request->merge(['phone' => $cleanPhone]);
        }

        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'phone' => [
                'required',
                'string',
                'unique:users,phone,'.$user->id,
                function ($attribute, $value, $fail) {
                    $clean = preg_replace('/[^0-9]/', '', (string) $value);
                    if (!str_starts_with($clean, '0')) {
                        $fail('The phone number must start with 0.');
                    } elseif (str_starts_with($clean, '088') || str_starts_with($clean, '096')) {
                        if (strlen($clean) !== 10) {
                            $fail('Phone numbers starting with 088 or 096 must be exactly 10 digits.');
                        }
                    } else {
                        if (strlen($clean) !== 9) {
                            $fail('Phone numbers must be exactly 9 digits (or 10 digits for 088 and 096).');
                        }
                    }
                },
            ],
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
            if ($user->avatar && !str_starts_with($user->avatar, 'data:image') && file_exists(public_path('img/avatars/'.$user->avatar))) {
                @unlink(public_path('img/avatars/'.$user->avatar));
            }

            $destDir = public_path('img/avatars');
            $saved = false;
            if (@is_writable($destDir) || (!file_exists($destDir) && @mkdir($destDir, 0755, true) && @is_writable($destDir))) {
                if (preg_match('/^data:image\/(\w+);base64,/i', $data, $matches)) {
                    $ext = strtolower($matches[1]) === 'jpeg' ? 'jpg' : strtolower($matches[1]);
                    $decoded = base64_decode(substr($data, strpos($data, ',') + 1));
                    if ($decoded !== false) {
                        $filename = 'avatar_'.$user->id.'_'.time().'_'.uniqid().'.'.$ext;
                        if (@file_put_contents($destDir.'/'.$filename, $decoded) !== false) {
                            $user->avatar = $filename;
                            $saved = true;
                        }
                    }
                }
            }

            // Fall back to direct Data URI on serverless platforms
            if (! $saved) {
                $user->avatar = $data;
            }
        } elseif ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            if ($user->avatar && !str_starts_with($user->avatar, 'data:image') && file_exists(public_path('img/avatars/'.$user->avatar))) {
                @unlink(public_path('img/avatars/'.$user->avatar));
            }

            $destDir = public_path('img/avatars');
            $saved = false;
            if (@is_writable($destDir) || (!file_exists($destDir) && @mkdir($destDir, 0755, true) && @is_writable($destDir))) {
                try {
                    $ext = strtolower($file->getClientOriginalExtension()) ?: 'png';
                    $filename = 'avatar_'.$user->id.'_'.time().'_'.uniqid().'.'.$ext;
                    $file->move($destDir, $filename);
                    $user->avatar = $filename;
                    $saved = true;
                } catch (\Throwable $e) {
                    // Fall back to Data URI
                }
            }

            // Fall back to Data URI on serverless platforms
            if (! $saved) {
                $mime = $file->getMimeType() ?: 'image/jpeg';
                $user->avatar = 'data:'.$mime.';base64,'.base64_encode(file_get_contents($file->getRealPath()));
            }
        } elseif ($request->boolean('remove_avatar')) {
            if ($user->avatar && !str_starts_with($user->avatar, 'data:image') && file_exists(public_path('img/avatars/'.$user->avatar))) {
                @unlink(public_path('img/avatars/'.$user->avatar));
            }
            $user->avatar = null;
        }

        $user->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Serve user avatar directly as binary stream.
     * Prevents large base64 strings from bloating HTML responses and causing Vercel FUNCTION_RESPONSE_PAYLOAD_TOO_LARGE.
     */
    public function serveAvatar($id)
    {
        $user = \App\Models\User::select('id', 'avatar', 'name', 'updated_at')->find($id);

        if (! $user || ! $user->avatar) {
            $name = $user ? $user->name : 'User';
            return redirect('https://ui-avatars.com/api/?name=' . urlencode($name) . '&background=3b82f6&color=ffffff&size=200');
        }

        if (str_starts_with($user->avatar, 'data:image')) {
            $commaPos = strpos($user->avatar, ',');
            if ($commaPos !== false) {
                $header = substr($user->avatar, 0, $commaPos);
                $data = substr($user->avatar, $commaPos + 1);

                preg_match('/^data:([^;]+);base64/', $header, $matches);
                $mime = $matches[1] ?? 'image/jpeg';
                $binary = base64_decode($data);

                if ($binary !== false) {
                    $etag = '"' . md5($user->id . '_' . ($user->updated_at?->timestamp ?? '')) . '"';

                    if (request()->header('If-None-Match') === $etag) {
                        return response('', 304, [
                            'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
                            'ETag' => $etag,
                        ]);
                    }

                    return response($binary, 200, [
                        'Content-Type' => $mime,
                        'Content-Length' => strlen($binary),
                        'Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
                        'ETag' => $etag,
                    ]);
                }
            }
        }

        if (str_starts_with($user->avatar, 'http://') || str_starts_with($user->avatar, 'https://')) {
            return redirect($user->avatar);
        }

        return redirect(asset('img/avatars/' . $user->avatar));
    }
}
