<?php

namespace App\Core;

use App\Helpers\PermissionHelper;
use App\Repositories\UserRepository;
use App\Services\NavigationService;
use RuntimeException;
use Throwable;

/**
 * Provides shared rendering, authentication, CSRF, redirect, and error-response helpers.
 */
class Controller
{
    private bool $authUserResolved = false;
    private ?array $authUser = null;

    protected function view(string $viewPath, array $data = [])
    {
        // Every page receives the same navigation, account, security, and asset state.
        $navigation = new NavigationService();

        if (!array_key_exists('navbarModuleLinks', $data)) {
            $data['navbarModuleLinks'] = $navigation->moduleLinks();
        }

        if (!array_key_exists('navbarActiveMenuKey', $data)) {
            $data['navbarActiveMenuKey'] = $navigation->activeMenuKey(
                (string) ($_GET['url'] ?? '')
            );
        }

        if (!array_key_exists('authUser', $data)) {
            $data['authUser'] = $this->currentUser();
        }

        $data = array_merge($data, $navigation->authDisplay($data['authUser']));
        $authRole = strtolower(
            trim((string) ($data['authUser']['role'] ?? 'student'))
        );

        $data['authRole'] = in_array(
            $authRole,
            ['student', 'tutor', 'admin'],
            true
        ) ? $authRole : 'student';
        $data['isAdmin'] = PermissionHelper::isAdmin($data['authUser']);

        if (!array_key_exists('pageScriptUrls', $data)) {
            $pageScripts = $data['pageScripts'] ?? [];

            if (!in_array('site-animations.js', $pageScripts, true)) {
                array_unshift($pageScripts, 'site-animations.js');
            }

            $data['pageScriptUrls'] = $this->pageScriptUrls($pageScripts);
        }

        $data['navbarScriptUrl'] = $this->assetScriptUrl('navbar.js');
        $data['showPasswordScriptUrl'] = $this->assetScriptUrl('show-password.js');
        $data['csrfToken'] = $this->csrfToken();
        $data['flashToast'] = $this->flashToast();

        if (!empty($data)) {
            extract($data);
        }

        $mainViewFile = ROOT_PATH . '/app/Views/' . $viewPath . '.php';

        if (!file_exists($mainViewFile)) {
            throw new RuntimeException('View not found: ' . $viewPath);
        }

        require ROOT_PATH . '/app/Views/layouts/header.php';
        require ROOT_PATH . '/app/Views/layouts/navbar.php';
        echo '<main id="main-content">';
        require $mainViewFile;
        require ROOT_PATH . '/app/Views/layouts/footer.php';
    }

    protected function csrfToken()
    {
        // A session-scoped random token protects every state-changing form submission.
        if (empty($_SESSION['_csrf_token']) || !is_string($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    protected function verifyCsrfToken(?string $token)
    {
        $sessionToken = $_SESSION['_csrf_token'] ?? '';

        return is_string($sessionToken)
            && is_string($token)
            && $sessionToken !== ''
            && hash_equals($sessionToken, $token);
    }

    protected function requirePost(string $redirectUrl)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectTo($redirectUrl);
        }
        elseif (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->forbidden();
        }
    }

    protected function redirectTo(string $url)
    {
        header('Location: ' . $url);
        exit;
    }

    protected function currentUser()
    {
        if ($this->authUserResolved) {
            return $this->authUser;
        }

        // Refresh the session snapshot from the database so role/deletion changes apply immediately.
        $this->authUserResolved = true;
        $storedAuthUser = $_SESSION['auth_user'] ?? null;

        if (!is_array($storedAuthUser)) {
            return null;
        }

        $authUserId = filter_var($storedAuthUser['id'] ?? 0, FILTER_VALIDATE_INT);

        if ($authUserId === false || $authUserId <= 0) {
            unset($_SESSION['auth_user']);
            return null;
        }

        try {
            $authUserEntity = (new UserRepository())->findById($authUserId);
        } catch (Throwable $exception) {
            error_log('Authenticated user could not be refreshed: ' . $exception->getMessage());
            unset($_SESSION['auth_user']);
            return null;
        }

        if ($authUserEntity === null) {
            unset($_SESSION['auth_user']);
            return null;
        }

        $this->authUser = $authUserEntity->toArray();
        $_SESSION['auth_user'] = $this->authUser;

        return $this->authUser;
    }

    protected function currentUserId()
    {
        $authUser = $this->currentUser();

        if ($authUser === null) {
            return null;
        }

        $userId = filter_var($authUser['id'] ?? 0, FILTER_VALIDATE_INT);

        return $userId > 0 ? $userId : null;
    }

    protected function notFound()
    {
        http_response_code(404);
        require ROOT_PATH . '/app/Views/errors/404.php';
        exit;
    }

    protected function redirectWithToast(string $url, array $toast)
    {
        $type = trim((string) ($toast['type'] ?? 'info'));

        $_SESSION['flash_toast'] = [
            'type' => $type !== '' ? $type : 'info',
            'title' => trim((string) ($toast['title'] ?? '')),
            'message' => trim((string) ($toast['message'] ?? '')),
        ];

        $this->redirectTo($url);
    }

    protected function forbidden(string $redirectUrl = '')
    {
        http_response_code(403);
        require ROOT_PATH . '/app/Views/errors/403.php';
        exit;
    }

    private function flashToast()
    {
        $toast = $_SESSION['flash_toast'] ?? null;
        unset($_SESSION['flash_toast']);

        if (!is_array($toast)) {
            return null;
        }

        $message = trim((string) ($toast['message'] ?? ''));

        if ($message === '') {
            return null;
        }

        $type = trim((string) ($toast['type'] ?? 'info'));

        return [
            'type' => $type !== '' ? $type : 'info',
            'title' => trim((string) ($toast['title'] ?? '')),
            'message' => $message,
        ];
    }

    private function pageScriptUrls(array $pageScripts)
    {
        $scriptUrls = [];

        foreach ($pageScripts as $pageScript) {
            $scriptUrl = $this->assetScriptUrl($pageScript);

            if ($scriptUrl !== '') {
                $scriptUrls[] = $scriptUrl;
            }
        }

        return $scriptUrls;
    }

    private function assetScriptUrl(string $script)
    {
        // basename prevents callers from escaping the JavaScript asset directory.
        $scriptName = basename($script);
        $scriptPath = ROOT_PATH . '/public/assets/js/' . $scriptName;

        if (!is_file($scriptPath)) {
            return '';
        }

        return BASE_URL . '/assets/js/' . rawurlencode($scriptName) . '?v=' . filemtime($scriptPath);
    }
}
