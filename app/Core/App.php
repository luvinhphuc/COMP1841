<?php

namespace App\Core;

class App
{
    private const AUTH_ROUTES = [
        'register' => [
            'default_action' => 'register',
            'accepts_child_action' => true,
        ],
        'login' => [
            'default_action' => 'login',
            'accepts_child_action' => true,
        ],
        'logout' => [
            'default_action' => 'logout',
            'accepts_child_action' => false,
        ],
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
    ];

    private const RESERVED_DISCUSSION_ACTIONS = [
        'index',
        'unsolved',
        'show',
    ];

    private const PREFERENCES_ACTION_ROUTES = [
        'profile' => 'updateProfile',
        'avatar' => 'updateAvatar',
        'password' => 'updatePassword',
    ];

    private const MODULE_SELECTION_ROUTES = [
        'onboarding/modules' => ['onboarding', 'modules'],
        'onboarding/modules/save' => ['onboarding', 'saveModules'],
        'preferences/modules' => ['onboarding', 'manageModules'],
        'preferences/modules/save' => ['onboarding', 'savePreferenceModules'],
    ];

    private const ADMIN_ACTION_ROUTES = [
        'users' => 'users',
        'users/edit' => 'editUser',
        'users/update' => 'updateUser',
        'users/delete' => 'deleteUser',
        'modules' => 'modules',
        'modules/store' => 'storeModule',
        'modules/edit' => 'editModule',
        'modules/update' => 'updateModule',
        'modules/delete' => 'deleteModule',
        'posts' => 'posts',
        'posts/status' => 'updatePostStatus',
        'posts/delete' => 'deletePost',
    ];

    protected $controller = 'HomeController';
    protected $action = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = $this->mapRoutes($this->parseUrl());

        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerFile = ROOT_PATH . '/app/Controllers/' . $controllerName . '.php';

            if (!file_exists($controllerFile)) {
                $this->show404();
            }

            $this->controller = $controllerName;
            unset($url[0]);
        }

        $controllerClass = '\\App\\Controllers\\' . $this->controller;

        if (!class_exists($controllerClass)) {
            $this->show404();
        }

        $controller = new $controllerClass();

        if (isset($url[1])) {
            if (!method_exists($controller, $url[1])) {
                $this->show404();
            }

            $this->action = $url[1];
            unset($url[1]);
        }

        $this->params = $url ? array_values($url) : [];

        call_user_func_array([$controller, $this->action], $this->params);
    }

    private function parseUrl()
    {
        if (!isset($_GET['url'])) {
            return [];
        }

        return explode('/', filter_var(rtrim((string) $_GET['url'], '/'), FILTER_SANITIZE_URL));
    }

    private function mapRoutes($url)
    {
        if (!isset($url[0])) {
            return $url;
        }

        if (isset(self::AUTH_ROUTES[$url[0]])) {
            $route = self::AUTH_ROUTES[$url[0]];
            $action = $route['default_action'];

            if ($route['accepts_child_action'] && isset($url[1])) {
                $action = $url[1];
            }

            return ['auth', $action];
        }

        $threePartRoute = implode('/', array_slice($url, 0, 3));
        $twoPartRoute = implode('/', array_slice($url, 0, 2));

        if (isset(self::MODULE_SELECTION_ROUTES[$threePartRoute])) {
            return self::MODULE_SELECTION_ROUTES[$threePartRoute];
        }

        if (isset(self::MODULE_SELECTION_ROUTES[$twoPartRoute])) {
            return self::MODULE_SELECTION_ROUTES[$twoPartRoute];
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
                return $url;
            }

            if (!in_array($url[1], self::RESERVED_DISCUSSION_ACTIONS, true)) {
                if (isset($url[3])) {
                    return $url;
                }

                $mappedRoute = ['discussions', 'show', $url[1]];

                if (isset($url[2])) {
                    $mappedRoute[] = $url[2];
                }

                return $mappedRoute;
            }
        }

        if ($url[0] === 'preferences' && isset($url[1], self::PREFERENCES_ACTION_ROUTES[$url[1]])) {
            return ['preferences', self::PREFERENCES_ACTION_ROUTES[$url[1]]];
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

            $mappedRoute = ['admin', self::ADMIN_ACTION_ROUTES[$routeKey]];

            if (isset($url[3])) {
                $mappedRoute[] = $url[3];
            }

            return $mappedRoute;
        }

        return $url;
    }

    private function show404()
    {
        http_response_code(404);

        require dirname(__DIR__) . '/Views/errors/404.php';

        exit;
    }
}
