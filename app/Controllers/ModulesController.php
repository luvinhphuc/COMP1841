<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Module;
use Throwable;

class ModulesController extends Controller
{
    public function index()
    {
        try {
            $modules = (new Module())->getAll();
        } catch (Throwable) {
            $modules = [];
        }

        $this->view('modules/index', [
            'modules' => $modules,
        ]);
    }
}