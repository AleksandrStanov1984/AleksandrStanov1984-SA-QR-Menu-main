<?php

return [

    'security' => [
        'title' => 'Sicherheit',
        'subtitle' => 'E-Mail und Passwort',
        'h2' => 'Sicherheit',

        'user_object' => 'Benutzer des Objekts',

        'status' => [
            'email_changed' => 'E-Mail erfolgreich geändert',
            'password_changed' => 'Passwort erfolgreich geändert',
        ],

        'errors' => [
            'current_email_wrong' => 'Die aktuelle E-Mail ist falsch',
            'current_password_wrong' => 'Das aktuelle Passwort ist falsch',
        ],

        'validation' => [
            'current_email' => [
                'required' => 'Bitte aktuelle E-Mail eingeben',
                'email' => 'Ungültige E-Mail-Adresse',
            ],

            'new_email' => [
                'required' => 'Bitte neue E-Mail eingeben',
                'email' => 'Ungültige E-Mail-Adresse',
                'unique' => 'Diese E-Mail wird bereits verwendet',
            ],

            'current_password' => [
                'required' => 'Bitte aktuelles Passwort eingeben',
            ],

            'new_password' => [
                'required' => 'Bitte neues Passwort eingeben',
                'min' => 'Das Passwort muss mindestens 8 Zeichen lang sein',
                'regex' => 'Das Passwort muss Groß- und Kleinbuchstaben, eine Zahl und ein Sonderzeichen enthalten',
            ],

            'new_password_confirm' => [
                'required' => 'Bitte Passwort bestätigen',
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
