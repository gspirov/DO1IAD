<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Rules\Password as PasswordRule;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function create(string $token, Request $request): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email ?? ''
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                PasswordRule::default()
            ],
            'password_confirmation' => ['required'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PasswordReset
               ? redirect()->route('login')->with('success', __($status))
               : back()->withErrors(['email' => [__($status)]]);
    }
}
