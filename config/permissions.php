<?php

return [
    // categories
    'categories.create' => ['group' => 'menu', 'label' => 'Создать категорию'],
    'categories.edit'   => ['group' => 'menu', 'label' => 'Редактировать категорию'],
    'categories.delete' => ['group' => 'menu', 'label' => 'Удалить категорию'],
    'categories.toggle' => ['group' => 'menu', 'label' => 'Включать/выключать категорию'],

    // subcategories
    'subcategories.create' => ['group' => 'menu', 'label' => 'Создать подкатегорию'],
    'subcategories.edit'   => ['group' => 'menu', 'label' => 'Редактировать подкатегорию'],
    'subcategories.delete' => ['group' => 'menu', 'label' => 'Удалить подкатегорию'],
    'subcategories.toggle' => ['group' => 'menu', 'label' => 'Включать/выключать подкатегорию'],

    // items (минимум, дальше расширим)
    'items.create' => ['group' => 'menu', 'label' => 'Создать блюдо'],
    'items.edit'   => ['group' => 'menu', 'label' => 'Редактировать блюдо'],
    'items.delete' => ['group' => 'menu', 'label' => 'Удалить блюдо'],

    // item advanced fields / flags (на будущее, уже фиксируем ключи)
    'items.image.upload'      => ['group' => 'menu', 'label' => 'Загрузка изображения блюда'],
    'items.toggle.active'     => ['group' => 'menu', 'label' => 'Вкл/выкл блюдо'],
    'items.toggle.show_image' => ['group' => 'menu', 'label' => 'Показывать изображение блюда'],
    'items.flag.new'          => ['group' => 'menu', 'label' => 'Флаг NEW'],
    'items.flag.spicy'        => ['group' => 'menu', 'label' => 'Острота'],
    'items.flag.dish_of_day'  => ['group' => 'menu', 'label' => 'Блюдо дня'],

    // branding
    'branding.logo.upload'        => ['group' => 'branding', 'label' => 'Загрузка/смена логотипа'],
    'branding.backgrounds.upload' => ['group' => 'branding', 'label' => 'Загрузка фонов (светлый/тёмный)'],
    'branding.theme_mode.edit'    => ['group' => 'branding', 'label' => 'Режим темы (auto/light/dark)'],

    // profile
    'languages_manage' => ['group' => 'admin', 'label' => 'Управление языками'],
    'sections_manage'  => ['group' => 'menu',  'label' => 'Управление разделами/категориями'],
    'items_manage'     => ['group' => 'menu',  'label' => 'Управление блюдами'],
    'banners_manage'   => ['group' => 'content', 'label' => 'Управление баннерами'],
    'socials_manage'   => ['group' => 'content', 'label' => 'Управление соц. ссылками'],
    'theme_manage'     => ['group' => 'branding', 'label' => 'Управление темой'],
    'import_manage'    => ['group' => 'import', 'label' => 'Импорт'],

    'restaurant.profile.edit' => ['group' => 'restaurant', 'label' => 'Редактировать профиль ресторана'],
    'restaurants.edit' => ['group' => 'restaurant', 'label' => 'Редактировать ресторан (настройки)'],

    // socials (footer links)
    // socials (footer links)
    'socials.edit'              => ['group' => 'socials', 'label' => 'Соц. ссылки: изменить'],
    'socials.delete'            => ['group' => 'socials', 'label' => 'Соц. ссылки: удалить'],
    'socials.icon.upload'       => ['group' => 'socials', 'label' => 'Соц. ссылки: загрузка SVG иконки'],
    'socials.toggle.active'     => ['group' => 'socials', 'label' => 'Соц. ссылки: активировать/деактивировать'],

    // лимиты добавления (3,4,5)
    'socials.add.3'             => ['group' => 'socials', 'label' => 'Соц. ссылки: можно 3 ссылки'],
    'socials.add.4'             => ['group' => 'socials', 'label' => 'Соц. ссылки: можно 4 ссылки'],
    'socials.add.5'             => ['group' => 'socials', 'label' => 'Соц. ссылки: можно 5 ссылок'],


];
