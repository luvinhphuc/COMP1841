<?php

namespace App\Core;

use App\Controllers\Admin\ContactController as AdminContactController;
use App\Controllers\Admin\ModuleController as AdminModuleController;
use App\Controllers\Admin\PostController as AdminPostController;
use App\Controllers\Admin\UserController as AdminUserController;

/**
 * Resolves request paths to controller actions and enforces route-level access rules.
 */
class App
{
    // Explicit route maps keep public URLs independent from internal class names.
    private const AUTH_ROUTES = [
        'login' => ['method' => 'GET', 'action' => 'login'],
        'login/authenticate' => ['method' => 'POST', 'action' => 'authenticate'],
        'register' => ['method' => 'GET', 'action' => 'register'],
        'register/store' => ['method' => 'POST', 'action' => 'store'],
        'logout' => ['method' => 'POST', 'action' => 'logout'],
    ];

    private const DISCUSSION_ACTION_ROUTES = [
        'create' => ['post', 'create'],
        'store' => ['post', 'store'],
        'edit' => ['post', 'edit'],
        'update' => ['post', 'update'],
        'delete' => ['post', 'delete'],
        'destroy' => ['post', 'destroy'],
        'reply' => ['reply', 'store'],
        'reply-edit' => ['reply', 'edit'],
        'reply-update' => ['reply', 'update'],
        'reply-delete' => ['reply', 'delete'],
        'reply-destroy' => ['reply', 'destroy'],
        'reply-mark-solved' => ['reply', 'markAsSolved'],
        'reply-unmark-solved' => ['reply', 'unmarkAsSolved'],
    ];

    private const RESERVED_DISCUSSION_ACTIONS = [
        'index',
        'unsolved',
        'show',
    ];

    private const PROFILE_PREFERENCE_ACTION_ROUTES = [
        'profile' => 'updateProfile',
        'avatar' => 'updateAvatar',
        'password' => 'updatePassword',
    ];

    private const PROFILE_ROUTES = [
        'questions' => 'questions',
        'preferences' => 'preferences',
    ];

    private const ADMIN_ACTION_ROUTES = [
        'users' => [AdminUserController::class, 'index'],
        'users/edit' => [AdminUserController::class, 'edit'],
        'users/update' => [AdminUserController::class, 'update'],
        'users/delete' => [AdminUserController::class, 'delete'],
        'modules' => [AdminModuleController::class, 'index'],
        'modules/store' => [AdminModuleController::class, 'store'],
        'modules/edit' => [AdminModuleController::class, 'edit'],
        'modules/update' => [AdminModuleController::class, 'update'],
        'modules/delete' => [AdminModuleController::class, 'delete'],
        'posts' => [AdminPostController::class, 'index'],
        'posts/delete' => [AdminPostController::class, 'delete'],
        'contacts' => [AdminContactController::class, 'index'],
        'contacts/show' => [AdminContactController::class, 'show'],
        'contacts/read' => [AdminContactController::class, 'markAsRead'],
        'contacts/status' => [AdminContactController::class, 'updateStatus'],
        'contacts/delete' => [AdminContactController::class, 'delete'],
    ];

    protected $controller = 'HomeController';
    protected $action = 'index';
    protected $params = [];

