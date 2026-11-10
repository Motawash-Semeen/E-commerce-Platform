<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        // The form reads name and email from the shared auth.user prop.
        return Inertia::render('Profile/Edit');
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('profile.edit')->with('success', 'Profile updated.');
    }

    // No destroy(): there is a single admin and no registration,
    // so deleting an account here could lock everyone out.
}
