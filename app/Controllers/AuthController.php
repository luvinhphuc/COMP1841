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
            'pageScripts' => ['form-utils.js', 'login.js'],
        ]);

        unset($_SESSION['login_errors'], $_SESSION['login_old'], $_SESSION['login_success']);
    }

    public function authenticate()
    {
        $this->requirePost(BASE_URL . '/login');

        $data = [
            'username' => trim((string) ($_POST['username'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
        ];

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectLoginWithErrors([
                'general' => 'Your session expired. Please try again.',
            ], $data);
        }

        try {
            $result = (new AuthService())->login($data);

            if (!$result['success']) {
                $this->redirectLoginWithErrors($result['errors'], $data);
            }

            session_regenerate_id(true);
            $_SESSION['auth_user'] = $result['user'];

            $this->redirectTo(BASE_URL . '/dashboard');
        } catch (Throwable) {
            unset($_SESSION['auth_user']);
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
            'pageScripts' => ['form-utils.js', 'register.js'],
        ]);

        unset($_SESSION['register_errors'], $_SESSION['register_old']);
    }

    public function store()
    {
        $this->requirePost(BASE_URL . '/register');

        $firstName = trim((string) ($_POST['first_name'] ?? ''));
        $lastName = trim((string) ($_POST['last_name'] ?? ''));

        $data = [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'username' => trim((string) ($_POST['username'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'password' => (string) ($_POST['password'] ?? ''),
            'confirm_password' => (string) ($_POST['confirm_password'] ?? ''),
        ];

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectBackWithErrors([
                'general' => 'Your session expired. Please try again.',
            ], $data);
        }

        try {
            $result = (new AuthService())->register($data);

            if (!$result['success']) {
                $this->redirectBackWithErrors($result['errors'], $data);
            }

            $_SESSION['login_success'] = 'Account created. Please sign in with your username.';

            $this->redirectTo(BASE_URL . '/login');
        } catch (Throwable) {
            $this->redirectBackWithErrors([
                'general' => 'Unable to create your account right now. Please try again.',
            ], $data);
        }
    }

    public function logout()
    {
        $this->requirePost(BASE_URL . '/dashboard');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectWithToast(BASE_URL . '/dashboard', [
                'type' => 'error',
                'title' => 'Unable to sign out',
                'message' => 'Your session expired. Please try again.',
            ]);
        }

        session_unset();
        session_regenerate_id(true);

        $this->redirectTo(BASE_URL . '/login');
    }

    private function redirectBackWithErrors($errors, $old = [])
    {
        unset($old['password'], $old['confirm_password']);

        $_SESSION['register_errors'] = $errors;
        $_SESSION['register_old'] = $old;

        $this->redirectTo(BASE_URL . '/register');
    }

    private function hasRegisterFieldErrors($errors)
    {
        foreach (['first_name', 'last_name', 'username', 'email', 'password', 'confirm_password'] as $field) {
            if (trim((string) ($errors[$field] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }

    private function hasLoginFieldErrors($errors)
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

        $this->redirectTo(BASE_URL . '/login');
    }

}
