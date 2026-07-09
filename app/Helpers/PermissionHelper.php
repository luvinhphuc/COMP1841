<?php

namespace App\Helpers;

class PermissionHelper
{
    public static function isAdmin(?array $user)
    {
        if ($user === null) {
            return false;
        }

        return strtolower(trim((string) ($user['role'] ?? ''))) === 'admin';
    }

    public static function isOwner(?array $user, mixed $ownerId)
    {
        if ($user === null) {
            return false;
        }

        $userId = (int) ($user['id'] ?? 0);

        return $userId > 0 && $userId === (int) $ownerId;
    }

    public static function canEditPost(?array $user, array $post)
    {
        return self::isAdmin($user)
            || self::isOwner($user, $post['user_id'] ?? 0);
    }

    public static function canEditReply(?array $user, array $reply)
    {
        return self::isAdmin($user)
            || self::isOwner($user, $reply['user_id'] ?? 0);
    }

    public static function canDeleteReply(?array $user, array $reply)
    {
        return self::isAdmin($user)
            || self::isOwner($user, $reply['user_id'] ?? 0)
            || self::isOwner($user, $reply['post_user_id'] ?? 0);
    }

}
