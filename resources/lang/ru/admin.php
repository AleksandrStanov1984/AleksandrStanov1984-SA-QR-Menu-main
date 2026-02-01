<?php

return [
    'brand' => 'SA QR Menu — Admin',

    'ui' => [
        'admin' => 'Админка',
        'language' => 'Язык',
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
        'email' => 'E-mail',
        'password' => 'Пароль',
    ],

    'templates' => [
        'classic' => 'Classic',
        'fastfood' => 'Fastfood',
        'bar' => 'Bar',
        'services' => 'Services',
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
        // legacy keys
        'login_title' => 'Вход',
        'subtitle' => 'Войти, чтобы управлять объектами',
        'signin' => 'Войти',

        // ✅ alias keys used in some blades: admin.auth.login.*
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
            // ✅ alias for breadcrumbs: admin.restaurants.create.title
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
            'logo_saved' => 'Логотип сохранён.',
        ],
    ],

    'languages' => [
        'h2' => 'Языки',
        'add_h3' => 'Добавить',
        'default_h3' => 'По умолчанию',

        // ✅ alias used in some blades: admin.languages.default
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
        'user' => 'Пользователь: :name (:email)',
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
            'h2' => 'Смена e-mail',
            'current_email' => 'Текущий e-mail',
            'current_password' => 'Текущий пароль',
            'new_email' => 'Новый e-mail',
        ],

        'change_password' => [
            'h2' => 'Смена пароля',
            'current_email' => 'Текущий e-mail',
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

            'show_image_modal' => 'Aktiv img и modal',
            'flag_new' => 'Новинка',
            'flag_dish_of_day' => 'Элемент дня',
            'image_hint' => 'Только JPG / PNG / WEBP (SVG запрещён)',

            'field_title' => 'Заголовок',
            'field_desc' => 'Описание',
            'field_details' => 'Детали',

            'style_font' => 'Шрифт: :field',
            'style_color' => 'Цвет: :field',
            'style_size' => 'Размер: :field',

            'title_locale' => 'Заголовок (:locale)',
            'description_locale' => 'Описание (:locale)',
            'details_locale' => 'Детали (:locale)',

            // (опционально, если хочешь перевести pill'ы и confirm)
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
        'about' => 'Обо мне',
        'profile' => 'Мой профиль',
        'my_menu' => 'Моё меню',
        'restaurants_select' => 'Выбор объект',
        'logo' => 'LOGO',
        'menu_profile' => 'Профиль',
            'security' => 'Безопасность',
            'password' => 'Пароль',
    ],

    'security' => [
        'password_hint' => 'Оставьте пароль пустым, если не хотите менять',
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
                'required' => 'Укажите текущий e-mail.',
                'email' => 'Неверный формат e-mail.',
            ],
            'new_email' => [
                'required' => 'Укажите новый e-mail.',
                'email' => 'Неверный формат нового e-mail.',
                'unique' => 'Этот e-mail уже используется.',
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

        'errors' => [
            'current_email_wrong' => 'Текущий e-mail указан неверно.',
            'current_password_wrong' => 'Текущий пароль указан неверно.',
        ],

        'status' => [
            'email_changed' => 'E-mail изменён.',
            'password_changed' => 'Пароль изменён.',
        ],
    ],

    'socials' => [
      'title' => 'Ссылки в футере',
      'hint' => 'Максимум 5 ссылок. Первые две доступны всем, дополнительные — по правам (3/4/5).',
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










];
