<?php

namespace App\Helpers;

/**
 * Centralises role and ownership checks so controllers apply one permission policy.
 */
class PermissionHelper
{
    public static function isAdmin(?array $authUser)
    {
        if ($authUser === null) {
            return false;
        }

        return strtolower(trim((string) ($authUser['role'] ?? ''))) === 'admin';
    }

    public static function isTutor(?array $authUser)
    {
        if ($authUser === null) {
            return false;
        }

        return strtolower(trim((string) ($authUser['role'] ?? ''))) === 'tutor';
    }

    public static function isOwner(?array $authUser, mixed $ownerId)
    {
        if ($authUser === null) {
            return false;
        }

        $authUserId = (int) ($authUser['id'] ?? 0);

        return $authUserId > 0 && $authUserId === (int) $ownerId;
    }

    public static function canEditDiscussion(?array $authUser, array $postRecord)
    {
        return self::isAdmin($authUser)
            || self::isOwner($authUser, $postRecord['user_id'] ?? 0);
    }

    public static function canMarkDiscussionAsSolved(?array $authUser, array $postRecord)
    {
        return self::isOwner($authUser, $postRecord['user_id'] ?? 0)
            || self::isTutor($authUser)
            || self::isAdmin($authUser);
    }

    public static function canEditReply(?array $authUser, array $reply)
    {
        return self::isAdmin($authUser)
            || self::isOwner($authUser, $reply['user_id'] ?? 0);
    }

    public static function canDeleteReply(?array $authUser, array $reply)
    {
        return self::isAdmin($authUser)
            || self::isOwner($authUser, $reply['user_id'] ?? 0)
            || self::isOwner($authUser, $reply['post_user_id'] ?? 0);
    }

}
