<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\AuthService;
use Throwable;

class AuthController extends Controller
{
    public function login()
    {
        $errors = $_SESSION['login_errors'] ?? [];

        $this->view('auth/login', [
            'errors' => $errors,
            'old' => $_SESSION['login_old'] ?? [],
            'success' => $_SESSION['login_success'] ?? null,
            'hasFieldErrors' => $this->hasLoginFieldErrors($errors),
            'pageScripts' => ['login.js'],
        ]);

        unset($_SESSION['login_errors'], $_SESSION['login_old'], $_SESSION['login_success']);
    }

    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'password' => $_POST['password'] ?? '',
        ];

        try {
            $result = (new AuthService())->login($data);

            if (!$result['success']) {
                $this->redirectLoginWithErrors($result['errors'], $data);
            }

            session_regenerate_id(true);
            $_SESSION['auth_user'] = $result['user'];

            header('Location: ' . BASE_URL . '/');
            exit;
        } catch (Throwable) {
            $this->redirectLoginWithErrors([
                'general' => 'Unable to sign in right now. Please try again.',
            ], $data);
        }
    }

    public function register()
    {
        $errors = $_SESSION['register_errors'] ?? [];

        $this->view('auth/register', [
            'errors' => $errors,
            'old' => $_SESSION['register_old'] ?? [],
            'hasFieldErrors' => $this->hasRegisterFieldErrors($errors),
            'pageScripts' => ['register.js'],
        ]);

        unset($_SESSION['register_errors'], $_SESSION['register_old']);
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/register');
            exit;
        }

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');

        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'confirm_password' => $_POST['confirm_password'] ?? '',
        ];

        try {
            $result = (new AuthService())->register($data, $_FILES['avatar'] ?? null);

            if (!$result['success']) {
                $this->redirectBackWithErrors($result['errors'], $data);
            }

            $_SESSION['login_success'] = 'Account created. Please sign in with your username.';

            header('Location: ' . BASE_URL . '/login');
            exit;
        } catch (Throwable) {
            $this->redirectBackWithErrors([
                'general' => 'Unable to create your account right now. Please try again.',
            ], $data);
        }
    }

    public function logout()
    {
        unset($_SESSION['auth_user'], $_SESSION['user']);
        session_regenerate_id(true);

        header('Location: ' . BASE_URL . '/login');
        exit;
    }

    private function redirectBackWithErrors($errors, array $old = [])
    {
        unset($old['password'], $old['confirm_password']);

        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_old'] = $old;

        header('Location: ' . BASE_URL . '/register');
        exit;
    }

    private function hasRegisterFieldErrors(array $errors): bool
    {
        foreach (['first_name', 'last_name', 'username', 'email', 'password', 'confirm_password'] as $field) {
            if (trim((string) ($errors[$field] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }

    private function hasLoginFieldErrors(array $errors): bool
    {
        foreach (['username', 'password'] as $field) {
            if (trim((string) ($errors[$field] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }

    private function redirectLoginWithErrors($errors, $old)
    {
        unset($old['password']);

        $_SESSION['login_errors'] = $errors;
        $_SESSION['login_old'] = $old;

        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
