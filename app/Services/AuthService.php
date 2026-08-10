<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

/**
 * Applies account validation and password rules around authentication workflows.
 */
class AuthService
{
    private UserRepository $userRepository;

    public function __construct(?UserRepository $userRepository = null)
    {
        $this->userRepository = $userRepository ?? new UserRepository();
    }

    public function login(array $data)
    {
        $errors = $this->validateLogin($data);

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
                'authUser' => null,
            ];
        }

        $userEntity = $this->userRepository->findByUsername($data['username']);

        if (!$userEntity || !password_verify($data['password'], (string) ($userEntity->password ?? ''))) {
            return [
                'success' => false,
                'errors' => ['general' => 'The username or password is incorrect.'],
                'authUser' => null,
            ];
        }

        return [
            'success' => true,
            'errors' => [],
            'authUser' => $userEntity->toArray(),
        ];
    }

    public function register(array $data)
    {
        $data = $this->normaliseAccountData($data);
        $errors = $this->validateAccount($data);
        $errors = array_merge($errors, $this->validatePassword(
            (string) ($data['password'] ?? ''),
            (string) ($data['confirm_password'] ?? '')
        ));

        if (!empty($errors)) {
            return [
                'success' => false,
                'errors' => $errors,
            ];
        }

        $created = $this->userRepository->create([
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

    private function normaliseAccountData(array $data)
    {
        return [
            'first_name' => trim((string) ($data['first_name'] ?? '')),
            'last_name' => trim((string) ($data['last_name'] ?? '')),
            'username' => trim((string) ($data['username'] ?? '')),
            'email' => trim((string) ($data['email'] ?? '')),
            'password' => (string) ($data['password'] ?? ''),
            'confirm_password' => (string) ($data['confirm_password'] ?? ''),
        ];
    }

    public function validateAccount(array $data, int $exceptUserId = 0)
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
        } elseif (mb_strlen($data['username']) > 75) {
            $errors['username'] = 'Username must be 75 characters or fewer.';
        } elseif (!preg_match('/^[A-Za-z0-9_.-]+$/', $data['username'])) {
            $errors['username'] = 'Use only letters, numbers, underscores, dots, or hyphens.';
        } elseif (preg_match('/^deleted_user_[0-9]+$/i', $data['username'])) {
            $errors['username'] = 'This username is reserved.';
        } elseif ($this->userRepository->existsByUsernameExceptUser(
            $data['username'],
            $exceptUserId
        )) {
            $errors['username'] = 'Username is already in use.';
        }

        if ($data['email'] === '') {
            $errors['email'] = 'Email is required.';
        } elseif (mb_strlen($data['email']) > 150) {
            $errors['email'] = 'Email must be 150 characters or fewer.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        } elseif (str_ends_with(strtolower($data['email']), '@deleted.invalid')) {
            $errors['email'] = 'This email address is reserved.';
        } elseif ($this->userRepository->existsByEmailExceptUser($data['email'], $exceptUserId)) {
            $errors['email'] = 'Email is already in use.';
        }

        return $errors;
    }

    public function validatePassword(
        string $password,
        string $confirmation,
        string $passwordField = 'password',
        string $confirmationField = 'confirm_password',
        string $passwordLabel = 'Password'
    ) {
        $errors = [];

        if ($password === '') {
            $errors[$passwordField] = $passwordLabel . ' is required.';
        } elseif (mb_strlen($password) < 8) {
            $errors[$passwordField] = $passwordLabel . ' must be at least 8 characters.';
        } elseif (mb_strlen($password) > 128) {
            $errors[$passwordField] = $passwordLabel . ' must be 128 characters or fewer.';
        }

        if ($confirmation === '') {
            $errors[$confirmationField] = 'Please confirm your password.';
        } elseif (mb_strlen($confirmation) > 128) {
            $errors[$confirmationField] = 'Confirm password must be 128 characters or fewer.';
        } elseif ($password !== $confirmation) {
            $errors[$confirmationField] = 'Passwords do not match.';
        }

        return $errors;
    }

}
