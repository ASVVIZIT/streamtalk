<?php

use Illuminate\Support\Facades\Route;
use StreamTalk\Http\Controllers\StreamTalk\Api\ApiMessagesController;

/**
 * API Маршруты для StreamTalk
 * API Routes for StreamTalk
 */
Route::group([
    'namespace' => config('streamtalk.api_routes.namespace'),
    'prefix' => config('streamtalk.api_routes.prefix'), // Префикс URL: /streamtalk/api
    'as' => config('streamtalk.api_routes.as'),         // Префикс имен: api.streamtalk.
    'middleware' => config('streamtalk.api_routes.middleware'),
], function () {

    // Аутентификация Pusher для приватных каналов [POST]
    // Pusher authentication for private channels
    Route::post('/chat/auth', [ApiMessagesController::class, 'pusherAuth'])->name('pusher.auth');

    // Получение информации о пользователе/группе [POST]
    // Fetch user/group information
    Route::post('/idInfo', [ApiMessagesController::class, 'idFetchData'])->name('idInfo');

    // Отправка сообщения [POST]
    // Send message
    Route::post('/sendMessage', [ApiMessagesController::class, 'send'])->name('send.message');

    // Загрузка сообщений [POST]
    // Fetch messages
    Route::post('/fetchMessages', [ApiMessagesController::class, 'fetch'])->name('fetch.messages');

    // Скачивание вложения (возвращает JSON с URL для скачивания) [GET]
    // Download attachment (returns JSON with download URL)
    Route::get('/download/{fileName}', [ApiMessagesController::class, 'download'])->name('attachments.download');

    // Пометка сообщений как прочитанных [POST]
    // Mark messages as seen
    Route::post('/makeSeen', [ApiMessagesController::class, 'seen'])->name('messages.seen');

    // Получение списка контактов [GET]
    // Get contacts list
    Route::get('/getContacts', [ApiMessagesController::class, 'getContacts'])->name('contacts.get');

    // Добавление/удаление из избранного [POST]
    // Add/remove from favorites
    Route::post('/star', [ApiMessagesController::class, 'favorite'])->name('star');

    // Получение списка избранных контактов [POST]
    // Get favorites list
    Route::post('/favorites', [ApiMessagesController::class, 'getFavorites'])->name('favorites');

    // Поиск [GET]
    // Search
    Route::get('/search', [ApiMessagesController::class, 'search'])->name('search');

    // Получение общих фото [POST]
    // Get shared photos
    Route::post('/shared', [ApiMessagesController::class, 'sharedPhotos'])->name('shared');

    // Удаление беседы [POST]
    // Delete conversation
    Route::post('/deleteConversation', [ApiMessagesController::class, 'deleteConversation'])->name('conversation.delete');

    // Обновление настроек (аватар, цвет, тема) [POST]
    // Update settings (avatar, color, theme)
    Route::post('/updateSettings', [ApiMessagesController::class, 'updateSettings'])->name('avatar.update');

    // Установка статуса активности (онлайн/офлайн) [POST]
    // Set active status (online/offline)
    Route::post('/setActiveStatus', [ApiMessagesController::class, 'setActiveStatus'])->name('activeStatus.set');
});
