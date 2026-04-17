<?php

return [
    'choose_file' => 'Выбрать файл',
    'carousel_settings' => 'Настройки карусели',
    'carousel_saved' => 'Настройки карусели сохранены',
    'carousel_no_items_for_source' => 'К сожалению, нет блюд в категории ":source". Добавьте хотя бы 3 наименования.',

    'brand' => 'SA QR Menu — Admin',

    'ui' => [
        'admin' => 'Админка',
        'language' => 'Язык',
    ],

    'plans' => [
        'starter' => 'Starter',
        'basic' => 'Basic',
        'pro' => 'Pro',
    ],

    'og' => [
        'title' => 'OG изображения',
    ],

    'common' => [
        'save' => 'Сохранить',
        'cancel' => 'Отмена',
        'change' => 'Изменить',
        'admin' => 'Админка',
        'edit' => 'Изменить',
        'active'   => 'Активно',
        'inactive' => 'Отключено',
        'disabled' => 'Отключено',
        'dash'     => '—',
        'fix_these' => 'Ошибка загрузки',
        'remove' => 'Удалить',
        'disable' => 'Отключить',
        'enable' => 'Включить',
        'close' => 'Закрыть',
        'saving' => 'Сохранино!',
        'choose_file' => 'Выбрать файл',
    ],

    'fields' => [
        'id' => 'ID',
        'name' => 'Название',
        'slug' => 'Slug',
        'template' => 'Шаблон',
        'languages' => 'Языки',
        'status' => 'Статус',
        'actions' => 'Действия',
        'phone' => 'Телефон',
        'city' => 'Город',
        'street' => 'Улица',
        'house_number' => 'Дом',
        'postal_code' => 'Индекс',
        'user_name' => 'Имя',
        'user_email' => 'E-mail',
        'email' => 'Логин',
        'password' => 'Пароль',
        'plan' => 'Тарифный план',
        'password_confirm' => 'Повторите новый пароль.',

    ],

    'templates' => [
        'classic' => 'Classic',
        'fastfood' => 'Fastfood',
        'bar' => 'Bar',
        'services' => 'Services',
        'united' => 'United',
    ],

    'status' => [
        'active' => 'АКТИВЕН',
        'inactive' => 'НЕАКТИВЕН',
    ],

    'actions' => [
        'add' => 'Добавить',
        'edit' => 'Редактировать',
        'save' => 'Сохранить',
        'select' => 'Выбрать',
        'open' => 'Открыть',
        'activate' => 'Активировать',
        'deactivate' => 'Деактивировать',
        'logout' => 'Выйти',
        'cancel' => 'Отмена',
        'back' => 'Назад',
        'create_restaurant' => 'Создать',
        'create' => 'Создать',
        'delete' => 'Удалить',
        'close'  => 'Закрыть',
    ],

    'auth' => [
        'login_title' => 'Вход',
        'subtitle' => 'Войти, чтобы управлять объектами',
        'signin' => 'Войти',

        'login' => [
            'h2' => 'Вход',
            'subtitle' => 'Войти, чтобы управлять объектами',
            'submit' => 'Войти',
        ],
    ],

    'dashboard' => [
        'super_admin' => 'Супер-админ',
        'restaurant_admin' => 'Админ объекта',
        'current_context' => 'Текущий контекст',
        'no_selected' => 'Объект не выбран.',
        'open_editor' => 'Открыть редактор объекта',
        'pick_restaurant' => 'Выбрать объекта',
        'select_placeholder' => 'Выбрать…',
        'all_restaurants' => 'Все объекты',
        'next_steps' => 'Следующие шаги',
        'dashboard' => 'Панель',
        'home' => 'Панель',
    ],

    'breadcrumbs' => [
        'dashboard' => 'Панель',
        'home' => 'Панель',
    ],


    'restaurants' => [
        'index' => [
            'h1' => 'Объекты',
            'subtitle' => 'Управление всеми объектами (супер-админ)',
            'add' => '+ Добавить объект',
        ],
        'create' => [
            'title' => 'Создать объект',

            'h2' => 'Создать объект',
            'subtitle' => 'Создание объекта',
            'sections' => [
                'restaurant' => 'Объект',
                'user' => 'Пользователь',
            ],
        ],
        'edit' => [
            'h2' => 'Редактировать объект',
            'subtitle' => 'Настройки объекта',
        ],
        'brand' => [
            'h2' => 'Логотип',
            'logo_label' => 'Загрузить логотип (PNG/JPG/WEBP, до 2 MB)',
            'logo_saved' => 'Логотип сохранён',
            'logo_upload_failed' => 'Ошибка загрузки логотипа',

            'background_updated' => 'Фон и тема обновлены',
            'background_upload_failed' => 'Ошибка загрузки фона',
        ],
    ],

    'languages' => [
        'h2' => 'Языки',
        'add_h3' => 'Добавить',
        'default_h3' => 'По умолчанию',
        'default' => 'По умолчанию',
        'locale_label' => 'Код языка (например en, ru)',
        'file_label' => 'JSON-файл меню',
        'set_default_checkbox' => 'Сделать языком по умолчанию',
        'upload_import' => 'Загрузка и импорт',
        'default_select_label' => 'Язык по умолчанию',
        'save_default' => 'Сохранить',
        'note_de_default' => 'Примечание: если нигде не выбран язык по умолчанию, используется DE.',
    ],

    'permissions' => [
        'h2' => 'Права пользователя',
        'saved' => 'Права доступа сохранены',
        'user' => 'Пользователь: ',
        'languages' => 'Языки',
        'sections' => 'Категории / Разделы',
        'items' => 'Блюда / Позиции',
        'banners' => 'Баннеры',
        'socials' => 'Соцсети',
        'theme' => 'Тема',
        'import' => 'Импорт',
        'save' => 'Сохранить',
        'branding' => 'Брендинг',
        'enabled' => 'Включено',
        'groups' => [
                'menu' => 'Меню',
                'branding' => 'Брендинг',
                'import' => 'Импорт',
                'restaurant' => 'Ресторан',
                'admin' => 'Админка',
                'content' => 'Контент',
                'other' => 'Другое',
                'socials' => 'Соц. сети',
            ],
    ],

    'uploads' => [
        'block_title' => 'Хранилище загрузок',
        'path_hint' => 'Для этого объекта файлы лежат в:',
        'folders_hint' => 'Папки создаются автоматически:',
    ],

    'sections' => [
        'block_title' => 'Категории и подкатегории',
        'block_hint' => 'Управление категориями и подкатегориями.',
        'open_manager' => 'Открыть менеджер категорий',
         'categories' => [
                'h2' => 'Категории',
                'hint' => 'Создайте новую категорию. Название до 50 символов.',
                'title' => 'Название',
                'font' => 'Шрифт',
                'color' => 'Цвет',
                'create_btn' => 'Создать категорию',
                'created' => 'Категория создана.',
             'change' => 'Изменить',
         ],
         'deleted' => 'Раздел удалён.',
                 'toggled' => 'Статус раздела изменён.',
                 'subcategories' => [
                     'created' => 'Подкатегория создана.',
                 ],
    ],

    'profile' => [
        'title' => 'Профиль',
        'subtitle' => 'Профиль',
        'h2' => 'Профиль',
        'saved' => 'Профиль сохранён.',
        'change_email_btn' => 'Изменить e-mail',
        'change_password_btn' => 'Изменить пароль',

        'restaurant' => [
            'h2' => 'Данные заведения',
            'restaurant_name' => 'Название заведения',
            'contact_name' => 'Контактное имя',
            'email' => 'E-mail заведения',
            'address_h3' => 'Адрес',
            'saved' => 'Данные заведения сохранены.',
            'no_restaurant_context' => 'В текущем админ-контексте объект не выбран.',
        ],

        'permissions' => [
            'h2' => 'Ваши права',
            'super_admin' => 'Супер-админ: полный доступ',
            'no_permissions' => 'Права не назначены.',
        ],

        'change_email' => [
            'h2' => 'Смена Логина',
            'current_email' => 'Текущий Логин',
            'current_password' => 'Текущий пароль',
            'new_email' => 'Новый Логин',
        ],

        'change_password' => [
            'h2' => 'Смена пароля',
            'current_email' => 'Текущий Логин',
            'current_password' => 'Текущий пароль',
            'new_password' => 'Новый пароль',
            'confirm_new_password' => 'Повторите новый пароль',
        ],
    ],

    'items' => [
        'created' => 'Элемент создан.',
        'updated' => 'Элемент сохранен.',
        'deleted' => 'Элемент удален.',
    ],

    'menu_builder' => [
        'h2' => 'Конструктор меню',
            'hint' => 'Создавайте категории, подкатегории и элементов. Перетаскивайте элемент, чтобы менять порядок.',
            'add_category' => 'Категорию',
            'add_subcategory' => 'Подкатегорию',
            'add_item' => 'Элемент',
            'flags' => 'Опции',
            'spicy' => 'Острота (0–5)',
            'image' => 'Изображение',
            'styles_hint' => 'Выберите шрифт/цвет/размер.',
            'empty' => 'Категорий пока нет.',

            'show_image_modal' => 'Img и modal',
            'flag_new' => 'Новинка',
            'flag_dish_of_day' => 'Элемент дня',
            'image_hint' => 'JPG/PNG/WEBP (SVG запрещён)',

            'field_title' => 'Заголовок',
            'field_desc' => 'Описание',
            'field_details' => 'Детали',

            'style_font' => 'Шрифт: :field',
            'style_color' => 'Цвет: :field',
            'style_size' => 'Размер: :field',

            'title_locale' => 'Заголовок (:locale)',
            'description_locale' => 'Описание (:locale)',
            'details_locale' => 'Детали (:locale)',

            'pill_new' => 'Новинка',
            'pill_day' => 'Элемент дня',
            'confirm_delete_item' => 'Удалить Элемент?',
            'show_deleted' => 'Показывать удалённые',
            'show_deleted_hint' => 'Если выключить — будут показаны только активные (не удалённые) элементы.',
            'price' => 'Цена',
            'price_hint' => 'Введите цену. Разрешены только цифры и одна точка (например: 9.50).',
            'details' => 'Делали',
            'description' => 'Описание',
    ],

    'sidebar' => [

        // ===== PROFILE =====
        'profile_group' => 'Профиль',
        'profile' => 'Профиль',
        'languages' => 'Языки',

        // ===== RESTAURANT =====
        'restaurant_group' => 'Ресторан',
        'settings' => 'Настройки',
        'hours' => 'График работы',

        // ===== MENU =====
        'menu_group' => 'Меню',
        'my_menu' => 'Моё меню',
        'menu_builder' => 'Конструктор меню',

        'banners_group' => 'Маркетинг',
        'banners' => 'Баннеры',
        'carousel' => 'Карусель',

        // ===== CONTENT =====
        'content_group' => 'Контент',
        'branding' => 'Брендинг',
        'socials' => 'Социальные сети',

        // ===== IMPORT =====
        'import_group' => 'Импорт',
        'import_menu' => 'Импорт меню',
        'import_images' => 'Импорт изображений',

        // ===== SECURITY =====
        'security_group' => 'Безопасность АДМИН',
        'security' => 'Безопасность',
        'password' => 'Пароль',
        'permissions' => 'Права доступа',

        // ===== SYSTEM =====
        'restaurants_select' => 'Выбор объекта',
        'menu' => 'Меню',

        'qr' => "QR Code",

        'system_group' => 'Системные настройки',
    ],

    'branding' => [
      'title' => 'Брендинг',
      'mode_title' => 'Режим темы',
      'mode_auto' => 'Авто',
      'mode_light' => 'Светлая',
      'mode_dark' => 'Тёмная',
      'bg_light' => 'Фон (светлая тема)',
      'bg_dark' => 'Фон (тёмная тема)',
      'save_bg' => 'Сохранить фон',
    ],

    'validation' => [
        'first_letter_uppercase' => 'Первая буква должна быть заглавной.',
        'parent_must_be_category' => 'Родитель должен быть категорией.',
    ],

    'confirm' => [
      'title' => 'Подтверждение',
      'delete_generic' => 'Вы уверены, что хотите удалить?',
      'delete_category' => 'Вы уверены, что хотите удалить категорию?',
      'delete_subcategory' => 'Вы уверены, что хотите удалить подкатегорию?',
      'delete_item' => 'Вы уверены, что хотите удалить элемент?',
    ],

    'security' => [
        'validation' => [
            'current_email' => [
                'required' => 'Укажите текущий логин.',
                'email' => 'Неверный формат логина.',
            ],
            'new_email' => [
                'required' => 'Укажите новый логин.',
                'email' => 'Неверный формат нового догин.',
                'unique' => 'Этот логин уже используется.',
            ],
            'current_password' => [
                'required' => 'Введите текущий пароль.',
            ],
            'new_password' => [
                'required' => 'Введите новый пароль.',
                'min' => 'Пароль должен быть минимум 8 символов.',
                'regex' => 'Пароль должен содержать: 1 заглавную, 1 маленькую, 1 цифру и 1 спецсимвол.',
            ],
            'new_password_confirm' => [
                'required' => 'Повторите новый пароль.',
                'same' => 'Подтверждение пароля не совпадает.',
            ],
        ],

        'password_hint' => 'Оставьте пароль пустым, если не хотите менять',
        'h2' => 'Логин, Пароль',

        'errors' => [
            'current_email_wrong' => 'Текущий логин указан неверно.',
            'current_password_wrong' => 'Текущий пароль указан неверно.',
        ],

        'status' => [
            'email_changed' => 'Логин изменён.',
            'password_changed' => 'Пароль изменён.',
        ],

        'user_object' => 'Пользователь / Обьект'
    ],

    'socials' => [
        'limit_info' => 'Активными будут только первые :limit ссылок (в зависимости от тарифа).',
      'title' => 'Соц. Ссылки',
      'hint' => 'Максимум 5 ссылок. Ссылки доступны по тарифному плану.',
      'add' => 'Добавить ссылку',
      'edit' => 'Изменить',
      'delete' => 'Удалить',
      'save' => 'Сохранить',
      'cancel' => 'Отмена',
      'url_example' => 'Пример: https://instagram.com/yourpage',

      'fields' => [
        'title' => 'Название',
        'url' => 'Ссылка',
        'icon' => 'SVG иконка',
      ],

      'placeholders' => [
        'title' => 'Например: Instagram',
        'url' => 'https://instagram.com/yourpage',
      ],

      'active' => 'Активно',
      'inactive' => 'Неактивно',
      'deleted' => 'Удалено',

      'confirm_delete' => 'Удалить ссылку?',
      'saved' => 'Ссылка сохранена.',
      'updated_ok' => 'Ссылка обновлена.',
      'deleted_ok' => 'Ссылка удалена.',
    ],

    'about' => [
            'title' => 'Обо мне',
            'subtitle' => 'Full Stack .NET, WEB разработчик, Ротвайль (Баден-Вюртемберг).',

            'p1' => 'Уже несколько лет я разрабатываю веб-системы, интернет-магазины и промышленные приложения — от онлайн-витрин и личных кабинетов до тестовых стендов для силовой электроники и умных контроллеров.',
            'p2' => 'Я структурированно разбираю сложные требования и превращаю их в стабильные и поддерживаемые решения. Люблю работать с заказчиками и предметными экспертами и беру ответственность за качество, безопасность и архитектуру.',
            'p3' => 'Если вам нужна новая веб-система, интернет-магазин, веб сайтов, визиток и тд — напишите мне.',

            'send' => 'Отправить сообщение',
            'location' => 'Местоположение',
            'email' => 'E-mail',
            'phone' => 'Телефон / мессенджеры',

            'location_value' => '78628 Rottweil, Baden-Württemberg',
            'email_value' => 'aleksstanov84@gmail.com',
            'phone_value' => '+49 173 5141827',
    ],

  /*  'import' => [
        'title' => 'Импорт меню',
        'json_label' => 'Загрузить JSON меню',
        'zip_label' => 'Загрузить ZIP ассетов (картинки/фоны/иконки)',
        'import_btn' => 'Импортировать',
        'rules_btn' => 'Правила',
        'rules_title' => 'Правила импорта',
        'rules_soon' => 'Спецификация формата будет добавлена следующим коммитом.',

        'success' => [
            'json_uploaded' => 'JSON загружен. Импорт-пайплайн будет добавлен следующим шагом.',
            'zip_imported' => 'ZIP ассеты импортированы.',
        ],

        'errors' => [
            'invalid_json' => 'Невалидный JSON.',
            'zip_open_failed' => 'Не удалось открыть ZIP архив.',
            'zip_too_many_files' => 'Слишком много файлов в ZIP архиве.',
            'zip_unsafe_path' => 'Найден небезопасный путь файла в ZIP (zip-slip).',
            'zip_type_not_allowed' => 'Запрещённый тип файла в ZIP',
            'zip_extract_failed' => 'Ошибка извлечения файла из ZIP',
        ],
    ],*/

    // ===== IMPORT =====
        'import' => [

            'json' => [
                'title' => 'Импорт меню (JSON)',
                'upload' => 'Загрузить JSON',
              ],

              'zip' => [
                'title' => 'Импорт ассетов (ZIP)',
                'upload' => 'Загрузить ZIP',
              ],

              'rules' => 'Правила',

              'log' => [
                  'btn' => 'Лог',
                  'title' => 'Лог импорта',
                  'download' => 'Скачать лог',
                  'ok' => 'Ошибок нет.',
                  'has_errors' => 'Обнаружены ошибки',
              ],

            'success' => [
                'import_done' => 'Импорт выполнен успешно.',
                'dry_run_done' => 'Проверка без применения: создать: :create, обновить: :update, удалить: :delete.',
            ],

            'errors' => [

                // base schema
                'mode_invalid' => 'Поле "mode" должно иметь значение "patch".',
                'operations_required' => 'Поле "operations" обязательно и должно быть непустым массивом.',
                'operation_object' => 'Операция должна быть объектом.',

                'required' => 'Обязательное поле ":field" отсутствует или пустое.',

                'type_not_supported' => 'Тип операции ":type" не поддерживается.',
                'op_invalid' => 'Недопустимое значение операции (op). Допустимо: update, delete, upsert.',

                // item lookup
                'item_key_ambiguous' => 'Ключ блюда ":key" найден несколько раз. Ключи должны быть уникальны.',
                'item_not_found' => 'Блюдо с ключом ":key" не найдено или уже удалено.',

                // set / parent
                'set_object_required' => 'Поле "set" обязательно и должно быть объектом.',
                'set_empty' => 'Поле "set" не может быть пустым.',

                'parent_required' => 'Для создания нового блюда необходимо указать parent (category_key / subcategory_key).',
                'parent_not_found' => 'Родительская категория не найдена (category: ":category_key", subcategory: ":subcategory_key").',
                'subcategory_key_invalid' => 'Поле subcategory_key должно быть строкой.',

                // values
                'price_invalid' => 'Цена указана некорректно. Пример допустимого значения: 9.50',
                'currency_invalid' => 'Недопустимая валюта. Сейчас поддерживается только EUR.',
                'boolean_required' => 'Значение должно быть true или false.',
                'spicy_invalid' => 'Острота должна быть целым числом от 0 до 3.',

                // paths / images
                'image_path_invalid' => 'Путь к изображению указан некорректно.',
                'path_unsafe' => 'Небезопасный путь к файлу (запрещены "..", "\\" и абсолютные пути).',

                // meta
                'meta_object_required' => 'Поле meta должно быть объектом.',

                // translations
                'translations_object_required' => 'Поле translations должно быть объектом.',
                'translation_object_required' => 'Перевод должен быть объектом.',
                'translation_empty' => 'Перевод не содержит ни одного допустимого поля.',
                'locale_not_supported' => 'Язык ":locale" не поддерживается.',

                'invalid_json' => 'Невалидный JSON.',
                'unknown' => 'Неизвестная ошибка.',
                'import_failed_open_log' => 'Импорт не выполнен. Откройте “Лог” для деталей.',
            ],

            'rules_modal' => [
                'title' => 'Правила импорта',
                'intro' => 'Загрузите JSON (patch) для изменений и ZIP для ассетов. Все изменения применяются только при отсутствии ошибок.',
                'patch_title' => 'Формат JSON (patch)',
                'patch_desc' => 'Файл должен содержать mode="patch" и operations[]. Поддерживаются item/category/subcategory/social + reorder.',
                'patch_example' => "{\n  \"mode\": \"patch\",\n  \"dry_run\": false,\n  \"operations\": [\n    {\n      \"type\": \"item\",\n      \"op\": \"update\",\n      \"key\": \"cheeseburger\",\n      \"set\": {\n        \"price\": \"9.50\",\n        \"translations\": {\"ru\": {\"title\": \"Чизбургер\"}}\n      }\n    }\n  ]\n}\n",
                'assets_title' => 'ZIP ассеты',
                'assets_desc' => 'Архив должен содержать файлы по относительным путям. Разрешены: jpg/jpeg/png/webp/svg. SVG очищается.',
                'assets_example' => "branding/logo.png\nbackgrounds/light.jpg\nbackgrounds/dark.jpg\nitems/cheeseburger.jpg\nsocial/icons/instagram.svg\n",
                'notes_title' => 'Важно',
                'note_atomic' => 'Если файл содержит ошибки — ничего не импортируется.',
                'note_permissions' => 'Если у пользователя нет права на поле (например spicy) — импорт будет отклонён с ошибкой.',
                'note_paths' => 'Пути в ZIP и JSON должны быть безопасными: без \"..\", без абсолютных путей.',
                'copy' => 'Скопировать',
              ],
        ],

        'zip_too_large'       => 'ZIP архив слишком большой.',
        'zip_open_failed'     => 'Не удалось открыть ZIP архив.',
        'zip_too_many_files'  => 'В архиве слишком много файлов.',
        'zip_unsafe_path'     => 'Обнаружен небезопасный путь в архиве.',
        'zip_blocked_ext'     => 'В архиве найден запрещённый тип файла.',
        'zip_ext_not_allowed' => 'Тип файла в архиве не разрешён.',

        'keys_required'   => 'Поле keys обязательно и должно быть массивом.',
        'keys_duplicate'  => 'Список keys содержит дубликаты.',
        'keys_mismatch'   => 'Список keys не совпадает с текущим набором элементов.',

];
