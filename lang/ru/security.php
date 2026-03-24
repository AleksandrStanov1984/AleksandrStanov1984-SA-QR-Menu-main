<?php

return [

    'security' => [
        'title' => 'Безопасность',
        'subtitle' => 'Email и пароль',
        'h2' => 'Безопасность',

        'user_object' => 'Пользователь объекта',

        'status' => [
            'email_changed' => 'Email успешно изменён',
            'password_changed' => 'Пароль успешно изменён',
        ],

        'errors' => [
            'current_email_wrong' => 'Текущий email указан неверно',
            'current_password_wrong' => 'Неверный текущий пароль',
        ],

        'validation' => [
            'current_email' => [
                'required' => 'Введите текущий email',
                'email' => 'Некорректный email',
            ],

            'new_email' => [
                'required' => 'Введите новый email',
                'email' => 'Некорректный email',
                'unique' => 'Этот email уже используется',
            ],

            'current_password' => [
                'required' => 'Введите текущий пароль',
            ],

            'new_password' => [
                'required' => 'Введите новый пароль',
                'min' => 'Пароль должен содержать минимум 8 символов',
                'regex' => 'Пароль должен содержать заглавную букву, строчную букву, цифру и спецсимвол',
            ],

            'new_password_confirm' => [
                'required' => 'Подтвердите пароль',
                'same' => 'Пароли не совпадают',
            ],
        ],
    ],

    'profile' => [
        'change_email' => [
            'h2' => 'Смена email',
            'current_email' => 'Текущий email',
            'current_password' => 'Текущий пароль',
            'new_email' => 'Новый email',
        ],

        'change_password' => [
            'h2' => 'Смена пароля',
            'current_email' => 'Текущий email',
            'current_password' => 'Текущий пароль',
            'new_password' => 'Новый пароль',
            'confirm_new_password' => 'Подтвердите новый пароль',
        ],
    ],

    'common' => [
        'change' => 'Изменить',
    ],
    'password_hint' => 'Пароль: минимум 8 символов, 1 заглавная, 1 маленькая, 1 цифра и 1 спецсимвол.',

];
