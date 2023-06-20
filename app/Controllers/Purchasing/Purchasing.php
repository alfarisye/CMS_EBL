<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class Purchasing extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function index()
    {
        $data['topSupplier'] = $this->topSupplier();
        $data['topCategory'] = $this->topCategory();
        $data['title'] = "Purchasing";
        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/purchasing/dashboard', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function topSupplier(){
        $db = \Config\Database::connect();
        $sql = "select sum(t1.NETWR/1000000) as TOTAL_NETWR, t2.LIFNR, 
        (select NAME1 from t_vendor tv where tv.LIFNR = t2.LIFNR and tv.BUKRS = t1.BUKRS limit 1) AS nm_vendor
        from t_po_item t1 join t_po t2 on t1.EBELN = t2.EBELN
        where t1.LOEKZ <> 'L'
          and t1.BUKRS = 'HH10'
          group by t2.LIFNR
          order by TOTAL_NETWR DESC limit 5";

        $query = $db->query($sql);
        $data = $query->getResultArray();
        return $data;
    }

    public function topCategory(){
        $db = \Config\Database::connect();
        $sql = "select sum(t1.NETWR/1000000) as TOTAL_NETWR, t2.MATKL
        from t_po_item t1 join T_MDMATERIAL t2 on t1.MATNR = t2.MATNR
        where t1.LOEKZ <> 'L'
          and t1.BUKRS = 'HH10'
          group by t2.MATKL
          order by TOTAL_NETWR DESC;";

        $query = $db->query($sql);
        $data = $query->getResultArray();
        return $data;
    }

    public function get_purchasing_total(){
        @$dari_tanggal=$_GET['dari_tanggal'];
        @$sampai_tanggal=$_GET['sampai_tanggal'];
        @$type=$_GET['type'];
        $db = \Config\Database::connect();
        if($type=='purchase_order'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("select COALESCE(SUM(tb1.NETWR), 0) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN where  tb1.WERKS IN ('10G1','10H1') AND tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' order by tb1.id desc");
            }
            else{
                $year = date('Y');
                $query = $db->query("select COALESCE(SUM(tb1.NETWR), 0) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN where  tb1.WERKS IN ('10G1','10H1') AND YEAR(tb2.BEDAT)='$year' order by tb1.id desc");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='cost_invoice'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("select COALESCE(SUM(tb1.TBINV), 0) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN where  tb1.WERKS IN ('10G1','10H1') AND tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' order by tb1.id desc");
            }
            else{
                $year = date('Y');
                $query = $db->query("select COALESCE(SUM(tb1.TBINV), 0) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN where  tb1.WERKS IN ('10G1','10H1') AND YEAR(tb2.BEDAT)='$year' order by tb1.id desc");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='pr_estimate'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("select COALESCE(SUM(PREIS), 0) as total from t_pr where BADAT between '$dari_tanggal' AND '$sampai_tanggal' order by id desc");
            }
            else{
                $year = date('Y');
                $query = $db->query("select COALESCE(SUM(PREIS), 0) as total from t_pr where YEAR(BADAT)='$year' order by id desc");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='average_po'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("select AVG(tb1.TBINV) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN where tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' order by tb1.id desc");
            }
            else{
                $year = date('Y');
                $query = $db->query("select AVG(tb1.TBINV) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN where YEAR(tb2.BEDAT)='$year' order by tb1.id desc");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='pr_doc'){
            if($dari_tanggal && $sampai_tanggal){
                // $query = $db->query("select ((select COUNT(DISTINCT BANFN) as total from t_pr 
                // where BADAT between '$dari_tanggal' AND '$sampai_tanggal' AND EBELN!='' 
                // AND FULL_RELEASE='X') as total 
                // / 
                // ((select COUNT(DISTINCT BANFN) as total from t_pr 
                // where BADAT between '$dari_tanggal' AND '$sampai_tanggal' AND EBELN='' 
                // AND FULL_RELEASE!='X')+(select COUNT(DISTINCT BANFN) as total from t_pr 
                // where BADAT between '$dari_tanggal' AND '$sampai_tanggal' AND EBELN!='' 
                // AND FULL_RELEASE='X'))) * 100 as total");

                $query = $db->query("select ((select COUNT(DISTINCT BANFN) as total from t_pr 
                where BADAT between '$dari_tanggal' AND '$sampai_tanggal' AND EBELN!='' AND FULL_RELEASE='X')
                /
                ((select COUNT(DISTINCT BANFN) as total from t_pr 
                where BADAT between '$dari_tanggal' AND '$sampai_tanggal' AND EBELN!='' AND FULL_RELEASE='X')
                +
                (select COUNT(DISTINCT BANFN) as total from t_pr 
                where BADAT between '$dari_tanggal' AND '$sampai_tanggal' AND EBELN='' AND FULL_RELEASE!='X'))) 
                *100 as total");
            }
            else{
                $year = date('Y');
                $query = $db->query("select ((select COUNT(DISTINCT BANFN) as total from t_pr 
                where YEAR(BADAT)='$year' AND EBELN!='' AND FULL_RELEASE='X')
                /
                ((select COUNT(DISTINCT BANFN) as total from t_pr 
                where YEAR(BADAT)='$year' AND EBELN!='' AND FULL_RELEASE='X')
                +
                (select COUNT(DISTINCT BANFN) as total from t_pr 
                where YEAR(BADAT)='$year' AND EBELN='' AND FULL_RELEASE!='X'))) 
                *100 as total");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='pr_item'){
            if($dari_tanggal && $sampai_tanggal){
                // $query = $db->query("select (select COUNT(BANFN) as total from t_pr where BADAT 
                // between '$dari_tanggal' AND '$sampai_tanggal' 
                // AND EBELN!='' AND FULL_RELEASE='X') 
                // / 
                // ((select COUNT(BANFN) as total from t_pr where BADAT 
                // between '$dari_tanggal' AND '$sampai_tanggal' 
                // AND EBELN='' AND FULL_RELEASE!='X')+(select COUNT(BANFN) as total from t_pr where 
                // BADAT between '$dari_tanggal' AND '$sampai_tanggal' 
                // AND EBELN!='' AND FULL_RELEASE='X'))) 
                // *100 as total");

                $query = $db->query("select ((select COUNT(BNFPO) as total from t_pr where BADAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND EBELN!='' AND FULL_RELEASE='X') 
                /
                ((select COUNT(BNFPO) as total from t_pr where BADAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND EBELN='' AND FULL_RELEASE!='X')
                +
                (select COUNT(BNFPO) as total from t_pr where BADAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND EBELN!='' AND FULL_RELEASE='X'))) 
                * 100 as total");
            }
            else{
                $year = date('Y');
                $query = $db->query("select ((select COUNT(BNFPO) as total from t_pr where YEAR(BADAT)='$year' 
                AND EBELN!='' AND FULL_RELEASE='X') 
                /
                ((select COUNT(BNFPO) as total from t_pr where YEAR(BADAT)='$year' 
                AND EBELN='' AND FULL_RELEASE!='X')
                +
                (select COUNT(BNFPO) as total from t_pr where YEAR(BADAT)='$year' 
                AND EBELN!='' AND FULL_RELEASE='X'))) 
                * 100 as total");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='po_doc'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("
                select ((select COUNT(DISTINCT tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND tb2.FULL_RELEASE='X') / ((select COUNT(DISTINCT tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND tb2.FULL_RELEASE!='X') + (select COUNT(DISTINCT tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND tb2.FULL_RELEASE='X'))) * 100 as total 
                ");
            }
            else{
                $year = date('Y');
                $query = $db->query("select ((select COUNT(DISTINCT tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where YEAR(tb2.BEDAT)='$year' 
                AND tb2.FULL_RELEASE='X') / ((select COUNT(DISTINCT tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where YEAR(tb2.BEDAT)='$year' 
                AND tb2.FULL_RELEASE!='X') + (select COUNT(DISTINCT tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where YEAR(tb2.BEDAT)='$year' 
                AND tb2.FULL_RELEASE='X'))) * 100 as total 
                ");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='po_item'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("
                select ((select COUNT(tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND tb2.FULL_RELEASE='X') / ((select COUNT(tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND tb2.FULL_RELEASE!='X') + (select COUNT(tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND tb2.FULL_RELEASE='X'))) * 100 as total 
                ");
            }
            else{
                $year = date('Y');
                $query = $db->query("select ((select COUNT(tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where YEAR(tb2.BEDAT)='$year' 
                AND tb2.FULL_RELEASE='X') / ((select COUNT(tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where YEAR(tb2.BEDAT)='$year' 
                AND tb2.FULL_RELEASE!='X') + (select COUNT(tb1.EBELN) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where YEAR(tb2.BEDAT)='$year' 
                AND tb2.FULL_RELEASE='X'))) * 100 as total 
                ");
            }
            return $this->respond($query->getResult(), 200);
        }

        // ============== CREATED 
        else if($type=='pr_created'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("select COUNT(DISTINCT BANFN) as total from t_pr 
                where BADAT between '$dari_tanggal' AND '$sampai_tanggal'
                AND EBAKZ!='X' 
                ");
            }
            else{
                $year = date('Y');
                $query = $db->query("select COUNT(DISTINCT BANFN) as total from t_pr 
                where YEAR(BADAT)='$year'
                AND EBAKZ!='X' 
                ");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='pr_item_created'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("select COUNT(BNFPO) as total from t_pr 
                where BADAT between '$dari_tanggal' AND '$sampai_tanggal'
                 AND EBAKZ!='X' 
                ");
            }
            else{
                $year = date('Y');
                $query = $db->query("select COUNT(BNFPO) as total from t_pr 
                where YEAR(BADAT)='$year'
                 AND EBAKZ!='X' 
                ");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='po_created'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("SELECT COUNT(tb2.EBELN) as total from t_po tb2 
                where tb2.BEDAT BETWEEN '$dari_tanggal' AND '$sampai_tanggal'
                ");
            }
            else{
                $year = date('Y');
                $query = $db->query("
                SELECT COUNT(tb2.EBELN) as total from t_po tb2 
                where YEAR(tb2.BEDAT)='$year'
                ");
            }
            return $this->respond($query->getResult(), 200);
        }else if($type=='po_item_created'){
            if($dari_tanggal && $sampai_tanggal){
                $query = $db->query("select COUNT(tb1.EBELP) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal' 
                AND tb1.LOEKZ!='L' 
                ");
            }
            else{
                $year = date('Y');
                $query = $db->query("select COUNT(tb1.EBELP) as total from t_po_item tb1 left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where YEAR(tb2.BEDAT)='$year' 
                AND tb1.LOEKZ!='L' 
                ");
            }
            return $this->respond($query->getResult(), 200);
        }
        
    }

    public function get_purchasing_chart(){
        @$dari_tanggal=$_GET['dari_tanggal'];
        @$sampai_tanggal=$_GET['sampai_tanggal'];
        @$type=$_GET['type'];
        $db = \Config\Database::connect();
        $data=array();
        if ($dari_tanggal && $sampai_tanggal) {
            $query1 = $db->query("select COUNT(BANFN) as total, MONTH(BADAT) AS bulan
                from t_pr where Between '$dari_tanggal' AND '$sampai_tanggal'
                AND LOEKZ!='X' AND EBAKZ!='X' AND FULL_RELEASE='X' AND WERKS IN ('10G1','10H1')
                GROUP BY bulan");
            $pr_release=$query1->getResult();

            $query2 = $db->query("
                select COUNT(tb1.EBELN) as total, MONTH(BEDAT) AS bulan from t_po_item tb1 
                left join t_po tb2 on tb2.EBELN=tb1.EBELN 
                where  tb2.BEDAT between '$dari_tanggal' AND '$sampai_tanggal'
                AND tb1.LOEKZ!='L'  AND tb2.FULL_RELEASE='X'
                GROUP BY bulan
             ");
            $po_create=$query2->getResult();
        } else {
            $year = date('Y');
            $query1 = $db->query("select COUNT(BANFN) as total, MONTH(BADAT) AS bulan
                from t_pr where YEAR(BADAT) = '$year' 
                AND LOEKZ!='X' AND EBAKZ!='X' AND FULL_RELEASE='X' AND WERKS IN ('10G1','10H1')
                GROUP BY bulan
            ");
            $pr_release=$query1->getResult();

            $query2 = $db->query("
            select COUNT(tb1.EBELN) as total, MONTH(BEDAT) AS bulan from t_po_item tb1 
            left join t_po tb2 on tb2.EBELN=tb1.EBELN 
            where  YEAR(tb2.BEDAT) = '$year'
            AND tb1.LOEKZ!='L'  AND tb2.FULL_RELEASE='X'
            GROUP BY bulan
             ");
            $po_create=$query2->getResult();
        }
        $data=array(
            'pr_release'=>$pr_release,
            'po_create'=>$po_create
        );
        return $this->respond($data, 200);
    }

    public function get_purchasing_estimate(){
        @$dari_tanggal=$_GET['dari_tanggal'];
        @$sampai_tanggal=$_GET['sampai_tanggal'];
        @$type=$_GET['type'];
        $db = \Config\Database::connect();
        $data=array();
        if($dari_tanggal && $sampai_tanggal){
            $query1 = $db->query("select COALESCE(SUM(PREIS * MENGE), 0) as total, MONTH(BADAT) AS bulan
                from t_pr where BADAT BETWEEN '$dari_tanggal' AND '$sampai_tanggal'
                AND WERKS IN ('10G1','10H1')
                AND LOEKZ!='X' AND EBAKZ!='X'
                GROUP BY bulan
            ");
            $pr_release=$query1->getResult();
    
            $query2 = $db->query("
            select COALESCE(SUM(tb1.NETWR), 0) as total, MONTH(BEDAT) AS bulan from t_po_item tb1 
            left join t_po tb2 on tb2.EBELN=tb1.EBELN 
            where  BADAT BETWEEN '$dari_tanggal' AND '$sampai_tanggal'
            AND tb1.LOEKZ!='L' 
                AND WERKS IN ('10G1','10H1')
                GROUP BY bulan
                ");
            $po_create=$query2->getResult();
            $data=array(
                'pr_release'=>$pr_release,
                'po_create'=>$po_create
            );
            return $this->respond($data, 200);
        }else{
            $year = date('Y');
            $query1 = $db->query("select COALESCE(SUM(PREIS * MENGE), 0) as total, MONTH(BADAT) AS bulan
                from t_pr where YEAR(BADAT) = '$year' 
                AND WERKS IN ('10G1','10H1')
                AND LOEKZ!='X' AND EBAKZ!='X'
                GROUP BY bulan
            ");
            $pr_release=$query1->getResult();
    
            $query2 = $db->query("
            select COALESCE(SUM(tb1.NETWR * MENGE), 0) as total, MONTH(BEDAT) AS bulan from t_po_item tb1 
            left join t_po tb2 on tb2.EBELN=tb1.EBELN 
            where  YEAR(tb2.BEDAT) = '$year'
            AND tb1.LOEKZ!='L' 
                AND WERKS IN ('10G1','10H1')
                GROUP BY bulan
                ");
            $po_create=$query2->getResult();
            $data=array(
                'pr_release'=>$pr_release,
                'po_create'=>$po_create
            );
            return $this->respond($data, 200);
        }
    }

    public function get_average(){
        @$dari_tanggal=$_GET['dari_tanggal'];
        @$sampai_tanggal=$_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        $data=array();
        if($dari_tanggal && $sampai_tanggal){
            $query1 = $db->query("SELECT SUM(LT_PRPO) / (SELECT COUNT(id) AS total FROM t_pr WHERE FULL_RELEASE='X' AND BADAT BETWEEN '$dari_tanggal' AND '$sampai_tanggal') AS total FROM t_pr 
                WHERE BADAT BETWEEN '$dari_tanggal' AND '$sampai_tanggal'
            ");
            $query1=$query1->getResult();
    
            $query2 = $db->query("SELECT SUM(LT_PR) / (SELECT COUNT(id) AS total FROM t_pr WHERE FULL_RELEASE='X' AND BADAT BETWEEN '$dari_tanggal' AND '$sampai_tanggal') AS total FROM t_pr 
             WHERE BADAT BETWEEN '$dari_tanggal' AND '$sampai_tanggal'
            ");
            $query2=$query2->getResult();
            $data=array(
                'pr_created'=>$query2,
                'po_created'=>$query1,
            );
            return $this->respond($data, 200);
        }else{
            $year = date('Y');
            $query1 = $db->query("SELECT SUM(LT_PRPO) 
            / 
            (SELECT COUNT(id) AS total FROM t_pr 
            WHERE FULL_RELEASE='X' AND YEAR(BADAT)='$year') 
            AS total FROM t_pr WHERE YEAR(BADAT)='$year'
            ");
            $query1=$query1->getResult();
    
            $query2 = $db->query("SELECT SUM(LT_PR) 
            /
            (SELECT COUNT(id) AS total FROM t_pr 
            WHERE FULL_RELEASE='X' AND YEAR(BADAT)='$year') 
            AS total FROM t_pr 
            WHERE YEAR(BADAT)='$year'
                ");
            $query2=$query2->getResult();
            $data=array(
                'pr_created'=>$query2,
                'po_created'=>$query1,
            );
            return $this->respond($data, 200);
        }
    }

    public function get_list_wbs(){
        $db = \Config\Database::connect();
        $listWBS = $db->query("SELECT tb1.*,tb2.WLP00,
        (SELECT SUM(tb4.WLP00) AS total FROM T_PRPS tb3 LEFT JOIN T_RPSCO tb4 on tb4.OBJNR=tb3.OBJNR WHERE tb3.POSID LIKE CONCAT(SUBSTRING(tb1.POSID,1,LENGTH(tb1.POSID)-2),'%') AND tb4.VORGA='KBUD') AS total
         FROM T_PRPS tb1 LEFT JOIN T_RPSCO tb2 on tb2.OBJNR=tb1.OBJNR WHERE tb1.POSID LIKE 'AB3.11-06.02.%.00' AND tb2.VORGA='KBUD'");
        $listWBS=$listWBS->getResult();
        return $this->respond($listWBS, 200);
    }

    public function get_list_wbs_budget(){
        @$wbs=$_GET['wbs'].'%';
        // var_dump($wbs);
        $db = \Config\Database::connect();
        $listWBS = $db->query("SELECT tb1.*,tb2.WLP00 FROM T_PRPS tb1 LEFT JOIN T_RPSCO tb2 on tb2.OBJNR=tb1.OBJNR WHERE tb1.POSID LIKE '$wbs' AND tb2.VORGA='KBUD'");
        $listWBS=$listWBS->getResult();
        return $this->respond($listWBS, 200);
    }
    
    // // == #Tempcode Malik
}
