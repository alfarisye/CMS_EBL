<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\MDCustomers;

class MDCustomer extends BaseController
{
    public function index()
    {
        $data['title'] = "Master Data Customer";

        $MDCustomers = new MDCustomers();
        $data['customers'] = $MDCustomers->findAll();
        echo view('pages/md-customer', $data);
    }
}
