<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Mail\TemporaryPasswordMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $user = Auth::user();

            if (!$user->isActive()) {
                Auth::logout();

                // Log inactive user login attempt
                \App\Models\ActivityLog::create([
                    'activity_type' => \App\Models\ActivityLog::TYPE_FAILED_LOGIN,
                    'user_id' => $user->id,
                    'edited_by' => $user->name,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'severity' => \App\Models\ActivityLog::SEVERITY_WARNING,
                    'field_name' => 'inactive_account',
                    'reason' => 'Attempted login with inactive account'
                ]);

                return redirect()->back()
                    ->withErrors(['email' => 'Your account is inactive. Please contact administrator.'])
                    ->withInput();
            }

            $request->session()->regenerate();

            // Users holding a temporary password must set a new one first
            if ($user->must_change_password) {
                return redirect()->route('password.change')
                    ->with('warning', 'እባክዎ መጀመሪያ ፓስዎርድዎን ይቀይሩ። Please change your password first.');
            }

            // Redirect based on role
            $redirectRoute = $this->getRedirectRouteForRole($user->role);
            return redirect()->intended(route($redirectRoute));
        }

        // Log failed login attempt
        \App\Models\ActivityLog::create([
            'activity_type' => \App\Models\ActivityLog::TYPE_FAILED_LOGIN,
            'edited_by' => $request->email, // Since user not found or wrong password
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'severity' => \App\Models\ActivityLog::SEVERITY_WARNING,
            'field_name' => 'invalid_credentials',
            'reason' => 'Invalid email or password'
        ]);

        return redirect()->back()
            ->withErrors(['email' => 'Invalid credentials.'])
            ->withInput();
    }

    /**
     * Show registration form
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,general_service,cmd,finance,gs_manager,cmd_manager',
            'department' => 'nullable|in:general_service,cmd,finance',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'department' => $request->filled('department') ? $request->department : null,
            'status' => true,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $redirectRoute = $this->getRedirectRouteForRole($user->role);
        return redirect()->route($redirectRoute)
            ->with('success', 'Registration successful! Welcome to Contract Management System.');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $response = redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');

        // Prevent browser caching so user cannot view previous pages after logout
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');

        return $response;
    }

    /**
     * Show forgot password form
     */
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send password reset link
     */
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Log failed password reset attempt for non-existent user
            \App\Models\ActivityLog::create([
                'activity_type' => \App\Models\ActivityLog::TYPE_PASSWORD_RESET_REQUEST,
                'edited_by' => $request->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => \App\Models\ActivityLog::SEVERITY_INFO,
                'field_name' => 'password_reset_attempt',
                'reason' => 'Password reset requested for non-existent email'
            ]);

            return back()->withErrors(['email' => 'We cannot find a user with that email address.']);
        }

        if (!$user->isActive()) {
            // Log password reset attempt for inactive user
            \App\Models\ActivityLog::create([
                'activity_type' => \App\Models\ActivityLog::TYPE_PASSWORD_RESET_REQUEST,
                'user_id' => $user->id,
                'edited_by' => $user->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => \App\Models\ActivityLog::SEVERITY_WARNING,
                'field_name' => 'password_reset_attempt',
                'reason' => 'Password reset requested for inactive account'
            ]);

            return back()->withErrors(['email' => 'Your account is inactive. Please contact administrator.']);
        }

        $temporaryPassword = Str::random(10);
        $oldPassword = $user->password;
        $oldMustChange = $user->must_change_password;

        $user->forceFill([
            'password' => Hash::make($temporaryPassword),
            'must_change_password' => true,
        ])->save();

        try {
            Mail::to($user->email)->send(new TemporaryPasswordMail($user, $temporaryPassword));
        } catch (\Throwable $e) {
            // Restore the previous hash so a failed send does not lock the account
            $user->forceFill([
                'password' => $oldPassword,
                'must_change_password' => $oldMustChange,
            ])->save();

            \App\Models\ActivityLog::create([
                'activity_type' => \App\Models\ActivityLog::TYPE_PASSWORD_RESET_REQUEST,
                'user_id' => $user->id,
                'edited_by' => $user->name,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'severity' => \App\Models\ActivityLog::SEVERITY_DANGER,
                'field_name' => 'password_reset_attempt',
                'reason' => 'Temporary password email failed: ' . $e->getMessage()
            ]);

            return back()->withErrors([
                'email' => 'ጊዜያዊ ፓስዎርድ ኢሜይል መላክ አልተቻለም። እባክዎ በኋላ እንደገና ይሞክሩ ወይም አስተዳዳሪውን ያግኙ።',
            ]);
        }

        \App\Models\ActivityLog::create([
            'activity_type' => \App\Models\ActivityLog::TYPE_PASSWORD_RESET_REQUEST,
            'user_id' => $user->id,
            'edited_by' => $user->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'severity' => \App\Models\ActivityLog::SEVERITY_INFO,
            'field_name' => 'password_reset_attempt',
            'reason' => 'Temporary password sent successfully'
        ]);

        return back()->with('status', 'ጊዜያዊ ፓስዎርድ ወደ ኢሜይልዎ ተልኳል።');
    }

    /**
     * Generate a random password
     */
    private function generateRandomPassword($length = 12)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }

    /**
     * Show reset password form
     */
    public function showResetPassword(Request $request)
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make(config('app.default_password')),
                    'must_change_password' => true,
                    'password_changed_at' => null,
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Get redirect route based on user role
     */
    private function getRedirectRouteForRole($role)
    {
        return User::routeForRole($role);
    }
}
