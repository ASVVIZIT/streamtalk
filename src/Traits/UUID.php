<?php

namespace StreamTalk\Traits;

use Illuminate\Support\Str;

/**
 * Трейт для генерации UUID первичных ключей
 * UUID primary key generation trait
 */
trait UUID
{
    /**
     * Инициализация трейта
     * Boot the trait
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }

    /**
     * Указание, что автоинкремент отключен
     * Get the auto-incrementing state
     *
     * @return bool
     */
    public function getIncrementing()
    {
        return false;
    }

    /**
     * Указание типа первичного ключа
     * Get the primary key type
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
}
