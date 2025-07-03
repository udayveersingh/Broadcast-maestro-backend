<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.cover-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->status !== 'active') {
                Auth::logout();
                return redirect()->back()->withErrors(['Your account is inactive.']);
            }

            // Role-based redirection
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'manager') {
                return redirect()->route('manager.dashboard'); // Make sure this exists
            } else {
                return redirect()->route('user.dashboard'); // Or some default user area
            }
        }

        return redirect()->back()->withErrors(['Invalid credentials']);
    }

    public function dashboard()
    {
        return view('index'); // or a separate admin dashboard view
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('auth.login.form');
    }
}


