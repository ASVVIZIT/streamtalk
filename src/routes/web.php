<?php

use Illuminate\Support\Facades\Route;
use StreamTalk\Http\Controllers\StreamTalk\WebMessagesController;

Route::group([
    'namespace' => config('streamtalk.routes.namespace'),
    'prefix' => config('streamtalk.routes.prefix'), // Префикс URL: /streamtalk
    'as' => config('streamtalk.routes.as'),         // Префикс имен: streamtalk.
    'middleware' => config('streamtalk.routes.middleware'),
], function () {

    // Главная страница мессенджера (список чатов)
    Route::get('/', [WebMessagesController::class, 'index'])->name('main');

    // Получение информации о пользователе/группе
    Route::post('/idInfo', [WebMessagesController::class, 'idFetchData'])->name('idInfo');

    // Отправка сообщения
    Route::post('/sendMessage', [WebMessagesController::class, 'send'])->name('send.message');

    // Загрузка сообщений
    Route::post('/fetchMessages', [WebMessagesController::class, 'fetch'])->name('fetch.messages');

    // Скачивание вложения
    Route::get('/download/{fileName}', [WebMessagesController::class, 'download'])->name('attachments.download');

    // Аутентификация Pusher
    Route::post('/chat/auth', [WebMessagesController::class, 'pusherAuth'])->name('pusher.auth');

    // Пометка сообщений как прочитанных
    Route::post('/makeSeen', [WebMessagesController::class, 'seen'])->name('messages.seen');

    // Получение списка контактов
    Route::get('/getContacts', [WebMessagesController::class, 'getContacts'])->name('contacts.get');

    // Обновление элемента контакта
    Route::post('/updateContacts', [WebMessagesController::class, 'updateContactItem'])->name('contacts.update');

    // Добавление/удаление из избранного
    Route::post('/star', [WebMessagesController::class, 'favorite'])->name('star');

    // Получение списка избранных контактов
    Route::post('/favorites', [WebMessagesController::class, 'getFavorites'])->name('favorites');

    // Поиск
    Route::get('/search', [WebMessagesController::class, 'search'])->name('search');

    // Получение общих фото
    Route::post('/shared', [WebMessagesController::class, 'sharedPhotos'])->name('shared');

    // Удаление беседы
    Route::post('/deleteConversation', [WebMessagesController::class, 'deleteConversation'])->name('conversation.delete');

    // Удаление сообщения
    Route::post('/deleteMessage', [WebMessagesController::class, 'deleteMessage'])->name('message.delete');

    // Обновление настроек (аватар, цвет, тема)
    Route::post('/updateSettings', [WebMessagesController::class, 'updateSettings'])->name('avatar.update');

    // Установка статуса активности (онлайн/офлайн)
    Route::post('/setActiveStatus', [WebMessagesController::class, 'setActiveStatus'])->name('activeStatus.set');

    // Групповой чат (просмотр)
    Route::get('/group/{id}', [WebMessagesController::class, 'index'])->name('group');

    // Чат с пользователем (просмотр)
    Route::get('/{id}', [WebMessagesController::class, 'index'])->name('user');
});
