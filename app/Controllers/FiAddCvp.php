<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Message;
use CodeIgniter\I18n\Time;
use PhpParser\Node\Expr\FuncCall;

class FiAddCvp extends BaseController
{
    public function index()
    {
        $data['title'] = "CMS – Additional for CVP Analysis Report";

        $db = db_connect();

        $id_cat = $_GET['idcat'] ?? false;

        if ($id_cat == '' or is_null($id_cat)) {
            $qstr_add_cvp = "select * from FI_ADD_CVP where status = '1' order by CRTDA desc, EDTAT desc";
            $q_add_cvp = $db->query($qstr_add_cvp);
        }else{

            // if ($id_cat == '1' ) {
            //     $categ = 'C (B + Profit)';
            // }elseif ($id_cat == '2'){
            //     $categ = 'D (C & Qty)';
            // }
            $sql_get_cat = "SELECT * FROM cvp_category WHERE id=".$id_cat.";";
            $get_cat = $db->query($sql_get_cat)->getFirstRow();
            $categ = $get_cat->category;

            $qstr_add_cvp = "select * from FI_ADD_CVP where status = '1' 
                        and CTGRY = :idcat: order by CRTDA desc, EDTAT desc";
            $q_add_cvp = $db->query($qstr_add_cvp, ['idcat' => $categ]);
        }
        
        $data['add_cvp'] = $q_add_cvp->getResultArray();

        $sql_cat = "SELECT * FROM cvp_category ORDER BY id ASC";
        $qcat = $db->query($sql_cat);
        $data['qcat'] = $qcat->getResultArray();

        $sql_remark = "SELECT * FROM cvp_remark ORDER BY value ASC";
        $qremark = $db->query($sql_remark);
        $data['qremark'] = $qremark->getResultArray();

        echo view('pages/finance/cvpanlysis', $data);
    }

    public function add()
    { 
        try {
            $db = db_connect();
            $categ = $this->request->getPost('category');
            // if ($categ == '1') {
            //     $category = 'C (B + Profit)';
            // }elseif($categ == '2'){
            //     $category = 'D (C & Qty)';
            // }
            $sql_get_cat = "SELECT * FROM cvp_category WHERE id=".$categ.";";
            $get_cat = $db->query($sql_get_cat)->getFirstRow();
            $category = $get_cat->category;

            $remark = $this->request->getPost('remark');
            $year = $this->request->getPost('year');
            $period = $this->request->getPost('period');
            $amount = $this->request->getPost('amount');
            $comments = $this->request->getPost('comments');

            $qstr_insert = "INSERT INTO `FI_ADD_CVP` (`CTGRY`, `RKMRK`, `GJAHR`, `MONAT`, `DMBTR`,
                         `COMMT`, `CRTDB`, `CRTDA`) VALUES 
                         (:CTGRY:, :RKMRK:, :GJAHR:, :MONAT:, :DMBTR:, :COMMT:, :CRTDB:, :CRTDA:);";
            $qinsert = $db->query($qstr_insert,[
                'CTGRY' => $category,
                'RKMRK' => $remark,
                'GJAHR' => $year,
                'MONAT' => $period,
                'DMBTR' => $amount,
                'COMMT' => $comments,
                'CRTDB' => session()->get('username'),
                'CRTDA' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            if ($qinsert) {
                $message = "Additional for CVP Analysis has been created";
            }else{
                $message = "Create data cancelled";
            }

        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/finance/cvpanlysis/")->with('message', $message);
    }
    
    public function delete($id)
    {
        try {
            $db = db_connect();
            $qstr_del = "UPDATE `FI_ADD_CVP` SET `status`='0', `EDTBY`=:EDTBY:, `EDTAT`=:EDTAT: WHERE `id`=:id:;";  
            $qdel = $db->query($qstr_del,[
                'id' => $id,
                'EDTBY' => session()->get('username'),
                'EDTAT' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            if ($qdel) {
                $message = "Additional for CVP Analysis Data has been deleted";
            }else{
                $message = "No data deleted";
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/finance/cvpanlysis/")->with('message', $message);
    }

    public function get($id)
    {
        header('Content-Type: application/json');

        $db = db_connect();
        $qstr_get = "select *,IF(CTGRY='C (B + Profit)','1','2')as id_cat from FI_ADD_CVP where id=".$id;
        $qget = $db->query($qstr_get)->getRowArray();
        return $this->response->setJSON($qget);
    }

    public function update()
    {   
        $db = db_connect();
        $id = $this->request->getPost('id');
        $categ = $this->request->getPost('edcategory');
        // if ($categ == '1') {
        //     $category = 'C (B + Profit)';
        // }elseif($categ == '2'){
        //     $category = 'D (C & Qty)';
        // }
        $sql_get_cat = "SELECT * FROM cvp_category WHERE id=".$categ.";";
        $get_cat = $db->query($sql_get_cat)->getFirstRow();
        $category = $get_cat->category;
        
        $remark = $this->request->getPost('edremark');
        $year = $this->request->getPost('edyear');
        $period = $this->request->getPost('edperiod');
        $amount = $this->request->getPost('edamount');
        $comments = $this->request->getPost('edcomments');
        
        try {
            $db = db_connect();
            $qstr_upd = "UPDATE `FI_ADD_CVP` SET `CTGRY`=:CTGRY:, `RKMRK`=:RKMRK:, `GJAHR`=:GJAHR:, `MONAT`=:MONAT:, 
                        `DMBTR`=:DMBTR:, `COMMT`=:COMMT:, `EDTBY`=:EDTBY:, `EDTAT`=:EDTAT: WHERE  `id`=:id:;";  
            $qdel = $db->query($qstr_upd,[
                'id' => $id,
                'CTGRY' => $category,
                'RKMRK' => $remark,
                'GJAHR' => $year,
                'MONAT' => $period,
                'DMBTR' => $amount,
                'COMMT' => $comments,
                'EDTBY' => session()->get('username'),
                'EDTAT' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            if ($qdel) {
                $message = "Additional for CVP Analysis Data has been updated";
            }else{
                $message = "No data update";
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/finance/cvpanlysis/")->with('message', $message);
    }
}
