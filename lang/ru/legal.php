<?php

return [

    'nav' => [
        'impressum' => 'Импрессум',
        'datenschutz' => 'Политика конфиденциальности',
    ],

    'impressum' => [
        'kicker' => 'Юридическая информация',
        'title' => 'Импрессум',
        'updated' => '',
        'toc_title' => 'Содержание',

        'toc' => [
            ['id' => 'angaben', 'label' => 'Сведения согласно § 5 TMG'],
            ['id' => 'kontakt', 'label' => 'Контакты'],
            ['id' => 'verantwortlich', 'label' => 'Ответственный за содержание'],
            ['id' => 'haftung-inhalte', 'label' => 'Ответственность за содержание'],
            ['id' => 'haftung-links', 'label' => 'Ответственность за ссылки'],
            ['id' => 'urheberrecht', 'label' => 'Авторское право'],
            ['id' => 'plattform', 'label' => 'Платформа'],
            ['id' => 'sprachversionen', 'label' => 'Языковые версии'],
        ],

        'sections' => [

            [
                'id' => 'angaben',
                'title' => 'Сведения согласно § 5 TMG',
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
                'title' => 'Контакты',
                'body' => '
                <p>
                    E-Mail: <a href="mailto::owner_email">:owner_email</a><br>
                    Телефон: <a href="tel::owner_phone">:owner_phone</a>
                </p>
            ',
            ],

            [
                'id' => 'verantwortlich',
                'title' => 'Ответственный за содержание согласно § 55 Abs. 2 RStV',
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
                'title' => 'Ответственность за содержание',
                'body' => '
                <p>
                    Содержимое наших страниц было создано с максимальной тщательностью.
                    Однако мы не можем гарантировать правильность, полноту и актуальность информации.
                </p>
            ',
            ],

            [
                'id' => 'haftung-links',
                'title' => 'Ответственность за ссылки',
                'body' => '
                <p>
                    Наш сайт содержит ссылки на внешние ресурсы, за содержание которых мы ответственности не несем.
                </p>
            ',
            ],

            [
                'id' => 'urheberrecht',
                'title' => 'Авторское право',
                'body' => '
                <p>
                    Контент сайта защищен авторским правом.
                </p>
            ',
            ],

            [
                'id' => 'plattform',
                'title' => 'Платформа',
                'body' => '
                <p>
                    Данный веб-сайт предоставляется через платформу <strong>SA QR Menu</strong>.
                </p>

                <p>
                    Информация о платформе:
                    <a href="/legal/impressum" target="_blank" rel="noopener">
                        Импрессум платформы
                    </a>
                </p>
            ',
            ],

            [
                'id' => 'sprachversionen',
                'title' => 'Языковые версии',
                'body' => '
                <p>
                    Настоящий импрессум доступен на нескольких языках.
                    Юридически обязательной является версия на немецком языке.
                    Переводы предоставляются только для удобства.
                </p>
            ',
            ],

        ],
    ],

    'datenschutz' => [
        'kicker' => 'Юридическая информация',
        'title' => 'Политика конфиденциальности',
        'updated' => '',
        'toc_title' => 'Содержание',

        'toc' => [
            ['id' => 'allgemeine-hinweise', 'label' => 'Общая информация'],
            ['id' => 'verantwortlicher', 'label' => 'Ответственное лицо'],
            ['id' => 'erhobene-daten', 'label' => 'Собираемые данные'],
            ['id' => 'zweck', 'label' => 'Цель обработки'],
            ['id' => 'hosting', 'label' => 'Хостинг'],
            ['id' => 'cookies', 'label' => 'Cookies'],
            ['id' => 'email', 'label' => 'E-Mail-коммуникация'],
            ['id' => 'analyse', 'label' => 'Аналитика'],
            ['id' => 'plattform', 'label' => 'Платформа'],
            ['id' => 'rechte', 'label' => 'Права пользователей'],
            ['id' => 'aenderungen', 'label' => 'Изменения'],
            ['id' => 'sprachversionen', 'label' => 'Языковые версии'],
        ],

        'sections' => [

            [
                'id' => 'allgemeine-hinweise',
                'title' => '1. Общая информация',
                'body' => '
                    <p>
                        Мы обрабатываем данные конфиденциально и в соответствии с GDPR/DSGVO.
                    </p>
                ',
            ],

            [
                'id' => 'verantwortlicher',
                'title' => '2. Ответственное лицо',
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
                'title' => '3. Собираемые данные',
                'body' => '
                    <p>Мы собираем только технически необходимые данные.</p>
                ',
            ],

            [
                'id' => 'zweck',
                'title' => '4. Цель обработки',
                'body' => '
                    <p>Обеспечение работы сервиса.</p>
                ',
            ],

            [
                'id' => 'hosting',
                'title' => '5. Хостинг',
                'body' => '
                    <p>Данные размещаются на серверах в ЕС.</p>
                ',
            ],

            [
                'id' => 'cookies',
                'title' => '6. Cookies',
                'body' => '
                    <p>Используются только необходимые cookies.</p>
                ',
            ],

            [
                'id' => 'email',
                'title' => '7. E-Mail',
                'body' => '
                    <p>Отправляются только технические письма.</p>
                ',
            ],

            [
                'id' => 'analyse',
                'title' => '8. Аналитика',
                'body' => '<p>Аналитика не используется.</p>',
            ],

            [
                'id' => 'plattform',
                'title' => '9. Платформа',
                'body' => '
                    <p>
                        Данный сайт работает на платформе <strong>SA QR Menu</strong>,
                        которая обеспечивает техническую работу сервиса (хостинг, безопасность, обработка данных).
                    </p>

                    <p>
                        Подробнее о защите данных платформы:
                        <a href="/legal/datenschutz" target="_blank" rel="noopener">
                            Политика конфиденциальности платформы
                        </a>
                    </p>
                ',
            ],

            [
                'id' => 'rechte',
                'title' => '10. Права пользователей',
                'body' => '
                    <p>Пользователь имеет право на доступ, исправление и удаление данных.</p>
                ',
            ],

            [
                'id' => 'aenderungen',
                'title' => '11. Изменения',
                'body' => '<p>Документ может обновляться.</p>',
            ],

            [
                'id' => 'sprachversionen',
                'title' => '12. Языковые версии',
                'body' => '
                    <p>
                        Данный документ доступен на нескольких языках.
                        Юридически обязательной является немецкая версия.
                    </p>
                ',
            ],

        ],

        'source_note' => '',
    ],
];
