<?php

return [
    'nav' => [
        'impressum' => 'Imprint',
        'datenschutz' => 'Privacy Policy',
    ],
    'impressum' => [
        'kicker' => 'Legal Information',
        'title' => 'Imprint',
        'updated' => '',
        'toc_title' => 'Table of Contents',
        'toc' => [
            ['id' => 'angaben', 'label' => 'Information according to § 5 TMG'],
            ['id' => 'kontakt', 'label' => 'Contact'],
            ['id' => 'verantwortlich', 'label' => 'Responsible for Content'],
            ['id' => 'haftung-inhalte', 'label' => 'Liability for Content'],
            ['id' => 'haftung-links', 'label' => 'Liability for Links'],
            ['id' => 'urheberrecht', 'label' => 'Copyright'],
            ['id' => 'plattform', 'label' => 'Platform'],
            ['id' => 'sprachversionen', 'label' => 'Language Versions'],
        ],
        'sections' => [
            [
                'id' => 'angaben',
                'title' => 'Information according to § 5 TMG',
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
                'title' => 'Contact',
                'body' => '
                <p>
                    E-Mail: <a href="mailto::owner_email">:owner_email</a><br>
                    Phone: <a href="tel::owner_phone">:owner_phone</a>
                </p>
            ',
            ],
            [
                'id' => 'verantwortlich',
                'title' => 'Responsible for Content according to § 55 Abs. 2 RStV',
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
                'title' => 'Liability for Content',
                'body' => '
                <p>
                    The content of our pages has been created with the greatest care.
                    However, we cannot guarantee the accuracy, completeness, or timeliness of the information provided.
                </p>
            ',
            ],
            [
                'id' => 'haftung-links',
                'title' => 'Liability for Links',
                'body' => '
                <p>
                    Our website contains links to external websites whose content we have no influence over.
                </p>
            ',
            ],
            [
                'id' => 'urheberrecht',
                'title' => 'Copyright',
                'body' => '
                <p>
                    The content of this website is protected by copyright law.
                </p>
            ',
            ],
            [
                'id' => 'plattform',
                'title' => 'Platform',
                'body' => '
                <p>
                    This website is provided through the <strong>SA QR Menu</strong> platform.
                </p>

                <p>
                    Platform information:
                    <a href="/legal/impressum" target="_blank" rel="noopener">
                        Platform Imprint
                    </a>
                </p>
            ',
            ],
            [
                'id' => 'sprachversionen',
                'title' => 'Language Versions',
                'body' => '
                <p>
                    This imprint is available in multiple languages.
                    Only the German version is legally binding.
                    Translations are provided for convenience only.
                </p>
            ',
            ],
        ],
    ],
    'datenschutz' => [
        'kicker' => 'Legal Information',
        'title' => 'Privacy Policy',
        'updated' => '',
        'toc_title' => 'Table of Contents',
        'toc' => [
            ['id' => 'allgemeine-hinweise', 'label' => 'General Information'],
            ['id' => 'verantwortlicher', 'label' => 'Responsible Entity'],
            ['id' => 'erhobene-daten', 'label' => 'Collected Data'],
            ['id' => 'zweck', 'label' => 'Purpose of Processing'],
            ['id' => 'hosting', 'label' => 'Hosting'],
            ['id' => 'cookies', 'label' => 'Cookies'],
            ['id' => 'email', 'label' => 'E-Mail Communication'],
            ['id' => 'analyse', 'label' => 'Analytics'],
            ['id' => 'plattform', 'label' => 'Platform'],
            ['id' => 'rechte', 'label' => 'User Rights'],
            ['id' => 'aenderungen', 'label' => 'Changes'],
            ['id' => 'sprachversionen', 'label' => 'Language Versions'],
        ],
        'sections' => [
            [
                'id' => 'allgemeine-hinweise',
                'title' => '1. General Information',
                'body' => '
                    <p>
                        We process personal data confidentially and in accordance with GDPR/DSGVO.
                    </p>
                ',
            ],
            [
                'id' => 'verantwortlicher',
                'title' => '2. Responsible Entity',
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
                'title' => '3. Collected Data',
                'body' => '
                    <p>We collect only technically necessary data.</p>
                ',
            ],
            [
                'id' => 'zweck',
                'title' => '4. Purpose of Processing',
                'body' => '
                    <p>Ensuring the operation of the service.</p>
                ',
            ],
            [
                'id' => 'hosting',
                'title' => '5. Hosting',
                'body' => '
                    <p>Data is hosted on servers within the EU.</p>
                ',
            ],
            [
                'id' => 'cookies',
                'title' => '6. Cookies',
                'body' => '
                    <p>Only technically necessary cookies are used.</p>
                ',
            ],
            [
                'id' => 'email',
                'title' => '7. E-Mail',
                'body' => '
                    <p>Only technical emails are sent.</p>
                ',
            ],
            [
                'id' => 'analyse',
                'title' => '8. Analytics',
                'body' => '<p>Analytics are not used.</p>',
            ],
            [
                'id' => 'plattform',
                'title' => '9. Platform',
                'body' => '
                    <p>
                        This website operates on the <strong>SA QR Menu</strong> platform,
                        which provides the technical operation of the service (hosting, security, data processing).
                    </p>

                    <p>
                        More information about platform data protection:
                        <a href="/legal/datenschutz" target="_blank" rel="noopener">
                            Platform Privacy Policy
                        </a>
                    </p>
                ',
            ],
            [
                'id' => 'rechte',
                'title' => '10. User Rights',
                'body' => '
                    <p>Users have the right to access, correct, and delete their data.</p>
                ',
            ],
            [
                'id' => 'aenderungen',
                'title' => '11. Changes',
                'body' => '<p>This document may be updated.</p>',
            ],
            [
                'id' => 'sprachversionen',
                'title' => '12. Language Versions',
                'body' => '
                    <p>
                        This document is available in multiple languages.
                        Only the German version is legally binding.
                    </p>
                ',
            ],
        ],
        'source_note' => '',
    ],
];
