<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;

/**
 * Миграция для создания таблиц StreamTalk
 * StreamTalk tables migration
 */
class CreateStreamTalkTables extends Migration
{
    /**
     * Применение миграции
     * Run the migrations
     */
    public function up()
    {
        // Получение конфигурации
        $tables = Config::get('streamtalk.tables');
        $columns = Config::get('streamtalk.columns');

        // Создание таблицы избранных
        // Create favorites table
        if (!Schema::hasTable($tables['favorites'])) {
            Schema::create($tables['favorites'], function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('favorite_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'favorite_id']);
            });
        }

        // Создание таблицы сообщений
        // Create messages table
        if (!Schema::hasTable($tables['messages'])) {
            Schema::create($tables['messages'], function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('to_id')->constrained('users')->onDelete('cascade');
                $table->text('body')->nullable();
                $table->json('attachment')->nullable();
                $table->boolean('seen')->default(false);
                $table->timestamps();
            });
        }

        // Добавление столбцов в users
        // Add columns to users table
        Schema::table('users', function (Blueprint $table) use ($columns) {
            if (!Schema::hasColumn('users', $columns['active_status'])) {
                $table->boolean($columns['active_status'])->default(false);
            }

            if (!Schema::hasColumn('users', $columns['avatar'])) {
                $table->string($columns['avatar'])->nullable();
            }

            if (!Schema::hasColumn('users', $columns['dark_mode'])) {
                $table->boolean($columns['dark_mode'])->default(false);
            }

            if (!Schema::hasColumn('users', $columns['messenger_color'])) {
                $table->string($columns['messenger_color'], 20)->nullable();
            }
        });
    }

    /**
     * Откат миграции
     * Reverse the migrations
     */
    public function down()
    {
        $tables = Config::get('streamtalk.tables');
        $columns = Config::get('streamtalk.columns');

        Schema::dropIfExists($tables['favorites']);
        Schema::dropIfExists($tables['messages']);

        Schema::table('users', function (Blueprint $table) use ($columns) {
            $table->dropColumn([
                $columns['active_status'],
                $columns['avatar'],
                $columns['dark_mode'],
                $columns['messenger_color']
            ]);
        });
    }
}
