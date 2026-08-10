<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Serves public informational pages and routes members to the discussion feed.
 */
class HomeController extends Controller
{
    public function index()
    {
        if ($this->currentUser() !== null) {
            $this->redirectTo(BASE_URL . '/discussions');
        }

        $this->view('home/index', [
            'pageScripts' => ['landing.js'],
        ]);
    }

    public function privacy()
    {
        if (strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET')) !== 'GET') {
            $this->notFound();
        }

        $this->view('privacy/index', [
            'pageTitle' => 'Privacy Policy',
            'privacyLastUpdated' => '10 August 2026',
            'privacyLastUpdatedIso' => '2026-08-10',
        ]);
    }
}
