<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
