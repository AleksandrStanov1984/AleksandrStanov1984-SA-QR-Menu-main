<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IMPRESSUM (PLATFORM)
    |--------------------------------------------------------------------------
    */

    'impressum' => [
        'kicker' => 'Rechtliche Hinweise',
        'title' => 'Impressum',
        'updated' => '',
        'toc_title' => 'Inhaltsverzeichnis',

        'toc' => [
            ['id' => 'angaben', 'label' => 'Angaben gemäß § 5 TMG'],
            ['id' => 'kontakt', 'label' => 'Kontakt'],
            ['id' => 'haftung', 'label' => 'Haftung für Inhalte'],
            ['id' => 'sprachversionen', 'label' => 'Sprachversionen'],
        ],

        'sections' => [

            [
                'id' => 'angaben',
                'title' => 'Angaben gemäß § 5 TMG',
                'body' => '
                    <p>
                        :platform_name<br>
                        :platform_address_line_1<br>
                        :platform_address_line_2<br>
                        :platform_country
                    </p>
                ',
            ],

            [
                'id' => 'kontakt',
                'title' => 'Kontakt',
                'body' => '
                    <p>
                        E-Mail: <a href="mailto::platform_email">:platform_email</a><br>
                        Telefon: <a href="tel::platform_phone">:platform_phone</a>
                    </p>
                ',
            ],

            [
                'id' => 'haftung',
                'title' => 'Haftung für Inhalte',
                'body' => '
                    <p>
                        Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt.
                        Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte
                        können wir jedoch keine Gewähr übernehmen.
                    </p>

                    <p>
                        Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte
                        auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich.
                    </p>
                ',
            ],

            [
                'id' => 'sprachversionen',
                'title' => 'Sprachversionen',
                'body' => '
                    <p>
                        Dieses Impressum ist in mehreren Sprachen verfügbar.
                        Maßgeblich und rechtlich verbindlich ist die deutsche Fassung.
                        Übersetzungen dienen lediglich der besseren Verständlichkeit.
                    </p>
                ',
            ],

        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | DATENSCHUTZ (FULL SAAS)
    |--------------------------------------------------------------------------
    */

    'datenschutz' => [
        'kicker' => 'Rechtliche Hinweise',
        'title' => 'Datenschutzerklärung',
        'updated' => '',
        'toc_title' => 'Inhaltsverzeichnis',

        'toc' => [
            ['id' => 'allgemeine', 'label' => 'Allgemeine Hinweise'],
            ['id' => 'verantwortlicher', 'label' => 'Verantwortlicher'],
            ['id' => 'daten', 'label' => 'Erhobene Daten'],
            ['id' => 'zweck', 'label' => 'Zweck der Verarbeitung'],
            ['id' => 'hosting', 'label' => 'Hosting'],
            ['id' => 'cookies', 'label' => 'Cookies'],
            ['id' => 'email', 'label' => 'E-Mail-Kommunikation'],
            ['id' => 'sicherheit', 'label' => 'Datensicherheit'],
            ['id' => 'analyse', 'label' => 'Analyse'],
            ['id' => 'uebersetzungen', 'label' => 'Übersetzungen'],
            ['id' => 'rechte', 'label' => 'Rechte der Nutzer'],
            ['id' => 'aenderungen', 'label' => 'Änderungen'],
            ['id' => 'sprachversionen', 'label' => 'Sprachversionen'],
        ],

        'sections' => [

            [
                'id' => 'allgemeine',
                'title' => '1. Allgemeine Hinweise',
                'body' => '
                    <p>
                        Der Schutz Ihrer persönlichen Daten ist uns ein wichtiges Anliegen.
                        Die Verarbeitung personenbezogener Daten erfolgt in Übereinstimmung
                        mit den gesetzlichen Datenschutzvorschriften, insbesondere der DSGVO.
                    </p>
                ',
            ],

            [
                'id' => 'verantwortlicher',
                'title' => '2. Verantwortlicher',
                'body' => '
                    <p>
                        :platform_name<br>
                        :platform_address_line_1<br>
                        :platform_address_line_2<br>
                        :platform_country<br>
                        E-Mail: <a href="mailto::platform_email">:platform_email</a><br>
                        Telefon: <a href="tel::platform_phone">:platform_phone</a>
                    </p>
                ',
            ],

            [
                'id' => 'daten',
                'title' => '3. Erhobene Daten',
                'body' => '
                    <p><strong>a) Benutzerdaten</strong></p>
                    <ul>
                        <li>Name</li>
                        <li>E-Mail-Adresse</li>
                        <li>Passwort (verschlüsselt gespeichert)</li>
                        <li>Anmelde- und Zugriffsdaten</li>
                    </ul>

                    <p><strong>b) Objektdaten (z. B. Restaurant)</strong></p>
                    <ul>
                        <li>Name</li>
                        <li>Adresse</li>
                        <li>Telefonnummer</li>
                        <li>E-Mail-Adresse</li>
                        <li>Öffnungszeiten</li>
                        <li>Social-Media-Links</li>
                    </ul>

                    <p><strong>c) Inhalte</strong></p>
                    <ul>
                        <li>Kategorien und Unterkategorien</li>
                        <li>Menüeinträge</li>
                        <li>Beschreibungen</li>
                        <li>Bilder (Speisen, Logos, Hintergründe, OG-Bilder)</li>
                    </ul>
                ',
            ],

            [
                'id' => 'zweck',
                'title' => '4. Zweck der Verarbeitung',
                'body' => '
                    <ul>
                        <li>Bereitstellung und Betrieb der Plattform</li>
                        <li>Verwaltung von Benutzerkonten</li>
                        <li>Verwaltung von Inhalten</li>
                        <li>Sicherstellung der technischen Funktionalität</li>
                        <li>Gewährleistung von Sicherheit und Stabilität</li>
                    </ul>
                ',
            ],

            [
                'id' => 'hosting',
                'title' => '5. Hosting',
                'body' => '
                    <p>
                        Die Daten werden auf Servern innerhalb der Europäischen Union gespeichert.
                        Die Übertragung erfolgt verschlüsselt mittels SSL/TLS.
                    </p>
                ',
            ],

            [
                'id' => 'cookies',
                'title' => '6. Cookies',
                'body' => '
                    <p>
                        Es werden ausschließlich technisch notwendige Cookies verwendet,
                        insbesondere zur Sitzungsverwaltung und Authentifizierung.
                    </p>
                ',
            ],

            [
                'id' => 'email',
                'title' => '7. E-Mail-Kommunikation',
                'body' => '
                    <p>
                        Es können systembezogene E-Mails versendet werden, z. B. zur
                        Kontoverwaltung oder für technische Hinweise.
                    </p>
                    <p>
                        Marketing-E-Mails werden nur mit ausdrücklicher Einwilligung versendet.
                    </p>
                ',
            ],

            [
                'id' => 'sicherheit',
                'title' => '8. Datensicherheit',
                'body' => '
                    <p>
                        Wir setzen technische und organisatorische Maßnahmen ein,
                        um Ihre Daten vor Verlust, Manipulation und unberechtigtem Zugriff zu schützen.
                    </p>
                ',
            ],

            [
                'id' => 'analyse',
                'title' => '9. Analyse',
                'body' => '
                    <p>
                        Derzeit werden keine Analyse- oder Tracking-Tools eingesetzt.
                        Eine zukünftige Nutzung kann erfolgen.
                    </p>
                ',
            ],

            [
                'id' => 'uebersetzungen',
                'title' => '10. Übersetzungen',
                'body' => '
                    <p>
                        Je nach technischer Umsetzung können externe Übersetzungsdienste eingesetzt werden.
                    </p>
                ',
            ],

            [
                'id' => 'rechte',
                'title' => '11. Rechte der Nutzer',
                'body' => '
                    <ul>
                        <li>Auskunft über gespeicherte Daten</li>
                        <li>Berichtigung unrichtiger Daten</li>
                        <li>Löschung Ihrer Daten</li>
                        <li>Einschränkung der Verarbeitung</li>
                        <li>Widerspruch gegen die Verarbeitung</li>
                    </ul>
                ',
            ],

            [
                'id' => 'aenderungen',
                'title' => '12. Änderungen',
                'body' => '
                    <p>
                        Diese Datenschutzerklärung kann bei Änderungen des Dienstes angepasst werden.
                    </p>
                ',
            ],

            [
                'id' => 'sprachversionen',
                'title' => '13. Sprachversionen',
                'body' => '
                    <p>
                        Diese Datenschutzerklärung ist in mehreren Sprachen verfügbar.
                        Die deutsche Version ist die maßgebliche und rechtlich verbindliche Fassung.
                        Übersetzungen dienen lediglich der besseren Verständlichkeit.
                    </p>
                ',
            ],

        ],
    ],

];
