<?php

return [
    'security' => [
        'title' => 'Security',
        'subtitle' => 'Email and password',
        'h2' => 'Security',
        'user_object' => 'Object user',
        'status' => [
            'email_changed' => 'Email changed successfully',
            'password_changed' => 'Password changed successfully',
        ],
        'errors' => [
            'current_email_wrong' => 'Current email is incorrect',
            'current_password_wrong' => 'Current password is incorrect',
        ],
        'validation' => [
            'current_email' => [
                'required' => 'Please enter the current email',
                'email' => 'Invalid email',
            ],
            'new_email' => [
                'required' => 'Please enter a new email',
                'email' => 'Invalid email',
                'unique' => 'This email is already in use',
            ],
            'current_password' => [
                'required' => 'Please enter the current password',
            ],
            'new_password' => [
                'required' => 'Please enter a new password',
                'min' => 'Password must contain at least 8 characters',
                'regex' => 'Password must contain uppercase letter, lowercase letter, number and special character',
            ],
            'new_password_confirm' => [
                'required' => 'Please confirm the password',
                'same' => 'Passwords do not match',
            ],
        ],
    ],
    'profile' => [
        'change_email' => [
            'h2' => 'Change email',
            'current_email' => 'Current email',
            'current_password' => 'Current password',
            'new_email' => 'New email',
        ],
        'change_password' => [
            'h2' => 'Change password',
            'current_email' => 'Current email',
            'current_password' => 'Current password',
            'new_password' => 'New password',
            'confirm_new_password' => 'Confirm new password',
        ],
    ],
    'common' => [
        'change' => 'Change',
    ],
    'password_hint' => 'Password: minimum 8 characters, 1 uppercase letter, 1 lowercase letter, 1 number and 1 special character.',
];
