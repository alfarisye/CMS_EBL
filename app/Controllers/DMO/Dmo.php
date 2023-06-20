<?php

namespace App\Controllers\DMO;

use App\Controllers\BaseController;
use App\Models\QualityReport;
use App\Models\GLogs;
use App\Models\DMO\MasterDmo;
use App\Models\DMO\SalesDmo;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class Dmo extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function master_dmo()
    {
        $data['title'] = "Master DMO";
        echo view('pages/master/master_dmo', $data);
    }

    public function get_master_dmo(){
        @$limit=$_GET['limit']?"limit ".$_GET['limit']:'';
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_MASTER_DMO where status!='X' $limit");
        return $this->respond($query->getResult(), 200);
    }
    
    public function master_dmo_insert(){
        $data=$this->request->getJSON();
        $db = \Config\Database::connect();
        $cek = $db->query("select * from T_SAL_MASTER_DMO where sequence='".$data->sequence."'");
        if(count($cek->getResult())>0){
            return $this->respond(array("message"=>"Sequence code already on database"), 404,"Sequence code already on database");
        }else{
            $MasterDmo = new MasterDmo();
            $MasterDmo->save($data);
            $this->GLogs->after_insert('T_SAL_MASTER_DMO');
            return $this->respond($data, 200);
        }
    }

    public function master_dmo_update($id)
    {
        $data=$this->request->getJSON();
        $MasterDmo = new MasterDmo();
        $MasterDmo->find($id);
        $this->GLogs->before_update($id,'T_SAL_MASTER_DMO');
        $MasterDmo->update($id, $data);
        $this->GLogs->after_update($id,'T_SAL_MASTER_DMO');
        return $this->respond($MasterDmo, 200);
    }

    public function master_dmo_delete($id)
    {
        $this->GLogs->before_delete($id,'T_SAL_MASTER_DMO');
        $db = \Config\Database::connect();
        $query = $db->query("delete from T_SAL_MASTER_DMO where id='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }


    public function sales_dmo()
    {
        $data['title'] = "Saes DMO";
        echo view('pages/sales/sales_dmo', $data);
    }

    public function get_sales_dmo(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_DMO ");
        return $this->respond($query->getResult(), 200);
    }
    
    public function sales_dmo_insert(){
        $data=$this->request->getJSON();
        $SalesDmo = new SalesDmo();
        $SalesDmo->save($data);
        $this->GLogs->after_insert('T_SAL_DMO');
        return $this->respond($data, 200);
    }

    public function sales_dmo_update($id)
    {
        $data=$this->request->getJSON();
        $SalesDmo = new SalesDmo();
        $SalesDmo->find($id);
        $this->GLogs->before_update($id,'T_SAL_DMO');
        $SalesDmo->update($id, $data);
        $this->GLogs->after_update($id,'T_SAL_DMO');
        return $this->respond($SalesDmo, 200);
    }

    public function sales_dmo_delete($id)
    {
        $this->GLogs->before_delete($id,'T_SAL_DMO');
        $db = \Config\Database::connect();
        $query = $db->query("delete from T_SAL_DMO where id='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }

    
    // // == #Tempcode Malik
}
