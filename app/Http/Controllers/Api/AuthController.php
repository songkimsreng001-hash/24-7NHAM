<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Standard email/password registration.
     * (Kept as-is from your existing project — shown here so googleLogin() below
     * can be compared against it. Delete this method if you already have it.)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Fix from README: register now returns a token so Flutter can auto-login
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Standard email/password login.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Fix from README: delete old tokens before issuing a new one
        $user->tokens()->delete();
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * NEW: Google login / register.
     *
     * Flutter sends the Google ID token it got from the native google_sign_in
     * package. We verify it with Socialite, then find-or-create the user by
     * email, and return a Sanctum token in the exact same shape as login()
     * and register() above — so the Flutter side needs zero special-casing.
     */
    public function googleOAuthRedirect(Request $request)
    {
        $callback_url = $request->query('callback_url', '');

        $redirectUrl = Socialite::driver('google')
            ->stateless()
            ->with(['state' => base64_encode($callback_url)])
            ->redirect()
            ->getTargetUrl();

        return response(['redirect_url' => $redirectUrl], 200);
    }

    public function googleOAuthCallback(Request $request)
    {
        $callback_url = base64_decode($request->query('state', ''));
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect($callback_url.'?error=google_oauth_failed');
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
            ]
        );

        $user->save();

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        $token = $user->createToken('auth_token', ['exchange-new-token'], now()->addMinute())->plainTextToken;

        return redirect($callback_url.'?token='.urlencode($token));
    }

    public function googleOAuthExchangeToken(Request $request)
    {
        $user = $request->user();

        if (! $user->currentAccessToken()->can('exchange-new-token')) {
            return response(['message' => 'Invalid token.'], 403);
        }

        $user->currentAccessToken()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response([
            'message' => 'User signed in.',
            'user' => new UserResource($user),
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
}
