<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileCredentialsController extends Controller
{
    public function changeEmail(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'current_password' => ['required', 'string', 'max:255'],
            'new_email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ], [
            'current_email.required' => __('admin.security.validation.current_email.required'),
            'current_email.email' => __('admin.security.validation.current_email.email'),
            'new_email.required' => __('admin.security.validation.new_email.required'),
            'new_email.email' => __('admin.security.validation.new_email.email'),
            'new_email.unique' => __('admin.security.validation.new_email.unique'),
            'current_password.required' => __('admin.security.validation.current_password.required'),
        ]);

        if (mb_strtolower(trim($data['current_email'])) !== mb_strtolower((string) $user->email)) {
            return back()->withErrors([
                'current_email' => __('admin.security.errors.current_email_wrong'),
            ]);
        }

        if (!Hash::check($data['current_password'], (string) $user->password)) {
            return back()->withErrors([
                'current_password' => __('admin.security.errors.current_password_wrong'),
            ]);
        }

        $user->email = mb_strtolower(trim($data['new_email']));
        $user->save();

        return back()->with('status', __('admin.security.status.email_changed'));
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'current_password' => ['required', 'string', 'max:255'],

            'new_password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/\d/',
                'regex:/[^A-Za-z0-9]/',
            ],

            'new_password_confirm' => ['required', 'same:new_password'],
        ], [
            'current_email.required' => __('admin.security.validation.current_email.required'),
            'current_email.email' => __('admin.security.validation.current_email.email'),
            'current_password.required' => __('admin.security.validation.current_password.required'),

            'new_password.required' => __('admin.security.validation.new_password.required'),
            'new_password.min' => __('admin.security.validation.new_password.min'),
            'new_password.regex' => __('admin.security.validation.new_password.regex'),
            'new_password_confirm.required' => __('admin.security.validation.new_password_confirm.required'),
            'new_password_confirm.same' => __('admin.security.validation.new_password_confirm.same'),
        ]);

        if (mb_strtolower(trim($data['current_email'])) !== mb_strtolower((string) $user->email)) {
            return back()->withErrors([
                'current_email' => __('admin.security.errors.current_email_wrong'),
            ]);
        }

        if (!Hash::check($data['current_password'], (string) $user->password)) {
            return back()->withErrors([
                'current_password' => __('admin.security.errors.current_password_wrong'),
            ]);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return back()->with('status', __('admin.security.status.password_changed'));
    }

    public function showSecurity(Request $request): View
    {
        $user = $request->user();
        return view('admin.security.index', compact('user'));
    }
}
