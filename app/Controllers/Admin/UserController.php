<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\AttachmentService;
use App\Services\AuthService;
use Throwable;

/**
 * Manages accounts while protecting the active administrator from unsafe changes.
 */
class UserController extends AdminController
{
    private const USER_ROLES = ['student', 'tutor', 'admin'];
    private const USER_FORM_STATE = 'admin_user_form_state';

    public function index()
    {
        $this->requireAdmin();
        $page = $this->pageNumber();
        $pageLimit = $this->pageLimit();

        try {
            $userRepository = new UserRepository();
            $total = $userRepository->countAll();
            $pagination = $this->pagination('/admin/users', $total, $page, [], $pageLimit);
            $targetUserRecords = $userRepository->findPaginated($pageLimit, $pagination['offset']);
        } catch (Throwable) {
            $targetUserRecords = [];
            $pagination = $this->pagination('/admin/users', 0, 1, [], $pageLimit);
        }

        $this->view('admin/users', [
            'adminSection' => 'users',
            'pageTitle' => 'Manage Users',
            'pageScripts' => ['modal.js'],
            'targetUserRecords' => $targetUserRecords,
            'pagination' => $pagination,
            'authUserId' => $this->currentUserId(),
        ]);
    }

    public function edit($targetUserId = 0)
    {
        $this->requireAdmin();
        $targetUserEntity = $this->findTargetUser((int)$targetUserId);

        if ($targetUserEntity === null) {
            $this->notFound();
        }

        $targetUserData = $targetUserEntity->toArray();
        $targetUserFormState = $this->userFormState((int)$targetUserData['id']);
        $targetUserData = array_merge($targetUserData, $targetUserFormState['old'] ?? []);

        $this->view('admin/user-edit', [
            'adminSection' => 'users',
            'pageTitle' => 'Edit User',
            'formAction' => BASE_URL . '/admin/users/update/' . (int)$targetUserData['id'],
            'targetUserData' => $targetUserData,
            'targetUserErrors' => $targetUserFormState['errors'] ?? [],
            'roles' => self::USER_ROLES,
            'authUserId' => $this->currentUserId(),
        ]);
    }

    public function update($targetUserId = 0)
    {
        $authUser = $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/users');

        $targetUserId = (int)$targetUserId;
        $targetUserEntity = $this->findTargetUser($targetUserId);

        if ($targetUserEntity === null) {
            $this->notFound();
        }

        $targetUserData = $this->targetUserData();

        try {
            $userRepository = new UserRepository();
            $targetUserErrors = $this->validateTargetUser($targetUserData, $targetUserId, $userRepository);
            $targetUserErrors = array_merge(
                $targetUserErrors,
                $this->validateAdminRoleChange(
                    $targetUserData,
                    $targetUserId,
                    $targetUserEntity,
                    $authUser,
                    $userRepository
                )
            );

            if (!empty($targetUserErrors)) {
                $this->redirectUserForm(
                    '/admin/users/edit/' . $targetUserId,
                    $targetUserId,
                    $targetUserErrors,
                    $targetUserData
                );
            }

            if (!$userRepository->updateFromAdmin($targetUserId, $targetUserData)) {
                $this->redirectUserForm('/admin/users/edit/' . $targetUserId, $targetUserId, [
                    'general' => 'The user could not be updated.',
                ], $targetUserData);
            }

            $this->refreshAuthUserSession($targetUserId, $userRepository);
        } catch (Throwable) {
            $this->redirectUserForm('/admin/users/edit/' . $targetUserId, $targetUserId, [
                'general' => 'The user could not be updated right now.',
            ], $targetUserData);
        }

        $this->adminSuccess('/admin/users', 'User updated successfully.');
    }

