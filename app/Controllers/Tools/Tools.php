<?php

namespace App\Controllers\Tools;

use App\Controllers\BaseController;
use App\Models\QualityReport;
use App\Models\GLogs;
use App\Models\DMO\MasterDmo;
use App\Models\DMO\SalesDmo;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;
use App\Models\Timesheets;
use App\Models\Sales\SalesShipment;
use App\Models\CrushCoal;
use App\Models\Inventory\InqueryTransfer;
use App\Models\TCoalIndex;
use App\Models\TCostmining;
use App\Models\Sales\TSalPrice;
use Config\Database;
use DateInterval;
use DatePeriod;
use DateTime;


class Tools extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function index()
    {
        $data['title'] = "Tools";
        echo view('pages/tools/generate', $data);
    }

    public function chart()
    {
        $data['title'] = "CHART";
        echo view('pages/tools/chart', $data);
    }

    public function test()
    {
        $data['title'] = "TEST";
        echo view('pages/tools/test', $data);
    }

    


    public function get_table_field(){
        @$table=$_GET['table']?$_GET['table']:'';
        $db = \Config\Database::connect();
        // $database=getenv('database.default.database');
        $query = $db->query("select COLUMN_NAME,
        DATA_TYPE, 
        IS_NULLABLE, 
        CHARACTER_MAXIMUM_LENGTH, 
        NUMERIC_PRECISION, 
        NUMERIC_SCALE   
        from information_schema.columns 
        where table_schema = 'cms-ebl-dev' 
        and table_name = '$table'");
        return $this->respond($query->getResult(), 200);
    }
    public function monitor()
    {
        echo view('pages/tools/monitor');
    }

    public function monitor_api()
    {
         // filter month and date
         $month = $_GET['month'] ?? false;
         $year = $_GET['year'] ?? false;
         $type = $_GET['time'] ?? 'yearly';
         $date = $_GET['date'] ?? false;
 
         $now = Time::now();
         $parsed = Time::parse($now);
         if (!$month && !$year) {
             $month = $parsed->getMonth();
             $year = $parsed->getYear();
         }
         $month_sum = $month;
         if ($year != $parsed->getYear()) {
             $month_sum = 12;
         }
         $data['selectedParams'] = ['month' => $month, 'year' => $year];
         $data['todayDate'] = ['month' => $parsed->getMonth(), 'year' => $parsed->getYear()];
 
         $db = Database::connect();
 
         $Timesheet = new Timesheets();
         $builder = $Timesheet->builder();
 
         // create timbangan table
 
         // receive
         $db->query("DROP TEMPORARY TABLE IF EXISTS temp_timbangan");
         $db->query("CREATE TEMPORARY TABLE temp_timbangan SELECT Net_Weigh, Posting_Date, Supplier_Id, Supplier_Name,
             Transporter_Id, Transporter_Description,
             (CASE WHEN Transporter_Id = 'FAB' THEN 'CK' ELSE Transporter_Id END) AS contractor,
             (CASE WHEN DAY(Posting_Date) > 25 THEN MONTH(Posting_Date) + 1 
             WHEN MONTH(Posting_Date) = 12 THEN MONTH(Posting_Date) ELSE MONTH(Posting_Date) END) AS bulan,
             YEAR(Posting_Date) AS tahun
             FROM inquiry_receive");
 
         // transfer
         $db->query("DROP TEMPORARY TABLE IF EXISTS temp_transfer");
         $db->query("CREATE TEMPORARY TABLE temp_transfer SELECT Net_Weigh, Posting_Date, Supplier_Id, Supplier_Name,
             Transporter_Id, Transporter_Description,
             (CASE WHEN DAY(Posting_Date) > 25 THEN MONTH(Posting_Date) + 1 
             WHEN MONTH(Posting_Date) = 12 THEN MONTH(Posting_Date) ELSE MONTH(Posting_Date) END) AS bulan,
             Crusher_Description, Crusher_Code,
             YEAR(Posting_Date) AS tahun
             FROM inquiry_transfer");
 
         $query_addon = "year = $year AND month <= $month_sum";
         // $coal_getting_production = $builder->select("SUM(prd_cg_total) AS actual, 
         //     (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month <= $month_sum) AS budget")
         //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
         //     ->where("month <= $month_sum")
         //     ->where("mm.year = $year")
         //     ->where("timesheets.status = 'approved'")
         //     ->where("deleted_at IS NULL")
         //     ->get()->getRowArray();
         $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
             (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month <= $month_sum) AS budget 
             FROM temp_timbangan WHERE tahun = $year AND bulan <= $month_sum")->getRowArray();
        $coal_getting_production_contractor = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, contractor 
            FROM temp_timbangan WHERE tahun = $year AND bulan <= $month_sum group by contractor")->getResult();
        $ob_production_contractor = $db->query("SELECT SUM(prd_ob_total) AS actual, id_contractor,tb2.contractor_name
        FROM timesheets tb1 left join md_contractors tb2 on tb2.id=tb1.id_contractor WHERE YEAR(prd_date) = $year AND MONTH(prd_date) <= $month_sum AND tb1.`status`='approved' AND deleted_at IS NULL GROUP BY id_contractor")->getResult();
        $ob_production = $builder->select("SUM(prd_ob_total) AS actual, 
             (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month <= $month_sum) AS budget")
             ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
             ->where("month <= $month_sum")
             ->where("mm.year = $year")
             ->where("timesheets.status = 'approved'")
             ->where("deleted_at IS NULL")
             ->get()->getRowArray();
         $stripping_today = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, 
             COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
             (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month <= $month_sum) AS budget_cg, 
             (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month <= $month_sum) AS budget_ob")
             ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
             ->where("month <= $month_sum")
             ->where("mm.year = $year")
             ->where("timesheets.status = 'approved'")
             ->where("deleted_at IS NULL")
             ->get()->getRowArray();
         // $cc_ytd = "SELECT SUM(cc_qty) AS total, 
         //     (SELECT SUM(mmc2.cc_mounthlybudget_qty) FROM md_monthlybudget_cc mmc2 
         //     WHERE `year`= $year AND `month` <= $month) AS budget 
         //     FROM t_crushcoal tc
         //     INNER JOIN md_monthlybudget_cc mmc  ON mmc.id_monthlybudgetcc  = tc.id_monthlybudgetcc 
         //     WHERE mmc.year = $year
         //     AND mmc.month <= $month";
         $cc_ytd = "SELECT SUM(Net_Weigh)/1000 AS total, (SELECT SUM(mmc2.cc_mounthlybudget_qty) FROM md_monthlybudget_cc mmc2 
             WHERE `year`= $year AND `month` <= $month) AS budget FROM temp_transfer WHERE tahun = $year AND bulan <= $month";
         $inquiry_trf_ytd = "WITH temp_hauling AS (SELECT Net_Weigh, Transporter_Description AS transporter, Posting_Date,
             (CASE WHEN DAY(Posting_Date) > 25 THEN MONTH(Posting_Date) + 1 ELSE MONTH(Posting_Date) END) AS bulan
             FROM inquiry_transfer
             WHERE YEAR(Posting_Date) = $year)
         SELECT SUM(Net_Weigh)/1000 AS total,
             (SELECT COALESCE(SUM(hp_mounthlybudget_qty), 0) FROM md_monthlybudget_hp 
                 WHERE year = $year AND month <= $month_sum) AS budget
             FROM temp_hauling th
             WHERE bulan <= $month_sum";
         $SalesShipment = new SalesShipment();
         $builder_shipment = $SalesShipment->builder();
         $barging_today = $builder_shipment->select("COALESCE(SUM(bl_qty), 0) AS total, 
             (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE year = $year AND month <= $month_sum) AS target")
             ->where("MONTH(bl_date) <= $month_sum")
             ->where("YEAR(bl_date) = $year")
             ->get()->getRowArray();
         $sql_ob_distance = "SELECT SUM(prd_ob_total * prd_ob_distance) AS total,
             SUM(prd_ob_total) AS ob_total
             FROM timesheets
             WHERE YEAR(prd_date) = $year AND MONTH(prd_date) <= $month";
         $sql_plan_ob_distance = "WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
                 FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(disob_monthlybudget_qty) AS distance_ob, SUM(mm.ob_monthlybudget_qt) AS ob 
                 FROM md_monthly_disob mmd
                 INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                 WHERE mmd.`year` = $year
                 AND mmd.`month` <= $month
                 GROUP BY bulan, project) A
                 GROUP BY A.bulan) B)
             SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance";
         $sql_actual_cg_distance = "SELECT SUM(prd_cg_total * prd_cg_distance) AS total,
             SUM(prd_cg_total) AS cg_total
             FROM timesheets
             WHERE YEAR(prd_date) = $year AND MONTH(prd_date) <= $month";
         $sql_plan_cg_distance = "WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
                 FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(discg_monthlybudget_qty) AS distance_ob, SUM(mm.cg_monthlybudget_qt) AS ob 
                 FROM md_monthly_discg mmd
                 INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                 WHERE mmd.`year` = $year
                 AND mmd.`month` <= $month
                 GROUP BY bulan, project) A
                 GROUP BY A.bulan) B)
             SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance";
 
         if ($type == 'monthly') {
             $query_addon = "year = $year and month = $month";
             // $coal_getting_production = $builder->select("SUM(prd_cg_total) AS actual, 
             //     (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget")
             //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
             //     ->where("month = $month")
             //     ->where("mm.year = $year")
             //     ->where("timesheets.status = 'approved'")
             //     ->where("deleted_at IS NULL")
             //     ->get()->getRowArray();
             $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
                 (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget 
                 FROM temp_timbangan WHERE tahun = $year AND bulan = $month")->getRowArray();
            $coal_getting_production_contractor = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, contractor 
                FROM temp_timbangan WHERE tahun = $year AND bulan = $month group by contractor")->getResult();
             $ob_production_contractor = $db->query("SELECT SUM(prd_ob_total) AS actual, id_contractor,tb2.contractor_name
             FROM timesheets tb1 left join md_contractors tb2 on tb2.id=tb1.id_contractor WHERE YEAR(prd_date) = $year AND MONTH(prd_date) = $month AND tb1.`status`='approved' AND deleted_at IS NULL GROUP BY id_contractor")->getResult();
             $ob_production = $builder->select("SUM(prd_ob_total) AS actual, 
                 (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget")
                 ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
                 ->where("month = $month")
                 ->where("mm.year = $year")
                 ->where("timesheets.status = 'approved'")
                 ->where("deleted_at IS NULL")
                 ->get()->getRowArray();
             $stripping_today = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, 
                 COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
                 (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget_cg, 
                 (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget_ob")
                 ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
                 ->where("month = $month")
                 ->where("mm.year = $year")
                 ->where("timesheets.status = 'approved'")
                 ->where("deleted_at IS NULL")
                 ->get()->getRowArray();
             // $cc_ytd = "SELECT SUM(cc_qty) AS total, 
             //         (SELECT SUM(mmc2.cc_mounthlybudget_qty) FROM md_monthlybudget_cc mmc2 
             //         WHERE `year`= $year AND `month` = $month) AS budget 
             //     FROM t_crushcoal tc
             //     INNER JOIN md_monthlybudget_cc mmc  ON mmc.id_monthlybudgetcc  = tc.id_monthlybudgetcc 
             //     WHERE mmc.year = $year
             //     AND mmc.month = $month";
             $cc_ytd = "SELECT SUM(Net_Weigh)/1000 AS total, (SELECT SUM(mmc2.cc_mounthlybudget_qty) FROM md_monthlybudget_cc mmc2 
                 WHERE `year`= $year AND `month` = $month) AS budget FROM temp_transfer WHERE tahun = $year AND bulan = $month";
             $inquiry_trf_ytd = "WITH temp_hauling AS (SELECT Net_Weigh, Transporter_Description AS transporter, Posting_Date,
                 (CASE WHEN DAY(Posting_Date) > 25 THEN MONTH(Posting_Date) + 1 ELSE MONTH(Posting_Date) END) AS bulan
                 FROM inquiry_transfer
                 WHERE YEAR(Posting_Date) = $year)
             SELECT SUM(Net_Weigh)/1000 AS total,
                 (SELECT COALESCE(SUM(hp_mounthlybudget_qty), 0) FROM md_monthlybudget_hp 
                     WHERE year = $year AND month = $month) AS budget
                 FROM temp_hauling th
                 WHERE bulan = $month";
             $barging_today = $builder_shipment->select("COALESCE(SUM(bl_qty), 0) AS total, 
                 (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE year = $year AND month = $month) AS target")
                 ->where("MONTH(bl_date) = $month")
                 ->where("YEAR(bl_date) = $year")
                 ->get()->getRowArray();
             $sql_ob_distance = "SELECT SUM(prd_ob_total * prd_ob_distance) AS total,
                 SUM(prd_ob_total) AS ob_total
                 FROM timesheets
                 WHERE YEAR(prd_date) = $year AND MONTH(prd_date) = $month";
             $sql_plan_ob_distance = "WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
                     FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(disob_monthlybudget_qty) AS distance_ob, SUM(mm.ob_monthlybudget_qt) AS ob 
                     FROM md_monthly_disob mmd
                     INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                     WHERE mmd.`year` = $year
                     AND mmd.`month` = $month
                     GROUP BY bulan, project) A
                     GROUP BY A.bulan) B)
                 SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance";
             $sql_actual_cg_distance = "SELECT SUM(prd_cg_total * prd_cg_distance) AS total,
                 SUM(prd_cg_total) AS cg_total
                 FROM timesheets
                 WHERE YEAR(prd_date) = $year AND MONTH(prd_date) = $month";
             $sql_plan_cg_distance = "WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
                     FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(discg_monthlybudget_qty) AS distance_ob, SUM(mm.cg_monthlybudget_qt) AS ob 
                     FROM md_monthly_discg mmd
                     INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                     WHERE mmd.`year` = $year
                     AND mmd.`month` = $month
                     GROUP BY bulan, project) A
                     GROUP BY A.bulan) B)
                 SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance";
         }
 
         if ($date) {
             $parsed_date = Time::parse($date);
             $month_date = $parsed_date->getMonth();
             $year_date = $parsed_date->getYear();
             if ($parsed_date->getDay() > 25 && $month_date < 12) {
                 $month_date++;
             }
             // $coal_getting_production = $db->query("WITH temp_ts AS (SELECT prd_date, prd_cg_total, prd_ob_total, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mm.id_monthlybudget
             //         FROM timesheets t
             //         INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget 
             //         WHERE prd_date = '$date' AND t.status = 'approved' AND deleted_at IS NULL)
             //     SELECT SUM(prd_cg_total) AS actual, SUM(cg_dailybudget_qt) AS budget FROM temp_ts")->getRowArray();
             $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
                 (SELECT SUM(cg_dailybudget_qt) FROM md_monthlybudget mms WHERE year = YEAR('$date') AND month = MONTH('$date')) AS budget FROM temp_timbangan tt
                 WHERE DATE(Posting_Date) = '$date';")->getRowArray();
            $coal_getting_production_contractor = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, contractor 
                 FROM temp_timbangan WHERE DATE(Posting_Date) = '$date' group by contractor")->getResult();
             $ob_production_contractor = $db->query("SELECT SUM(prd_ob_total) AS actual, id_contractor,tb2.contractor_name
             FROM timesheets tb1 left join md_contractors tb2 on tb2.id=tb1.id_contractor WHERE prd_date = '$date' AND tb1.`status`='approved' AND deleted_at IS NULL GROUP BY id_contractor")->getResult();
             $ob_production = $db->query("WITH temp_ts AS (SELECT prd_date, prd_cg_total, prd_ob_total, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mm.id_monthlybudget
                     FROM timesheets t
                     INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget 
                     WHERE prd_date = '$date' AND t.status = 'approved' AND deleted_at IS NULL)
                 SELECT SUM(prd_ob_total) AS actual, SUM(ob_dailybudget_qt) AS budget FROM temp_ts")->getRowArray();
             $stripping_today = $db->query("WITH temp_ts AS (SELECT prd_date, prd_cg_total, prd_ob_total, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mm.id_monthlybudget
                     FROM timesheets t
                     INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget 
                     WHERE prd_date = '$date' AND t.status = 'approved' AND deleted_at IS NULL)
                 SELECT SUM(prd_ob_total) AS actual_ob, 
                     (SELECT SUM(ob_dailybudget_qt) FROM md_monthlybudget WHERE id_monthlybudget IN (SELECT id_monthlybudget FROM temp_ts)) AS budget_ob, 
                     (SELECT SUM(cg_dailybudget_qt) FROM md_monthlybudget WHERE id_monthlybudget IN (SELECT id_monthlybudget FROM temp_ts)) AS actual_cg,
                     SUM(cg_dailybudget_qt) AS budget_cg FROM temp_ts")->getRowArray();
             // $cc_ytd = "SELECT SUM(cc_qty) AS total, 
             //         (SELECT SUM(mmc2.cc_dailybudget_qty) FROM md_monthlybudget_cc mmc2 
             //         WHERE `year`= $year_date AND `month` = $month_date) AS budget 
             //     FROM t_crushcoal tc
             //     WHERE tc.production_date = '$date'";
             $cc_ytd = "SELECT SUM(Net_Weigh)/1000 AS total, (SELECT SUM(mmc2.cc_dailybudget_qty) FROM md_monthlybudget_cc mmc2 
                 WHERE `year`= $year_date AND `month` = $month_date) AS budget FROM temp_transfer WHERE DATE(Posting_Date) = '$date'";
             $inquiry_trf_ytd = "SELECT SUM(Net_Weigh)/1000 AS total,
                 (SELECT COALESCE(SUM(hp_mounthlybudget_qty), 0) FROM md_monthlybudget_hp 
                     WHERE year = $year_date AND month = $month_date) AS budget
                 FROM inquiry_transfer ip
                 WHERE DATE(Posting_Date) = '$date'";
             $barging_today = $builder_shipment->select("COALESCE(SUM(bl_qty), 0) AS total, 
                 (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE year = $year_date AND month = $month_date) AS target")
                 ->where("bl_date = '$date'")
                 ->get()->getRowArray();
             $sql_ob_distance = "SELECT SUM(prd_ob_total * prd_ob_distance) AS total,
                 SUM(prd_ob_total) AS ob_total
                 FROM timesheets
                 WHERE prd_date = '$date'";
             $sql_plan_ob_distance = "WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
                     FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(disob_monthlybudget_qty) AS distance_ob, SUM(mm.ob_monthlybudget_qt) AS ob 
                     FROM md_monthly_disob mmd
                     INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                     WHERE mmd.`year` = $year
                     AND mmd.`month` = $month
                     GROUP BY bulan, project) A
                     GROUP BY A.bulan) B)
                 SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance";
             $sql_actual_cg_distance = "SELECT SUM(prd_cg_total * prd_cg_distance) AS total,
                 SUM(prd_cg_total) AS cg_total
                 FROM timesheets
                 WHERE prd_date = '$date'";
             $sql_plan_cg_distance = "WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
                     FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(discg_monthlybudget_qty) AS distance_ob, SUM(mm.cg_monthlybudget_qt) AS ob 
                     FROM md_monthly_discg mmd
                     INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                     WHERE mmd.`year` = $year
                     AND mmd.`month` = $month
                     GROUP BY bulan, project) A
                     GROUP BY A.bulan) B)
                 SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance";
         }
 
         $data['cg_production'] = $coal_getting_production ?? 0;
         $data['cg_production_contractor'] = $coal_getting_production_contractor ?? 0;
         $data['ob_production'] = $ob_production ?? 0;
         $data['ob_production_contractor'] = $ob_production_contractor ?? 0;
         $data['barging_ytd'] = $barging_today ?? 0;
         $data['stripping_ytd'] = $stripping_today ?? 0;
         $data['date']="SELECT SUM(Net_Weigh)/1000 AS actual, contractor 
         FROM temp_timbangan WHERE DATE(Posting_Date) = '$date' group by contractor";
 
        return $this->respond($data, 200);
    }
    
    // // == #Tempcode Malik
}
