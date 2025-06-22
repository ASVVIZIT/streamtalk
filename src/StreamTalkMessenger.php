<?php

namespace StreamTalk;

use StreamTalk\Models\Favorite;
use StreamTalk\Models\Message;
use Illuminate\Support\Facades\Storage;
use Pusher\Pusher;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * Основной класс мессенджера StreamTalk
 * Core StreamTalk messenger class
 */
class StreamTalkMessenger
{
    public $pusher;

    /**
     * Инициализация Pusher
     * Initialize Pusher
     */
    public function __construct()
    {
        $this->pusher = new Pusher(
            config('streamtalk.pusher.key'),
            config('streamtalk.pusher.secret'),
            config('streamtalk.pusher.app_id'),
            config('streamtalk.pusher.options')
        );
    }

    /**
     * Получить максимальный размер загружаемого файла (в байтах)
     * Get max upload size in bytes
     *
     * @return int
     */
    public function getMaxUploadSize()
    {
        return config('streamtalk.attachments.max_upload_size') * 1048576;
    }

    /**
     * Получить разрешенные типы изображений
     * Get allowed image types
     *
     * @return array
     */
    public function getAllowedImages()
    {
        return config('streamtalk.attachments.allowed_images');
    }

    /**
     * Получить разрешенные типы файлов
     * Get allowed file types
     *
     * @return array
     */
    public function getAllowedFiles()
    {
        return config('streamtalk.attachments.allowed_files');
    }

    /**
     * Получить доступные цвета для интерфейса
     * Get available messenger colors
     *
     * @return array
     */
    public function getMessengerColors()
    {
        return config('streamtalk.colors');
    }

    /**
     * Получить цвет по умолчанию
     * Get fallback color
     *
     * @return string
     */
    public function getFallbackColor()
    {
        $colors = $this->getMessengerColors();
        return count($colors) > 0 ? $colors[0] : '#000000';
    }

    /**
     * Отправить событие через Pusher
     * Push event via Pusher
     *
     * @param string $channel
     * @param string $event
     * @param mixed $data
     * @return mixed
     */
    public function push($channel, $event, $data)
    {
        return $this->pusher->trigger($channel, $event, $data);
    }

