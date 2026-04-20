<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;

use App\Services\SecurityEventService;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileCredentialsController extends Controller
{
    public function changeEmail(Request $request): RedirectResponse
    {
        $user = $this->resolveUser($request);

        $request->merge([
            'new_email' => mb_strtolower(trim((string) $request->input('new_email')))
        ]);

        $data = $request->validate([
            'new_email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password' => ['required', 'string'],
        ]);

        if ($data['new_email'] === $user->email) {
            return back()->withErrors([
                'new_email' => __('admin.security.errors.email_same'),
            ]);
        }

        if (!$this->checkPasswordAccess($user, $data['current_password'], $request)) {
            return back()->withErrors([
                'current_password' => __('admin.security.errors.current_password_wrong'),
            ]);
        }

        $oldEmail = $user->email;

        $user->update([
            'email' => $data['new_email'],
        ]);

        app(SecurityEventService::class)->log(
            'email_changed',
            $request->user(),
            $user,
            $user->restaurant_id,
            [
                'mode' => $request->user()->is_super_admin ? 'admin_override' : 'self',
                'old_email' => $oldEmail,
                'new_email' => $data['new_email'],
            ],
            $request
        );

        return back()->with('status', __('admin.security.status.email_changed'));
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $user = $this->resolveUser($request);

        $data = $request->validate([
            'current_password' => ['required', 'string'],
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
        ]);

        if (!$this->checkPasswordAccess($user, $data['current_password'], $request)) {
            return back()->withErrors([
                'current_password' => __('admin.security.errors.current_password_wrong'),
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        app(SecurityEventService::class)->log(
            'password_changed',
            $request->user(),
            $user,
            $user->restaurant_id,
            [
                'mode' => $request->user()->is_super_admin ? 'admin_override' : 'self',
            ],
            $request
        );

        auth()->logoutOtherDevices($data['new_password']);

        return back()->with('status', __('admin.security.status.password_changed'));
    }

    public function showAdminLogin(Request $request): View
    {
        return view('admin.security.login', [
            'user' => $request->user(),
            'mode' => 'admin',
        ]);
    }

    public function showAdminPassword(Request $request): View
    {
        return view('admin.security.index', [
            'user' => $request->user(),
            'mode' => 'admin',
        ]);
    }

    public function showRestaurantLogin(Request $request, Restaurant $restaurant): View
    {
        $this->authorizeRestaurant($restaurant, $request);

        return view('admin.security.login', [
            'restaurant' => $restaurant,
            'user' => $this->restaurantUser($restaurant),
            'mode' => 'restaurant',
        ]);
    }

    public function showRestaurantCredentials(Request $request, Restaurant $restaurant): View
    {
        $this->authorizeRestaurant($restaurant, $request);

        return view('admin.security.index', [
            'restaurant' => $restaurant,
            'user' => $this->restaurantUser($restaurant),
            'mode' => 'restaurant',
        ]);
    }

    public function changeRestaurantEmail(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $this->authorizeRestaurant($restaurant, $request);

        $user = $this->restaurantUser($restaurant);

        $request->merge([
            'new_email' => mb_strtolower(trim((string) $request->input('new_email')))
        ]);

        $data = $request->validate([
            'new_email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password' => ['required', 'string'],
        ]);

        if ($data['new_email'] === $user->email) {
            return back()->withErrors([
                'new_email' => __('admin.security.errors.email_same'),
            ]);
        }

        $exists = User::where('email', $data['new_email'])
            ->where('id', '!=', $user->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'new_email' => __('admin.security.errors.email_exists'),
            ]);
        }

        if (!$this->checkPasswordAccess($user, $data['current_password'], $request)) {
            return back()->withErrors([
                'current_password' => __('admin.security.errors.current_password_wrong'),
            ]);
        }

        $oldEmail = $user->email;

        $user->update([
            'email' => $data['new_email'],
        ]);

        app(SecurityEventService::class)->log(
            'email_changed',
            $request->user(),
            $user,
            $restaurant->id,
            [
                'mode' => $request->user()->is_super_admin ? 'admin_override' : 'self',
                'context' => 'restaurant',
                'old_email' => $oldEmail,
                'new_email' => $data['new_email'],
            ],
            $request
        );

        return back()->with('status', __('admin.security.status.email_changed'));
    }

    public function changeRestaurantPassword(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $this->authorizeRestaurant($restaurant, $request);

        $user = $this->restaurantUser($restaurant);

        $data = $request->validate([
            'current_password' => ['required', 'string'],
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
        ]);

        if (!$this->checkPasswordAccess($user, $data['current_password'], $request)) {
            return back()->withErrors([
                'current_password' => __('admin.security.errors.current_password_wrong'),
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        app(SecurityEventService::class)->log(
            'password_changed',
            $request->user(),
            $user,
            $restaurant->id,
            [
                'mode' => $request->user()->is_super_admin ? 'admin_override' : 'self',
                'context' => 'restaurant',
            ],
            $request
        );

        return back()->with('status', __('admin.security.status.password_changed'));
    }

    private function resolveUser(Request $request)
    {
        $auth = $request->user();

        if (!$auth->is_super_admin) {
            return $auth;
        }

        if ($request->filled('restaurant_id')) {
            return User::where('restaurant_id', (int) $request->restaurant_id)
                ->firstOrFail();
        }

        return $auth;
    }

    private function restaurantUser(Restaurant $restaurant)
    {
        return User::where('restaurant_id', $restaurant->id)
            ->firstOrFail();
    }

    private function checkPasswordAccess(User $targetUser, string $password, Request $request): bool
    {
        $auth = $request->user();

        if (Hash::check($password, (string) $targetUser->password)) {
            return true;
        }

        if ($auth->is_super_admin && Hash::check($password, (string) $auth->password)) {
            return true;
        }

        return false;
    }

    private function authorizeRestaurant(Restaurant $restaurant, Request $request): void
    {
        $auth = $request->user();

        if ($auth->is_super_admin) {
            return;
        }

        if ((int)$auth->restaurant_id !== (int)$restaurant->id) {
            abort(403);
        }
    }
}
