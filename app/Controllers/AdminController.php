<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\PermissionHelper;
use App\Models\Module;
use App\Models\Post;
use App\Models\User;
use Throwable;

class AdminController extends Controller
{
    private const PAGE_LIMIT = 5;
    private const USER_ROLES = ['student', 'tutor', 'admin'];
    private const USER_FORM_STATE = 'admin_user_form_state';

    public function index()
    {
        $this->requireAdmin();

        try {
            $userCount = (new User())->countAll();
            $moduleCount = (new Module())->countAll();
            $postCounts = (new Post())->getAdminCounts();
        } catch (Throwable) {
            $userCount = 0;
            $moduleCount = 0;
            $postCounts = ['total' => 0, 'open' => 0, 'solved' => 0];
        }

        $this->view('admin/index', [
            'adminSection' => 'overview',
            'pageTitle' => 'Admin Overview',
            'userCount' => $userCount,
            'moduleCount' => $moduleCount,
            'postCounts' => $postCounts,
        ]);
    }

    public function users()
    {
        $this->requireAdmin();
        $page = $this->pageNumber();

        try {
            $model = new User();
            $total = $model->countAll();
            $pagination = $this->pagination('/admin/users', $total, $page);
            $users = $model->getPaginated(self::PAGE_LIMIT, $pagination['offset']);
        } catch (Throwable) {
            $users = [];
            $pagination = $this->pagination('/admin/users', 0, 1);
        }

        $this->view('admin/users', [
            'adminSection' => 'users',
            'pageTitle' => 'Manage Users',
            'pageScripts' => ['confirm-modal.js'],
            'users' => $users,
            'pagination' => $pagination,
            'currentUserId' => $this->currentUserId(),
        ]);
    }

    public function createUser()
    {
        $this->requireAdmin();
        $state = $this->userFormState('create');
        $user = array_merge([
            'id' => 0,
            'first_name' => '',
            'last_name' => '',
            'username' => '',
            'email' => '',
            'role' => 'student',
        ], $state['old'] ?? []);

        $this->view('admin/user-edit', [
            'adminSection' => 'users',
            'pageTitle' => 'Create User',
            'mode' => 'create',
            'formAction' => BASE_URL . '/admin/users/store',
            'user' => $user,
            'errors' => $state['errors'] ?? [],
            'roles' => self::USER_ROLES,
            'currentUserId' => $this->currentUserId(),
        ]);
    }

