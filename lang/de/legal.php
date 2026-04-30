<?php

return [

    'nav' => [
        'impressum' => 'Impressum',
        'datenschutz' => 'Datenschutzerklärung',
    ],

    'impressum' => [
        'kicker' => 'Rechtliche Hinweise',
        'title' => 'Impressum',
        'updated' => '',
        'toc_title' => 'Inhaltsverzeichnis',

        'toc' => [
            ['id' => 'angaben', 'label' => 'Angaben gemäß § 5 TMG'],
            ['id' => 'kontakt', 'label' => 'Kontakt'],
            ['id' => 'verantwortlich', 'label' => 'Verantwortlich für den Inhalt'],
            ['id' => 'haftung-inhalte', 'label' => 'Haftung für Inhalte'],
            ['id' => 'haftung-links', 'label' => 'Haftung für Links'],
            ['id' => 'urheberrecht', 'label' => 'Urheberrecht'],
            ['id' => 'plattform', 'label' => 'Plattform'],
            ['id' => 'sprachversionen', 'label' => 'Sprachversionen'],
        ],

        'sections' => [

            [
                'id' => 'angaben',
                'title' => 'Angaben gemäß § 5 TMG',
                'body' => '
                <p>
                    :owner_name<br>
                    :owner_address_line_1<br>
                    :owner_address_line_2<br>
                    :owner_country
                </p>
            ',
            ],

            [
                'id' => 'kontakt',
                'title' => 'Kontakt',
                'body' => '
                <p>
                    E-Mail: <a href="mailto::owner_email">:owner_email</a><br>
                    Telefon: <a href="tel::owner_phone">:owner_phone</a>
                </p>
            ',
            ],

            [
                'id' => 'verantwortlich',
                'title' => 'Verantwortlich für den Inhalt gemäß § 55 Abs. 2 RStV',
                'body' => '
                <p>
                    :owner_name<br>
                    :owner_address_line_1<br>
                    :owner_address_line_2<br>
                    :owner_country
                </p>
            ',
            ],

            [
                'id' => 'haftung-inhalte',
                'title' => 'Haftung für Inhalte',
                'body' => '
                <p>
                    Die Inhalte unserer Seiten wurden mit größter Sorgfalt erstellt.
                    Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte
                    können wir jedoch keine Gewähr übernehmen.
                </p>
            ',
            ],

            [
                'id' => 'haftung-links',
                'title' => 'Haftung für Links',
                'body' => '
                <p>
                    Unser Angebot enthält Links zu externen Websites Dritter,
                    auf deren Inhalte wir keinen Einfluss haben.
                </p>
            ',
            ],

            [
                'id' => 'urheberrecht',
                'title' => 'Urheberrecht',
                'body' => '
                <p>
                    Die Inhalte dieser Seiten unterliegen dem deutschen Urheberrecht.
                </p>
            ',
            ],

            [
                'id' => 'plattform',
                'title' => 'Plattform',
                'body' => '
                <p>
                    Diese Website wird über die Plattform <strong>SA QR Menu</strong> bereitgestellt.
                </p>

                <p>
                    Informationen zur Plattform:
                    <a href="/legal/impressum" target="_blank" rel="noopener">
                        Plattform-Impressum
                    </a>
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

    'datenschutz' => [
        'kicker' => 'Rechtliche Hinweise',
        'title' => 'Datenschutzerklärung',
        'updated' => '',
        'toc_title' => 'Inhaltsverzeichnis',

        'toc' => [
            ['id' => 'allgemeine-hinweise', 'label' => 'Allgemeine Hinweise'],
            ['id' => 'verantwortlicher', 'label' => 'Verantwortlicher'],
            ['id' => 'erhobene-daten', 'label' => 'Erhobene Daten'],
            ['id' => 'zweck', 'label' => 'Zweck der Verarbeitung'],
            ['id' => 'hosting', 'label' => 'Hosting'],
            ['id' => 'cookies', 'label' => 'Cookies'],
            ['id' => 'email', 'label' => 'E-Mail-Kommunikation'],
            ['id' => 'analyse', 'label' => 'Analyse'],
            ['id' => 'plattform', 'label' => 'Plattform'],
            ['id' => 'rechte', 'label' => 'Rechte der Nutzer'],
            ['id' => 'aenderungen', 'label' => 'Änderungen'],
            ['id' => 'sprachversionen', 'label' => 'Sprachversionen'],
        ],

        'sections' => [

            [
                'id' => 'allgemeine-hinweise',
                'title' => '1. Allgemeine Hinweise',
                'body' => '
                    <p>
                        Wir behandeln Ihre personenbezogenen Daten vertraulich
                        und entsprechend den gesetzlichen Datenschutzvorschriften (DSGVO).
                    </p>
                ',
            ],

            [
                'id' => 'verantwortlicher',
                'title' => '2. Verantwortlicher',
                'body' => '
                    <p>
                        :owner_name<br>
                        :owner_address_line_1<br>
                        :owner_address_line_2<br>
                        :owner_country<br>
                        E-Mail: <a href="mailto::owner_email">:owner_email</a>
                    </p>
                ',
            ],

            [
                'id' => 'erhobene-daten',
                'title' => '3. Erhobene Daten',
                'body' => '
                    <p>
                        Es werden nur technisch notwendige Daten erhoben.
                    </p>
                ',
            ],

            [
                'id' => 'zweck',
                'title' => '4. Zweck der Verarbeitung',
                'body' => '
                    <p>
                        Die Verarbeitung erfolgt ausschließlich zur Bereitstellung
                        des Dienstes.
                    </p>
                ',
            ],

            [
                'id' => 'hosting',
                'title' => '5. Hosting',
                'body' => '
                    <p>
                        Die Daten werden auf Servern innerhalb der EU gespeichert.
                    </p>
                ',
            ],

            [
                'id' => 'cookies',
                'title' => '6. Cookies',
                'body' => '
                    <p>
                        Es werden nur notwendige Cookies verwendet.
                    </p>
                ',
            ],

            [
                'id' => 'email',
                'title' => '7. E-Mail-Kommunikation',
                'body' => '
                    <p>
                        Es werden ausschließlich systemrelevante E-Mails versendet.
                    </p>
                ',
            ],

            [
                'id' => 'analyse',
                'title' => '8. Analyse',
                'body' => '<p>Es werden keine Analyse-Tools verwendet.</p>',
            ],

            [
                'id' => 'plattform',
                'title' => '9. Plattform',
                'body' => '
                    <p>
                        Diese Website nutzt die Plattform <strong>SA QR Menu</strong>,
                        die für Hosting, Sicherheit und technische Funktion verantwortlich ist.
                    </p>

                    <p>
                        Weitere Informationen:
                        <a href="/legal/datenschutz" target="_blank" rel="noopener">
                            Datenschutzerklärung der Plattform
                        </a>
                    </p>
                ',
            ],

            [
                'id' => 'rechte',
                'title' => '10. Rechte der Nutzer',
                'body' => '
                    <p>
                        Sie haben das Recht auf Auskunft, Berichtigung und Löschung Ihrer Daten.
                    </p>
                ',
            ],

            [
                'id' => 'aenderungen',
                'title' => '11. Änderungen',
                'body' => '<p>Diese Erklärung kann aktualisiert werden.</p>',
            ],

            [
                'id' => 'sprachversionen',
                'title' => '12. Sprachversionen',
                'body' => '
                    <p>
                        Diese Erklärung ist in mehreren Sprachen verfügbar.
                        Maßgeblich ist die deutsche Version.
                    </p>
                ',
            ],

        ],
    ],

];
