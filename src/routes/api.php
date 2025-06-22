<?php

use Illuminate\Support\Facades\Route;
use StreamTalk\Http\Controllers\StreamTalk\Api\ApiMessagesController;

Route::group([
    'namespace' => config('streamtalk.api_routes.namespace'),
    'prefix' => config('streamtalk.api_routes.prefix'), // Префикс URL: /streamtalk/api
    'as' => config('streamtalk.api_routes.as'),         // Префикс имен: api.streamtalk.
    'middleware' => config('streamtalk.api_routes.middleware'),
], function () {

    // Аутентификация Pusher для приватных каналов
    Route::post('/chat/auth', [ApiMessagesController::class, 'pusherAuth'])->name('pusher.auth');

    // Получение информации о пользователе/группе
    Route::post('/idInfo', [ApiMessagesController::class, 'idFetchData'])->name('idInfo');

    // Отправка сообщения
    Route::post('/sendMessage', [ApiMessagesController::class, 'send'])->name('send.message');

    // Загрузка сообщений
    Route::post('/fetchMessages', [ApiMessagesController::class, 'fetch'])->name('fetch.messages');

    // Скачивание вложения (возвращает JSON с URL для скачивания)
    Route::get('/download/{fileName}', [ApiMessagesController::class, 'download'])->name('attachments.download');

    // Пометка сообщений как прочитанных
    Route::post('/makeSeen', [ApiMessagesController::class, 'seen'])->name('messages.seen');

    // Получение списка контактов
    Route::get('/getContacts', [ApiMessagesController::class, 'getContacts'])->name('contacts.get');

    // Добавление/удаление из избранного
    Route::post('/star', [ApiMessagesController::class, 'favorite'])->name('star');

    // Получение списка избранных контактов
    Route::post('/favorites', [ApiMessagesController::class, 'getFavorites'])->name('favorites');

    // Поиск
    Route::get('/search', [ApiMessagesController::class, 'search'])->name('search');

    // Получение общих фото
    Route::post('/shared', [ApiMessagesController::class, 'sharedPhotos'])->name('shared');

    // Удаление беседы
    Route::post('/deleteConversation', [ApiMessagesController::class, 'deleteConversation'])->name('conversation.delete');

    // Обновление настроек (аватар, цвет, тема)
    Route::post('/updateSettings', [ApiMessagesController::class, 'updateSettings'])->name('avatar.update');

    // Установка статуса активности (онлайн/офлайн)
    Route::post('/setActiveStatus', [ApiMessagesController::class, 'setActiveStatus'])->name('activeStatus.set');
});
