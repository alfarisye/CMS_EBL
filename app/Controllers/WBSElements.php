<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Search;

class WBSElements extends BaseController
{
    public function index()
    {
        $data['title'] = "WBS Element";
        $db = db_connect();
        $begin_wbs = $_GET['begwbs'] ?? '';
        $end_wbs = $_GET['endwbs'] ?? '';

        if ($begin_wbs == '' and $end_wbs == '') {
            $sql_data = "select * from T_PRPS order by POSID asc";
        }elseif ($begin_wbs == '' and $end_wbs <> ''){
            $end_wbs = str_replace('*','%',$end_wbs);
            $sql_data = "select * from T_PRPS where POSID like '".$end_wbs."' order by POSID asc";
        }elseif ($begin_wbs <> '' and $end_wbs == ''){
            $begin_wbs = str_replace('*','%',$begin_wbs);
            $sql_data = "select * from T_PRPS where POSID like '".$begin_wbs."' order by POSID asc";
        }elseif ($begin_wbs <> '' and $end_wbs <> ''){
            $begin_wbs = str_replace('*','%',$begin_wbs);
            $end_wbs = str_replace('*','%',$end_wbs);
            $sql_data = "select * from T_PRPS where POSID between '".$begin_wbs."' and '".$end_wbs."' order by POSID asc";
        }
        $begin_wbs = str_replace('*','%',$begin_wbs);
        $end_wbs = str_replace('*','%',$end_wbs);

        
        $v_data = $db->query($sql_data);
        $data['tdata'] = $v_data->getResultArray();

        echo view('pages/wbs_element', $data);
    }
}
