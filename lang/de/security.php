<?php

return [
    'security' => [
        'title' => 'Sicherheit',
        'subtitle' => 'E-Mail und Passwort',
        'h2' => 'Sicherheit',
        'user_object' => 'Objektbenutzer',
        'status' => [
            'email_changed' => 'E-Mail erfolgreich geändert',
            'password_changed' => 'Passwort erfolgreich geändert',
        ],
        'errors' => [
            'current_email_wrong' => 'Die aktuelle E-Mail ist falsch',
            'current_password_wrong' => 'Aktuelles Passwort ist falsch',
        ],
        'validation' => [
            'current_email' => [
                'required' => 'Bitte geben Sie die aktuelle E-Mail ein',
                'email' => 'Ungültige E-Mail',
            ],
            'new_email' => [
                'required' => 'Bitte geben Sie eine neue E-Mail ein',
                'email' => 'Ungültige E-Mail',
                'unique' => 'Diese E-Mail wird bereits verwendet',
            ],
            'current_password' => [
                'required' => 'Bitte geben Sie das aktuelle Passwort ein',
            ],
            'new_password' => [
                'required' => 'Bitte geben Sie ein neues Passwort ein',
                'min' => 'Das Passwort muss mindestens 8 Zeichen enthalten',
                'regex' => 'Das Passwort muss Großbuchstaben, Kleinbuchstaben, Zahlen und Sonderzeichen enthalten',
            ],
            'new_password_confirm' => [
                'required' => 'Bitte bestätigen Sie das Passwort',
                'same' => 'Die Passwörter stimmen nicht überein',
            ],
        ],
    ],
    'profile' => [
        'change_email' => [
            'h2' => 'E-Mail ändern',
            'current_email' => 'Aktuelle E-Mail',
            'current_password' => 'Aktuelles Passwort',
            'new_email' => 'Neue E-Mail',
        ],
        'change_password' => [
            'h2' => 'Passwort ändern',
            'current_email' => 'Aktuelle E-Mail',
            'current_password' => 'Aktuelles Passwort',
            'new_password' => 'Neues Passwort',
            'confirm_new_password' => 'Neues Passwort bestätigen',
        ],
    ],
    'common' => [
        'change' => 'Ändern',
    ],
    'password_hint' => 'Passwort: mindestens 8 Zeichen, 1 Großbuchstabe, 1 Kleinbuchstabe, 1 Zahl und 1 Sonderzeichen.',
];
