<?php

use Illuminate\Support\Facades\Route;
use StreamTalk\Http\Controllers\StreamTalk\Api\ApiMessagesController;

// Группа маршрутов для API чата
// Chat API route group
Route::group([
    'prefix' => 'streamtalk/api', // Префикс URL: /streamtalk/api
    'middleware' => ['api', 'auth:sanctum'] // API middleware и Sanctum аутентификация
], function () {

    // Аутентификация Pusher для приватных каналов
    // Pusher authentication for private channels
    Route::post('/chat/auth', [ApiMessagesController::class, 'pusherAuth'])->name('api.pusher.auth');

    // Получение информации о пользователе/группе
    // Get user/group information
    Route::post('/idInfo', [ApiMessagesController::class, 'idFetchData'])->name('api.idInfo');

    // Отправка сообщения
    // Send message
    Route::post('/sendMessage', [ApiMessagesController::class, 'send'])->name('api.send.message');

    // Загрузка сообщений
    // Fetch messages
    Route::post('/fetchMessages', [ApiMessagesController::class, 'fetch'])->name('api.fetch.messages');

    // Скачивание вложений
    // Download attachments
    Route::get('/download/{fileName}', [ApiMessagesController::class, 'download'])->name('api.download');

    // Пометка сообщений как прочитанных
    // Mark messages as seen
    Route::post('/makeSeen', [ApiMessagesController::class, 'seen'])->name('api.messages.seen');

    // Получение контактов
    // Get contacts
    Route::get('/getContacts', [ApiMessagesController::class, 'getContacts'])->name('api.contacts.get');

    // Управление избранным
    // Manage favorites
    Route::post('/star', [ApiMessagesController::class, 'favorite'])->name('api.star');

    // Получение избранного
    // Get favorites
    Route::post('/favorites', [ApiMessagesController::class, 'getFavorites'])->name('api.favorites');

    // Поиск
    // Search
    Route::get('/search', [ApiMessagesController::class, 'search'])->name('api.search');

    // Общие фото
    // Shared photos
    Route::post('/shared', [ApiMessagesController::class, 'sharedPhotos'])->name('api.shared');

    // Удаление беседы
    // Delete conversation
    Route::post('/deleteConversation', [ApiMessagesController::class, 'deleteConversation'])->name('api.conversation.delete');

    // Обновление настроек
    // Update settings
    Route::post('/updateSettings', [ApiMessagesController::class, 'updateSettings'])->name('api.avatar.update');

    // Установка статуса активности
    // Set active status
    Route::post('/setActiveStatus', [ApiMessagesController::class, 'setActiveStatus'])->name('api.activeStatus.set');
});
