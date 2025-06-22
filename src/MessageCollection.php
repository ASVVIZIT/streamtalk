<?php

namespace StreamTalk;

use Illuminate\Database\Eloquent\Collection;

/**
 * Кастомная коллекция сообщений
 * Custom message collection
 */
class MessageCollection extends Collection
{
    /**
     * Пометить все сообщения как прочитанные
     * Mark all notifications as read
     */
    public function markAsRead()
    {
        $this->each->markAsRead();
    }

    /**
     * Пометить все сообщения как непрочитанные
     * Mark all notifications as unread
     */
    public function markAsUnread()
    {
        $this->each->markAsUnread();
    }
}
