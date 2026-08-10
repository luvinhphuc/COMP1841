<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\FormatHelper;
use App\Repositories\ModuleRepository;
use Throwable;

/**
 * Serves the searchable public module catalogue.
 */
class ModuleController extends Controller
{
    // Route actions --------------------------------------------------------
    public function index()
    {
        $searchQuery = FormatHelper::shortText(trim((string) ($_GET['q'] ?? '')), 100);

        try {
            $modules = (new ModuleRepository())->findAll();
        } catch (Throwable) {
            $modules = [];
        }

        if ($searchQuery !== '') {
            $containsSearchQuery = static function (string $value) use ($searchQuery) {
                if (function_exists('mb_stripos')) {
                    return mb_stripos($value, $searchQuery) !== false;
                }

                return stripos($value, $searchQuery) !== false;
            };

            $modules = array_values(array_filter($modules, static function (array $module) use ($containsSearchQuery) {
                return $containsSearchQuery((string) ($module['code'] ?? ''))
                    || $containsSearchQuery((string) ($module['name'] ?? ''));
            }));
        }

        $this->view('modules/index', [
            'pageTitle' => 'Modules - ',
            'modules' => $modules,
            'searchQuery' => $searchQuery,
        ]);
    }
}
