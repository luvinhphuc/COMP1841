<?php

namespace App\Controllers\Admin;

use App\Controllers\AdminController;
use App\Repositories\ModuleRepository;
use Throwable;

/**
 * Manages the academic module catalogue from the administration area.
 */
class ModuleController extends AdminController
{
    // Administrative route actions ---------------------------------------
    public function index()
    {
        $this->requireAdmin();
        $page = $this->pageNumber();
        $pageLimit = $this->pageLimit();

        try {
            $moduleRepository = new ModuleRepository();
            $total = $moduleRepository->countAll();
            $pagination = $this->pagination('/admin/modules', $total, $page, [], $pageLimit);
            $modules = $moduleRepository->findPaginated($pageLimit, $pagination['offset']);
        } catch (Throwable) {
            $modules = [];
            $pagination = $this->pagination('/admin/modules', 0, 1, [], $pageLimit);
        }

        $this->view('admin/modules', [
            'adminSection' => 'modules',
            'pageTitle' => 'Manage Modules',
            'pageScripts' => ['modal.js'],
            'modules' => $modules,
            'pagination' => $pagination,
        ]);
    }

    public function store()
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/modules');

        $moduleData = $this->moduleData();
        $moduleRepository = new ModuleRepository();
        $validationError = $this->validateModule($moduleData, 0, $moduleRepository);

        if ($validationError !== '') {
            $this->adminError('/admin/modules', $validationError);
        }

        try {
            if ($moduleRepository->create($moduleData) <= 0) {
                $this->adminError('/admin/modules', 'The module could not be created.');
            }
        } catch (Throwable) {
            $this->adminError('/admin/modules', 'The module could not be created.');
        }

        $this->adminSuccess('/admin/modules', 'Module created successfully.');
    }

    public function edit($moduleId = 0)
    {
        $this->requireAdmin();

        try {
            $moduleEntity = (new ModuleRepository())->findById((int)$moduleId);
            $moduleData = $moduleEntity === null ? null : [
                'id' => $moduleEntity->id,
                'code' => $moduleEntity->module_code,
                'name' => $moduleEntity->module_name,
                'description' => $moduleEntity->description,
            ];
        } catch (Throwable) {
            $moduleData = null;
        }

        if ($moduleData === null) {
            $this->notFound();
        }

        $this->view('admin/module-edit', [
            'adminSection' => 'modules',
            'pageTitle' => 'Edit Module',
            'module' => $moduleData,
        ]);
    }

    public function update($moduleId = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/modules');
        $moduleId = (int)$moduleId;

        $moduleData = $this->moduleData();
        $moduleRepository = new ModuleRepository();

        try {
            if ($moduleRepository->findById($moduleId) === null) {
                $this->notFound();
            }
        } catch (Throwable) {
            $this->adminError('/admin/modules', 'The module could not be loaded.');
        }

        $validationError = $this->validateModule($moduleData, $moduleId, $moduleRepository);

        if ($validationError !== '') {
            $this->adminError('/admin/modules/edit/' . $moduleId, $validationError);
        }

        try {
            if (!$moduleRepository->update($moduleId, $moduleData)) {
                $this->adminError('/admin/modules/edit/' . $moduleId, 'The module could not be updated.');
            }
        } catch (Throwable) {
            $this->adminError('/admin/modules/edit/' . $moduleId, 'The module could not be updated.');
        }

        $this->adminSuccess('/admin/modules', 'Module updated successfully.');
    }

    public function delete($moduleId = 0)
    {
        $this->requireAdmin();
        $this->requirePost(BASE_URL . '/admin/modules');

        try {
            if (!(new ModuleRepository())->delete((int)$moduleId)) {
                $this->adminError(
                    '/admin/modules',
                    'This module cannot be deleted because discussions depend on it.'
                );
            }
        } catch (Throwable) {
            $this->adminError('/admin/modules', 'The module could not be deleted.');
        }

        $this->adminSuccess('/admin/modules', 'Module deleted successfully.');
    }

    // Input normalisation and validation ---------------------------------
    private function moduleData(): array
    {
        return [
            'code' => strtoupper(trim((string)($_POST['code'] ?? ''))),
            'name' => trim((string)($_POST['name'] ?? '')),
            'description' => trim((string)($_POST['description'] ?? '')),
        ];
    }

    private function validateModule(
        array $moduleData,
        int $moduleId,
        ModuleRepository $moduleRepository
    ): string
    {
        if ($moduleData['code'] === '' || mb_strlen($moduleData['code']) > 20) {
            return 'Module code is required and must be 20 characters or fewer.';
        }

        if ($moduleData['name'] === '' || mb_strlen($moduleData['name']) > 150) {
            return 'Module name is required and must be 150 characters or fewer.';
        }

        if ($moduleRepository->existsByCodeExceptModule($moduleData['code'], $moduleId)) {
            return 'Module code is already in use.';
        }

        return '';
    }
}
