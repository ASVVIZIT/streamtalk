<?php

return [
    // Основные настройки
    'name' => env('STREAMTALK_NAME', 'StreamTalk Messenger'),
    'storage_disk_name' => env('STREAMTALK_STORAGE_DISK', 'public'),

    // Наименования таблиц с префиксом st_
    'tables' => [
        'favorites' => 'st_favorites', // Таблица избранного
        'messages' => 'st_messages',   // Таблица сообщений
    ],

    // Наименования столбцов с префиксом st_
    'columns' => [
        'active_status' => 'st_active_status', // Статус активности
        'avatar' => 'st_avatar',               // Аватар
        'dark_mode' => 'st_dark_mode',         // Тёмная тема
        'messenger_color' => 'st_messenger_color', // Цвет интерфейса
    ],

    // Настройки маршрутов
    'routes' => [
        'custom' => env('STREAMTALK_CUSTOM_ROUTES', false),
        'prefix' => env('STREAMTALK_ROUTES_PREFIX', 'streamtalk'),
        'as' => env('STREAMTALK_ROUTES_AS', 'streamtalk.'),
        'middleware' => env('STREAMTALK_ROUTES_MIDDLEWARE', ['web','auth']),
        'namespace' => env('STREAMTALK_ROUTES_NAMESPACE', 'App\Http\Controllers\StreamTalk'),
    ],

    // Настройки API маршрутов
    'api_routes' => [
        'prefix' => env('STREAMTALK_API_ROUTES_PREFIX', 'streamtalk/api'),
        'middleware' => env('STREAMTALK_API_ROUTES_MIDDLEWARE', ['api']),
        'namespace' => env('STREAMTALK_API_ROUTES_NAMESPACE', 'App\Http\Controllers\StreamTalk\Api'),
    ],

    // Настройки Pusher
    'pusher' => [
        'debug' => env('APP_DEBUG', false),
        'key' => env('PUSHER_APP_KEY'),
        'secret' => env('PUSHER_APP_SECRET'),
        'app_id' => env('PUSHER_APP_ID'),
        'options' => [
            'cluster' => env('PUSHER_APP_CLUSTER', 'mt1'),
            'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'mt1').'.pusher.com',
            'port' => env('PUSHER_PORT', 443),
            'scheme' => env('PUSHER_SCHEME', 'https'),
            'encrypted' => true,
            'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
        ],
    ],

    // Настройки аватара пользователя
    'user_avatar' => [
        'folder' => 'users-avatar', // Папка для аватарок
        'default' => 'avatar.png',  // Аватар по умолчанию
    ],

    // Настройки Gravatar
    'gravatar' => [
        'enabled' => true,         // Включить Gravatar
        'image_size' => 200,       // Размер изображения
        'imageset' => 'identicon'  // Стиль изображения
    ],

    // Настройки вложений
    'attachments' => [
        'folder' => 'attachments', // Папка для вложений
        'download_route_name' => 'download', // Имя маршрута для скачивания
        'allowed_images' => ['png','jpg','jpeg','gif'], // Разрешенные изображения
        'allowed_files' => ['zip','rar','txt'],         // Разрешенные файлы
        'max_upload_size' => env('STREAMTALK_MAX_FILE_SIZE', 150), // Макс. размер (MB)
    ],

    // Цветовая палитра
    'colors' => [
        '#2180f3', '#2196F3', '#00BCD4', '#3F51B5',
        '#673AB7', '#4CAF50', '#FFC107', '#FF9800',
        '#ff2522', '#9C27B0',
    ],

    // Звуковые уведомления
    'sounds' => [
        'enabled' => true, // Включить звуки
        'public_path' => 'sounds/StreamTalk', // Путь к звукам
        'new_message' => 'new-message-sound.mp3', // Звук нового сообщения
    ]
];
