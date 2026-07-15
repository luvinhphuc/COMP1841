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

    public function login(array $data)
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

    public function register(array $data)
    {
        $data = $this->userModel->normaliseAccountData($data);
        $errors = $this->userModel->validateAccount($data);
        $errors = array_merge($errors, $this->userModel->validatePassword(
            (string) ($data['password'] ?? ''),
            (string) ($data['confirm_password'] ?? '')
        ));

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        $created = $this->userModel->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            'avatar' => null,
            'role' => 'student',
        ]);

        if (!$created) {
            return [
                'success' => false,
                'errors' => ['general' => 'Unable to create your account. Please try again.'],
            ];
        }

        return [
            'success' => true,
            'errors' => [],
        ];
    }

    private function validateLogin(array $data)
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

}
