<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ResetCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'])
            : response()->json(['message' => 'Unable to send reset link.'], 500);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                // Optional: log user in automatically or trigger an event
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successful.']);
        } else {
            return response()->json(['message' => __($status)], 400);
        }
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $code = rand(100000, 999999);

        DB::table('password_reset_codes')->updateOrInsert(
            ['email' => $request->email],
            [
                'code' => $code,
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        // Send the code via email
        // Mail::to($request->email)->send(new \App\Mail\ResetCodeMail($code));
        Mail::to($request->email)->send(new ResetCodeMail($code));


        return response()->json(['message' => 'Reset code sent to your email.']);
    }

    public function resetWithCode(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'code' => 'required|digits:6',
                'password' => 'required|min:8',
            ]);
            // dd("Resetting password for {$request->email} with code {$request->code} {$request->password}");


            $record = DB::table('password_reset_codes')
                ->where('email', $request->email)
                ->where('code', $request->code)
                ->where('expires_at', '>', now())
                ->first();

            if (!$record) {
                return response()->json(['message' => 'Invalid or expired code.'], 422);
            }

            $user = \App\Models\User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Optionally delete used code
            DB::table('password_reset_codes')->where('email', $request->email)->delete();

            return response()->json(['message' => 'Password has been reset.']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }


    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'old_password' => 'required|min:8',
                'new_password' => 'required|min:8',
            ]);
            // dd("Resetting password for {$request->email} with code {$request->code} {$request->password}");

            $user = \App\Models\User::where('email', $request->email)->first();
            if (!$user || !Hash::check($request->old_password, $user->password)) {
                return response()->json(['message' => 'Invalid credentials.'], 422);
            }

            $user = \App\Models\User::where('email', $request->email)->first();
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Optionally delete used code
            DB::table('password_reset_codes')->where('email', $request->email)->delete();

            return response()->json(['message' => 'Password has been reset.']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTrace()], 500);
        }
    }

}
