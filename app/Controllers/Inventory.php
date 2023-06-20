<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QualityReport;
use App\Models\GLogs;
use App\Models\Sales\MasterActivity;
use App\Models\Inventory\InqueryReceive;
use App\Models\Inventory\InqueryTransfer;
use App\Models\Inventory\ExplosiveMaterial;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class Inventory extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }


    public function dashboard()
    {
        $data['title'] = "Inventory Dashboard";
        echo view('pages/inventory/dashboard', $data);
    }

    public function ex_material()
    {
        $data['title'] = "Stock Explosive Material";
        echo view('pages/inventory/explosive_material', $data);
    }

    public function get_inquiry_receive()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        // if ($dari_tanggal && $sampai_tanggal) {
        //     $query = $db->query("select SUM(Net_Weigh) as total, DAY(Posting_Date) AS day
        //     from inquiry_receive where 
        //      Posting_Date Between '$dari_tanggal' AND '$sampai_tanggal' AND stat='1' GROUP BY day");
        // } else {
        //     $year = date('Y');
        //     $query = $db->query("select SUM(Net_Weigh) as total, MONTH(Posting_Date) AS bulan
        //         from inquiry_receive where YEAR(Posting_Date) = '$year' AND stat='1'
        //         GROUP BY bulan
        //     ");
        // }
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("
            select ABS(SUM(stock)) as total, DAY(posting_date) AS day
            from im_stock_raw where 
             posting_date Between '$dari_tanggal' AND '$sampai_tanggal' GROUP BY day");
        } else {
            $year = date('Y');
            $query = $db->query("select ABS(SUM(stock)) as total, MONTH(posting_date) AS bulan,
                (select SUM(stock) as total from im_stock_raw where YEAR(posting_date)='$year') as totalAll 
                from im_stock_raw where YEAR(posting_date) = '$year' AND posting_date=CONCAT(YEAR(posting_date), '-', MONTH(posting_date), '-25')
                GROUP BY bulan
            ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function get_inquiry_transfer()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("select ABS(SUM(Net_Weigh)) as total, DAY(Posting_Date) AS day
            from inquiry_transfer where 
             Posting_Date Between '$dari_tanggal' AND '$sampai_tanggal' GROUP BY day");
        } else {
            $year = date('Y');
            $query = $db->query("select ABS(SUM(Net_Weigh)) as total, MONTH(Posting_Date) AS bulan
            from inquiry_transfer where YEAR(Posting_Date) = '$year' 
            GROUP BY bulan
            ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function get_inquiry_port()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        // if ($dari_tanggal && $sampai_tanggal) {
        //     $query = $db->query("select SUM(Weight_In_Port) as total, DAY(Posting_Date) AS day
        //     from inquiry_port where 
        //      Posting_Date Between '$dari_tanggal' AND '$sampai_tanggal' GROUP BY day");
        // } else {
        //     $year = date('Y');
        //     $query = $db->query("select SUM(Weight_In_Port) as total, MONTH(Posting_Date) AS bulan
        //     from inquiry_port where YEAR(Posting_Date) = '$year' 
        //     GROUP BY bulan
        //     ");
        // }
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("
            select ABS(SUM(stock)) as total, DAY(posting_date) AS day
            from im_stock_port where 
             posting_date Between '$dari_tanggal' AND '$sampai_tanggal' GROUP BY day");
        } else {
            $year = date('Y');
            $query = $db->query("select ABS(SUM(stock)) as total, 
                (select SUM(stock) as total from im_stock_port where YEAR(posting_date)='$year') as totalAll, 
                MONTH(posting_date) AS bulan
                from im_stock_port where YEAR(posting_date) = '$year' AND posting_date=CONCAT(YEAR(posting_date), '-', MONTH(posting_date), '-25')
                GROUP BY bulan
            ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function get_crushcoal()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("select SUM(cc_qty) as total, DAY(created_on) AS day
            from log_t_crushcoal where 
             created_on Between '$dari_tanggal' AND '$sampai_tanggal' GROUP BY day");
        } else {
            $year = date('Y');
            $query = $db->query("select SUM(cc_qty) as total, MONTH(created_on) AS bulan
            from log_t_crushcoal where YEAR(created_on) = '$year' 
            GROUP BY bulan
            ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function get_t_crushcoal()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        // if ($dari_tanggal && $sampai_tanggal) {
        //     $query = $db->query("select SUM(cc_qty) as total, DAY(production_date) AS day
        //     from t_crushcoal where 
        //      production_date Between '$dari_tanggal' AND '$sampai_tanggal'AND status='approved' GROUP BY day");
        // } else {
        //     $year = date('Y');
        //     $query = $db->query("select SUM(cc_qty) as total, MONTH(production_date) AS bulan
        //     from t_crushcoal where YEAR(production_date) = '$year' AND status='approved'
        //     GROUP BY bulan
        //     ");
        // }
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("
            select SUM(stock) as total, DAY(posting_date) AS day
            from im_stock_cc where 
             posting_date Between '$dari_tanggal' AND '$sampai_tanggal' GROUP BY day");
        } else {
            $year = date('Y');
            $query = $db->query("select SUM(stock) as total, MONTH(posting_date) AS bulan,
                (select SUM(stock) as total from im_stock_cc where YEAR(posting_date)='$year') as totalAll 
                from im_stock_cc where YEAR(posting_date) = '$year' AND posting_date=CONCAT(YEAR(posting_date), '-', MONTH(posting_date), '-25')
                GROUP BY bulan
            ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function get_t_crushcoal_latest(){
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * from im_stock_cc ORDER BY posting_date DESC LIMIT 1");
        return $this->respond($query->getResult(), 200);
    }
    

    public function get_stock_explosive_material()
    {
        @$sort = $_GET['sort'];
        @$limit = $_GET['limit'] ? "limit " . $_GET['limit'] . "" : '';
        @$order = $_GET['order'] ? $_GET['order'] : "";
        $db = \Config\Database::connect();
        if ($sort) {
            $query = $db->query("select * from im_stock_explsvmaterial order by $order $sort $limit");
        } else {
            $query = $db->query("select * from im_stock_explsvmaterial ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function stock_explosive_material_insert()
    {
        $data = $this->request->getJSON();
        $db = \Config\Database::connect();
        $ExplosiveMaterial = new ExplosiveMaterial();
        $ExplosiveMaterial->save($data);
        $this->GLogs->after_insert('im_stock_explsvmaterial');
        return $this->respond($data, 200);
    }

    public function stock_explosive_material_update($id)
    {
        $data = $this->request->getJSON();
        $ExplosiveMaterial = new ExplosiveMaterial();
        $ExplosiveMaterial->find($id);
        $this->GLogs->before_update($id, 'im_stock_explsvmaterial');
        $ExplosiveMaterial->update($id, $data);
        $this->GLogs->after_update($id, 'im_stock_explsvmaterial');
        return $this->respond($ExplosiveMaterial, 200);
    }

    public function stock_explosive_material_delete($id)
    {
        $this->GLogs->before_delete($id, 'im_stock_explsvmaterial');
        $db = \Config\Database::connect();
        $query = $db->query("delete from im_stock_explsvmaterial where id_explsvmaterial='$id'");
        $data = array(array("data" => $id));
        return $this->respond($data, 200);
    }

    public function get_sales_order()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        @$blank = $_GET['blank'];
        @$status = $_GET['status'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            if ($blank) {
                $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status='1' AND date between '$dari_tanggal' AND '$sampai_tanggal' ");
                // $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status!='2' AND date between '$dari_tanggal' AND '$sampai_tanggal' AND contract_no=''");
            } else {
                $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status!='2' AND date between '$dari_tanggal' AND '$sampai_tanggal'");
            }
        } else {
            if ($status) {
                $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status='$status'");
            } else {
                $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status!='2'");
            }
        }
        return $this->respond($query->getResult(), 200);
    }

    // // == #Tempcode Malik
}
