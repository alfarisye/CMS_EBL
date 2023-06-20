<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\K3lhReport;
use App\Models\Type;
use App\Models\Category;
use CodeIgniter\I18n\Time;
use Config\Database;

class Landing extends BaseController
{
    public function index()
    {
        $data['title'] = "Welcome To CMS EBL";

        echo view('pages/landing.php', $data);
    }

}
