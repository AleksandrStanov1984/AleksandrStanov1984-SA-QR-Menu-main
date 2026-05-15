<?php

return [

    'title' => 'Биллинг',

    'billing_group' => 'Биллинг',
    'billing' => 'Биллинг',

    'status' => [
        'active' => 'Активен',
        'trial' => 'Тестовый период',
        'paid' => 'Оплачен',
        'expired' => 'Истёк',
        'inactive' => 'Неактивен',
        'deactivated' => 'Деактивирован',
    ],

    'warnings' => [
        'ok' => 'Активен',
        'warning' => 'Осталось менее 10 дней',
        'danger' => 'Осталось менее 5 дней',
        'expired' => 'Период оплаты истёк',
    ],

    'fields' => [
        'restaurant' => 'Объект',
        'status' => 'Статус',
        'active' => 'Активен',

        'trial_ends_at' => 'Тест до',
        'paid_until' => 'Оплачено до',

        'days_left' => 'Осталось дней',

        'plan' => 'Тариф',
        'monthly_price' => 'Цена в месяц',

        'keep_data' => 'Не удалять объект',
    ],

    'filters' => [
        'date_from' => 'Дата от',
        'date_to' => 'Дата до',

        'sort' => 'Сортировка',

        'newest' => 'Сначала новые',
        'oldest' => 'Сначала старые',

        'apply' => 'Применить',
        'reset' => 'Сбросить',
    ],

    'actions' => [
        'confirm_payment' => 'Подтвердить оплату',

        'start_trial' => 'Запустить тестовый период',

        'extend_trial' => 'Продлить тестовый период',

        'deactivate' => 'Деактивировать',

        'resume' => 'Включить объект',
    ],

    'table' => [
        'date' => 'Дата',
        'type' => 'Тип',
        'status' => 'Статус',

        'amount' => 'Сумма',
        'period' => 'Период',

        'user' => 'Пользователь',

        'notes' => 'Заметки',

        'empty' => 'История биллинга пока пуста',
    ],

    'messages' => [
        'payment_confirmed' => 'Оплата подтверждена',

        'trial_started' => 'Тестовый период запущен',

        'trial_extended' => 'Тестовый период продлён',

        'restaurant_deactivated' => 'Объект деактивирован',

        'restaurant_resumed' => 'Объект снова активирован',

        'keep_data_updated' => 'Настройки хранения данных обновлены',
    ],

    'errors' => [
        'not_allowed' => 'Недостаточно прав',

        'keep_data_delete_blocked' => 'Объект защищён от удаления пользователем',
    ],

    'delete' => [
        'confirm_title' => 'Удалить объект?',

        'success' => 'Объект успешно удалён.',

        'not_allowed' => 'Объект пока нельзя удалить.',

        'button' => 'Удалить',

        'confirm_text' => 'Объект и все связанные данные будут удалены навсегда.',

    ],

];
