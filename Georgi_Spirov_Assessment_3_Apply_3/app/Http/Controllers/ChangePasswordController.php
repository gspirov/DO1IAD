<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ChangePasswordController extends Controller
{
    /**
     * Displays the form for changing the user's password.
     */
    public function edit(Request $request): View
    {
        return view('profile.change-password', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Updates the authenticated user's password.
     *
     * This method validates the incoming password change request, hashes the new password,
     * and updates the user's password in the database.
     */
    public function update(ChangePasswordRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }
}
