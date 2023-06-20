<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use App\Models\FiInRkps;
use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Concatenate;
use PhpParser\Node\Expr\AssignOp\Concat;
use PhpParser\Node\Expr\BinaryOp\Concat as BinaryOpConcat;
use Shuchkin\SimpleXLSX;

class FiInRkp extends BaseController
{
    public function index()
    {
        $data['title'] = "CMS – Input RKAP";

        $db = db_connect();

        $idmth = $_GET['idmth'] ?? false;

        if ($idmth == '' or is_null($idmth)) {
            $qstr_rkap = "select * from FI_IN_RKP where 
                status = '1' order by CRTDA desc, EDTAT desc";
        }else{
            $qstr_rkap = "select * from FI_IN_RKP where 
                MONAT = '".$idmth."' and status = '1' order by CRTDA desc, EDTAT desc";
        }
        
        $q_rkap = $db->query($qstr_rkap);
        $data['rkap'] = $q_rkap->getResultArray();

        $qctype = "select type as ctype from type_shipment group by ctype order by ctype";
        $vctype = $db->query($qctype);
        $data['ctype'] = $vctype->getResultArray();

        $qshipment = "select * from type_shipment order by `type`";
        $vhsipment = $db->query($qshipment);
        $data['tshipment'] = $vhsipment->getResultArray();

        echo view('pages/finance/rkap', $data);
    }

