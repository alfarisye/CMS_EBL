<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\FiSalesInv;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class FiSalesInvs extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function fi_sales_inv()
    {
        $data['title'] = "FI SALES INV";
        echo view('pages/fi_sales_inv', $data);
    }

    public function get_fi_sales_inv(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from FI_SALES_INV where deleted_at=''");
        return $this->respond($query->getResult(), 200);
    }
    
    public function fi_sales_inv_insert(){
        $data=$this->request->getJSON();
        $FiSalesInv = new FiSalesInv();
        $FiSalesInv->save($data);
        $this->GLogs->after_insert('FI_SALES_INV');
        return $this->respond($data, 200);
    }

    public function fi_sales_inv_update($id)
    {
        $data=$this->request->getJSON();
        $FiSalesInv = new FiSalesInv();
        $FiSalesInv->find($id);
        $this->GLogs->before_update($id,'FI_SALES_INV','id');
        $FiSalesInv->update($id, $data);
        $this->GLogs->after_update($id,'FI_SALES_INV','id');
        return $this->respond($FiSalesInv, 200);
    }

    public function fi_sales_inv_delete($id)
    {
        $this->GLogs->before_delete($id,'FI_SALES_INV','id');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("update FI_SALES_INV set deleted_at='$date' where id='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
