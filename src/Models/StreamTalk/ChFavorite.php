<?php

namespace App\Models\StreamTalk;

use Illuminate\Database\Eloquent\Model;
use StreamTalk\Traits\UUID;

class ChFavorite extends Model
{
    // Указание таблицы с префиксом st_
    protected $table = 'st_favorites';

    // Использование UUID трейта
    use UUID;
}
