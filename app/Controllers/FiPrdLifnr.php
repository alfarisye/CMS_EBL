<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class FiPrdLifnr extends BaseController
{
    public function index()
    {
        $data['title'] = "CMS – Update Production Vendor";

        $db = db_connect();

        $qstr_prd_lifnr = "select * from FI_PRD_LIFNR where status = '1' order by CRTDA desc, EDTAT desc";
        $q_prd_lifnr = $db->query($qstr_prd_lifnr);
        $data['prd_lifnr'] = $q_prd_lifnr->getResultArray();

        echo view('pages/finance/updateproductionvendor', $data);
    }
}
