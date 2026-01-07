<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
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
                return redirect()->back()
                    ->withErrors(['email' => 'Your account is inactive. Please contact administrator.'])
                    ->withInput();
            }

            $request->session()->regenerate();

            // Redirect based on role
            $redirectRoute = $this->getRedirectRouteForRole($user->role);
            return redirect()->intended(route($redirectRoute));
        }

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
            return back()->withErrors(['email' => 'We cannot find a user with that email address.']);
        }

        // Generate temporary password
        $temporaryPassword = 'epss@1234';

        // Update user's password
        $user->password = Hash::make($temporaryPassword);
        $user->save();

        // Send email with temporary password
        try {
            Mail::to($user->email)->send(new PasswordResetMail($user, $temporaryPassword));

            // Log successful email sending
            \Illuminate\Support\Facades\Log::info('Password reset email sent successfully to: ' . $user->email);

            return back()->with([
                'status' => 'SUCCESS: A temporary password has been sent to your email address (' . $user->email . '). Please check your inbox and spam folder.'
            ]);
        } catch (\Exception $e) {
            // Log the error
            \Illuminate\Support\Facades\Log::error('Failed to send password reset email to ' . $user->email . ': ' . $e->getMessage());

            return back()->withErrors([
                'email' => 'FAILED: Unable to send email. Please contact ICT support at ict@epss.gov.et. Error: ' . $e->getMessage()
            ]);
        }
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
                    'password' => Hash::make($password)
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
        switch ($role) {
            case User::ROLE_ADMIN:
                return 'admin.users.index';
            case User::ROLE_GS_MANAGER:
            case User::ROLE_CMD_MANAGER:
                return 'contracts.reports';
            default:
                return 'contracts.index';
        }
    }
}
