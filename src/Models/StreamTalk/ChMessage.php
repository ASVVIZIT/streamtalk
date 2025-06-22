<?php

namespace App\Models\StreamTalk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use StreamTalk\Traits\UUID;
use StreamTalk\MessageCollection;

class ChMessage extends Model
{
    // Указание таблицы с префиксом st_
    protected $table = 'st_messages';

    // Использование UUID трейта
    use UUID;

    // Связь с отправителем
    public function from()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    // Связь с получателем
    public function to()
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    // Пометить как прочитанное
    public function markAsRead()
    {
        if ($this->seen !== 1) {
            $this->forceFill(['seen' => 1])->save();
        }
    }

    // Пометить как непрочитанное
    public function markAsUnread()
    {
        if ($this->seen !== 0) {
            $this->forceFill(['seen' => 0])->save();
        }
    }

    // Проверка прочитано ли
    public function read()
    {
        return $this->seen !== 0;
    }

    // Проверка не прочитано ли
    public function unread()
    {
        return $this->seen === 0;
    }

    // Запрос прочитанных
    public function scopeRead(Builder $query)
    {
        return $query->where('seen', 1);
    }

    // Запрос непрочитанных
    public function scopeUnread(Builder $query)
    {
        return $query->where('seen', 0);
    }

    // Кастомная коллекция
    public function newCollection(array $models = [])
    {
        return new MessageCollection($models);
    }
}
