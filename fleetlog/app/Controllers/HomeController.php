<?php

namespace FleetLog\App\Controllers;

class HomeController extends BaseController
{
    public function index(): void
    {
        $this->render('home', ['title' => 'FleetLog - Home']);
    }
}
