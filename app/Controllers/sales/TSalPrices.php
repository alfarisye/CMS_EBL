<?php
namespace App\Controllers\Sales;

use App\Controllers\BaseController;
use App\Models\Sales\TSalPrice;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class TSalPrices extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function t_sal_price()
    {
        $db = \Config\Database::connect();
        $data['title'] = "T SAL PRICE";
        $data['T_SAL_SHIPMENT'] = $db->query("select * from T_SAL_SHIPMENT where status='2'")->getResult();
        $data['FI_CUR_EXC'] = $db->query("select DISTINCT(TCURR) from FI_CUR_EXC where KURST = 'B' & 'M'")->getResult();
        echo view('pages/Sales/t_sal_price', $data);
    }

    public function get_t_sal_price(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_PRICE ");
        return $this->respond($query->getResult(), 200);
    }
    
    public function t_sal_price_insert(){
        $data=$this->request->getJSON();
        $TSalPrice = new TSalPrice();
        $TSalPrice->save($data);
        $this->GLogs->after_insert('T_SAL_PRICE');
        return $this->respond($data, 200);
    }

    public function t_sal_price_add(){
        $dataJSON=$this->request->getJSON();
        $db = \Config\Database::connect();
        @$data=$_GET['data'];
        $data=json_decode($data);
        $TSalPrice = new TSalPrice();
        $datas = $db->query("select * from T_SAL_PRICE where shipment_id='$dataJSON->shipment_id'")->getResult();
        if(count($datas)>0){
            $id=$datas[0]->id;
            $TSalPrice->update($id, $dataJSON);
            return $this->respond($dataJSON, 200);
        }else{
            $TSalPrice->save($dataJSON);
            return $this->respond($dataJSON, 200);
        }
    }

    public function t_sal_price_update($id)
    {
        $data=$this->request->getJSON();
        $TSalPrice = new TSalPrice();
        $TSalPrice->find($id);
        $this->GLogs->before_update($id,'T_SAL_PRICE','id');
        $TSalPrice->update($id, $data);
        $this->GLogs->after_update($id,'T_SAL_PRICE','id');
        return $this->respond($TSalPrice, 200);
    }

    public function t_sal_price_delete($id)
    {
        $this->GLogs->before_delete($id,'T_SAL_PRICE','id');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("delete from T_SAL_PRICE where id='$id'");
        // $query = $db->query("update T_SAL_PRICE set deleted_at='$date' where id='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
