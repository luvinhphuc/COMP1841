<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    private User $userModel;

    public function __construct(?User $userModel = null)
    {
        $this->userModel = $userModel ?? new User();
    }

    public function login(array $data): array
    {
        $errors = $this->validateLogin($data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'user' => null,
            ];
        }

        $user = $this->userModel->findByUsername($data['username']);

        if (!$user || !password_verify($data['password'], (string) ($user['password'] ?? ''))) {
            return [
                'success' => false,
                'errors' => ['general' => 'The username or password is incorrect.'],
                'user' => null,
            ];
        }

        unset($user['password']);

        return [
            'success' => true,
            'errors' => [],
            'user' => $user,
        ];
    }

    public function register(array $data, ?array $avatar): array
    {
        $errors = $this->validateRegister($data);
        $avatarPath = null;

        if (empty($errors)) {
            if ($data['username'] !== '' && $this->userModel->usernameExists($data['username'])) {
                $errors['username'] = 'Username is already taken.';
            }

            if ($data['email'] !== '' && $this->userModel->emailExists($data['email'])) {
                $errors['email'] = 'Email is already registered.';
            }
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        $this->userModel->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'avatar' => $avatarPath,
            'role' => 'student',
        ]);

        return [
            'success' => true,
            'errors' => [],
        ];
    }

    private function validateLogin(array $data): array
    {
        $errors = [];

        if ($data['username'] === '') {
            $errors['username'] = 'Username is required.';
        } elseif (mb_strlen($data['username']) > 75) {
            $errors['username'] = 'Username must be 75 characters or fewer.';
        }

        if ($data['password'] === '') {
            $errors['password'] = 'Password is required.';
        }

        return $errors;
    }

    private function validateRegister(array $data): array
    {
        $errors = [];
        $firstName = trim((string) ($data['first_name'] ?? ''));
        $lastName = trim((string) ($data['last_name'] ?? ''));

        if ($firstName === '') {
            $errors['first_name'] = "What's your first name?";
        } elseif (mb_strlen($firstName) > 35) {
            $errors['first_name'] = 'First name must be 35 characters or fewer.';
        }

        if ($lastName === '') {
            $errors['last_name'] = "What's your last name?";
        } elseif (mb_strlen($lastName) > 35) {
            $errors['last_name'] = 'Last name must be 35 characters or fewer.';
        }

        if ($data['username'] === '') {
            $errors['username'] = 'Username is required.';
        } elseif (mb_strlen($data['username']) > 75) {
            $errors['username'] = 'Username must be 75 characters or fewer.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'Email is required.';
        } elseif (mb_strlen($data['email']) > 150) {
            $errors['email'] = 'Email must be 150 characters or fewer.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($data['password'] === '') {
            $errors['password'] = 'Password is required.';
        } elseif (mb_strlen($data['password']) < 8) {
            $errors['password'] = 'Password must be at least 8 characters.';
        } elseif (mb_strlen($data['password']) > 128) {
            $errors['password'] = 'Password must be 128 characters or fewer.';
        }

        if ($data['confirm_password'] === '') {
            $errors['confirm_password'] = 'Please confirm your password.';
        } elseif (mb_strlen($data['confirm_password']) > 128) {
            $errors['confirm_password'] = 'Confirm password must be 128 characters or fewer.';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        return $errors;
    }
}
