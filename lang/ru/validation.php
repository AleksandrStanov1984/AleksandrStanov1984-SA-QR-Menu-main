<?php

return [

    'security' => [

        'title' => 'Безопасность',
        'subtitle' => 'Логин и пароль',
        'h2' => 'Настройки безопасности',
        'user_object' => 'Пользователь',

        'validation' => [
            'new_email' => [
                'required' => 'Введите новый логин (email)',
                'email' => 'Неверный формат email',
                'unique' => 'Этот email уже используется',
            ],

            'current_password' => [
                'required' => 'Введите текущий пароль',
            ],

            'new_password' => [
                'required' => 'Введите новый пароль',
                'min' => 'Минимум 8 символов',
                'regex' => 'Пароль должен содержать: A-Z, a-z, цифру и символ',
            ],

            'new_password_confirm' => [
                'required' => 'Подтвердите пароль',
                'same' => 'Пароли не совпадают',
            ],
        ],

        'errors' => [
            'email_same' => 'Новый email совпадает с текущим',
            'email_exists' => 'Такой email уже существует',
            'current_password_wrong' => 'Неверный текущий пароль',
        ],

        'status' => [
            'email_changed' => 'Логин успешно изменён',
            'password_changed' => 'Пароль успешно изменён',
        ],

        'password_hint' => 'Оставьте поле пустым, если не хотите менять пароль',

    ],

];
