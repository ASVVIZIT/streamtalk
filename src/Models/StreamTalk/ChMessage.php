<?php

namespace App\Models\StreamTalk;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use StreamTalk\Traits\UUID;
use StreamTalk\MessageCollection;

/**
 * Модель сообщений чата
 * Chat message model
 */
class ChMessage extends Model
{
    // Указание таблицы с префиксом st_
    // Table name with st_ prefix
    protected $table = 'st_messages';

    // Использование UUID трейта
    // Using UUID trait
    use UUID;

    /**
     * Связь с отправителем сообщения
     * Relationship with message sender
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function from()
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    /**
     * Связь с получателем сообщения
     * Relationship with message recipient
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function to()
    {
        return $this->belongsTo(User::class, 'to_id');
    }

    /**
     * Пометить сообщение как прочитанное
     * Mark message as read
     */
    public function markAsRead()
    {
        if ($this->seen !== 1) {
            $this->forceFill(['seen' => 1])->save();
        }
    }

    /**
     * Пометить сообщение как непрочитанное
     * Mark message as unread
     */
    public function markAsUnread()
    {
        if ($this->seen !== 0) {
            $this->forceFill(['seen' => 0])->save();
        }
    }

    /**
     * Проверка, прочитано ли сообщение
     * Check if message is read
     *
     * @return bool
     */
    public function read()
    {
        return $this->seen !== 0;
    }

    /**
     * Проверка, не прочитано ли сообщение
     * Check if message is unread
     *
     * @return bool
     */
    public function unread()
    {
        return $this->seen === 0;
    }

    /**
     * Запрос прочитанных сообщений
     * Scope for read messages
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeRead(Builder $query)
    {
        return $query->where('seen', 1);
    }

    /**
     * Запрос непрочитанных сообщений
     * Scope for unread messages
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnread(Builder $query)
    {
        return $query->where('seen', 0);
    }

    /**
     * Создать кастомную коллекцию сообщений
     * Create custom message collection
     *
     * @param array $models
     * @return MessageCollection
     */
    public function newCollection(array $models = [])
    {
        return new MessageCollection($models);
    }
}
