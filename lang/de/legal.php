<?php

return [

    'nav' => [
        'impressum' => 'Impressum',
        'datenschutz' => 'Datenschutz',
    ],

    'impressum' => [
        'kicker' => 'Rechtliches',
        'title' => 'Impressum',
        'updated' => '',
        'toc_title' => 'Inhalt',

        'toc' => [
            ['id' => 'angaben', 'label' => 'Angaben gemäß § 5 TMG'],
            ['id' => 'kontakt', 'label' => 'Kontakt'],
            ['id' => 'verantwortlich', 'label' => 'Verantwortlich für Inhalte'],
            ['id' => 'haftung-inhalte', 'label' => 'Haftung für Inhalte'],
            ['id' => 'haftung-links', 'label' => 'Haftung für Links'],
            ['id' => 'urheberrecht', 'label' => 'Urheberrecht'],
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
                'title' => 'Verantwortlich für Inhalte gemäß § 55 Abs. 2 RStV',
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
                <p>
                    Als Diensteanbieter sind wir gemäß § 7 Abs.1 TMG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich.
                    Nach §§ 8 bis 10 TMG sind wir als Diensteanbieter jedoch nicht verpflichtet,
                    übermittelte oder gespeicherte fremde Informationen zu überwachen
                    oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.
                </p>
                <p>
                    Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt.
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
                <p>
                    Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen.
                    Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich.
                </p>
                <p>
                    Zum Zeitpunkt der Verlinkung waren keine rechtswidrigen Inhalte erkennbar.
                    Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar.
                </p>
            ',
            ],

            [
                'id' => 'urheberrecht',
                'title' => 'Urheberrecht',
                'body' => '
                <p>
                    Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten
                    unterliegen dem deutschen Urheberrecht.
                </p>
                <p>
                    Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes
                    bedürfen der schriftlichen Zustimmung des jeweiligen Autors bzw. Erstellers.
                </p>
                <p>
                    Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet.
                </p>
            ',
            ],

            [
                'id' => 'sprachversionen',
                'title' => 'Sprachversionen',
                'body' => '
                <p>
                    Dieses Impressum ist in mehreren Sprachen verfügbar.
                    Die deutsche Version ist die maßgebliche und rechtlich verbindliche Fassung.
                    Übersetzungen dienen ausschließlich der besseren Verständlichkeit.
                </p>
            ',
            ],

        ],
    ],

    'datenschutz' => [
        'kicker' => 'Rechtliches',
        'title' => 'Datenschutzerklärung',
        'updated' => 'Stand: ' . date('d.m.Y'),
        'toc_title' => 'Inhalt',

        'toc' => [
            ['id' => 'allgemeine-hinweise', 'label' => 'Allgemeine Hinweise'],
            ['id' => 'verantwortlicher', 'label' => 'Verantwortlicher'],
            ['id' => 'erhobene-daten', 'label' => 'Erhobene Daten'],
            ['id' => 'zweck', 'label' => 'Zweck der Verarbeitung'],
            ['id' => 'hosting', 'label' => 'Hosting'],
            ['id' => 'cookies', 'label' => 'Cookies'],
            ['id' => 'email', 'label' => 'E-Mail-Kommunikation'],
            ['id' => 'analyse', 'label' => 'Analyse-Tools'],
            ['id' => 'uebersetzungen', 'label' => 'Übersetzungen'],
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
                        Der Schutz Ihrer persönlichen Daten ist uns ein wichtiges Anliegen.
                        Wir behandeln Ihre personenbezogenen Daten vertraulich und entsprechend
                        der gesetzlichen Datenschutzvorschriften, insbesondere der DSGVO.
                    </p>',
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
                    </p>',
            ],

            [
                'id' => 'erhobene-daten',
                'title' => '3. Erhobene Daten',
                'body' => '
                    <p><strong>a) Benutzerdaten</strong></p>
                    <ul>
                        <li>Name</li>
                        <li>E-Mail-Adresse</li>
                        <li>Passwort (verschlüsselt)</li>
                        <li>Login-Daten</li>
                    </ul>

                    <p><strong>b) Objektdaten (z. B. Restaurant)</strong></p>
                    <ul>
                        <li>Name</li>
                        <li>Adresse</li>
                        <li>Telefonnummer</li>
                        <li>E-Mail-Adresse</li>
                        <li>Öffnungszeiten</li>
                        <li>Social Media Links</li>
                    </ul>

                    <p><strong>c) Inhalte</strong></p>
                    <ul>
                        <li>Kategorien</li>
                        <li>Unterkategorien</li>
                        <li>Menüeinträge</li>
                        <li>Beschreibungen</li>
                        <li>Bilder: Speisen, Logos, Hintergründe, OG-Bilder</li>
                    </ul>',
            ],

            [
                'id' => 'zweck',
                'title' => '4. Zweck der Verarbeitung',
                'body' => '
                    <ul>
                        <li>Plattformbetrieb</li>
                        <li>Verwaltung von Benutzerkonten</li>
                        <li>Content-Verwaltung</li>
                        <li>Technische Sicherheit</li>
                    </ul>',
            ],

            [
                'id' => 'hosting',
                'title' => '5. Hosting',
                'body' => '
                    <p>
                        Die Daten werden auf Servern innerhalb der Europäischen Union gespeichert.
                        Der Hosting-Anbieter wird im Rahmen des Betriebs festgelegt.
                        Die Übertragung erfolgt verschlüsselt über SSL/TLS.
                    </p>',
            ],

            [
                'id' => 'cookies',
                'title' => '6. Cookies',
                'body' => '
                    <p>
                        Diese Website verwendet technisch notwendige Cookies und Sessions
                        zur Sicherstellung der Funktionalität.
                    </p>',
            ],

            [
                'id' => 'email',
                'title' => '7. E-Mail-Kommunikation',
                'body' => '
                    <p>
                        Technische E-Mails können versendet werden.
                    </p>
                    <p>
                        Werbliche E-Mails erfolgen nur nach ausdrücklicher Einwilligung.
                    </p>',
            ],

            [
                'id' => 'analyse',
                'title' => '8. Analyse-Tools',
                'body' => '
                    <p>
                        Derzeit werden keine Analyse-Tools eingesetzt.
                    </p>',
            ],

            [
                'id' => 'uebersetzungen',
                'title' => '9. Übersetzungen',
                'body' => '
                    <p>
                        In Zukunft kann eine automatische Übersetzung erfolgen.
                    </p>',
            ],

            [
                'id' => 'rechte',
                'title' => '10. Rechte der Nutzer',
                'body' => '
                    <ul>
                        <li>Auskunft</li>
                        <li>Berichtigung</li>
                        <li>Löschung</li>
                        <li>Einschränkung</li>
                        <li>Widerspruch</li>
                    </ul>',
            ],

            [
                'id' => 'aenderungen',
                'title' => '11. Änderungen',
                'body' => '<p>Diese Datenschutzerklärung kann angepasst werden.</p>',
            ],

            [
                'id' => 'sprachversionen',
                'title' => '12. Sprachversionen',
                'body' => '
                    <p>
                        Diese Datenschutzerklärung ist in mehreren Sprachen verfügbar.
                        Die deutsche Version ist die rechtlich verbindliche Fassung.
                        Übersetzungen dienen nur der Verständlichkeit.
                    </p>',
            ],

        ],

        'source_note' => '',
    ],
];
