<?php
/**
 * -----------------------------------------------------------------
 * NOTE : There is two routes has a name (user & group),
 * any change in these two route's name may cause an issue
 * if not modified in all places that used in (e.g StreamTalk class,
 * Controllers, StreamTalk javascript file...).
 * -----------------------------------------------------------------
 */

use Illuminate\Support\Facades\Route;
use StreamTalk\Http\Controllers\StreamTalk\WebMessagesController;

// Группа маршрутов для веб-интерфейса чата
// Chat web interface route group
Route::group([
    'prefix' => config('streamtalk.routes.prefix'), // Префикс URL: /streamtalk
    'as' => config('streamtalk.routes.as'), // Префикс для имен маршрутов
    'middleware' => config('streamtalk.routes.middleware'), // Защита веб-сессии и аутентификация
], function () {

    // Главная страница чата
    // Chat main page
    Route::get('/', [WebMessagesController::class, 'index'])->name('main');

    // Получение информации о пользователе/группе
    // Get user/group information
    Route::post('/idInfo', [WebMessagesController::class, 'idFetchData'])->name('idInfo');

    // Отправка сообщения
    // Send message
    Route::post('/sendMessage', [WebMessagesController::class, 'send'])->name('send.message');

    // Загрузка сообщений
    // Fetch messages
    Route::post('/fetchMessages', [WebMessagesController::class, 'fetch'])->name('fetch.messages');

    // Скачивание вложений
    // Download attachments
    Route::get('/download/{fileName}', [WebMessagesController::class, 'download'])->name('download');

    // Аутентификация Pusher
    // Pusher authentication
    Route::post('/chat/auth', [WebMessagesController::class, 'pusherAuth'])->name('pusher.auth');

    // Пометка сообщений как прочитанных
    // Mark messages as seen
    Route::post('/makeSeen', [WebMessagesController::class, 'seen'])->name('messages.seen');

    // Получение контактов
    // Get contacts
    Route::get('/getContacts', [WebMessagesController::class, 'getContacts'])->name('contacts.get');

    // Обновление контакта
    // Update contact
    Route::post('/updateContacts', [WebMessagesController::class, 'updateContactItem'])->name('contacts.update');

    // Добавление в избранное
    // Add to favorites
    Route::post('/star', [WebMessagesController::class, 'favorite'])->name('star');

    // Получение избранного
    // Get favorites
    Route::post('/favorites', [WebMessagesController::class, 'getFavorites'])->name('favorites');

    // Поиск
    // Search
    Route::get('/search', [WebMessagesController::class, 'search'])->name('search');

    // Общие фото
    // Shared photos
    Route::post('/shared', [WebMessagesController::class, 'sharedPhotos'])->name('shared');

    // Удаление беседы
    // Delete conversation
    Route::post('/deleteConversation', [WebMessagesController::class, 'deleteConversation'])->name('conversation.delete');

    // Удаление сообщения
    // Delete message
    Route::post('/deleteMessage', [WebMessagesController::class, 'deleteMessage'])->name('message.delete');

    // Обновление настроек
    // Update settings
    Route::post('/updateSettings', [WebMessagesController::class, 'updateSettings'])->name('avatar.update');

    // Установка статуса активности
    // Set active status
    Route::post('/setActiveStatus', [WebMessagesController::class, 'setActiveStatus'])->name('activeStatus.set');

    // Страница группового чата
    // Group chat page
    Route::get('/group/{id}', [WebMessagesController::class, 'index'])->name('group');

    // Страница пользовательского чата
    // User chat page
    Route::get('/{id}', [WebMessagesController::class, 'index'])->name('user');
});
