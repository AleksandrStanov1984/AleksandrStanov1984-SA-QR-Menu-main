<?php

return [
    'nav' => [
        'impressum' => 'Impressum',
        'datenschutz' => 'Datenschutzerklärung',
    ],
    'impressum' => [
        'kicker' => 'Rechtliche Informationen',
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
                    Für die Richtigkeit, Vollständigkeit und Aktualität der Inhalte können wir jedoch keine Gewähr übernehmen.
                </p>
            ',
            ],
            [
                'id' => 'haftung-links',
                'title' => 'Haftung für Links',
                'body' => '
                <p>
                    Unsere Website enthält Links zu externen Webseiten, auf deren Inhalte wir keinen Einfluss haben.
                </p>
            ',
            ],
            [
                'id' => 'urheberrecht',
                'title' => 'Urheberrecht',
                'body' => '
                <p>
                    Die Inhalte dieser Website unterliegen dem Urheberrecht.
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
                    Informationen über die Plattform:
                    <a href="/legal/impressum" target="_blank" rel="noopener">
                        Impressum der Plattform
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
                    Rechtsverbindlich ist ausschließlich die deutsche Version.
                    Übersetzungen dienen nur der besseren Verständlichkeit.
                </p>
            ',
            ],
        ],
    ],
    'datenschutz' => [
        'kicker' => 'Rechtliche Informationen',
        'title' => 'Datenschutzerklärung',
        'updated' => '',
        'toc_title' => 'Inhaltsverzeichnis',
        'toc' => [
            ['id' => 'allgemeine-hinweise', 'label' => 'Allgemeine Hinweise'],
            ['id' => 'verantwortlicher', 'label' => 'Verantwortliche Stelle'],
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
                        Wir behandeln personenbezogene Daten vertraulich und entsprechend der DSGVO.
                    </p>
                ',
            ],
            [
                'id' => 'verantwortlicher',
                'title' => '2. Verantwortliche Stelle',
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
                    <p>Wir erfassen nur technisch notwendige Daten.</p>
                ',
            ],
            [
                'id' => 'zweck',
                'title' => '4. Zweck der Verarbeitung',
                'body' => '
                    <p>Sicherstellung des Betriebs des Dienstes.</p>
                ',
            ],
            [
                'id' => 'hosting',
                'title' => '5. Hosting',
                'body' => '
                    <p>Die Daten werden auf Servern innerhalb der EU gespeichert.</p>
                ',
            ],
            [
                'id' => 'cookies',
                'title' => '6. Cookies',
                'body' => '
                    <p>Es werden nur technisch notwendige Cookies verwendet.</p>
                ',
            ],
            [
                'id' => 'email',
                'title' => '7. E-Mail',
                'body' => '
                    <p>Es werden ausschließlich technische E-Mails versendet.</p>
                ',
            ],
            [
                'id' => 'analyse',
                'title' => '8. Analyse',
                'body' => '<p>Es wird keine Analyse verwendet.</p>',
            ],
            [
                'id' => 'plattform',
                'title' => '9. Plattform',
                'body' => '
                    <p>
                        Diese Website läuft auf der Plattform <strong>SA QR Menu</strong>,
                        welche den technischen Betrieb des Dienstes sicherstellt (Hosting, Sicherheit, Datenverarbeitung).
                    </p>

                    <p>
                        Weitere Informationen zum Datenschutz der Plattform:
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
                    <p>Nutzer haben das Recht auf Auskunft, Berichtigung und Löschung ihrer Daten.</p>
                ',
            ],
            [
                'id' => 'aenderungen',
                'title' => '11. Änderungen',
                'body' => '<p>Dieses Dokument kann aktualisiert werden.</p>',
            ],
            [
                'id' => 'sprachversionen',
                'title' => '12. Sprachversionen',
                'body' => '
                    <p>
                        Dieses Dokument ist in mehreren Sprachen verfügbar.
                        Rechtsverbindlich ist ausschließlich die deutsche Version.
                    </p>
                ',
            ],
        ],
        'source_note' => '',
    ],
];
