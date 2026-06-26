<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Services\HomePageService;

class HomeController extends Controller
{
    public function index()
    {
        $homePage = new HomePageService();
        $data = $homePage->getViewData();
        $data['pageScripts'] = ['home.js'];

        $this->view('home/index', $data);
    }
}
