<?php

namespace App\Modules\CasjoeMail\Controllers;

class DashboardController
{
    public function index()
    {
        require __DIR__ . '/../views/dashboard.php';
    }
}