    public function add()
    {
        try {
            $db = db_connect();
            
            $year = $this->request->getPost('year');
            $month = $this->request->getPost('month');
            $ctype = $this->request->getPost('ctype');
            $shipment = $this->request->getPost('shipment');
            $price = $this->request->getPost('price');
            $quantity = $this->request->getPost('quantity');
            $cost = $this->request->getPost('cost');
            $bdgtid = "BF-".$year."-".$month."-";

            // R0 = Data belum pernah Edit
            // R1 = Jika data sudah pernah diedit 1 kali
            $stat = "R0";

            $FiInRkps = new FiInRkps();
            // $qstr_insert = "INSERT INTO `FI_IN_RKP` (`BDGID`, `GJAHR`, `MONAT`, `SHPMN`, `PRC`, `QTY`, `STATS`, `CRTDB`, `CRTDA`) 
            //         VALUES (:BDGID:, :GJAHR:, :MONAT:, :SHPMN:, :PRC:, :QTY:, :STATS:, :CRTDB:, :CRTDA:);";
            // $qinsert = $db->query($qstr_insert,[
            $FiInRkps->save([
                'BDGID' => $bdgtid,
                'GJAHR' => $year,
                'MONAT' => $month,
                'TYPE' => $ctype,
                'SHPMN' => $shipment,
                'PRC' => $price,
                'QTY' => $quantity,
                'COST' => $cost,
                'STATS' => $stat,
                'CRTDB' => session()->get('username'),
                'CRTDA' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            
            // if ($qinsert) {
                $message = "RKAP has been created";
            // }else{
            //     $message = "Create data cancelled";
            // }
            
            $idaut = $FiInRkps->getInsertID();

            $id0 = "";
            $do = 4 - strlen($idaut);
            if ($do > 0) {
                for($i = 1; $i <= $do; $i++) {
                    $id0 = "0".$id0;
                }
            }
            $FiInRkp_upd = $FiInRkps->find($idaut);


            $idaut = $id0.$idaut; 
            $FiInRkp_upd['BDGID'] = $FiInRkp_upd['BDGID'].$idaut;
            $FiInRkps->save($FiInRkp_upd);
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/finance/rkap/")->with('message', $message);
    }

    public function get($id)
    {
        header('Content-Type: application/json');

        $db = db_connect();
        $qstr_get = "select *, MID(STATS,2,4) as id_rev from FI_IN_RKP where id=".$id;
        $qget = $db->query($qstr_get)->getRowArray();
        return $this->response->setJSON($qget);
    }

    public function update()
    {   

        $id = $this->request->getPost('id');
        $year = $this->request->getPost('edyear');
        $month = $this->request->getPost('edmonth');
        $ctype = $this->request->getPost('edctype');
        $shipment = $this->request->getPost('edshipment');
        $price = $this->request->getPost('edprice');
        $quantity = $this->request->getPost('edquantity');
        $cost = $this->request->getPost('edcost');
        // R0 = Data belum pernah Edit
        // R1 = Jika data sudah pernah diedit 1 kali
        $idrev = $this->request->getPost('idrev');
        $stat = "R".$idrev;
        
        try {
            $db = db_connect();
            $qstr_upd = "UPDATE `FI_IN_RKP` SET `GJAHR`=:GJAHR:, `MONAT`=:MONAT:, `TYPE`=:CTYPE:, `SHPMN`=:SHPMN:, `PRC`=:PRC:, `QTY`=:QTY:, `COST`=:COST:, `STATS`=:STATS:, `EDTBY`=:EDTBY:, `EDTAT`=:EDTAT: WHERE  `id`=:id:;";  
            $qdel = $db->query($qstr_upd,[
                'id' => $id,
                'GJAHR' => $year,
                'MONAT' => $month,
                'CTYPE' => $ctype,
                'SHPMN' => $shipment,
                'PRC' => $price,
                'QTY' => $quantity,
                'COST' => $cost,
                'STATS' => $stat,
                'EDTBY' => session()->get('username'),
                'EDTAT' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            if ($qdel) {
                $message = "RKAP has been updated";
            }else{
                $message = "No data update";
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/finance/rkap/")->with('message', $message);
    }
    
    public function delete($id)
    {

        try {
            // fields :
            // ACTVY, CRTDA, CRTDB, EDTAT, EDTBY, LIFNR, QTY, TRF, id,

            $FiInRkps = new FiInRkps();
            $FiInRkp_upd = $FiInRkps->find($id);
            if ($FiInRkp_upd) {
                $FiInRkp_upd['EDTBY'] = session()->get('username');
                $FiInRkp_upd['EDTAT'] = Time::now()->format('Y-m-d H:i:s');
                $FiInRkp_upd['status'] = false;

                $FiInRkps->save($FiInRkp_upd);

                $message = "Production Data has been deleted";
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        // $FiPrdDats = new FiPrdDats();
        // $FiPrdDats->update($id,['status' => false]);
        // $message = "Production Data has been deleted";
        return redirect()->to("/finance/rkap/")->with('message', $message);
    }

    public function form_upload()
    {
        $data['title'] = "CMS – Upload RKAP";    
        $data['upl'] = array();

        $data['ready_save'] = false;

        echo view('pages/finance/rkap_upload', $data);
    }

    public function get_upl()
    {   
        $data['title'] = "CMS – Upload RKAP";   
        $arraydata = array();
        try {            
            $userfile = $this->request->getPost('userfile');
            if (isset($_FILES['userfile']['name'])) {
                include "SimpleXLSX.php";
                $excel = SimpleXLSX::parse($_FILES['userfile']['tmp_name']);
                $arraydata = $excel->rows();    
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        // return redirect()->to("/finance/rkap/upload/")->with('message', $message);
        //dd($arraydata);
        $data['upl'] = $arraydata;

        $data['ready_save'] = true;

        echo view('pages/finance/rkap_upload', $data);
    }
    public function upl_data()
    {   
        $data['title'] = "CMS – Upload RKAP";  

        $arrdata=$_GET['data'] ?? false;
        //dd($arrdata);
        $arrdata=json_decode($arrdata);
        $result = array();
        
        foreach ($arrdata as $key => $row) {
            if ($key > 0) {
                
                $result[$key - 1] = $row;

                $year = $row[0];
                $month = $row[1];
                $ctype = $row[2];
                $shipment = $row[3];
                $price = $row[4];
                $quantity = $row[5];
                $cost = $row[6];

                $bdgtid = "BF-".$year."-".$month."-";

                // R0 = Data belum pernah Edit
                // R1 = Jika data sudah pernah diedit 1 kali
                $stat = "R0";

                $FiInRkps = new FiInRkps();
                // $qstr_insert = "INSERT INTO `FI_IN_RKP` (`BDGID`, `GJAHR`, `MONAT`, `SHPMN`, `PRC`, `QTY`, `STATS`, `CRTDB`, `CRTDA`) 
                //         VALUES (:BDGID:, :GJAHR:, :MONAT:, :SHPMN:, :PRC:, :QTY:, :STATS:, :CRTDB:, :CRTDA:);";
                // $qinsert = $db->query($qstr_insert,[
                $FiInRkps->save([
                    'BDGID' => $bdgtid,
                    'GJAHR' => $year,
                    'MONAT' => $month,
                    'SHPMN' => $shipment,
                    'PRC' => $price,
                    'QTY' => $quantity,
                    'STATS' => $stat,
                    'TYPE' => $ctype,
                    'COST' => $cost,
                    'CRTDB' => session()->get('username'),
                    'CRTDA' => Time::now()->format('Y-m-d H:i:s'),
                ]);
                
                
                $idaut = $FiInRkps->getInsertID();
                if ($idaut) {
                    $id0 = "";
                    $do = 4 - strlen($idaut);
                    if ($do > 0) {
                        for($i = 1; $i <= $do; $i++) {
                            $id0 = "0".$id0;
                        }
                    }
                    $FiInRkp_upd = $FiInRkps->find($idaut);

                    $idaut = $id0.$idaut; 
                    $FiInRkp_upd['BDGID'] = $FiInRkp_upd['BDGID'].$idaut;
                    $FiInRkps->save($FiInRkp_upd);

                    $result[$key - 1][7] = "Save success";
                }else{
                    $errorstr = $FiInRkps->errors();
                    $result[$key - 1][7] = $errorstr;
                } 
                
            }
        }

        $message = "Data Uploaded";

        $data['upl'] = $result;

        $data['ready_save'] = false;

        echo view('pages/finance/rkap_upload', $data);
    } 

}
