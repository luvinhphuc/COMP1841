<?php

namespace App\Services;

use App\Models\User;

class RegisterService
{
    private User $userModel;

    public function __construct(?User $userModel = null)
    {
        $this->userModel = $userModel ?? new User();
    }

    public function register(array $data, ?array $avatar): array
    {
        $errors = $this->validate($data);
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
            'full_name' => $data['full_name'],
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

    public function validate(array $data): array
    {
        $errors = [];

        if ($data['full_name'] === '') {
            $errors['full_name'] = 'Full name is required.';
        }

        if ($data['username'] === '') {
            $errors['username'] = 'Username is required.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'Email is required.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (!$this->isGreenwichEmail($data['email'])) {
            $errors['email'] = 'Please use your @gre.ac.uk email address.';
        }

        if ($data['password'] === '') {
            $errors['password'] = 'Password is required.';
        }

        if ($data['confirm_password'] === '') {
            $errors['confirm_password'] = 'Please confirm your password.';
        } elseif ($data['password'] !== $data['confirm_password']) {
            $errors['confirm_password'] = 'Passwords do not match.';
        }

        return $errors;
    }

    private function isGreenwichEmail(string $email): bool
    {
        return str_ends_with(strtolower($email), '@gre.ac.uk');
    }
}
