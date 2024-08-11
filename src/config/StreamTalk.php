<?php

return [
    /*
    |-------------------------------------
    | Messenger display name
    |-------------------------------------
    */
    'name' => env('STREAMTALK_NAME', 'StreamTalk Messenger'),

    /*
    |-------------------------------------
    | The disk on which to store added
    | files and derived images by default.
    |-------------------------------------
    */
    'storage_disk_name' => env('STREAMTALK_STORAGE_DISK', 'public'),

    /*
    |-------------------------------------
    | Routes configurations
    |-------------------------------------
    */
    'routes' => [
        'custom' => env('STREAMTALK_CUSTOM_ROUTES', false),
        'prefix' => env('STREAMTALK_ROUTES_PREFIX', 'StreamTalk'),
        'middleware' => env('STREAMTALK_ROUTES_MIDDLEWARE', ['web','auth']),
        'namespace' => env('STREAMTALK_ROUTES_NAMESPACE', 'StreamTalk\Http\Controllers'),
    ],
    'api_routes' => [
        'prefix' => env('STREAMTALK_API_ROUTES_PREFIX', 'StreamTalk/api'),
        'middleware' => env('STREAMTALK_API_ROUTES_MIDDLEWARE', ['api']),
        'namespace' => env('STREAMTALK_API_ROUTES_NAMESPACE', 'StreamTalk\Http\Controllers\Api'),
    ],

    /*
    |-------------------------------------
    | Pusher API credentials
    |-------------------------------------
    */
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
    |-------------------------------------
    | User Avatar
    |-------------------------------------
    */
    'user_avatar' => [
        'folder' => 'users-avatar',
        'default' => 'avatar.png',
    ],

    /*
    |-------------------------------------
    | Gravatar
    |
    | imageset property options:
    | [ 404 | mp | identicon (default) | monsterid | wavatar ]
    |-------------------------------------
    */
    'gravatar' => [
        'enabled' => true,
        'image_size' => 200,
        'imageset' => 'identicon'
    ],

    /*
    |-------------------------------------
    | Attachments
    |-------------------------------------
    */
    'attachments' => [
        'folder' => 'attachments',
        'download_route_name' => 'attachments.download',
        'allowed_images' => (array) ['png','jpg','jpeg','gif'],
        'allowed_files' => (array) ['zip','rar','txt'],
        'max_upload_size' => env('STREAMTALK_MAX_FILE_SIZE', 150), // MB
    ],

    /*
    |-------------------------------------
    | Messenger's colors
    |-------------------------------------
    */
    'colors' => (array) [
        '#2180f3',
        '#2196F3',
        '#00BCD4',
        '#3F51B5',
        '#673AB7',
        '#4CAF50',
        '#FFC107',
        '#FF9800',
        '#ff2522',
        '#9C27B0',
    ],
    /*
    |-------------------------------------
    | Sounds
    | You can enable/disable the sounds and
    | change sound's name/path placed at
    | `public/` directory of your app.
    |
    |-------------------------------------
    */
    'sounds' => [
        'enabled' => true,
        'public_path' => 'sounds/StreamTalk',
        'new_message' => 'new-message-sound.mp3',
    ]
];
