<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\AttachmentService;
use Throwable;

class PreferencesController extends Controller
{
    private ?User $userModel;

    public function __construct(?User $userModel = null)
    {
        $this->userModel = $userModel;
    }

    public function index()
    {
        [, $user] = $this->authenticatedUser();
        $profileState = $this->sessionState('preferences_profile_state');
        $avatarState = $this->sessionState('preferences_avatar_state');
        $passwordState = $this->sessionState('preferences_password_state');
        $authUser = $user;
        unset($authUser['password']);

        $this->view('preferences/index', [
            'authUser' => $authUser,
            'user' => $authUser,
            'profileErrors' => $profileState['errors'] ?? [],
            'profileOld' => $profileState['old'] ?? [],
            'avatarErrors' => $avatarState['errors'] ?? [],
            'passwordErrors' => $passwordState['errors'] ?? [],
        ]);

        unset(
            $_SESSION['preferences_profile_state'],
            $_SESSION['preferences_avatar_state'],
            $_SESSION['preferences_password_state']
        );
    }

    public function updateProfile()
    {
        $this->requirePost(BASE_URL . '/preferences');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectWithState('preferences_profile_state', [
                'errors' => ['general' => 'Your session expired. Please try again.'],
            ]);
        }

        [$userId] = $this->authenticatedUser();

        $data = $this->userModel->normaliseAccountData([
            'first_name' => trim((string) ($_POST['first_name'] ?? '')),
            'last_name' => trim((string) ($_POST['last_name'] ?? '')),
            'username' => trim((string) ($_POST['username'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
        ]);

        try {
            $errors = $this->userModel->validateAccount($data, $userId);

            if (!empty($errors)) {
                $this->redirectWithState('preferences_profile_state', [
                    'errors' => $errors,
                    'old' => $data,
                ]);
            }

            if (!$this->userModel->updateProfile($userId, $data)) {
                $this->redirectWithState('preferences_profile_state', [
                    'errors' => ['general' => 'Unable to update your profile. Please try again.'],
                    'old' => $data,
                ]);
            }

            $this->refreshAuthSession($userId);
            $this->redirectWithToast(BASE_URL . '/preferences', [
                'type' => 'success',
                'title' => 'Profile updated',
                'message' => 'Your profile information has been saved.',
            ]);
        } catch (Throwable) {
            $this->redirectWithState('preferences_profile_state', [
                'errors' => ['general' => 'Unable to update your profile right now. Please try again.'],
                'old' => $data,
            ]);
        }
    }

    public function updateAvatar()
    {
        $this->requirePost(BASE_URL . '/preferences');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectWithState('preferences_avatar_state', [
                'errors' => ['general' => 'Your session expired. Please try again.'],
            ]);
        }

        [$userId, $user] = $this->authenticatedUser();

        $attachmentService = new AttachmentService();
        $storedAvatar = null;

        try {
            $avatar = $attachmentService->validatedAvatar($_FILES['avatar'] ?? null);
            $avatarError = trim((string) ($avatar['error'] ?? ''));

            if ($avatarError !== '') {
                $this->redirectWithState('preferences_avatar_state', [
                    'errors' => ['avatar' => $avatarError],
                ]);
            }

            $storedAvatar = $attachmentService->storeAvatar($avatar);

            if ($storedAvatar === null) {
                $this->redirectWithState('preferences_avatar_state', [
                    'errors' => ['avatar' => 'The avatar could not be saved. Please choose another image.'],
                ]);
            }

            if (!$this->userModel->updateAvatar($userId, $storedAvatar)) {
                $attachmentService->removeStoredAttachment(['path' => $storedAvatar]);
                $this->redirectWithState('preferences_avatar_state', [
                    'errors' => ['general' => 'Unable to update your avatar. Please try again.'],
                ]);
            }

            $oldAvatar = ltrim(str_replace('\\', '/', trim((string) ($user['avatar'] ?? ''))), '/');

            if ($oldAvatar !== $storedAvatar
                && preg_match('#^uploads/avatars/[A-Za-z0-9._-]+$#', $oldAvatar)) {
                $attachmentService->removeStoredAttachment(['path' => $oldAvatar]);
            }

            $storedAvatar = null;
            $this->refreshAuthSession($userId);
            $this->redirectWithToast(BASE_URL . '/preferences', [
                'type' => 'success',
                'title' => 'Avatar updated',
                'message' => 'Your new avatar has been saved.',
            ]);
        } catch (Throwable) {
            if ($storedAvatar !== null) {
                $attachmentService->removeStoredAttachment(['path' => $storedAvatar]);
            }

            $this->redirectWithState('preferences_avatar_state', [
                'errors' => ['general' => 'Unable to update your avatar right now. Please try again.'],
            ]);
        }
    }

    public function updatePassword()
    {
        $this->requirePost(BASE_URL . '/preferences');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectWithState('preferences_password_state', [
                'errors' => ['general' => 'Your session expired. Please try again.'],
            ]);
        }

        [$userId, $user] = $this->authenticatedUser();

        $data = [
            'current_password' => (string) ($_POST['current_password'] ?? ''),
            'new_password' => (string) ($_POST['new_password'] ?? ''),
            'confirm_password' => (string) ($_POST['confirm_password'] ?? ''),
        ];
        $errors = $this->validatePassword($data, $user);

        if (!empty($errors)) {
            $this->redirectWithState('preferences_password_state', [
                'errors' => $errors,
            ]);
        }

        try {
            $passwordHash = password_hash($data['new_password'], PASSWORD_DEFAULT);

            if (!$this->userModel->updatePassword($userId, $passwordHash)) {
                $this->redirectWithState('preferences_password_state', [
                    'errors' => ['general' => 'Unable to change your password. Please try again.'],
                ]);
            }

            $this->redirectWithToast(BASE_URL . '/preferences', [
                'type' => 'success',
                'title' => 'Password changed',
                'message' => 'Your password has been updated successfully.',
            ]);
        } catch (Throwable) {
            $this->redirectWithState('preferences_password_state', [
                'errors' => ['general' => 'Unable to change your password right now. Please try again.'],
            ]);
        }
    }

