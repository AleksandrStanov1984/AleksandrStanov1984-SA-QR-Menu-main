<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (auth()->check()) {

            $user = auth()->user();

            if ($user?->is_super_admin) {
                return redirect()->route('admin.restaurants.index');
            }

            return redirect()->route('admin.menu.profile');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, false)) {

            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user) {

                Auth::logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('admin.login');
            }

            // USER
            if (!$user->is_super_admin) {
                return redirect()->route('admin.menu.profile');
            }

            // SUPER ADMIN
            if ($request->session()->has('admin.restaurant_id')) {
                return redirect()->route('admin.menu.profile');
            }

            return redirect()->route('admin.restaurants.index');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
