<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamTalkTables extends Migration
{
    public function up()
    {
        // Создание таблицы избранных контактов с префиксом st_
        if (!Schema::hasTable('st_favorites')) {
            Schema::create('st_favorites', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->bigInteger('user_id');       // ID пользователя
                $table->bigInteger('favorite_id');   // ID избранного контакта
                $table->timestamps();                // Метки времени создания/обновления
            });
        }

        // Создание таблицы сообщений с префиксом st_
        if (!Schema::hasTable('st_messages')) {
            Schema::create('st_messages', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->bigInteger('from_id');       // ID отправителя
                $table->bigInteger('to_id');          // ID получателя
                $table->string('body', 5000)->nullable(); // Текст сообщения
                $table->string('attachment')->nullable(); // Вложение
                $table->boolean('seen')->default(false); // Флаг прочтения
                $table->timestamps();                // Метки времени
            });
        }

        // Добавление столбцов в таблицу users с префиксом st_
        Schema::table('users', function (Blueprint $table) {
            // Статус активности
            if (!Schema::hasColumn('users', 'st_active_status')) {
                $table->boolean('st_active_status')->default(0)->comment('Статус активности в StreamTalk');
            }
            // Аватар
            if (!Schema::hasColumn('users', 'st_avatar')) {
                $table->string('st_avatar')->default(config('streamtalk.user_avatar.default'))->comment('Аватар StreamTalk');
            }
            // Тёмная тема
            if (!Schema::hasColumn('users', 'st_dark_mode')) {
                $table->boolean('st_dark_mode')->default(0)->comment('Тёмная тема StreamTalk');
            }
            // Цвет мессенджера
            if (!Schema::hasColumn('users', 'st_messenger_color')) {
                $table->string('st_messenger_color')->nullable()->comment('Цвет интерфейса StreamTalk');
            }
        });
    }

    public function down()
    {
        // Удаление таблиц при откате миграции
        Schema::dropIfExists('st_favorites');
        Schema::dropIfExists('st_messages');

        // Удаление добавленных столбцов
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('st_active_status');
            $table->dropColumn('st_avatar');
            $table->dropColumn('st_dark_mode');
            $table->dropColumn('st_messenger_color');
        });
    }
}