    private function authenticatedUser()
    {
        $userId = $this->currentUserId();

        if ($userId === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        $this->userModel = $this->userModel ?? new User();

        try {
            $user = $this->userModel->find($userId);
        } catch (Throwable) {
            $user = null;
        }

        if ($user === null) {
            unset($_SESSION['auth_user']);
            $this->redirectTo(BASE_URL . '/login');
        }

        return [$userId, $user];
    }

    private function refreshAuthSession(int $userId)
    {
        $user = $this->userModel->find($userId);

        if ($user === null) {
            unset($_SESSION['auth_user']);
            $this->redirectTo(BASE_URL . '/login');
        }

        unset($user['password']);
        $_SESSION['auth_user'] = $user;
    }

    private function validatePassword(array $data, array $user)
    {
        $errors = [];

        if ($data['current_password'] === '') {
            $errors['current_password'] = 'Current password is required.';
        } elseif (!password_verify($data['current_password'], (string) ($user['password'] ?? ''))) {
            $errors['current_password'] = 'Current password is incorrect.';
        }

        $errors = array_merge($errors, $this->userModel->validatePassword(
            $data['new_password'],
            $data['confirm_password'],
            'new_password',
            'confirm_password',
            'New password'
        ));

        return $errors;
    }

    private function sessionState(string $key)
    {
        $state = $_SESSION[$key] ?? [];

        return is_array($state) ? $state : [];
    }

    private function redirectWithState(string $key, array $state)
    {
        $_SESSION[$key] = $state;
        $this->redirectTo(BASE_URL . '/preferences');
    }
}