    public function delete($targetUserId = 0)
    {
        $authUser = $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/users');

        $targetUserId = (int)$targetUserId;

        if ($targetUserId === (int)($authUser['id'] ?? 0)) {
            $this->adminError('/admin/users', 'You cannot delete your own account.');
        }

        try {
            $userRepository = new UserRepository();
            $targetUserEntity = $userRepository->findById($targetUserId);

            if ($targetUserEntity === null) {
                $this->notFound();
            }

            if (($targetUserEntity->role ?? '') === 'admin' && $userRepository->countAdmins() <= 1) {
                $this->adminError('/admin/users', 'The final admin account cannot be deleted.');
            }

            $targetUserAvatar = (string) ($targetUserEntity->avatar ?? '');

            if (!$userRepository->softDeleteAndAnonymize($targetUserId)) {
                $this->adminError('/admin/users', 'The user could not be deleted.');
            }

            $this->removeDeletedUserAvatar($targetUserAvatar);
        } catch (Throwable) {
            $this->adminError('/admin/users', 'The user could not be deleted.');
        }

        $this->adminSuccess('/admin/users', 'User deleted successfully.');
    }

    private function removeDeletedUserAvatar(string $avatarPath): void
    {
        $avatarPath = ltrim(str_replace('\\', '/', trim($avatarPath)), '/');

        if (!preg_match('#^uploads/avatars/[A-Za-z0-9._-]+$#', $avatarPath)) {
            return;
        }

        (new AttachmentService())->removeStoredAttachment(['path' => $avatarPath]);
    }

    private function findTargetUser(int $targetUserId)
    {
        if ($targetUserId <= 0) {
            return null;
        }

        try {
            return (new UserRepository())->findById($targetUserId);
        } catch (Throwable) {
            return null;
        }
    }

    private function targetUserData(): array
    {
        return [
            'first_name' => trim((string)($_POST['first_name'] ?? '')),
            'last_name' => trim((string)($_POST['last_name'] ?? '')),
            'username' => trim((string)($_POST['username'] ?? '')),
            'email' => trim((string)($_POST['email'] ?? '')),
            'role' => strtolower(trim((string)($_POST['role'] ?? 'student'))),
        ];
    }

    private function validateTargetUser(
        array $targetUserData,
        int $targetUserId,
        UserRepository $userRepository
    ): array
    {
        $targetUserErrors = (new AuthService($userRepository))->validateAccount($targetUserData, $targetUserId);

        if (!in_array($targetUserData['role'], self::USER_ROLES, true)) {
            $targetUserErrors['role'] = 'Please choose a valid user role.';
        }

        return $targetUserErrors;
    }

    private function validateAdminRoleChange(
        array $targetUserData,
        int $targetUserId,
        User $targetUserEntity,
        array $authUser,
        UserRepository $userRepository
    ): array
    {
        $targetUserErrors = [];

        if ($targetUserId === (int)($authUser['id'] ?? 0) && $targetUserData['role'] !== 'admin') {
            $targetUserErrors['role'] = 'You cannot remove your own admin role.';
        }

        if (($targetUserEntity->role ?? '') === 'admin'
            && $targetUserData['role'] !== 'admin'
            && $userRepository->countAdmins() <= 1) {
            $targetUserErrors['role'] = 'The final admin account must keep the admin role.';
        }

        return $targetUserErrors;
    }

    private function refreshAuthUserSession(int $targetUserId, UserRepository $userRepository)
    {
        if ($targetUserId !== $this->currentUserId()) {
            return;
        }

        $authUserEntity = $userRepository->findById($targetUserId);

        if ($authUserEntity === null) {
            return;
        }

        $_SESSION['auth_user'] = $authUserEntity->toArray();
    }

    private function userFormState(int $targetUserId): array
    {
        $state = $_SESSION[self::USER_FORM_STATE] ?? [];
        unset($_SESSION[self::USER_FORM_STATE]);

        if (!is_array($state) || (int)($state['user_id'] ?? 0) !== $targetUserId) {
            return [];
        }

        return $state;
    }

    private function redirectUserForm(
        string $path,
        int $targetUserId,
        array $targetUserErrors,
        array $targetUserData
    ) {
        $_SESSION[self::USER_FORM_STATE] = [
            'user_id' => $targetUserId,
            'errors' => $targetUserErrors,
            'old' => $targetUserData,
        ];

        $this->redirectTo(BASE_URL . $path);
    }
}
