<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Display the view for the "forgot password" form.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the submission of the forgot password form and send a password reset link.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::ResetLinkSent
               ? back()->with('success', __($status))
               : back()->withErrors(['email' => __($status)]);
    }
}