    public function __construct()
    {
        // Mapping runs before reflection so only approved routes reach controller methods.
        $url = $this->mapRoutes($this->parseUrl());
        $controllerIdentifier = $this->controller;

        if (isset($url[0])) {
            $controllerIdentifier = (string)$url[0];
            unset($url[0]);
        }

        $controllerClass = $this->resolveControllerClass($controllerIdentifier);
        $this->controller = $controllerClass;
        $controller = new $controllerClass();

        if (isset($url[1])) {
            if (!method_exists($controller, $url[1])) {
                $this->notFound();
            }

            $this->action = $url[1];
            unset($url[1]);
        }

        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$controller, $this->action], $this->params);
    }

    private function resolveControllerClass(string $controllerIdentifier)
    {
        $controllerIdentifier = ltrim(trim($controllerIdentifier), '\\');

        if ($controllerIdentifier === '') {
            $this->notFound();
        }

        if (str_contains($controllerIdentifier, '\\')) {
            $controllerClass = $controllerIdentifier;
        } else {
            $controllerName = str_ends_with($controllerIdentifier, 'Controller')
                ? ucfirst($controllerIdentifier)
                : ucfirst($controllerIdentifier) . 'Controller';
            $controllerClass = 'App\\Controllers\\' . $controllerName;
        }

        if (!class_exists($controllerClass)
            || !is_subclass_of($controllerClass, Controller::class)) {
            $this->notFound();
        }

        return $controllerClass;
    }

    private function parseUrl()
    {
        if (!isset($_GET['url'])) {
            return [];
        }

        return explode('/', filter_var(rtrim((string)$_GET['url'], '/'), FILTER_SANITIZE_URL));
    }

    private function mapRoutes($url)
    {
        if (!isset($url[0])) {
            return $url;
        }

        if (in_array($url[0], ['auth', 'login', 'register', 'logout'], true)) {
            $routeKey = implode('/', $url);
            $route = self::AUTH_ROUTES[$routeKey] ?? null;
            $requestMethod = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

            if ($route === null || $route['method'] !== $requestMethod) {
                return ['auth', 'routeNotFound'];
            }

            return ['auth', $route['action']];
        }

        if ($url[0] === 'privacy-policy') {
            return isset($url[1])
                ? ['home', 'routeNotFound']
                : ['home', 'privacy'];
        }

        if ($url[0] === 'search') {
            return ['discussion', 'index'];
        }

        if ($url[0] === 'discussions') {
            if (!isset($url[1]) && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                return ['post', 'store'];
            }

            if (isset($url[1], self::DISCUSSION_ACTION_ROUTES[$url[1]])) {
                $mappedRoute = self::DISCUSSION_ACTION_ROUTES[$url[1]];

                if (isset($url[2])) {
                    $mappedRoute[] = $url[2];
                }

                return $mappedRoute;
            }

            if (!isset($url[1])) {
                return ['discussion', 'index'];
            }

            if (!in_array($url[1], self::RESERVED_DISCUSSION_ACTIONS, true)) {
                if (isset($url[3])) {
                    return $url;
                }

                $mappedRoute = ['discussion', 'show', $url[1]];

                if (isset($url[2])) {
                    $mappedRoute[] = $url[2];
                }

                return $mappedRoute;
            }
        }

        if ($url[0] === 'modules') {
            $url[0] = 'module';
            return $url;
        }

        if ($url[0] === 'profile') {
            $requestMethod = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

            if ($requestMethod === 'POST') {
                if (isset($url[1], $url[2])
                    && $url[1] === 'preferences'
                    && isset(self::PROFILE_PREFERENCE_ACTION_ROUTES[$url[2]])
                    && !isset($url[3])) {
                    return ['profile', self::PROFILE_PREFERENCE_ACTION_ROUTES[$url[2]]];
                }

                return ['profile', 'routeNotFound'];
            }

            if ($requestMethod !== 'GET') {
                return ['profile', 'routeNotFound'];
            }

            if (!isset($url[1])) {
                return ['profile', 'index'];
            }

            if (isset(self::PROFILE_ROUTES[$url[1]]) && !isset($url[2])) {
                return ['profile', self::PROFILE_ROUTES[$url[1]]];
            }

            return ['profile', 'routeNotFound'];
        }

        if ($url[0] === 'preferences') {
            $requestMethod = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));

            if ($requestMethod === 'GET' && !isset($url[1])) {
                return ['profile', 'preferences'];
            }

            if ($requestMethod === 'POST'
                && isset($url[1], self::PROFILE_PREFERENCE_ACTION_ROUTES[$url[1]])
                && !isset($url[2])) {
                return ['profile', self::PROFILE_PREFERENCE_ACTION_ROUTES[$url[1]]];
            }

            return ['profile', 'routeNotFound'];
        }

        if ($url[0] === 'admin') {
            if (!isset($url[1])) {
                return ['admin', 'index'];
            }

            $routeKey = $url[1];

            if (isset($url[2])) {
                $routeKey .= '/' . $url[2];
            }

            if (!isset(self::ADMIN_ACTION_ROUTES[$routeKey])) {
                return ['admin', 'routeNotFound'];
            }

            $mappedRoute = self::ADMIN_ACTION_ROUTES[$routeKey];

            if (isset($url[3])) {
                $mappedRoute[] = $url[3];
            }

            return $mappedRoute;
        }

        return $url;
    }

    private function notFound()
    {
        http_response_code(404);

        require dirname(__DIR__) . '/Views/errors/404.php';

        exit;
    }
}
