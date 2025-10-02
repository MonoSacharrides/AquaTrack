<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Maximum number of login attempts allowed before a temporary lockout.
     */
    protected $maxAttempts = 5;

    /**
     * Show the login form.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
            'csrf_token' => csrf_token(),
            'loginAttempts' => $this->getLoginAttempts(),
            'remainingTime' => $this->getRemainingTime(),
        ]);
    }

    /**
     * Get the number of login attempts for the current IP.
     */
    protected function getLoginAttempts()
    {
        $key = $this->throttleKey(request()->ip());
        return RateLimiter::attempts($key);
    }

    /**
     * Get remaining time in seconds for login attempts (only if locked).
     */
    protected function getRemainingTime()
    {
        $key = $this->throttleKey(request()->ip());
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            return RateLimiter::availableIn($key);
        }
        return 0;
    }

    /**
     * Key used by the rate limiter (per IP).
     */
    protected function throttleKey($ip)
    {
        return 'login:' . $ip;
    }

    /**
     * Decide decay seconds (lockout seconds) based on the number of attempts (new count).
     * Always return a positive number (minimum 30s).
     *
     * Levels:
     * 1-4 attempts => no immediate lockout (we still set a short decay so attempts persist)
     * 5-9 attempts => 30s lockout
     * 10-14 attempts => 60s lockout
     * 15+ attempts => 300s (5min) lockout
     *
     * @param int $attemptsNew The attempt count after increment
     * @return int seconds
     */
    protected function decayForAttempts(int $attemptsNew): int
    {
        if ($attemptsNew >= 15) {
            return 300; // 5 minutes
        } elseif ($attemptsNew >= 10) {
            return 60; // 1 minute
        } elseif ($attemptsNew >= 5) {
            return 30; // 30 seconds
        }

        // For first few attempts, keep attempts stored for at least 30s so they count toward escalation
        return 30;
    }

    /**
     * Verify admin/staff access code (AJAX).
     */
    public function verifyCode(Request $request)
    {
        try {
            $throttleKey = $this->throttleKey($request->ip());

            // If already locked, return throttled response
            if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                return response()->json([
                    'verified' => false,
                    'message' => "Too many login attempts. Please try again in {$seconds} seconds.",
                    'throttled' => true,
                    'remaining_time' => $seconds,
                ], 429);
            }

            $request->validate([
                'role' => 'required|in:admin,staff',
                'code' => 'required|string'
            ]);

            $role = strtoupper($request->role);
            $correctCode = env("{$role}_VERIFICATION_CODE");

            if (!$correctCode || $request->code !== $correctCode) {
                // compute new attempts count (current + 1) and choose decay
                $attemptsBefore = RateLimiter::attempts($throttleKey);
                $attemptsAfter = $attemptsBefore + 1;
                $decay = $this->decayForAttempts($attemptsAfter);

                // increment with chosen decay
                RateLimiter::hit($throttleKey, $decay);

                Activity::create([
                    'event' => 'verification_failed',
                    'description' => "Failed {$request->role} verification code attempt",
                    'properties' => [
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'attempts' => $attemptsAfter,
                    ]
                ]);

                // If this push exceeded maxAttempts, include remaining time
                $errors = ['code' => ['The provided verification code is invalid']];
                if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
                    $errors['throttle'] = ["Too many login attempts. Please try again in " . RateLimiter::availableIn($throttleKey) . " seconds."];
                    $errors['remaining_time'] = [RateLimiter::availableIn($throttleKey)];
                }

                throw ValidationException::withMessages($errors);
            }

            Activity::create([
                'event' => 'verification_success',
                'description' => "Successful {$request->role} verification",
                'properties' => [
                    'ip' => $request->ip()
                ]
            ]);

            return response()->json([
                'verified' => true,
                'message' => 'Verification successful',
                'csrf_token' => csrf_token(),
            ]);
        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Session\TokenMismatchException) {
                return response()->json([
                    'verified' => false,
                    'message' => 'CSRF token mismatch. Please refresh the page and try again.',
                    'csrf_token' => csrf_token(),
                ], 419);
            }
            throw $e;
        }
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws ValidationException
     */
    public function store(LoginRequest $request)
    {
        $throttleKey = $this->throttleKey($request->ip());

        // If locked out, return error with remaining_time
        if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'throttle' => ["Too many login attempts. Please try again in {$seconds} seconds."],
                'remaining_time' => [$seconds],
            ]);
        }

        try {
            $request->authenticate();

            // Clear attempts on success
            RateLimiter::clear($throttleKey);

            $user = Auth::user();
            $selectedRole = strtolower($request->input('role', 'customer'));

            // Prevent login if user is disabled
            if (!$user->enabled) {
                Activity::log('login_disabled', "Disabled user attempted login", $user, [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Your account has been deactivated. Please contact the administrator.'],
                ]);
            }

            // Prevent admin/staff from logging in through customer form
            if ($selectedRole === 'customer' && $user->hasAnyRole(['admin', 'staff'])) {
                Activity::log('unauthorized_access', "Admin/staff attempted customer login", $user, [
                    'ip' => $request->ip(),
                    'attempted_role' => $selectedRole
                ]);

                Auth::logout();
                throw ValidationException::withMessages([
                    'email' => ['Administrative accounts must use their specific login portal'],
                ]);
            }

            // Check role mismatch
            if (in_array($selectedRole, ['admin', 'staff']) && !$user->hasRole($selectedRole)) {
                $userRoles = $user->roles->pluck('name')->implode(', ');

                Activity::log('role_mismatch', "User attempted login with wrong role", $user, [
                    'attempted_role' => $selectedRole,
                    'actual_roles' => $userRoles
                ]);

                Auth::logout();
                throw ValidationException::withMessages([
                    'role_mismatch' => [
                        'title' => 'Role Access Denied',
                        'message' => "Your account has these roles: $userRoles. Please login through the appropriate portal for your account type."
                    ]
                ]);
            }

            $request->session()->regenerate();

            // Log successful login
            Activity::log('logged_in', "User logged in", $user, [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'role' => $selectedRole,
                'login_method' => $selectedRole === 'customer' ? 'account_number' : 'email' // Added login method tracking
            ]);

            // Role-based redirects
            $redirectRoute = match (true) {
                $user->hasRole('admin') => 'admin.dashboard',
                $user->hasRole('staff') => 'staff.dashboard',
                default => 'customer.dashboard',
            };

            return redirect()->intended(route($redirectRoute));
        } catch (ValidationException $e) {
            // compute new attempts count and set decay accordingly BEFORE hitting the limiter
            $attemptsBefore = RateLimiter::attempts($throttleKey);
            $attemptsAfter = $attemptsBefore + 1;
            $decay = $this->decayForAttempts($attemptsAfter);

            // increment with computed decay
            RateLimiter::hit($throttleKey, $decay);

            $attempts = $attemptsAfter;
            $remaining = max(0, $this->maxAttempts - $attempts);

            Activity::log('login_failed', "Failed login attempt", null, [
                'login_field' => $request->input('email') ?? $request->input('account_number'), // Changed from serial_number
                'ip' => $request->ip(),
                'errors' => $e->errors(),
                'attempts' => $attempts,
                'remaining_attempts' => $remaining,
            ]);

            $errors = $e->errors();

            if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                $errors['throttle'] = ["Too many login attempts. Please try again in {$seconds} seconds."];
                $errors['remaining_time'] = [$seconds];
            } else {
                $errors['attempts'] = ["{$remaining} attempts remaining."];
            }

            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Activity::log('logged_out', "User logged out", $user);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->flash('logout_success', true);

        return Inertia::location('/'); // <-- send user to the homepage / hero section
    }
}
