<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TSalCoa;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class TSalCoas extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function t_sal_coa()
    {
        $data['title'] = "T SAL COA";
        echo view('pages/t_sal_coa', $data);
    }

    public function get_t_sal_coa(){
        $db = \Config\Database::connect();
        $dari_tanggal=$_GET['dari_tanggal'];
        $sampai_tanggal=$_GET['sampai_tanggal'];
        $query = $db->query("select * from T_SAL_COA where deleted_at=''");
        return $this->respond($query->getResult(), 200);
    }
    
    public function t_sal_coa_insert(){
        $data=$this->request->getJSON();
        $TSalCoa = new TSalCoa();
        $TSalCoa->save($data);
        $this->GLogs->after_insert('T_SAL_COA');
        return $this->respond($data, 200);
    }

    public function t_sal_coa_update($id)
    {
        $data=$this->request->getJSON();
        $TSalCoa = new TSalCoa();
        $TSalCoa->find($id);
        $this->GLogs->before_update($id,'T_SAL_COA');
        $TSalCoa->update($id, $data);
        $this->GLogs->after_update($id,'T_SAL_COA');
        return $this->respond($TSalCoa, 200);
    }

    public function t_sal_coa_delete($id)
    {
        $this->GLogs->before_delete($id,'T_SAL_COA');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("update T_SAL_COA set deleted_at='$date' where id='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
