<?php

use Illuminate\Support\Facades\Route;
use StreamTalk\Http\Controllers\StreamTalk\WebMessagesController;

/**
 * Веб-маршруты для StreamTalk
 * Web Routes for StreamTalk
 */
Route::group([
    'namespace' => config('streamtalk.routes.namespace'),
    'prefix' => config('streamtalk.routes.prefix'), // Префикс URL: /streamtalk
    'as' => config('streamtalk.routes.as'),         // Префикс имен: streamtalk.
    'middleware' => config('streamtalk.routes.middleware'),
], function () {

    // Главная страница мессенджера (список чатов) [GET]
    // Messenger home page (chats list)
    Route::get('/', [WebMessagesController::class, 'index'])->name('main');

    // Получение информации о пользователе/группе [POST]
    // Fetch user/group information
    Route::post('/idInfo', [WebMessagesController::class, 'idFetchData'])->name('idInfo');

    // Отправка сообщения [POST]
    // Send message
    Route::post('/sendMessage', [WebMessagesController::class, 'send'])->name('send.message');

    // Загрузка сообщений [POST]
    // Fetch messages
    Route::post('/fetchMessages', [WebMessagesController::class, 'fetch'])->name('fetch.messages');

    // Скачивание вложения [GET]
    // Download attachment
    Route::get('/download/{fileName}', [WebMessagesController::class, 'download'])->name('attachments.download');

    // Аутентификация Pusher [POST]
    // Pusher authentication
    Route::post('/chat/auth', [WebMessagesController::class, 'pusherAuth'])->name('pusher.auth');

    // Пометка сообщений как прочитанных [POST]
    // Mark messages as seen
    Route::post('/makeSeen', [WebMessagesController::class, 'seen'])->name('messages.seen');

    // Получение списка контактов [GET]
    // Get contacts list
    Route::get('/getContacts', [WebMessagesController::class, 'getContacts'])->name('contacts.get');

    // Обновление элемента контакта [POST]
    // Update contact item
    Route::post('/updateContacts', [WebMessagesController::class, 'updateContactItem'])->name('contacts.update');

    // Добавление/удаление из избранного [POST]
    // Add/remove from favorites
    Route::post('/star', [WebMessagesController::class, 'favorite'])->name('star');

    // Получение списка избранных контактов [POST]
    // Get favorites list
    Route::post('/favorites', [WebMessagesController::class, 'getFavorites'])->name('favorites');

    // Поиск [GET]
    // Search
    Route::get('/search', [WebMessagesController::class, 'search'])->name('search');

    // Получение общих фото [POST]
    // Get shared photos
    Route::post('/shared', [WebMessagesController::class, 'sharedPhotos'])->name('shared');

    // Удаление беседы [POST]
    // Delete conversation
    Route::post('/deleteConversation', [WebMessagesController::class, 'deleteConversation'])->name('conversation.delete');

    // Удаление сообщения [POST]
    // Delete message
    Route::post('/deleteMessage', [WebMessagesController::class, 'deleteMessage'])->name('message.delete');

    // Обновление настроек (аватар, цвет, тема) [POST]
    // Update settings (avatar, color, theme)
    Route::post('/updateSettings', [WebMessagesController::class, 'updateSettings'])->name('avatar.update');

    // Установка статуса активности (онлайн/офлайн) [POST]
    // Set active status (online/offline)
    Route::post('/setActiveStatus', [WebMessagesController::class, 'setActiveStatus'])->name('activeStatus.set');

    // Групповой чат (просмотр) [GET]
    // Group chat view
    Route::get('/group/{id}', [WebMessagesController::class, 'index'])->name('group');

    // Чат с пользователем (просмотр) [GET]
    // User chat view
    Route::get('/{id}', [WebMessagesController::class, 'index'])->name('user');
});
