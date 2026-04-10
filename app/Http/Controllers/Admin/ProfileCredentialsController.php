<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
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

        $data = $this->validateEmail($request, $user);

        if (!$this->checkCredentials($user, $data)) {
            return $this->credentialsError($user, $data);
        }

        $user->update([
            'email' => mb_strtolower(trim($data['new_email']))
        ]);

        return back()->with('status', __('admin.security.status.email_changed'));
    }

    public function changePassword(Request $request): RedirectResponse
    {
        $user = $this->resolveUser($request);

        $data = $this->validatePassword($request);

        if (!$this->checkCredentials($user, $data)) {
            return $this->credentialsError($user, $data);
        }

        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);

        return back()->with('status', __('admin.security.status.password_changed'));
    }

    public function showSecurity(Request $request): View
    {
        $user = $this->resolveUser($request);

        return view('admin.security.index', compact('user'));
    }

    private function resolveUser(Request $request)
    {
        $auth = $request->user();

        if (!$auth->is_super_admin) {
            return $auth;
        }

        if ($request->filled('restaurant_id')) {
            return User::where('restaurant_id', $request->restaurant_id)
                ->select(['id', 'email', 'password'])
                ->firstOrFail();
        }

        return $auth;
    }

    private function restaurantUser(Restaurant $restaurant)
    {
        return User::where('restaurant_id', $restaurant->id)
            ->select(['id', 'email', 'password'])
            ->firstOrFail();
    }

    public function showRestaurantCredentials(Restaurant $restaurant): View
    {
        $user = $this->restaurantUser($restaurant);

        return view('admin.restaurants.credentials', [
            'restaurant' => $restaurant,
            'user' => $user,
        ]);
    }

    public function changeRestaurantEmail(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $this->restaurantUser($restaurant);

        $data = $this->validateEmail($request, $user);

        if (!$this->checkCredentials($user, $data)) {
            return $this->credentialsError($user, $data);
        }

        $user->update([
            'email' => mb_strtolower(trim($data['new_email']))
        ]);

        return back()->with('status', __('admin.security.status.email_changed'));
    }

    public function changeRestaurantPassword(Request $request, Restaurant $restaurant): RedirectResponse
    {
        $user = $this->restaurantUser($restaurant);

        $data = $this->validatePassword($request);

        if (!$this->checkCredentials($user, $data)) {
            return $this->credentialsError($user, $data);
        }

        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);

        return back()->with('status', __('admin.security.status.password_changed'));
    }

    // =========================
    // 🔥 ВЫНЕСЕННЫЕ БЛОКИ
    // =========================

    private function validateEmail(Request $request, User $user): array
    {
        return $request->validate([
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
    }

    private function validatePassword(Request $request): array
    {
        return $request->validate([
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
    }

    private function checkCredentials(User $user, array $data): bool
    {
        if (mb_strtolower(trim($data['current_email'])) !== mb_strtolower((string) $user->email)) {
            return false;
        }

        if (!Hash::check($data['current_password'], (string) $user->password)) {
            return false;
        }

        return true;
    }

    private function credentialsError(User $user, array $data)
    {
        if (mb_strtolower(trim($data['current_email'])) !== mb_strtolower((string) $user->email)) {
            return back()->withErrors([
                'current_email' => __('admin.security.errors.current_email_wrong'),
            ]);
        }

        return back()->withErrors([
            'current_password' => __('admin.security.errors.current_password_wrong'),
        ]);
    }
}
