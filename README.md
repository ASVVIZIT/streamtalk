## StreamTalk for Project Services Laravel Package

Laravel's #1 one-to-one chatting system package. Adds a complete real-time messaging system to new/existing Laravel applications with minimal setup.

### Key Features
- Real-time messaging with Pusher
- User authentication middleware
- API and Web route separation
- File attachments support
- Dark/light mode

## Installation
```bash
composer require asvvizit/streamtalk
php artisan StreamTalk:install
```

## Add in file .env
### STREAMTALK ROUTE CONFIG
### Система распознаёт автоматические пути контроллеров, но вы можете их переопределить пред установкой.
### Внесите те ключи которые хотите переопределить или закомментируйте при помощи знака решётки в начале строки #
```bash  
# Кастомная структура проекта:
STREAMTALK_ROUTES_NAMESPACE=Modules\\Messenger\\Controllers
# Интеграция с другими пакетами:
STREAMTALK_API_ROUTES_NAMESPACE=Packages\\StreamTalk\\ApiControllers
# Специфические требования безопасности
STREAMTALK_ROUTES_NAMESPACE=App\\Secure\\Controllers\\Messenger

# Основные настройки
STREAMTALK_NAME="My Messenger"
STREAMTALK_STORAGE_DISK=streamtalk # по умолчанию streamtalk

# Веб-маршруты
STREAMTALK_ROUTES_PREFIX=chat
STREAMTALK_ROUTES_AS=chat.
STREAMTALK_ROUTES_MIDDLEWARE=web,auth,verified

# API-маршруты
STREAMTALK_API_ROUTES_PREFIX=chat/api
STREAMTALK_API_ROUTES_AS=api.chat.
STREAMTALK_API_ROUTES_MIDDLEWARE=api,auth:sanctum

# Дополнительные настройки
STREAMTALK_MAX_FILE_SIZE=50
```

## Add block in file config/filesystems.php
```bash
    /* disk for streamtalk */
    'streamtalk' => [
        'driver' => 'local',
        'root' => storage_path('/streamtalk'),
        'url' => env('APP_URL').'/storage/streamtalk',
        'visibility' => 'public',
        'throw' => false,
    ],
```
## Add custom block in file config/filesystems.php
```bash
    /* disk for custom */
    'custom' => [
        'driver' => 'local',
        'root' => storage_path('/custom'),
        'url' => env('APP_URL').'/storage/custom',
        'visibility' => 'public',
        'throw' => false,
    ],
```
