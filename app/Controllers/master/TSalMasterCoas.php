<?php
namespace App\Controllers\master;

use App\Controllers\BaseController;
use App\Models\master\TSalMasterCoa;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class TSalMasterCoas extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function t_sal_master_coa()
    {
        $data['title'] = "T SAL MASTER COA";
        echo view('pages/master/t_sal_master_coa', $data);
    }

    public function get_t_sal_master_coa(){
        @$limit=$_GET['limit']?"limit ".$_GET['limit']:'';
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_MASTER_COA where status!='X' AND deleted_at='' $limit");
        return $this->respond($query->getResult(), 200);
    }
    
    public function t_sal_master_coa_insert(){
        $data=$this->request->getJSON();
        $db = \Config\Database::connect();
        $cek = $db->query("select * from T_SAL_MASTER_COA where sequence='".$data->sequence."'");
        if(count($cek->getResult())>0){
            return $this->respond(array("message"=>"Sequence code already on database"), 404,"Sequence code already on database");
        }else{
            $TSalMasterCoa = new TSalMasterCoa();
            $TSalMasterCoa->save($data);
            $this->GLogs->after_insert('T_SAL_MASTER_COA');
            return $this->respond($data, 200);
        }
    }

    public function t_sal_master_coa_update($id)
    {
        $data=$this->request->getJSON();
        $TSalMasterCoa = new TSalMasterCoa();
        $TSalMasterCoa->find($id);
        $this->GLogs->before_update($id,'T_SAL_MASTER_COA');
        $TSalMasterCoa->update($id, $data);
        $this->GLogs->after_update($id,'T_SAL_MASTER_COA');
        return $this->respond($TSalMasterCoa, 200);
    }

    public function t_sal_master_coa_delete($id)
    {
        $this->GLogs->before_delete($id,'T_SAL_MASTER_COA');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("update T_SAL_MASTER_COA set deleted_at='$date' where id='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
