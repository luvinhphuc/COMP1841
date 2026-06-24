<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Module;

class HomeController extends Controller
{
    private Module $moduleModel;

    public function __construct()
    {
        $this->moduleModel = new Module();
    }

    public function index(): void
    {
        $this->view('home/index', [
            'modules' => $this->moduleModel->getAll(),
        ]);
    }
}
