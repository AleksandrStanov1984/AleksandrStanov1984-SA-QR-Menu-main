<?php

return [
    'invalid_form' => 'Bitte füllen Sie die Felder korrekt aus',
    'email' => 'Ungültiges E-Mail-Format',
    'regex' => 'Ungültiges Feldformat',
    'security' => [
        'title' => 'Sicherheit',
        'subtitle' => 'Login und Passwort',
        'h2' => 'Sicherheitseinstellungen',
        'user_object' => 'Benutzer',
        'validation' => [
            'new_email' => [
                'required' => 'Bitte geben Sie einen neuen Login (E-Mail) ein',
                'email' => 'Ungültiges E-Mail-Format',
                'unique' => 'Diese E-Mail wird bereits verwendet',
            ],
            'current_password' => [
                'required' => 'Bitte geben Sie das aktuelle Passwort ein',
            ],
            'new_password' => [
                'required' => 'Bitte geben Sie ein neues Passwort ein',
                'min' => 'Mindestens 8 Zeichen',
                'regex' => 'Das Passwort muss enthalten: A-Z, a-z, Zahl und Sonderzeichen',
            ],
            'new_password_confirm' => [
                'required' => 'Bitte bestätigen Sie das Passwort',
                'same' => 'Die Passwörter stimmen nicht überein',
            ],
        ],
        'errors' => [
            'email_same' => 'Die neue E-Mail entspricht der aktuellen',
            'email_exists' => 'Diese E-Mail existiert bereits',
            'current_password_wrong' => 'Aktuelles Passwort ist falsch',
        ],
        'status' => [
            'email_changed' => 'Login wurde erfolgreich geändert',
            'password_changed' => 'Passwort wurde erfolgreich geändert',
        ],
        'password_hint' => 'Lassen Sie das Feld leer, wenn Sie das Passwort nicht ändern möchten',
    ],
    'custom' => [
        'new_email' => [
            'email' => 'Ungültiges E-Mail-Format',
            'regex' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein',
        ],
    ],
];
