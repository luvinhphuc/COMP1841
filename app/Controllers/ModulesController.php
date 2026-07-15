<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Module;
use App\Models\UserModule;
use Throwable;

class ModulesController extends Controller
{
    private const ONBOARDING_STATE = 'onboarding_module_state';
    private const PREFERENCES_STATE = 'preferences_module_state';

    public function index()
    {
        $user = $this->currentUser();
        $role = strtolower(trim((string) ($user['role'] ?? '')));

        if ($role === 'student') {
            $this->redirectTo(BASE_URL . '/preferences/modules');
        }

        $this->redirectTo(BASE_URL . '/discussions');
    }

    public function onboarding()
    {
        [$userId, $user] = $this->authenticatedStudent();
        $state = $this->moduleState(self::ONBOARDING_STATE);

        try {
            $userModuleModel = new UserModule();

            if ($userModuleModel->hasSelectedModules($userId)) {
                $this->redirectTo(BASE_URL . '/preferences/modules');
            }

            $modules = (new Module())->getAll();
        } catch (Throwable) {
            $modules = [];
            $state['errors']['general'] = 'Modules could not be loaded right now. Please try again.';
        }

        unset($_SESSION[self::ONBOARDING_STATE]);

        $this->renderModuleForm(
            'onboarding',
            $user,
            $modules,
            $state['selected_module_ids'] ?? [],
            $state['errors'] ?? []
        );
    }

    public function saveOnboarding()
    {
        $this->saveSelection('onboarding');
    }

    public function preferences()
    {
        [$userId, $user] = $this->authenticatedStudent();
        $state = $this->moduleState(self::PREFERENCES_STATE);

        try {
            $userModuleModel = new UserModule();

            if (!$userModuleModel->hasSelectedModules($userId)) {
                $this->redirectTo(BASE_URL . '/onboarding/modules');
            }

            $modules = (new Module())->getAll();
            $savedModuleIds = $userModuleModel->getSelectedModuleIds($userId);
        } catch (Throwable) {
            $modules = [];
            $savedModuleIds = [];
            $state['errors']['general'] = 'Modules could not be loaded right now. Please try again.';
        }

        unset($_SESSION[self::PREFERENCES_STATE]);

        $this->renderModuleForm(
            'preferences',
            $user,
            $modules,
            $state['selected_module_ids'] ?? $savedModuleIds,
            $state['errors'] ?? []
        );
    }

    public function savePreferences()
    {
        $this->saveSelection('preferences');
    }

    private function saveSelection(string $mode)
    {
        $isPreferences = $mode === 'preferences';
        $formUrl = BASE_URL . ($isPreferences ? '/preferences/modules' : '/onboarding/modules');
        $stateKey = $isPreferences ? self::PREFERENCES_STATE : self::ONBOARDING_STATE;

        $this->requirePost($formUrl);
        [$userId] = $this->authenticatedStudent();

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $this->redirectWithModuleState($stateKey, $formUrl, [
                'general' => 'Your session expired. Please try again.',
            ], []);
        }

        $moduleIds = [];

        try {
            $userModuleModel = new UserModule();
            [$moduleIds, $errors] = $userModuleModel->validateModuleIds($_POST['module_ids'] ?? null);

            if (!empty($errors)) {
                $this->redirectWithModuleState($stateKey, $formUrl, $errors, $moduleIds);
            }

            if (!$userModuleModel->replaceUserModules($userId, $moduleIds)) {
                $this->redirectWithModuleState($stateKey, $formUrl, [
                    'general' => 'Your module selections could not be saved. Please try again.',
                ], $moduleIds);
            }
        } catch (Throwable) {
            $this->redirectWithModuleState($stateKey, $formUrl, [
                'general' => 'Your module selections could not be saved right now. Please try again.',
            ], $moduleIds);
        }

        if ($isPreferences) {
            $this->redirectWithToast(BASE_URL . '/preferences/modules', [
                'type' => 'success',
                'title' => 'Modules updated',
                'message' => 'Your module selections have been saved.',
            ]);
        }

        $this->redirectWithToast(BASE_URL . '/dashboard', [
            'type' => 'success',
            'title' => 'Modules saved',
            'message' => 'Your dashboard is now ready.',
        ]);
    }

    private function authenticatedStudent(): array
    {
        $user = $this->currentUser();
        $userId = $this->currentUserId();

        if (!is_array($user) || $userId === null) {
            $this->redirectTo(BASE_URL . '/login');
        }

        if (strtolower(trim((string) ($user['role'] ?? ''))) !== 'student') {
            $this->redirectTo(BASE_URL . '/dashboard');
        }

        return [$userId, $user];
    }

    private function moduleState(string $key): array
    {
        $state = $_SESSION[$key] ?? [];

        return is_array($state) ? $state : [];
    }

    private function redirectWithModuleState(
        string $stateKey,
        string $url,
        array $errors,
        array $moduleIds
    ) {
        $_SESSION[$stateKey] = [
            'errors' => $errors,
            'selected_module_ids' => $moduleIds,
        ];

        $this->redirectTo($url);
    }

    private function renderModuleForm(
        string $mode,
        array $user,
        array $modules,
        array $selectedModuleIds,
        array $errors
    ) {
        $isPreferences = $mode === 'preferences';

        $this->view('preferences/modules', [
            'authUser' => $user,
            'mode' => $mode,
            'modules' => $modules,
            'selectedModuleIds' => array_map('intval', $selectedModuleIds),
            'moduleErrors' => $errors,
            'formAction' => BASE_URL . ($isPreferences
                ? '/preferences/modules/save'
                : '/onboarding/modules/save'),
        ]);
    }
}
