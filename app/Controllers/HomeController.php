<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        if ($this->currentUser() !== null) {
            $this->redirectTo(BASE_URL . '/dashboard');
        }

        $this->view('home/index');
    }
}