    /**
     * Аутентификация Pusher
     * Pusher authentication
     *
     * @param mixed $requestUser
     * @param mixed $authUser
     * @param string $channelName
     * @param string $socket_id
     * @return mixed
     */
    public function pusherAuth($requestUser, $authUser, $channelName, $socket_id)
    {
        $authData = json_encode([
            'user_id' => $authUser->id,
            'user_info' => ['name' => $authUser->name]
        ]);

        if (Auth::check()) {
            if ($requestUser->id == $authUser->id) {
                return $this->pusher->socket_auth($channelName, $socket_id, $authData);
            }
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json(['message' => 'Not authenticated'], 403);
    }

    /**
     * Парсинг сообщения для отображения
     * Parse message for display
     *
     * @param mixed $prefetchedMessage
     * @param int|null $id
     * @return array
     */
    public function parseMessage($prefetchedMessage = null, $id = null)
    {
        $msg = null;
        $attachment = null;
        $attachment_type = null;
        $attachment_title = null;

        if (!!$prefetchedMessage) {
            $msg = $prefetchedMessage;
        } else {
            $msg = Message::where('id', $id)->first();
            if (!$msg) return [];
        }

        if (isset($msg->attachment)) {
            $attachmentOBJ = json_decode($msg->attachment);
            $attachment = $attachmentOBJ->new_name;
            $attachment_title = htmlentities(trim($attachmentOBJ->old_name), ENT_QUOTES, 'UTF-8');
            $ext = pathinfo($attachment, PATHINFO_EXTENSION);
            $attachment_type = in_array($ext, $this->getAllowedImages()) ? 'image' : 'file';
        }

        return [
            'id' => $msg->id,
            'from_id' => $msg->from_id,
            'to_id' => $msg->to_id,
            'message' => $msg->body,
            'attachment' => (object) [
                'file' => $attachment,
                'title' => $attachment_title,
                'type' => $attachment_type
            ],
            'timeAgo' => $msg->created_at->diffForHumans(),
            'created_at' => $msg->created_at->toIso8601String(),
            'isSender' => ($msg->from_id == Auth::user()->id),
            'seen' => $msg->seen,
        ];
    }

    /**
     * Генерация HTML карточки сообщения
     * Generate message card HTML
     *
     * @param array $data
     * @param bool $renderDefaultCard
     * @return string
     */
    public function messageCard($data, $renderDefaultCard = false)
    {
        if (!$data) return '';
        if ($renderDefaultCard) $data['isSender'] = false;
        return view('streamtalk::layouts.messageCard', $data)->render();
    }

    /**
     * Запрос для получения сообщений
     * Query for fetching messages
     *
     * @param int $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function fetchMessagesQuery($user_id)
    {
        return Message::where('from_id', Auth::user()->id)
            ->where('to_id', $user_id)
            ->orWhere('from_id', $user_id)
            ->where('to_id', Auth::user()->id);
    }

    /**
     * Создание нового сообщения
     * Create new message
     *
     * @param array $data
     * @return Message
     */
    public function newMessage($data)
    {
        $message = new Message();
        $message->from_id = $data['from_id'];
        $message->to_id = $data['to_id'];
        $message->body = $data['body'];
        $message->attachment = $data['attachment'];
        $message->save();
        return $message;
    }

    /**
     * Пометка сообщений как прочитанных
     * Mark messages as seen
     *
     * @param int $user_id
     * @return int
     */
    public function makeSeen($user_id)
    {
        Message::where('from_id', $user_id)
            ->where('to_id', Auth::user()->id)
            ->where('seen', 0)
            ->update(['seen' => 1]);
        return 1;
    }

    /**
     * Запрос для получения последнего сообщения
     * Query for last message
     *
     * @param int $user_id
     * @return Message|null
     */
    public function getLastMessageQuery($user_id)
    {
        return $this->fetchMessagesQuery($user_id)->latest()->first();
    }

    /**
     * Подсчет непрочитанных сообщений
     * Count unseen messages
     *
     * @param int $user_id
     * @return int
     */
    public function countUnseenMessages($user_id)
    {
        return Message::where('from_id', $user_id)
            ->where('to_id', Auth::user()->id)
            ->where('seen', 0)
            ->count();
    }

    /**
     * Генерация HTML элемента контакта
     * Generate contact list item HTML
     *
     * @param mixed $user
     * @return string
     */
    public function getContactItem($user)
    {
        try {
            $lastMessage = $this->getLastMessageQuery($user->id);
            $unseenCounter = $this->countUnseenMessages($user->id);

            if ($lastMessage) {
                $lastMessage->created_at = $lastMessage->created_at->toIso8601String();
                $lastMessage->timeAgo = $lastMessage->created_at->diffForHumans();
            }

            return view('streamtalk::layouts.listItem', [
                'get' => 'users',
                'user' => $this->getUserWithAvatar($user),
                'lastMessage' => $lastMessage,
                'unseenCounter' => $unseenCounter,
            ])->render();
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }
    }

    /**
     * Получение пользователя с обработанным аватаром
     * Get user with processed avatar
     *
     * @param mixed $user
     * @return mixed
     */
    public function getUserWithAvatar($user)
    {
        $avatarColumn = config('streamtalk.columns.avatar');
        $defaultAvatar = config('streamtalk.user_avatar.default');

        if (empty($user->{$avatarColumn})) {
            $user->{$avatarColumn} = $defaultAvatar;
        }

        // Использование Gravatar если включено
        // Use Gravatar if enabled
        if ($user->{$avatarColumn} == $defaultAvatar && config('streamtalk.gravatar.enabled')) {
            $imageSize = config('streamtalk.gravatar.image_size');
            $imageset = config('streamtalk.gravatar.imageset');
            $user->{$avatarColumn} = 'https://www.gravatar.com/avatar/' .
                md5(strtolower(trim($user->email))) .
                '?s=' . $imageSize . '&d=' . $imageset;
        } else {
            $user->{$avatarColumn} = $this->getUserAvatarUrl($user->{$avatarColumn});
        }
        return $user;
    }

    /**
     * Проверка, находится ли пользователь в избранном
     * Check if user is in favorites
     *
     * @param int $user_id
     * @return bool
     */
    public function inFavorite($user_id)
    {
        return Favorite::where('user_id', Auth::user()->id)
                ->where('favorite_id', $user_id)->count() > 0;
    }

    /**
     * Добавление/удаление пользователя из избранного
     * Add/remove user to/from favorites
     *
     * @param int $user_id
     * @param int $action 1 - добавить, 0 - удалить
     * @return bool
     */
    public function makeInFavorite($user_id, $action)
    {
        if ($action > 0) {
            $star = new Favorite();
            $star->user_id = Auth::user()->id;
            $star->favorite_id = $user_id;
            $star->save();
            return $star ? true : false;
        } else {
            return Favorite::where('user_id', Auth::user()->id)
                ->where('favorite_id', $user_id)
                ->delete();
        }
    }

    /**
     * Получить общие фото с пользователем
     * Get shared photos with user
     *
     * @param int $user_id
     * @return array
     */
    public function getSharedPhotos($user_id)
    {
        $images = [];
        $msgs = $this->fetchMessagesQuery($user_id)->orderBy('created_at', 'DESC');

        if ($msgs->count() > 0) {
            foreach ($msgs->get() as $msg) {
                if ($msg->attachment) {
                    $attachment = json_decode($msg->attachment);
                    $ext = pathinfo($attachment->new_name, PATHINFO_EXTENSION);
                    if (in_array($ext, $this->getAllowedImages())) {
                        $images[] = $attachment->new_name;
                    }
                }
            }
        }
        return $images;
    }

    /**
     * Удаление беседы с пользователем
     * Delete conversation with user
     *
     * @param int $user_id
     * @return int
     */
    public function deleteConversation($user_id)
    {
        try {
            foreach ($this->fetchMessagesQuery($user_id)->get() as $msg) {
                if (isset($msg->attachment)) {
                    $path = config('streamtalk.attachments.folder') . '/' . json_decode($msg->attachment)->new_name;
                    if (self::storage()->exists($path)) {
                        self::storage()->delete($path);
                    }
                }
                $msg->delete();
            }
            return 1;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Удаление сообщения
     * Delete message
     *
     * @param int $id
     * @return int
     */
    public function deleteMessage($id)
    {
        try {
            $msg = Message::where('from_id', auth()->id())->where('id', $id)->firstOrFail();
            if (isset($msg->attachment)) {
                $path = config('streamtalk.attachments.folder') . '/' . json_decode($msg->attachment)->new_name;
                if (self::storage()->exists($path)) {
                    self::storage()->delete($path);
                }
            }
            $msg->delete();
            return 1;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Получить экземпляр хранилища
     * Get storage instance
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public function storage()
    {
        return Storage::disk(config('streamtalk.storage_disk_name'));
    }

    /**
     * Получить URL аватара пользователя
     * Get user avatar URL
     *
     * @param string $user_avatar_name
     * @return string
     */
    public function getUserAvatarUrl($user_avatar_name)
    {
        if (empty($user_avatar_name)) {
            return asset(config('streamtalk.user_avatar.default'));
        }
        return self::storage()->url(config('streamtalk.user_avatar.folder') . '/' . $user_avatar_name);
    }

    /**
     * Получить URL вложения
     * Get attachment URL
     *
     * @param string $attachment_name
     * @return string
     */
    public function getAttachmentUrl($attachment_name)
    {
        return self::storage()->url(config('streamtalk.attachments.folder') . '/' . $attachment_name);
    }
}
