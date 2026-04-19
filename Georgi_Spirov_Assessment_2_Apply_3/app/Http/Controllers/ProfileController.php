<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ProfileUploadAvatarRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());
        $request->user()->save();

        $request->session()->flash('success', 'Profile updated successfully.');

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }


    /**
     * Handle the request to upload a new avatar for the authenticated user.
     */
    public function avatar(ProfileUploadAvatarRequest $request)
    {
        $validated = $request->validated();

        /* @var UploadedFile $file */
        $file = $validated['avatar'];
        $oldPath = Auth::user()->avatar;

        if (false !== $path = $file->store('avatars', 'public')) {
            Auth::user()->update(['avatar' => $path]);

            if (!empty($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            $ok = true;
            $message = 'Avatar uploaded successfully.';
            $statusCode = 200;
        } else {
            $ok = false;
            $message = 'Failed to upload avatar.';
            $statusCode = 400;
        }

        return response()->json(compact('ok', 'message'), $statusCode);
    }

    /**
     * Handle the request to delete the avatar of the authenticated user.
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);

            $user->update([
                'avatar' => null
            ]);
        }

        return back()->with('success', 'Avatar deleted successfully.');
    }
}
