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
    'items.image.upload'        => ['group' => 'menu', 'label' => 'Загрузка изображения блюда'],
    'items.toggle.active'       => ['group' => 'menu', 'label' => 'Вкл/выкл блюдо'],
    'items.toggle.show_image'   => ['group' => 'menu', 'label' => 'Показывать изображение блюда'],
    'items.flag.new'            => ['group' => 'menu', 'label' => 'Флаг NEW'],
    'items.flag.spicy'          => ['group' => 'menu', 'label' => 'Острота'],
    'items.flag.dish_of_day'    => ['group' => 'menu', 'label' => 'Блюдо дня'],

    // branding
    'branding.logo.upload'         => ['group' => 'branding', 'label' => 'Загрузка/смена логотипа'],
    'branding.backgrounds.upload'  => ['group' => 'branding', 'label' => 'Загрузка фонов (светлый/тёмный)'],
    'branding.theme_mode.edit'     => ['group' => 'branding', 'label' => 'Режим темы (auto/light/dark)'],

];
