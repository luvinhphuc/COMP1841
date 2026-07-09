<?php

namespace App\Core;

class App
{
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

        if ($url[0] === 'register') {
            return ['auth', $url[1] ?? 'register'];
        }

        if ($url[0] === 'login') {
            return ['auth', $url[1] ?? 'login'];
        }

        if ($url[0] === 'logout') {
            return ['auth', 'logout'];
        }

        if ($url[0] === 'discussions') {
            if (!isset($url[1]) && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                return ['post', 'store'];
            }

            $discussionActionMap = [
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

            if (isset($url[1], $discussionActionMap[$url[1]])) {
                $mappedRoute = [
                    $discussionActionMap[$url[1]][0],
                    $discussionActionMap[$url[1]][1],
                ];

                if (isset($url[2])) {
                    $mappedRoute[] = $url[2];
                }

                return $mappedRoute;
            }

            if (!isset($url[1])) {
                return $url;
            }

            $reservedDiscussionActions = [
                'index',
                'create',
                'store',
                'edit',
                'update',
                'delete',
                'destroy',
                'reply',
                'reply-edit',
                'reply-update',
                'reply-delete',
                'reply-destroy',
                'unsolved',
                'show',
            ];

            if (!in_array($url[1], $reservedDiscussionActions, true)) {
                return ['discussions', 'show', $url[1]];
            }
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
