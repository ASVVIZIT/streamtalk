<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Основные настройки
    | Main Configuration
    |--------------------------------------------------------------------------
    */
    'name' => env('STREAMTALK_NAME', 'StreamTalk Messenger'),
    'version' => env('STREAMTALK_VERSION', include __DIR__.'/../../version.php'),
    'debug' => env('STREAMTALK_DEBUG', env('APP_DEBUG', false)),
    'timezone' => env('STREAMTALK_TIMEZONE', config('app.timezone')),

    /*
    |--------------------------------------------------------------------------
    | Хранилище
    | Storage
    |--------------------------------------------------------------------------
    */
    'storage_disk_name' => env('STREAMTALK_STORAGE_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Настройки таблиц
    | Table Settings
    |--------------------------------------------------------------------------
    */
    'tables' => [
        'favorites' => 'st_favorites',
        'messages' => 'st_messages',
        'participants' => 'st_participants',
        'conversations' => 'st_conversations',
        'message_reads' => 'st_message_reads',
    ],

    /*
    |--------------------------------------------------------------------------
    | Настройки пользователя
    | User Settings
    |--------------------------------------------------------------------------
    */
    'user_model' => \App\Models\User::class,
    'columns' => [
        'active_status' => 'st_active_status',
        'avatar' => 'st_avatar',
        'dark_mode' => 'st_dark_mode',
        'messenger_color' => 'st_messenger_color',
    ],

    /*
    |--------------------------------------------------------------------------
    | Маршрутизация
    | Routing
    |--------------------------------------------------------------------------
    */
    'routes' => [
        'enabled' => env('STREAMTALK_ROUTES_ENABLED', true),
        'custom' => env('STREAMTALK_CUSTOM_ROUTES', false),
        'prefix' => env('STREAMTALK_ROUTES_PREFIX', 'streamtalk'),
        'as' => env('STREAMTALK_ROUTES_AS', 'streamtalk.'),
        'middleware' => env('STREAMTALK_ROUTES_MIDDLEWARE', ['web','auth']),
        'namespace' => env('STREAMTALK_ROUTES_NAMESPACE', 'App\Http\Controllers\StreamTalk'),
    ],

    'api_routes' => [
        'enabled' => env('STREAMTALK_API_ROUTES_ENABLED', true),
        'prefix' => env('STREAMTALK_API_ROUTES_PREFIX', 'streamtalk/api'),
        'as' => env('STREAMTALK_API_ROUTES_AS', 'api.streamtalk.'),
        'middleware' => env('STREAMTALK_API_ROUTES_MIDDLEWARE', ['api','auth:sanctum']),
        'namespace' => env('STREAMTALK_API_ROUTES_NAMESPACE', 'App\Http\Controllers\StreamTalk\Api'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Реальное время (Pusher)
    | Real-time (Pusher)
    |--------------------------------------------------------------------------
    */
    'broadcast_driver' => env('BROADCAST_DRIVER', 'pusher'),
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

    /*
    |--------------------------------------------------------------------------
    | Аватар пользователя
    | User Avatar
    |--------------------------------------------------------------------------
    */
    'user_avatar' => [
        'folder' => env('STREAMTALK_AVATAR_FOLDER', 'users-avatar'),
        'default' => env('STREAMTALK_DEFAULT_AVATAR', 'avatar.png'),
        'gravatar' => [
            'enabled' => env('STREAMTALK_GRAVATAR_ENABLED', true),
            'image_size' => env('STREAMTALK_GRAVATAR_SIZE', 200),
            'imageset' => env('STREAMTALK_GRAVATAR_IMAGESET', 'identicon')
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Вложения
    | Attachments
    |--------------------------------------------------------------------------
    */
    'attachments' => [
        'folder' => env('STREAMTALK_ATTACHMENTS_FOLDER', 'attachments'),
        'download_route_name' => env('STREAMTALK_DOWNLOAD_ROUTE', 'attachments.download'),
        'allowed_images' => ['png','jpg','jpeg','gif','webp'],
        'allowed_files' => ['zip','rar','txt','pdf','doc','docx','xls','xlsx','ppt','pptx','csv'],
        'max_upload_size' => env('STREAMTALK_MAX_FILE_SIZE', 150), // MB
        'max_uploads_per_message' => env('STREAMTALK_MAX_UPLOADS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Интерфейс
    | UI Settings
    |--------------------------------------------------------------------------
    */
    'theme' => env('STREAMTALK_THEME', 'auto'), // light, dark, auto
    'colors' => [
        '#2180f3', '#2196F3', '#00BCD4', '#3F51B5',
        '#673AB7', '#4CAF50', '#FFC107', '#FF9800',
        '#ff2522', '#9C27B0',
    ],
    'ui' => [
        'show_user_status' => env('STREAMTALK_SHOW_STATUS', true),
        'show_read_receipts' => env('STREAMTALK_SHOW_READ_RECEIPTS', true),
        'message_date_format' => env('STREAMTALK_DATE_FORMAT', 'F j, Y, g:i a'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Уведомления
    | Notifications
    |--------------------------------------------------------------------------
    */
    'notifications' => [
        'enabled' => env('STREAMTALK_NOTIFICATIONS_ENABLED', true),
        'channels' => env('STREAMTALK_NOTIFICATION_CHANNELS', ['database', 'broadcast']),
        'mail' => [
            'enabled' => env('STREAMTALK_MAIL_NOTIFICATIONS', false),
            'template' => env('STREAMTALK_MAIL_TEMPLATE', 'streamtalk::emails.new-message'),
        ],
    ],

    'sounds' => [
        'enabled' => env('STREAMTALK_SOUNDS_ENABLED', true),
        'public_path' => env('STREAMTALK_SOUNDS_PATH', 'sounds/StreamTalk'),
        'new_message' => env('STREAMTALK_NEW_MESSAGE_SOUND', 'new-message-sound.mp3'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Сообщения
    | Messages
    |--------------------------------------------------------------------------
    */
    'messages' => [
        'paginate' => env('STREAMTALK_MESSAGES_PER_PAGE', 25),
        'order' => env('STREAMTALK_MESSAGES_ORDER', 'asc'),
        'max_length' => env('STREAMTALK_MAX_MESSAGE_LENGTH', 1000),
        'purge_deleted_after' => env('STREAMTALK_PURGE_DELETED', 30), // days
    ],

    /*
    |--------------------------------------------------------------------------
    | Производительность
    | Performance
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('STREAMTALK_CACHE_ENABLED', true),
        'duration' => env('STREAMTALK_CACHE_DURATION', 60), // minutes
        'prefix' => env('STREAMTALK_CACHE_PREFIX', 'streamtalk_'),
    ],

    'queue' => [
        'notifications' => env('STREAMTALK_QUEUE_NOTIFICATIONS', 'default'),
        'events' => env('STREAMTALK_QUEUE_EVENTS', 'default'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Безопасность
    | Security
    |--------------------------------------------------------------------------
    */
    'throttle' => [
        'enabled' => env('STREAMTALK_THROTTLE_ENABLED', true),
        'max_attempts' => env('STREAMTALK_THROTTLE_ATTEMPTS', 10),
        'decay_minutes' => env('STREAMTALK_THROTTLE_DECAY', 1),
    ],

    'content_moderation' => [
        'enabled' => env('STREAMTALK_CONTENT_MODERATION', false),
        'blocked_words' => [],
        'blocked_ips' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Интеграции
    | Integrations
    |--------------------------------------------------------------------------
    */
    'hooks' => [
        'before_message_sent' => env('STREAMTALK_HOOK_BEFORE_MESSAGE', null),
        'after_message_sent' => env('STREAMTALK_HOOK_AFTER_MESSAGE', null),
        'message_received' => env('STREAMTALK_HOOK_MESSAGE_RECEIVED', null),
        'before_file_upload' => env('STREAMTALK_HOOK_BEFORE_UPLOAD', null),
        'after_file_upload' => env('STREAMTALK_HOOK_AFTER_UPLOAD', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Установка
    | Installation
    |--------------------------------------------------------------------------
    */
    'install' => [
        'publish' => [
            'config' => true,
            'migrations' => true,
            'views' => true,
            'assets' => true,
        ],
        'commands' => [
            'storage:link',
            'migrate',
        ],
    ],
];
