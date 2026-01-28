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
        'user_name' => 'Имя пользователя',
        'user_email' => 'E-mail пользователя',
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
        'create_restaurant' => 'Создать ресторан',
        'create' => 'Создать',
        'delete' => 'Удалить',
        'close'  => 'Закрыть',
    ],

    'auth' => [
        // legacy keys
        'login_title' => 'Вход',
        'subtitle' => 'Войдите, чтобы управлять ресторанами',
        'signin' => 'Войти',

        // ✅ alias keys used in some blades: admin.auth.login.*
        'login' => [
            'h2' => 'Вход',
            'subtitle' => 'Войдите, чтобы управлять ресторанами',
            'submit' => 'Войти',
        ],
    ],

    'dashboard' => [
        'super_admin' => 'Супер-админ',
        'restaurant_admin' => 'Админ ресторана',
        'current_context' => 'Текущий контекст',
        'no_selected' => 'Ресторан не выбран.',
        'open_editor' => 'Открыть редактор ресторана',
        'pick_restaurant' => 'Выбрать ресторан',
        'select_placeholder' => 'Выбрать…',
        'all_restaurants' => 'Все рестораны',
        'next_steps' => 'Следующие шаги (MVP)',
        'dashboard' => 'Панель',
        'home' => 'Панель',
    ],

    'breadcrumbs' => [
        'dashboard' => 'Панель',
        'home' => 'Панель',
    ],


    'restaurants' => [
        'index' => [
            'h1' => 'Рестораны',
            'subtitle' => 'Управление всеми ресторанами (супер-админ)',
            'add' => '+ Добавить ресторан',
        ],
        'create' => [
            // ✅ alias for breadcrumbs: admin.restaurants.create.title
            'title' => 'Создать ресторан',

            'h2' => 'Создать ресторан',
            'subtitle' => 'Создание ресторана',
            'sections' => [
                'restaurant' => 'Ресторан',
                'user' => 'Пользователь ресторана',
            ],
        ],
        'edit' => [
            'h2' => 'Редактировать ресторан',
            'subtitle' => 'Настройки ресторана',
        ],
        'brand' => [
                            'h2' => 'Логотип',
                            'logo_label' => 'Загрузить логотип (PNG/JPG/WEBP, до 2 MB)',
                            'logo_saved' => 'Логотип сохранён.',
                        ],
    ],

    'languages' => [
        'h2' => 'Языки',
        'add_h3' => 'Добавить язык',
        'default_h3' => 'Язык по умолчанию',

        // ✅ alias used in some blades: admin.languages.default
        'default' => 'Язык по умолчанию',

        'locale_label' => 'Код языка (например en, ru)',
        'file_label' => 'JSON-файл меню',
        'set_default_checkbox' => 'Сделать языком по умолчанию',
        'upload_import' => 'Загрузить и импортировать',
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
        'save' => 'Сохранить права',
        'branding' => 'Брендинг (фон, оформление)',

    ],

    'uploads' => [
        'block_title' => 'Хранилище загрузок',
        'path_hint' => 'Для этого ресторана файлы лежат в:',
        'folders_hint' => 'Папки создаются автоматически:',
    ],

    'sections' => [
        'block_title' => 'Категории и подкатегории',
        'block_hint' => 'Управление категориями (sections) и подкатегориями.',
        'open_manager' => 'Открыть менеджер категорий',
         'categories' => [
                'h2' => 'Категории',
                'hint' => 'Создайте новую категорию (верхний уровень). Название до 50 символов.',
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
            'no_restaurant_context' => 'В текущем админ-контексте ресторан не выбран.',

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
        'created' => 'Блюдо создано.',
        'updated' => 'Блюдо сохранено.',
        'deleted' => 'Блюдо удалено.',
    ],

    'menu_builder' => [
        'h2' => 'Конструктор меню',
            'hint' => 'Создавайте категории, подкатегории и блюда. Перетаскивайте блюда, чтобы менять порядок.',
            'add_category' => 'Добавить категорию',
            'add_subcategory' => 'Добавить подкатегорию',
            'add_item' => 'Добавить блюдо',
            'flags' => 'Опции',
            'spicy' => 'Острота (0–5)',
            'image' => 'Изображение',
            'styles_hint' => 'Выберите шрифт/цвет/размер — поля обновляются мгновенно.',
            'empty' => 'Категорий пока нет.',

            'show_image_modal' => 'Показывать изображение и модальное окно',
            'flag_new' => 'Новинка',
            'flag_dish_of_day' => 'Блюдо дня',
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
            'pill_day' => 'Блюдо дня',
            'confirm_delete_item' => 'Удалить блюдо?',
    ],

    'sidebar' => [
        'about' => 'Обо мне',
        'profile' => 'Мой профиль',
        'my_menu' => 'Моё меню',
        'restaurants_select' => 'Выбор ресторана',
        'logo' => 'LOGO',
        'menu_profile' => 'Профиль',
            'security' => 'Безопасность',
            'password' => 'Пароль',
    ],

    'security' => [
        'password_hint' => 'Оставьте пароль пустым, если не хотите менять',
    ],

    'branding' => 'Брендирование (фоны)',

    'common' => [
            'active'   => 'Активно',
            'inactive' => 'Отключено',
            'disabled' => 'Отключено',
            'dash'     => '—',
    ],







];
