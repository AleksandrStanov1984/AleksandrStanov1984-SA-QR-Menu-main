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
            'current_email.required' => 'Укажите текущий e-mail.',
            'current_email.email' => 'Неверный формат e-mail.',
            'new_email.required' => 'Укажите новый e-mail.',
            'new_email.email' => 'Неверный формат нового e-mail.',
            'new_email.unique' => 'Этот e-mail уже используется.',
            'current_password.required' => 'Введите текущий пароль.',
        ]);

        if (mb_strtolower(trim($data['current_email'])) !== mb_strtolower((string) $user->email)) {
            return back()->withErrors(['current_email' => 'Текущий e-mail указан неверно.']);
        }

        if (!Hash::check($data['current_password'], (string) $user->password)) {
            return back()->withErrors(['current_password' => 'Текущий пароль указан неверно.']);
        }

        $user->email = mb_strtolower(trim($data['new_email']));
        $user->save();

        return back()->with('status', 'E-mail изменён.');
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
                'regex:/[A-Z]/',          // минимум 1 заглавная
                'regex:/[a-z]/',          // минимум 1 маленькая
                'regex:/\d/',             // минимум 1 цифра
                'regex:/[^A-Za-z0-9]/',   // минимум 1 спецсимвол
            ],

            'new_password_confirm' => ['required', 'same:new_password'],
        ], [
            'current_email.required' => 'Укажите текущий e-mail.',
            'current_email.email' => 'Неверный формат e-mail.',
            'current_password.required' => 'Введите текущий пароль.',

            'new_password.required' => 'Введите новый пароль.',
            'new_password.min' => 'Пароль должен быть минимум 8 символов.',
            'new_password.regex' => 'Пароль должен содержать: 1 заглавную, 1 маленькую, 1 цифру и 1 спецсимвол.',
            'new_password_confirm.required' => 'Повторите новый пароль.',
            'new_password_confirm.same' => 'Подтверждение пароля не совпадает.',
        ]);

        if (mb_strtolower(trim($data['current_email'])) !== mb_strtolower((string) $user->email)) {
            return back()->withErrors(['current_email' => 'Текущий e-mail указан неверно.']);
        }

        if (!Hash::check($data['current_password'], (string) $user->password)) {
            return back()->withErrors(['current_password' => 'Текущий пароль указан неверно.']);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        return back()->with('status', 'Пароль изменён.');
    }

    public function showSecurity(Request $request): View
    {
        $user = $request->user();
        return view('admin.security.index', compact('user'));
    }

}
