<?php

namespace App\Services;

use App\Models\User;

class LoginService
{
    private User $userModel;

    public function __construct(?User $userModel = null)
    {
        $this->userModel = $userModel ?? new User();
    }

    public function login(array $data): array
    {
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'user' => null,
            ];
        }

        $user = $this->userModel->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], (string) ($user['password'] ?? ''))) {
            return [
                'success' => false,
                'errors' => ['general' => 'The email or password is incorrect.'],
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

    public function validate(array $data): array
    {
        $errors = [];

        if ($data['email'] === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        if ($data['password'] === '') {
            $errors['password'] = 'Password is required.';
        }

        return $errors;
    }
}
