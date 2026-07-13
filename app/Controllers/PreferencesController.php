<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Models\UserModule;
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
        [$userId, $user] = $this->authenticatedUser();
        $profileState = $this->sessionState('preferences_profile_state');
        $avatarState = $this->sessionState('preferences_avatar_state');
        $passwordState = $this->sessionState('preferences_password_state');
        $authUser = $user;
        $showModulePreferences = strtolower(trim((string) ($user['role'] ?? ''))) === 'student';
        $selectedModules = [];

        if ($showModulePreferences) {
            try {
                $selectedModules = (new UserModule())->getModulesByUserId($userId);
            } catch (Throwable) {
                $selectedModules = [];
            }
        }

        unset($authUser['password']);

        $this->view('preferences/index', [
            'authUser' => $authUser,
            'user' => $authUser,
            'profileErrors' => $profileState['errors'] ?? [],
            'profileOld' => $profileState['old'] ?? [],
            'avatarErrors' => $avatarState['errors'] ?? [],
            'passwordErrors' => $passwordState['errors'] ?? [],
            'showModulePreferences' => $showModulePreferences,
            'selectedModules' => $selectedModules,
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
        [$userId] = $this->authenticatedUser();

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectWithState('preferences_profile_state', [
                'errors' => ['general' => 'Your session expired. Please try again.'],
            ]);
        }

        $data = [
            'first_name' => trim((string) ($_POST['first_name'] ?? '')),
            'last_name' => trim((string) ($_POST['last_name'] ?? '')),
            'username' => trim((string) ($_POST['username'] ?? '')),
        ];
        $errors = $this->validateProfile($data);

        try {
            if (!isset($errors['username'])
                && $this->userModel->usernameExistsExceptUser($data['username'], $userId)) {
                $errors['username'] = 'Username is already taken.';
            }

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
        [$userId, $user] = $this->authenticatedUser();

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectWithState('preferences_avatar_state', [
                'errors' => ['general' => 'Your session expired. Please try again.'],
            ]);
        }

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
        [$userId, $user] = $this->authenticatedUser();

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectWithState('preferences_password_state', [
                'errors' => ['general' => 'Your session expired. Please try again.'],
            ]);
        }

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
            unset($_SESSION['auth_user'], $_SESSION['user']);
            $this->redirectTo(BASE_URL . '/login');
        }

        return [$userId, $user];
    }

    private function refreshAuthSession(int $userId)
    {
        $user = $this->userModel->find($userId);

        if ($user === null) {
            unset($_SESSION['auth_user'], $_SESSION['user']);
            $this->redirectTo(BASE_URL . '/login');
        }

        unset($user['password']);
        $_SESSION['auth_user'] = $user;

        if (isset($_SESSION['user'])) {
            $_SESSION['user'] = $user;
        }
    }

    private function validateProfile(array $data)
    {
        $errors = [];

        if ($data['first_name'] === '') {
            $errors['first_name'] = 'First name is required.';
        } elseif (mb_strlen($data['first_name']) > 50) {
            $errors['first_name'] = 'First name must be 50 characters or fewer.';
        }

        if ($data['last_name'] === '') {
            $errors['last_name'] = 'Last name is required.';
        } elseif (mb_strlen($data['last_name']) > 50) {
            $errors['last_name'] = 'Last name must be 50 characters or fewer.';
        }

        if ($data['username'] === '') {
            $errors['username'] = 'Username is required.';
        } elseif (mb_strlen($data['username']) > 50) {
            $errors['username'] = 'Username must be 50 characters or fewer.';
        } elseif (!preg_match('/^[A-Za-z0-9_.-]+$/', $data['username'])) {
            $errors['username'] = 'Use only letters, numbers, underscores, dots, or hyphens.';
        }

        return $errors;
    }

    private function validatePassword(array $data, array $user)
    {
        $errors = [];

        if ($data['current_password'] === '') {
            $errors['current_password'] = 'Current password is required.';
        } elseif (!password_verify($data['current_password'], (string) ($user['password'] ?? ''))) {
            $errors['current_password'] = 'Current password is incorrect.';
        }

        if ($data['new_password'] === '') {
            $errors['new_password'] = 'New password is required.';
        } elseif (mb_strlen($data['new_password']) < 8) {
            $errors['new_password'] = 'New password must be at least 8 characters.';
        } elseif (mb_strlen($data['new_password']) > 128) {
            $errors['new_password'] = 'New password must be 128 characters or fewer.';
        }

        if ($data['confirm_password'] === '') {
            $errors['confirm_password'] = 'Please confirm your new password.';
        } elseif (mb_strlen($data['confirm_password']) > 128) {
            $errors['confirm_password'] = 'Confirm password must be 128 characters or fewer.';
        } elseif ($data['new_password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

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
