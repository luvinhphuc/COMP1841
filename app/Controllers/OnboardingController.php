<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Module;
use App\Models\UserModule;
use Throwable;

class OnboardingController extends Controller
{
    private const ONBOARDING_STATE = 'onboarding_module_state';
    private const PREFERENCES_STATE = 'preferences_module_state';

    public function modules()
    {
        [$userId] = $this->authenticatedStudent();
        $userModuleModel = new UserModule();

        try {
            if ($userModuleModel->hasSelectedModules($userId)) {
                $this->redirectTo(BASE_URL . '/preferences/modules');
            }
        } catch (Throwable) {
            $this->renderOnboarding([], [], [
                'general' => 'Modules could not be loaded right now. Please try again.',
            ]);
        }

        $state = $this->moduleState(self::ONBOARDING_STATE);

        try {
            $modules = (new Module())->getAll();
        } catch (Throwable) {
            $modules = [];
            $state['errors']['general'] = 'Modules could not be loaded right now. Please try again.';
        }

        $selectedModuleIds = $state['selected_module_ids'] ?? [];

        unset($_SESSION[self::ONBOARDING_STATE]);

        $this->renderOnboarding($modules, $selectedModuleIds, $state['errors'] ?? []);
    }

    public function saveModules()
    {
        $this->saveSelection('onboarding');
    }

    public function manageModules()
    {
        [$userId, $user] = $this->authenticatedStudent();
        $state = $this->moduleState(self::PREFERENCES_STATE);
        $userModuleModel = new UserModule();

        try {
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

        $selectedModuleIds = $state['selected_module_ids'] ?? $savedModuleIds;
        $selectedLookup = array_fill_keys(array_map('intval', $selectedModuleIds), true);
        $selectedModules = [];
        $availableModules = [];

        foreach ($modules as $module) {
            if (isset($selectedLookup[(int) ($module['id'] ?? 0)])) {
                $selectedModules[] = $module;
            } else {
                $availableModules[] = $module;
            }
        }

        unset($_SESSION[self::PREFERENCES_STATE]);

        $this->view('preferences/modules', [
            'authUser' => $user,
            'selectedModules' => $selectedModules,
            'availableModules' => $availableModules,
            'selectedModuleIds' => array_map('intval', $selectedModuleIds),
            'moduleErrors' => $state['errors'] ?? [],
        ]);
    }

    public function savePreferenceModules()
    {
        $this->saveSelection('preferences');
    }

    private function saveSelection(string $context)
    {
        $isPreferences = $context === 'preferences';
        $formUrl = BASE_URL . ($isPreferences ? '/preferences/modules' : '/onboarding/modules');
        $stateKey = $isPreferences ? self::PREFERENCES_STATE : self::ONBOARDING_STATE;

        $this->requirePost($formUrl);
        [$userId] = $this->authenticatedStudent();

        $moduleIds = $this->submittedPositiveModuleIds($_POST['module_ids'] ?? null);
        $errors = [];

        if (!$this->verifyCsrfToken($_POST['_csrf_token'] ?? null)) {
            $errors['general'] = 'Your session expired. Please try again.';
        } else {
            [$moduleIds, $errors] = $this->validatedModuleIds($_POST['module_ids'] ?? null);
        }

        if (!empty($errors)) {
            $this->redirectWithModuleState($stateKey, $formUrl, $errors, $moduleIds);
        }

        try {
            if (!(new UserModule())->replaceUserModules($userId, $moduleIds)) {
                $this->redirectWithModuleState($stateKey, $formUrl, [
                    'general' => 'Your module selections could not be saved. Please try again.',
                ], $moduleIds);
            }
        } catch (Throwable) {
            $this->redirectWithModuleState($stateKey, $formUrl, [
                'general' => 'Your module selections could not be saved right now. Please try again.',
            ], $moduleIds);
        }

        $this->storeDashboardModuleIds($moduleIds);

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

    private function validatedModuleIds(mixed $submittedModuleIds): array
    {
        if (!is_array($submittedModuleIds)) {
            return [[], ['module_ids' => 'Select at least one module.']];
        }

        $moduleIds = [];

        foreach ($submittedModuleIds as $submittedModuleId) {
            if (is_array($submittedModuleId)) {
                return [$moduleIds, ['module_ids' => 'One or more selected modules are invalid.']];
            }

            $moduleId = filter_var($submittedModuleId, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);

            if ($moduleId === false) {
                return [$moduleIds, ['module_ids' => 'One or more selected modules are invalid.']];
            }

            $moduleIds[] = (int) $moduleId;
        }

        $moduleIds = array_values(array_unique($moduleIds));

        if (empty($moduleIds)) {
            return [[], ['module_ids' => 'Select at least one module.']];
        }

        try {
            $moduleModel = new Module();

            foreach ($moduleIds as $moduleId) {
                if (!$moduleModel->exists($moduleId)) {
                    return [$moduleIds, ['module_ids' => 'One or more selected modules do not exist.']];
                }
            }
        } catch (Throwable) {
            return [$moduleIds, ['general' => 'Modules could not be verified right now. Please try again.']];
        }

        return [$moduleIds, []];
    }

    private function submittedPositiveModuleIds(mixed $submittedModuleIds): array
    {
        if (!is_array($submittedModuleIds)) {
            return [];
        }

        $moduleIds = [];

        foreach ($submittedModuleIds as $submittedModuleId) {
            if (is_array($submittedModuleId)) {
                continue;
            }

            $moduleId = filter_var($submittedModuleId, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1],
            ]);

            if ($moduleId !== false) {
                $moduleIds[] = (int) $moduleId;
            }
        }

        return array_values(array_unique($moduleIds));
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
    ): never {
        $_SESSION[$stateKey] = [
            'errors' => $errors,
            'selected_module_ids' => $moduleIds,
        ];

        $this->redirectTo($url);
    }

    private function storeDashboardModuleIds(array $moduleIds): void
    {
        $moduleIds = array_values(array_unique(array_map('intval', $moduleIds)));

        if (count($moduleIds) > 4) {
            shuffle($moduleIds);
            $moduleIds = array_slice($moduleIds, 0, 4);
        }

        $_SESSION['dashboard_module_ids'] = $moduleIds;
    }

    private function renderOnboarding(array $modules, array $selectedModuleIds, array $errors): never
    {
        $this->view('onboarding/modules', [
            'modules' => $modules,
            'selectedModuleIds' => array_map('intval', $selectedModuleIds),
            'moduleErrors' => $errors,
        ]);

        exit;
    }
}
