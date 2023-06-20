<?php
namespace App\Controllers\sales;

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
        $db = db_connect();
        $data['shipment']=$db->query("select * from T_SAL_SHIPMENT tb1 
        left join T_SAL_LAYCAN tb2 on tb2.shipment_no=tb1.shipment_id
        where (tb2.item_activity='Contract' OR tb2.item_activity='L/C' OR tb2.item_activity='Shipping Instruction'
        OR tb2.item_activity='B/L'
        OR tb2.item_activity='COO'
        OR tb2.item_activity='Certificate (COA, COW, COW, CDS, CHS)'
        OR tb2.item_activity='submit to finance'
        ) AND tb2.status='2'
        ")->getResult();
        $data['FI_ZTERM']=$db->query("select * from ZTERM")->getResult();
        $data['ZFIT_CMSPPN']=$db->query("select * from ZFIT_CMSPPN")->getResult();
        $data['T_MDCUSTOMER']=$db->query("select * from T_MDCUSTOMER")->getResult();
        $data['T_PRPS']=$db->query("select * from T_PRPS where PBUKR ='HH10'")->getResult();
        $data['FI_CUR_EXC']=$db->query("select DISTINCT(TCURR) from FI_CUR_EXC where KURST = 'B' & 'M'")->getResult();
        $data['FI_CEPC']=$db->query("select * from FI_CEPC where KHINR like 'HH10%'")->getResult();
        $data['FI_AUFK']=$db->query("select * from FI_AUFK where BUKRS like 'HH10%'")->getResult();
        $data['T_SAL_CONTRACT_ORDER']=$db->query("select * from T_SAL_CONTRACT_ORDER where status='1'")->getResult();
        // $data['FI_CUR_EXC']=$db->query("select * from FI_CUR_EXC")->getResult();
        echo view('pages/sales/fi_sales_inv', $data);
    }

    public function get_fi_sales_inv(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from FI_SALES_INV where deleted_at=''");
        return $this->respond($query->getResult(), 200);
    }
    
    private function generateId()
    {
        $FiSalesInv = new FiSalesInv();
        $builder = $FiSalesInv->builder();
        $builder->select('MAX(id) as max_id');
        $tgl = date('Y')."/".date('m')."/";
        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = $tgl  . "00001";
            return $new_id;
        } else {
            $new_id = $tgl . str_pad(substr($max_id['max_id'], -5) + 1, 5, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function fi_sales_inv_insert(){
        $data=$this->request->getJSON();
        $data->CPUDT=date('Y-m-d H:i:s');
        $data->DOC_NO=$this->generateId();
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
        // $this->GLogs->before_update($id,'FI_SALES_INV','id');
        $FiSalesInv->update($id, $data);
        // $this->GLogs->after_update($id,'FI_SALES_INV','id');
        return $this->respond($data, 200);
    }

    public function fi_sales_inv_delete($id)
    {
        $this->GLogs->before_delete($id,'FI_SALES_INV','id');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        // $query = $db->query("update FI_SALES_INV set deleted_at='$date' where id='$id'");
        $query = $db->query("delete from FI_SALES_INV  where id='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }


    public function fi_sales_inv_sap(){
        $db = db_connect();
        $SHIPMENT_ID=$_GET['SHIPMENT_ID'];
        $BELNR=$_GET['BELNR'];
        $STBLG=$_GET['STBLG'];
        if($STBLG){
            $db->query("update FI_SALES_INV set 
            BELNR='$BELNR',
            STBLG='$STBLG',
            FNL_AMOUNT=FNL_AMOUNT * -1,
            FNL_PRICE=FNL_PRICE * -1,
            FNL_QTY=FNL_QTY * -1
            where shipment_id='$SHIPMENT_ID'");
            $db->query("update T_SAL_PRICE set 
            FINAL_AMOUNT=FINAL_AMOUNT * -1,
            AMOUNT=AMOUNT * -1
            where shipment_id='$SHIPMENT_ID'");
        }
        return $this->respond(array(), 200);
    }
}
