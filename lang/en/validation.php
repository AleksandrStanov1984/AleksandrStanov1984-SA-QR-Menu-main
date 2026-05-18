<?php

return [
    'invalid_form' => 'Please fill in the fields correctly',
    'email' => 'Invalid email format',
    'regex' => 'Invalid field format',

    'security' => [
        'title' => 'Security',
        'subtitle' => 'Login and password',
        'h2' => 'Security settings',
        'user_object' => 'User',

        'validation' => [

            'new_email' => [
                'required' => 'Please enter a new login (email)',
                'email' => 'Invalid email format',
                'unique' => 'This email is already in use',
            ],

            'current_password' => [
                'required' => 'Please enter the current password',
            ],

            'new_password' => [
                'required' => 'Please enter a new password',
                'min' => 'Minimum 8 characters',
                'regex' => 'Password must contain: A-Z, a-z, number and special character',
            ],

            'new_password_confirm' => [
                'required' => 'Please confirm the password',
                'same' => 'Passwords do not match',
            ],
        ],

        'errors' => [
            'email_same' => 'The new email matches the current one',
            'email_exists' => 'This email already exists',
            'current_password_wrong' => 'Current password is incorrect',
        ],

        'status' => [
            'email_changed' => 'Login was changed successfully',
            'password_changed' => 'Password was changed successfully',
        ],

        'password_hint' => 'Leave the field empty if you do not want to change the password',
    ],

    'custom' => [

        'new_email' => [
            'email' => 'Invalid email format',
            'regex' => 'Please enter a valid email address',
        ],

    ],
];
