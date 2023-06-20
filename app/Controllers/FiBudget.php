<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use App\Models\FiInBudget;
use Shuchkin\SimpleXLSX;

class FiBudget extends BaseController
{
    public function index()//Done 100%
    {
        $data['title'] = "CMS – Input Budget Finance";
        $data['filterGLAccount'] = $this->filterGLAccount();
        $data['filterWBS'] = $this->filterWBS();
        
        $db = db_connect();

        $idglaccount = $_GET['idglaccount'] ?? false;

        if ($idglaccount == '' or is_null($idglaccount)) {
            $qstr_MD_BUDG = "select * from FI_MD_BUDG where status ='1'";
        }else{
            $qstr_MD_BUDG = "select * from FI_MD_BUDG  where 
            SAKNR = '".$idglaccount."' and status ='1'";
        }
        
        $q_MD_BUDG = $db->query($qstr_MD_BUDG);
        $data['md_budg'] = $q_MD_BUDG->getResultArray();

        echo view('pages/finance/budgetfinance', $data);
    }

    public function add()//100% done
    {
        try {
            $db = db_connect();

            $glaccount = $this->request->getPost('glaccount');

            // logic gl desc
            $q_gldesc = $db->query("SELECT txt50 FROM FI_MD_GL where saknr = '".$glaccount."'");
            $row = $q_gldesc->getRow(0);
            $gldesc = $row->txt50;
            // logic gl desc
            
            $wbs = $this->request->getPost('wbselement');

            // logic wbs desc
            $q_wbsdesc = $db->query("SELECT POST1 FROM T_PRPS where posid = '".$wbs."'");
            $row = $q_wbsdesc->getRow(0);
            $wbsdesc = $row->POST1;
            // logic wbs desc

            $year = $this->request->getPost('year');
            $month = $this->request->getPost('month');
            $amount = $this->request->getPost('amount');

            // logic untuk budgid
            $budgid = $year.'-'.$month.'-'.substr($glaccount,0,2).'-';
            $q_count = $db->query("select count(id) as jumlah from FI_MD_BUDG WHERE id LIKE '".$budgid."%'");
            $row = $q_count->getRow(0);
            if ($row->jumlah == 0){
                $budgid = $budgid."0001";
            }
            else{
                $q_count = $db->query("select max(id) as jumlah from FI_MD_BUDG WHERE id LIKE '".$budgid."%'");
                $row = $q_count->getRow(0);
                $row->jumlah = substr($row->jumlah,-4);
                $row->jumlah = $row->jumlah + 10001;
                $row->jumlah = substr($row->jumlah,1,4);
                $budgid = $budgid.$row->jumlah;
            }
            //logic untuk budgid

            $FiInBudget = new FiInBudget();
            $FiInBudget->save([
                'BUDGID' => $budgid,
                'SAKNR' => $glaccount,
                'TXT50' => $gldesc, 
                'PSPNR' => $wbs,
                'POSID' => $wbsdesc,
                'GJAHR' => $year,
                'MONAT' => $month,
                'DMBTR' => $amount,
                'CRTBY' => session()->get('username'),        
                'CRTON' => Time::now()->format('Y-m-d H:i:s'),
                'STATUS' => 1,
            ]);
            
            $message = "Budget Finance has been created";

        } catch (\Throwable $th) {
            $message = $th->getMessage();
            echo $message;
        }
        return redirect()->to("/finance/budgetfinance/")->with('message', $message);
    }

    public function get($id)
    {
        header('Content-Type: application/json');

        $db = db_connect();
        $qstr_get = "select * from FI_MD_BUDG where ID='".$id."'"; 
        $qget = $db->query($qstr_get)->getRowArray();
        return $this->response->setJSON($qget);
    }

