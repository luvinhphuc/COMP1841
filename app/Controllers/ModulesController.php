<?php

namespace App\Controllers;

use App\Core\Controller;

class ModulesController extends Controller
{
    public function index()
    {
        $user = $this->currentUser();
        $role = strtolower(trim((string) ($user['role'] ?? '')));

        if ($role === 'student') {
            $this->redirectTo(BASE_URL . '/preferences/modules');
        }

        $this->redirectTo(BASE_URL . '/discussions');
    }
}
