<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function showChangeForm()
    {
        return view('auth.passwords.change', ['user' => Auth::user()]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        // የአሁኑ ፓስዎርድ ማረጋገጫ
        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'የአሁኑ ፓስዎርድ ትክክል አይደለም።']);
        }

        // አዲስ ፓስዎርድ ማስቀመጥ
        $user->update([
            'password' => Hash::make($request->new_password),
            'must_change_password' => false,
        ]);

        // Logout አድርግ
        Auth::logout();

        // ወደ Login ገጽ በስኬት መልእክት መምራት
        return redirect()->route('login')
            ->with('success', 'የይለፍ ቃልዎ በተሳካ ሁኔታ ተቀይሯል። እባክዎ በአዲሱ የይለፍ ቃልዎ ይግቡ።');
    }
}
