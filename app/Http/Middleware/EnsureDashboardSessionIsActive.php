<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDashboardSessionIsActive
{
    private const IDLE_TIMEOUT_SECONDS = 3600;
    private const SESSION_KEY = 'dashboard_last_activity_at';

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, [User::ROLE_ADMIN, User::ROLE_TEACHER], true)) {
            return $next($request);
        }

        $lastActivityAt = (int) $request->session()->get(self::SESSION_KEY, 0);

        if ($lastActivityAt > 0 && (time() - $lastActivityAt) >= self::IDLE_TIMEOUT_SECONDS) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sesi dashboard berakhir karena tidak ada aktivitas selama 1 jam.',
                ], 401);
            }

            return redirect()
                ->route('login')
                ->with('status', 'Sesi admin/guru berakhir karena tidak ada aktivitas selama 1 jam. Silakan login kembali.');
        }

        $request->session()->put(self::SESSION_KEY, time());

        return $next($request);
    }
}
