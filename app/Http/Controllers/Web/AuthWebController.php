<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        return view('web.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors(['email' => 'Invalid credentials.']);
        }

        $token = $user->createToken('web-token')->plainTextToken;

        session([
            'web_token' => $token,
            'web_user' => [
                'id' => (string) $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ],
            'is_admin' => in_array($user->email, config('admin.emails', [])),
        ]);

        if (session('is_admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('web.dashboard');
    }

    public function showRegister()
    {
        return view('web.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'gender' => $request->gender,
        ]);

        $token = $user->createToken('web-token')->plainTextToken;

        session([
            'web_token' => $token,
            'web_user' => [
                'id' => (string) $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
            ],
            'is_admin' => false,
        ]);

        return redirect()->route('web.tags');
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['web_token', 'web_user', 'is_admin']);
        return redirect()->route('web.login');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['redirect_uri' => config('services.google.redirect')])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->with(['redirect_uri' => config('services.google.redirect')])
                ->stateless()
                ->user();
        } catch (\Throwable $e) {
            return redirect()->route('web.login')
                ->withErrors(['email' => 'Google login failed: ' . $e->getMessage()]);
        }

        // Cari atau buat user
        $user = User::where('email', $googleUser->getEmail())->first();

        if ($user === null) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            // Update google_id dan avatar kalau belum ada
            if (empty($user->google_id)) {
                $user->google_id = $googleUser->getId();
                $user->avatar    = $googleUser->getAvatar();
                $user->save();
            }
        }

        $token = $user->createToken('web-token')->plainTextToken;

        session([
            'web_token' => $token,
            'web_user' => [
                'id' => (string) $user->getKey(),
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
            ],
            'is_admin' => in_array(
                $user->email,
                config('admin.emails', [])
            ),
        ]);

        if (session('is_admin')) {
            return redirect()->route('admin.dashboard');
        }

        // Cek apakah sudah punya tags
        $hasTags = \App\Models\UserPersonalization::where(
            'user_id',
            (string) $user->getKey()
        )->exists();

        if (!$hasTags) {
            return redirect()->route('web.tags');
        }

        return redirect()->route('web.dashboard');
    }
}
