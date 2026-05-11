<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors([
                    'email' => 'Terlalu banyak percobaan login. Coba lagi dalam '.$seconds.' detik.',
                ])
                ->onlyInput('email');
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            return back()
                ->withErrors(['email' => 'Email atau password tidak valid.'])
                ->onlyInput('email');
        }

        if (! in_array($request->user()->role, [User::ROLE_ADMIN, User::ROLE_TEACHER], true)) {
            Auth::logout();

            return back()
                ->withErrors(['email' => 'Hanya admin dan guru yang dapat login ke dashboard.'])
                ->onlyInput('email');
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function dashboard(): RedirectResponse
    {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('teacher.dashboard');
    }

    public function profile(Request $request): View
    {
        return view('auth.profile', [
            'user' => $request->user(),
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ]);

        if (Hash::check($data['password'], $request->user()->password)) {
            return back()->withErrors([
                'password' => 'Password baru harus berbeda dari password lama.',
            ]);
        }

        $request->user()->update([
            'password' => $data['password'],
        ]);

        return redirect()
            ->route('profile.show')
            ->with('status', 'Password akun berhasil diperbarui.');
    }

    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip());
    }
}
