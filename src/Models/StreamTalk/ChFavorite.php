<?php

namespace App\Models\StreamTalk;

use Illuminate\Database\Eloquent\Model;
use StreamTalk\Traits\UUID;

/**
 * Модель избранных контактов
 * Favorite contacts model
 */
class ChFavorite extends Model
{
    // Указание таблицы с префиксом st_
    // Table name with st_ prefix
    protected $table = 'st_favorites';

    // Использование UUID трейта
    // Using UUID trait
    use UUID;
}