    public function update()
    {   
        $db = db_connect();
        $id = $this->request->getPost('edid');
        $glaccount = $this->request->getPost('edglaccount');
        // logic gl desc
        $q_gldesc = $db->query("SELECT txt50 FROM FI_MD_GL where saknr = '".$glaccount."'");
        $row = $q_gldesc->getRow(0);
        $gldesc = $row->txt50;
        // logic gl desc
        $wbs = $this->request->getPost('edwbselement');
        // logic wbs desc
        $q_wbsdesc = $db->query("SELECT POST1 FROM T_PRPS where posid = '".$wbs."'");
        $row = $q_wbsdesc->getRow(0);
        $wbsdesc = $row->POST1;
        // logic wbs desc
        $year = $this->request->getPost('edyear');
        $month = $this->request->getPost('edmonth');
        $amount = $this->request->getPost('edamount');
        
        // logic untuk budgid
        $budgid = $year.'-'.$month.'-'.substr($glaccount,0,2).'-';
        $q_count = $db->query("select count(id) as jumlah from FI_MD_BUDG WHERE id LIKE '".$budgid."%'");
        $row = $q_count->getRow(0);
        if ($row->jumlah == 0){
            $budgid = $budgid."0001";
        }
        else{
            $q_count = $db->query("select max(id) as jumlah from FI_MD_BUDG WHERE id LIKE '".$budgid."%'");
            $row = $q_count->getRow(0);
            $row->jumlah = substr($row->jumlah,-4);
            $row->jumlah = $row->jumlah + 10001;
            $row->jumlah = substr($row->jumlah,1,4);
            $budgid = $budgid.$row->jumlah;
        }
        //logic untuk budgid

        try {
            $qstr_upd = "UPDATE `FI_MD_BUDG`
                         SET 
                             `BUDGID`=:BUDGID:,
                             `SAKNR`=:SAKNR:,
                             `TXT50`=:TXT50:,
                             `PSPNR`=:PSPNR:,
                             `POSID`=:POSID:,
                             `GJAHR`=:GJAHR:,
                             `MONAT`=:MONAT:,
                             `DMBTR`=:DMBTR:,
                             `EDTBY`=:EDTBY:,
                             `EDTON`=:EDTON:
                         WHERE  `ID`=:ID:;";  
            $qdel = $db->query($qstr_upd,[
                'ID' => $id,
                'BUDGID' => $budgid,
                'SAKNR' => $glaccount,
                'TXT50' => $gldesc,
                'PSPNR' => $wbs,
                'POSID' => $wbsdesc,
                'GJAHR' => $year,
                'MONAT' => $month,
                'DMBTR' => $amount,
                'EDTBY' => session()->get('username'),        
                'EDTON' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            if ($qdel) {
                $message = "Budget Finance has been updated";
            }else{
                $message = "No data update";
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/finance/budgetfinance/")->with('message', $message);
    }
    
    public function delete($id)
    {
        $db = db_connect();
        
        try {
            $qstr_upd = "UPDATE `FI_MD_BUDG`
                         SET 
                             `EDTBY`=:EDTBY:,
                             `EDTON`=:EDTON:,
                             `STATUS`=:STATUS:
                         WHERE  `ID`=:ID:;";  
            $qdel = $db->query($qstr_upd,[
                'ID' => $id,
                'EDTBY' => session()->get('username'),        
                'EDTON' => Time::now()->format('Y-m-d H:i:s'),
                'STATUS' => '0',
            ]);
            if ($qdel) {
                $message = "Budget Finance has been updated";
            }else{
                $message = "No data update";
            }
            
        } catch (\Throwable $th) {
            echo $message = $th->getMessage();
        }
        
        return redirect()->to("/finance/budgetfinance/")->with('message', $message);
    }

    public function filterGLAccount(){
        $db = \Config\Database::connect();
        $sql = "select SAKNR,TXT50 from FI_MD_GL where bukrs = 'hh10' group by TXT50,SAKNR ";

        $query = $db->query($sql);
        $data = $query->getResultArray();
        return $data;
    }

    public function filterWBS(){
        $db = \Config\Database::connect();
        $sql = "select POSID,POST1 from T_PRPS group by POSID,POST1";

        $query = $db->query($sql);
        $data = $query->getResultArray();
        return $data;
    }

    public function form_upload()
    {
        $data['title'] = "CMS – Upload Budget Finance";    
        $data['upl'] = array();

        $data['ready_save'] = false;

        echo view('pages/finance/budgetfinance_upload', $data);
    }

    public function get_upl()
    {   
        $data['title'] = "CMS – Upload Budget Finance";    
        $arraydata = array();
        try {            
            $userfile = $this->request->getPost('userfile');
            if (isset($_FILES['userfile']['name'])) {
                require_once('SimpleXLSX.php');
                $excel = SimpleXLSX::parse($_FILES['userfile']['tmp_name']);
                $arraydata = $excel->rows();    
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        $temp='';$temp2='';
        for($i=0;$i<sizeof($arraydata);$i++){
            for($j=0;$j<6;$j++){
                if($j!=5)
                    $temp2=$arraydata[$i][$j];
                if($j==0)
                {
                        $db = db_connect();
                        $budgid = $arraydata[$i][2].'-'.$arraydata[$i][3].'-'.substr($arraydata[$i][0],0,2).'-';
                        $q_count = $db->query("select count(id) as jumlah from FI_MD_BUDG WHERE id LIKE '".$budgid."%'");
                        $row = $q_count->getRow(0);
                        if ($row->jumlah == 0){
                            $budgid = $budgid."0001";
                        }
                        else{
                            $q_count = $db->query("select max(id) as jumlah from FI_MD_BUDG WHERE id LIKE '".$budgid."%'");
                            $row = $q_count->getRow(0);
                            $row->jumlah = substr($row->jumlah,-4);
                            $row->jumlah = $row->jumlah + 10001;
                            $row->jumlah = substr($row->jumlah,1,4);
                            $budgid = $budgid.$row->jumlah;
                        }
                        $arraydata[$i][$j]=$budgid;
                }
                else{
                    $arraydata[$i][$j]=$temp;
                }
                $temp=$temp2;
            }
        };
        //dd($arraydata);
        for($i=0;$i<sizeof($arraydata);$i++){
            for($j=0;$j<7;$j++){
                if($j!=6)
                    $temp2=$arraydata[$i][$j];
                if($j==2 and $i>0)
                {
                    //dd($arraydata[$i][$j-1]);
                    // logic gl desc
                    $q_gldesc = $db->query("SELECT txt50 FROM FI_MD_GL where saknr = '".$arraydata[$i][$j-1]."'");
                    $row = $q_gldesc->getRow(0);
                    // logic gl desc
                    $arraydata[$i][$j]=$row->txt50;
                }
                elseif($j>2){
                    $arraydata[$i][$j]=$temp;
                }
                $temp=$temp2;
            }
        };
        //dd($arraydata);
        for($i=0;$i<sizeof($arraydata);$i++){
            for($j=0;$j<8;$j++){
                if($j!=7)
                    $temp2=$arraydata[$i][$j];
                if($j==4 and $i>0)
                {
                    // logic wbs desc
                    $q_wbsdesc = $db->query("SELECT POST1 FROM T_PRPS where posid = '".$arraydata[$i][$j-1]."'");
                    $row = $q_wbsdesc->getRow(0);
                    // logic wbs desc
                    //dd($row);
                    if($row == "")
                        $arraydata[$i][$j]="";
                    else
                        $arraydata[$i][$j]=$row->POST1;
                }
                elseif($j>4){
                    $arraydata[$i][$j]=$temp;
                }
                $temp=$temp2;
            }
            $arraydata[$i][8]='Ready';
        };
        //dd($arraydata);
        $data['upl'] = $arraydata;
        $data['ready_save'] = true;

        echo view('pages/finance/budgetfinance_upload', $data);
    }

    public function upl_data()
    {  
        $data['title'] = "CMS – Upload RKAP";  

        $arrdata=$_GET['data'] ?? false;
        $arrdata=json_decode($arrdata);
        $result = array();
        $db = db_connect();
        try {
            foreach ($arrdata as $key => $row) {
                if ($key > 0) {
                    $row[8]='Upload Success';
                    $glaccount = $row[1];
                    // logic gl desc
                    $q_gldesc = $db->query("SELECT txt50 FROM FI_MD_GL where saknr = '".$glaccount."'");
                    $hasil = $q_gldesc->getRow(0);
                    $gldesc = $hasil->txt50;
                    $row[2]=$gldesc;
                    //dd($hasil);
                    //dd($gldesc);
                    // logic gl desc
                    $wbs = $row[3];
                    
                    // logic wbs desc
                    $q_wbsdesc = $db->query("SELECT POST1 FROM T_PRPS where posid = '".$wbs."'");
                    $hasil = $q_wbsdesc->getRow(0);
                    $wbsdesc = "";
                    if($hasil != "")
                        $wbsdesc = $hasil->POST1;
                    
                    $row[4]=$wbsdesc;
                    // logic wbs desc
                    $year = $row[5];
                    $month = $row[6];
                    $amount = $row[7];
                    // logic untuk budgid
                    $budgid = $year.'-'.$month.'-'.substr($glaccount,0,2).'-';
                    $q_count = $db->query("select count(id) as jumlah from FI_MD_BUDG WHERE id LIKE '".$budgid."%'");
                    $hasil = $q_count->getRow(0);
                    if ($hasil->jumlah == 0){
                        $budgid = $budgid."0001";
                    }
                    else{
                        $q_count = $db->query("select max(id) as jumlah from FI_MD_BUDG WHERE id LIKE '".$budgid."%'");
                        $hasil = $q_count->getRow(0);
                        $hasil->jumlah = substr($hasil>jumlah,-4);
                        $hasil->jumlah = $hasil->jumlah + 10001;
                        $hasil->jumlah = substr($$hasil>jumlah,1,4);
                        $budgid = $budgid.$hasil->jumlah;
                    }
                    $row[0]=$budgid;
                    $result[$key - 1] = $row;
                    //logic untuk budgid
                    //=========================================
                    $FiInBudget = new FiInBudget();
                    $FiInBudget->save([
                        'BUDGID' => $budgid,
                        'SAKNR' => $glaccount,
                        'TXT50' => $gldesc, 
                        'PSPNR' => $wbs,
                        'POSID' => $wbsdesc,
                        'GJAHR' => $year,
                        'MONAT' => $month,
                        'DMBTR' => $amount,
                        'CRTBY' => session()->get('username'),        
                        'CRTON' => Time::now()->format('Y-m-d H:i:s'),
                        'STATUS' => 1,
                        
                    ]);
                    
                }
            }
            $message = "Budget Finance has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            echo $message;
        }
        
        $data['upl'] = $result;
        $data['ready_save'] = false;
        echo view('pages/finance/budgetfinance_upload', $data);

    }
}