    public function storeUser()
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/users/create');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectUserForm('/admin/users/create', 'create', 0, [
                'general' => 'The security token is invalid. Please try again.',
            ], []);
        }

        $data = $this->userData(true);

        try {
            $model = new User();
            $errors = $this->validateUser($data, 0, $model, true);

            if (!empty($errors)) {
                $this->redirectUserForm('/admin/users/create', 'create', 0, $errors, $data);
            }

            if (!$model->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'avatar' => null,
                'role' => $data['role'],
            ])) {
                $this->redirectUserForm('/admin/users/create', 'create', 0, [
                    'general' => 'The user could not be created.',
                ], $data);
            }
        } catch (Throwable) {
            $this->redirectUserForm('/admin/users/create', 'create', 0, [
                'general' => 'The user could not be created right now.',
            ], $data);
        }

        $this->adminSuccess('/admin/users', 'User created successfully.');
    }

    public function editUser($id = 0)
    {
        $this->requireAdmin();
        $user = $this->findUser((int) $id);

        if ($user === null) {
            $this->notFound();
        }

        unset($user['password']);
        $state = $this->userFormState('edit', (int) $user['id']);
        $user = array_merge($user, $state['old'] ?? []);

        $this->view('admin/user-edit', [
            'adminSection' => 'users',
            'pageTitle' => 'Edit User',
            'mode' => 'edit',
            'formAction' => BASE_URL . '/admin/users/update/' . (int) $user['id'],
            'user' => $user,
            'errors' => $state['errors'] ?? [],
            'roles' => self::USER_ROLES,
            'currentUserId' => $this->currentUserId(),
        ]);
    }

    public function updateUser($id = 0)
    {
        $admin = $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/users');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->adminError('/admin/users', 'The security token is invalid. Please try again.');
        }

        $userId = (int) $id;
        $user = $this->findUser($userId);

        if ($user === null) {
            $this->notFound();
        }

        $data = $this->userData();

        try {
            $model = new User();
            $errors = $this->validateUser($data, $userId, $model);

            if ($userId === (int) ($admin['id'] ?? 0) && $data['role'] !== 'admin') {
                $errors['role'] = 'You cannot remove your own admin role.';
            }

            if (($user['role'] ?? '') === 'admin'
                && $data['role'] !== 'admin'
                && $model->countAdmins() <= 1) {
                $errors['role'] = 'The final admin account must keep the admin role.';
            }

            if (!empty($errors)) {
                $this->redirectUserForm(
                    '/admin/users/edit/' . $userId,
                    'edit',
                    $userId,
                    $errors,
                    $data
                );
            }

            if (!$model->updateFromAdmin($userId, $data)) {
                $this->redirectUserForm('/admin/users/edit/' . $userId, 'edit', $userId, [
                    'general' => 'The user could not be updated.',
                ], $data);
            }

            $this->refreshCurrentUserSession($userId, $model);
        } catch (Throwable) {
            $this->redirectUserForm('/admin/users/edit/' . $userId, 'edit', $userId, [
                'general' => 'The user could not be updated right now.',
            ], $data);
        }

        $this->adminSuccess('/admin/users', 'User updated successfully.');
    }

    public function deleteUser($id = 0)
    {
        $admin = $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/users');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->adminError('/admin/users', 'The security token is invalid. Please try again.');
        }

        $userId = (int) $id;

        if ($userId === (int) ($admin['id'] ?? 0)) {
            $this->adminError('/admin/users', 'You cannot delete your own account.');
        }

        try {
            $model = new User();
            $user = $model->findById($userId);

            if ($user === null) {
                $this->notFound();
            }

            if (($user['role'] ?? '') === 'admin' && $model->countAdmins() <= 1) {
                $this->adminError('/admin/users', 'The final admin account cannot be deleted.');
            }

            if (!$model->delete($userId)) {
                $this->adminError('/admin/users', 'This user cannot be deleted because posts or replies depend on the account.');
            }
        } catch (Throwable) {
            $this->adminError('/admin/users', 'The user could not be deleted.');
        }

        $this->adminSuccess('/admin/users', 'User deleted successfully.');
    }

    public function modules()
    {
        $this->requireAdmin();
        $page = $this->pageNumber();

        try {
            $model = new Module();
            $total = $model->countAll();
            $pagination = $this->pagination('/admin/modules', $total, $page);
            $modules = $model->getPaginated(self::PAGE_LIMIT, $pagination['offset']);
        } catch (Throwable) {
            $modules = [];
            $pagination = $this->pagination('/admin/modules', 0, 1);
        }

        $this->view('admin/modules', [
            'adminSection' => 'modules',
            'pageTitle' => 'Manage Modules',
            'pageScripts' => ['confirm-modal.js'],
            'modules' => $modules,
            'pagination' => $pagination,
        ]);
    }

    public function storeModule()
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/modules');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->adminError('/admin/modules', 'The security token is invalid. Please try again.');
        }

        $data = $this->moduleData();
        $model = new Module();
        $error = $this->validateModule($data, 0, $model);

        if ($error !== '') {
            $this->adminError('/admin/modules', $error);
        }

        try {
            if ($model->create($data) <= 0) {
                $this->adminError('/admin/modules', 'The module could not be created.');
            }
        } catch (Throwable) {
            $this->adminError('/admin/modules', 'The module could not be created.');
        }

        $this->adminSuccess('/admin/modules', 'Module created successfully.');
    }

    public function editModule($id = 0)
    {
        $this->requireAdmin();

        try {
            $module = (new Module())->findById((int) $id);
        } catch (Throwable) {
            $module = null;
        }

        if ($module === null) {
            $this->notFound();
        }

        $this->view('admin/module-edit', [
            'adminSection' => 'modules',
            'pageTitle' => 'Edit Module',
            'module' => $module,
        ]);
    }

    public function updateModule($id = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/modules');
        $moduleId = (int) $id;

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->adminError('/admin/modules', 'The security token is invalid. Please try again.');
        }

        $data = $this->moduleData();
        $model = new Module();

        try {
            if ($model->findById($moduleId) === null) {
                $this->notFound();
            }
        } catch (Throwable) {
            $this->adminError('/admin/modules', 'The module could not be loaded.');
        }

        $error = $this->validateModule($data, $moduleId, $model);

        if ($error !== '') {
            $this->adminError('/admin/modules/edit/' . $moduleId, $error);
        }

        try {
            if (!$model->update($moduleId, $data)) {
                $this->adminError('/admin/modules/edit/' . $moduleId, 'The module could not be updated.');
            }
        } catch (Throwable) {
            $this->adminError('/admin/modules/edit/' . $moduleId, 'The module could not be updated.');
        }

        $this->adminSuccess('/admin/modules', 'Module updated successfully.');
    }

    public function deleteModule($id = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/modules');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->adminError('/admin/modules', 'The security token is invalid. Please try again.');
        }

        try {
            if (!(new Module())->delete((int) $id)) {
                $this->adminError('/admin/modules', 'This module cannot be deleted because posts depend on it.');
            }
        } catch (Throwable) {
            $this->adminError('/admin/modules', 'The module could not be deleted.');
        }

        $this->adminSuccess('/admin/modules', 'Module deleted successfully.');
    }

    public function posts()
    {
        $this->requireAdmin();
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => in_array($_GET['status'] ?? '', ['open', 'solved'], true) ? $_GET['status'] : '',
            'module' => trim((string) ($_GET['module'] ?? '')),
            'sort' => 'latest',
        ];
        $page = $this->pageNumber();

        try {
            $postModel = new Post();
            $moduleModel = new Module();
            $total = (int) $postModel->getDiscussionCount($filters);
            $pagination = $this->pagination('/admin/posts', $total, $page, $filters);
            $posts = $postModel->getDiscussionList($filters, self::PAGE_LIMIT, $pagination['offset']);
            $modules = $moduleModel->getAll();
        } catch (Throwable) {
            $posts = [];
            $modules = [];
            $pagination = $this->pagination('/admin/posts', 0, 1, $filters);
        }

        $this->view('admin/posts', [
            'adminSection' => 'posts',
            'pageTitle' => 'Manage Posts',
            'pageScripts' => ['confirm-modal.js'],
            'posts' => $posts,
            'modules' => $modules,
            'filters' => $filters,
            'pagination' => $pagination,
        ]);
    }

    public function updatePostStatus($id = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/posts');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->adminError('/admin/posts', 'The security token is invalid. Please try again.');
        }

        $status = strtolower(trim((string) ($_POST['status'] ?? '')));

        if (!in_array($status, ['open', 'solved'], true)) {
            $this->adminError('/admin/posts', 'Please choose a valid post status.');
        }

        try {
            if (!(new Post())->setStatus((int) $id, $status)) {
                $this->adminError('/admin/posts', 'The post status could not be updated.');
            }
        } catch (Throwable) {
            $this->adminError('/admin/posts', 'The post status could not be updated.');
        }

        $this->adminSuccess('/admin/posts', 'Post status updated successfully.');
    }

    public function deletePost($id = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/posts');

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->adminError('/admin/posts', 'The security token is invalid. Please try again.');
        }

        try {
            if (!(new Post())->delete((int) $id)) {
                $this->adminError('/admin/posts', 'The post could not be deleted.');
            }
        } catch (Throwable) {
            $this->adminError('/admin/posts', 'The post could not be deleted.');
        }

        $this->adminSuccess('/admin/posts', 'Post deleted successfully.');
    }

    private function requireAdmin()
    {
        $user = $this->currentUser();

        if ($user === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        if (!PermissionHelper::isAdmin($user)) {
            $this->redirectWithToast(BASE_URL . '/dashboard', [
                'type' => 'error',
                'title' => 'Permission denied',
                'message' => 'Only administrators can access the admin area.',
            ]);
        }

        return $user;
    }

    private function pageNumber()
    {
        $page = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT);

        return $page !== false && $page > 0 ? $page : 1;
    }

    private function pagination(string $path, int $totalItems, int $currentPage, array $query = [])
    {
        $totalPages = max(1, (int) ceil($totalItems / self::PAGE_LIMIT));
        $currentPage = min(max(1, $currentPage), $totalPages);

        return [
            'current' => $currentPage,
            'total' => $totalPages,
            'offset' => ($currentPage - 1) * self::PAGE_LIMIT,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_url' => $this->pageUrl($path, $query, max(1, $currentPage - 1)),
            'next_url' => $this->pageUrl($path, $query, min($totalPages, $currentPage + 1)),
        ];
    }

    private function pageUrl(string $path, array $query, int $page)
    {
        $query = array_filter($query, static fn ($value) => trim((string) $value) !== '');
        $query['page'] = $page;

        return BASE_URL . $path . '?' . http_build_query($query);
    }

    private function findUser(int $id)
    {
        if ($id <= 0) {
            return null;
        }

        try {
            return (new User())->findById($id);
        } catch (Throwable) {
            return null;
        }
    }

    private function userData(bool $includePassword = false)
    {
        return [
            'first_name' => trim((string) ($_POST['first_name'] ?? '')),
            'last_name' => trim((string) ($_POST['last_name'] ?? '')),
            'username' => trim((string) ($_POST['username'] ?? '')),
            'email' => trim((string) ($_POST['email'] ?? '')),
            'password' => $includePassword ? (string) ($_POST['password'] ?? '') : '',
            'confirm_password' => $includePassword ? (string) ($_POST['confirm_password'] ?? '') : '',
            'role' => strtolower(trim((string) ($_POST['role'] ?? 'student'))),
        ];
    }

    private function validateUser(
        array $data,
        int $userId,
        User $model,
        bool $includePassword = false
    ) {
        $errors = $model->validateAccount($data, $userId);

        if (!in_array($data['role'], self::USER_ROLES, true)) {
            $errors['role'] = 'Please choose a valid user role.';
        }

        if ($includePassword) {
            $errors = array_merge($errors, $model->validatePassword(
                $data['password'],
                $data['confirm_password']
            ));
        }

        return $errors;
    }

    private function refreshCurrentUserSession(int $userId, User $model)
    {
        if ($userId !== $this->currentUserId()) {
            return;
        }

        $user = $model->findById($userId);

        if ($user === null) {
            return;
        }

        unset($user['password']);
        $_SESSION['auth_user'] = $user;
    }

    private function userFormState(string $mode, int $userId = 0)
    {
        $state = $_SESSION[self::USER_FORM_STATE] ?? [];
        unset($_SESSION[self::USER_FORM_STATE]);

        if (!is_array($state)
            || ($state['mode'] ?? '') !== $mode
            || (int) ($state['user_id'] ?? 0) !== $userId) {
            return [];
        }

        return $state;
    }

    private function redirectUserForm(
        string $path,
        string $mode,
        int $userId,
        array $errors,
        array $old
    ) {
        unset($old['password'], $old['confirm_password']);

        $_SESSION[self::USER_FORM_STATE] = [
            'mode' => $mode,
            'user_id' => $userId,
            'errors' => $errors,
            'old' => $old,
        ];

        $this->redirectTo(BASE_URL . $path);
    }

    private function moduleData()
    {
        return [
            'code' => strtoupper(trim((string) ($_POST['code'] ?? ''))),
            'name' => trim((string) ($_POST['name'] ?? '')),
            'description' => trim((string) ($_POST['description'] ?? '')),
        ];
    }

    private function validateModule(array $data, int $moduleId, Module $model)
    {
        if ($data['code'] === '' || mb_strlen($data['code']) > 20) {
            return 'Module code is required and must be 20 characters or fewer.';
        }

        if ($data['name'] === '' || mb_strlen($data['name']) > 150) {
            return 'Module name is required and must be 150 characters or fewer.';
        }

        if ($model->codeExistsExceptModule($data['code'], $moduleId)) {
            return 'Module code is already in use.';
        }

        return '';
    }

    private function adminSuccess(string $path, string $message)
    {
        $this->redirectWithToast(BASE_URL . $path, [
            'type' => 'success',
            'title' => 'Saved',
            'message' => $message,
        ]);
    }

    private function adminError(string $path, string $message)
    {
        $this->redirectWithToast(BASE_URL . $path, [
            'type' => 'error',
            'title' => 'Unable to save',
            'message' => $message,
        ]);
    }
}
