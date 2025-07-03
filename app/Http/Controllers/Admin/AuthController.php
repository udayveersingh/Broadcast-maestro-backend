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
            $defaultRedirect = match ($user->role) {
            'admin' => route('admin.dashboard'),
            'manager' => route('manager.dashboard'),
            default => route('user.dashboard'),
        };

        return redirect()->intended($defaultRedirect);
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


