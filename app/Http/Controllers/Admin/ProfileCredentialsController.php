<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfileCredentialsController extends Controller
{
    public function changeEmail(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_email' => ['required', 'email'],
            'current_password' => ['required', 'string'],
            'new_email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        if ($data['current_email'] !== $user->email) {
            throw ValidationException::withMessages([
                'current_email' => 'Current email does not match.',
            ]);
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Wrong password.',
            ]);
        }

        $user->email = $data['new_email'];
        $user->save();

        return back()->with('status', 'Email changed.');
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_email' => ['required', 'email'],
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'max:255'],
            'new_password_confirm' => ['required', 'same:new_password'],
        ]);

        if ($data['current_email'] !== $user->email) {
            throw ValidationException::withMessages([
                'current_email' => 'Current email does not match.',
            ]);
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Wrong password.',
            ]);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return back()->with('status', 'Password changed.');
    }
}
