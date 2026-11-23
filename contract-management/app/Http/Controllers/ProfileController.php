<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // If only password is being changed, make name and email optional
        $isPasswordOnly = $request->filled('password') && !$request->filled('name') && !$request->filled('email');

        $rules = [
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ];

        // Only super admin can edit name and email
        if ($user->isSuperAdmin()) {
            $rules['name'] = $isPasswordOnly ? 'nullable|string|max:255' : 'required|string|max:255';
            $rules['email'] = $isPasswordOnly ? 'nullable|string|email|max:255|unique:users,email,' . $user->id : 'required|string|email|max:255|unique:users,email,' . $user->id;
        }

        $request->validate($rules);

        // Check current password if changing password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }

        $updateData = [];

        // Only update name and email if user is super admin
        if ($user->isSuperAdmin()) {
            if ($request->filled('name')) {
                $updateData['name'] = $request->name;
            }
            if ($request->filled('email')) {
                $updateData['email'] = $request->email;
            }
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        if (!empty($updateData)) {
            $user->update($updateData);
            return back()->with('success', 'Profile updated successfully.');
        }

        return back()->with('info', 'No changes were made.');
    }

    public function telegram()
    {
        $user = Auth::user();
        return view('profile.telegram', compact('user'));
    }

    public function updateTelegram(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'telegram_chat_id' => 'nullable|string|max:255',
        ]);

        $user->update([
            'phone' => $request->input('phone', $user->phone),
            'telegram_chat_id' => $request->input('telegram_chat_id'),
            'telegram_notifications' => $request->has('telegram_notifications'),
        ]);

        return back()->with('success', 'Telegram settings updated successfully.');
    }
}
