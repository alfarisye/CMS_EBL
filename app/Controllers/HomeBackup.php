<?php

namespace App\Controllers;

use CodeIgniter\Files\File;
use CodeIgniter\API\ResponseTrait;
use Config\Database;
use CodeIgniter\I18n\Time;

// Models
use App\Models\Timesheets;
use App\Models\TimesheetAdjustments;
use App\Models\Sales\SalesShipment;
use App\Models\CrushCoal;
use App\Models\Inventory\InqueryTransfer;
use App\Models\FiActTrs;
use App\Models\FiActBalance;
use App\Models\TCoalIndex;
use App\Models\TCostmining;
use App\Models\Inventory\InqueryReceive;
use App\Models\Sales\TSalPrice;
use DateInterval;
use DatePeriod;
use DateTime;

class Home extends BaseController
{
    use ResponseTrait;
    private function getEat($year, $month)
    {
        $FiActBal = new FiActBalance();
        $builder = $FiActBal->builder();

        // Revenue
        $data['revenue_ytd'] = $builder->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '4%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $data['revenue_mtd'] = $builder->select("SUM(PER_SALES) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '4%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // cogs
        $data['cogs_ytd'] = $builder->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '5%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $data['cogs_mtd'] = $builder->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '5%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // GP
        $data['gp_ytd'] = $data['revenue_ytd']['BALANCE'] - $data['cogs_ytd']['BALANCE'];
        $data['gp_mtd'] = $data['revenue_mtd']['BALANCE'] - $data['cogs_mtd']['BALANCE'];

        // GAE
        $data['gae_ytd'] = $builder->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '6%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $data['gae_mtd'] = $builder->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '6%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // op
        $data['op_ytd'] = $data['gp_ytd'] - $data['gae_ytd']['BALANCE'];
        $data['op_mtd'] = $data['gp_mtd'] - $data['gae_mtd']['BALANCE'];

        // OTHER I/E
        $data['oie_ytd'] = $builder->select("COALESCE(SUM(
                CASE WHEN GL_ACCOUNT LIKE '71%' THEN BALANCE * 1
                ELSE BALANCE END
            ), 0) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '7%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $data['oie_mtd'] = $builder->select("COALESCE(SUM(
                CASE WHEN GL_ACCOUNT LIKE '71%' THEN PER_SALES * 1
                ELSE PER_SALES END
            ), 0) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '7%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // EBT
        $data['ebt_ytd'] = $data['op_ytd'] - $data['oie_ytd']['BALANCE'];
        $data['ebt_mtd'] = $data['op_mtd'] - $data['oie_mtd']['BALANCE'];

        // TAX
        $data['tax_ytd'] = $builder->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '910001%' OR GL_ACCOUNT LIKE '920002%')")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $data['tax_mtd'] = $builder->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '91%' OR GL_ACCOUNT LIKE '92%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // EAT
        $data['eat_ytd'] = $data['ebt_ytd'] - $data['tax_ytd']['BALANCE'];
        $data['eat_mtd'] = $data['ebt_mtd'] - $data['tax_mtd']['BALANCE'];

        return ["ytd" => $data['eat_ytd'], "mtd" => $data['eat_mtd']];
    }
    public function index()
    {
        $data['title'] = "Home";
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

        $data['cg_production'] = $coal_getting_production;
        $data['ob_production'] = $ob_production;
        $data['barging_ytd'] = $barging_today;
        $data['stripping_ytd'] = $stripping_today;

        $sum_actual_ob_distance = $db->query($sql_ob_distance)->getRowArray();
        $sum_plan_ob_distance = $db->query($sql_plan_ob_distance)->getRowArray();
        $data['sum_actual_ob_distance'] = $sum_actual_ob_distance;
        $data['sum_plan_ob_distance'] = $sum_plan_ob_distance;

        $sum_actual_cg_distance = $db->query($sql_actual_cg_distance)->getRowArray();
        $sum_plan_cg_distance = $db->query($sql_plan_cg_distance)->getRowArray();
        $data['sum_actual_cg_distance'] = $sum_actual_cg_distance;
        $data['sum_plan_cg_distance'] = $sum_plan_cg_distance;

        $CrushCoal = new CrushCoal();
        $builder_cc = $CrushCoal->builder();

        $crush_coal_ytd = $db->query($cc_ytd)->getRowArray();
        $data['crush_coal_ytd'] = $crush_coal_ytd;

        $InquiryTranfer = new InqueryTransfer();
        $builder_inquiry = $InquiryTranfer->builder();

        $inquiry_transfer = $db->query($inquiry_trf_ytd)->getRowArray();
        $data['inquiry_transfer'] = $inquiry_transfer;

        if ($type == 'yearly') {
            // $cg_lines = $builder->select("SUM(prd_cg_total) AS actual, mm.year AS tahun, mm.year AS month,
            //     (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = tahun) AS budget,
            //     (SELECT SUM(PRD_OUTLOOK_CG_TOT) FROM t_outlook_timesheet tot WHERE year = tahun) AS outlook")
            //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            //     ->where("timesheets.status = 'approved'")
            //     ->where("deleted_at IS NULL")
            //     ->groupBy("tahun")
            //     ->get()->getResultArray();
            $cg_lines = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, tahun, (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = tahun) AS budget, 
                tahun AS month FROM temp_timbangan GROUP BY tahun ORDER BY tahun")->getResultArray();
            $ob_lines = $builder->select("SUM(prd_ob_total) AS actual, mm.year AS tahun, mm.year AS month,
                (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = tahun) AS budget,
                (SELECT SUM(PRD_OUTLOOK_OB_TOT) FROM t_outlook_timesheet tot WHERE year = tahun) AS outlook")
                ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
                ->where("timesheets.status = 'approved'")
                ->where("deleted_at IS NULL")
                ->groupBy("tahun")
                ->get()->getResultArray();
            $stripping_ration = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
                mm.year AS month, 
                (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = mm.year) AS budget_cg, 
                (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = mm.year) AS budget_ob,
                (SELECT SUM(PRD_OUTLOOK_OB_TOT) / SUM(PRD_OUTLOOK_CG_TOT) FROM t_outlook_timesheet tot WHERE year = mm.year) AS outlook")
                ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget", "right")
                ->groupBy("mm.year")
                ->get()->getResultArray();
            // CC
            // $sum_cc = $db->query("SELECT SUM(cc_qty) AS total, YEAR(production_date) AS bulan, mc.crusher_description AS crusher
            //     FROM t_crushcoal tc
            //     INNER JOIN md_crusher mc ON mc.id = tc.id_crusher
            //     GROUP BY bulan, crusher
            //     ORDER BY bulan, crusher")->getResultArray();
            $sum_cc = $db->query("SELECT SUM(Net_Weigh) AS total, tahun AS bulan, 'OFN' AS crusher FROM temp_transfer GROUP BY tahun ORDER BY tahun")->getResultArray();
            $grouped_cc = array();
            foreach ($sum_cc as $c) {
                $grouped_cc[$c['crusher']][] = $c;
            }
            $data['sum_cc'] = $grouped_cc;

            $plan_cc = $db->query("SELECT year AS bulan, SUM(cc_mounthlybudget_qty) FROM md_monthlybudget_cc GROUP BY bulan")->getResultArray();
            $data['plan_cc'] = $plan_cc;
            // sum hauling to port (tonase)

            $sum_hauling_to_tonase = $db->query("SELECT SUM(Net_Weigh)/1000 AS total, Transporter_Description AS transporter,
                YEAR(Posting_Date) AS bulan,
                (SELECT COALESCE(SUM(hp_mounthlybudget_qty), 0) FROM md_monthlybudget_hp 
                WHERE year = bulan) AS budget
                FROM inquiry_transfer
                -- WHERE Transporter_Description != ''
                GROUP BY transporter, bulan")->getResultArray();
            $grouped_tonase = array();
            foreach ($sum_hauling_to_tonase as $c) {
                $grouped_tonase[$c['transporter']][$c['bulan']] = $c;
            }
            $tonase_years = $db->query("SELECT DISTINCT YEAR(Posting_Date) AS year FROM inquiry_transfer")->getResultArray();
            foreach ($grouped_tonase as $k => $g) {
                foreach ($tonase_years as $t) {
                    if (!array_key_exists($t['year'], $g)) {
                        $grouped_tonase[$k][$t['year']] = array("bulan" => $t['year']);
                    }
                }
                ksort($grouped_tonase[$k]); // sort the damn array
            }
            $data['sum_tonase'] = $grouped_tonase;

            $hauling_plan_arr = array();
            $hauling_plan = $db->query("SELECT COALESCE(AVG(hp_mounthlybudget_qty), 0) AS plan, year AS month FROM md_monthlybudget_hp 
                GROUP BY year ORDER BY year")->getResultArray();
            foreach ($hauling_plan as $hp) {
                $hauling_plan_arr[$hp['month']] = $hp;
                foreach ($tonase_years as $t) {
                    if (!array_key_exists($t['year'], $hauling_plan_arr)) {
                        $hauling_plan_arr[$t['year']] = array('month' => $t['year']);
                    }
                }
                ksort($hauling_plan_arr); // sort the damn array
            }
            $data['hauling_plan'] = $hauling_plan_arr;

            // distance
            // TODO: hitung plan distance sesuai dengan actual
            $actual_distance = $db->query("SELECT SUM(prd_ob_total * prd_ob_distance) AS ob,
                SUM(prd_cg_total * prd_cg_distance) AS cg,
                YEAR(prd_date) AS bulan,
                SUM(prd_ob_total) AS ob_total,
                SUM(prd_cg_total) AS cg_total,
                (SELECT SUM(PRD_Outlook_discg) FROM t_outlook_timesheet tot WHERE year = bulan) AS outlook_cg,
                (SELECT SUM(PRD_Outlook_disob) FROM t_outlook_timesheet tot WHERE year = bulan) AS outlook_ob,
                (SELECT SUM(disob_monthlybudget_qty * mm.ob_monthlybudget_qt)/SUM(mm.ob_monthlybudget_qt) FROM md_monthly_disob mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = bulan) AS target_ob,
                (SELECT SUM(discg_monthlybudget_qty * mm.cg_monthlybudget_qt)/SUM(mm.cg_monthlybudget_qt) FROM md_monthly_discg mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = bulan) AS target_cg
                FROM timesheets
                GROUP BY bulan
                ORDER BY bulan")->getResultArray();
        } elseif ($type == 'monthly') {
            // $cg_lines = $builder->select("SUM(prd_cg_total) AS actual, mm.month, mm.month AS bulan,
            //     (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = bulan) AS budget,
            //     (SELECT SUM(PRD_OUTLOOK_CG_TOT) FROM t_outlook_timesheet tot WHERE year = $year AND month = bulan) AS outlook")
            //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            //     ->where("mm.year = $year")
            //     ->where("timesheets.status = 'approved'")
            //     ->where("deleted_at IS NULL")
            //     ->groupBy("bulan")
            //     ->get()->getResultArray();
            $cg_lines = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, bulan, (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = bulan) AS budget, 
                bulan AS month FROM temp_timbangan tt WHERE tt.tahun = $year GROUP BY bulan ORDER BY bulan")->getResultArray();
            $ob_lines = $builder->select("SUM(prd_ob_total) AS actual, mm.month, mm.month AS bulan,
                (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = bulan) AS budget,
                (SELECT SUM(PRD_OUTLOOK_OB_TOT) FROM t_outlook_timesheet tot WHERE year = $year AND month = bulan) AS outlook")
                ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
                ->where("mm.year = $year")
                ->where("timesheets.status = 'approved'")
                ->where("deleted_at IS NULL")
                ->groupBy("bulan")
                ->get()->getResultArray();
            $stripping_ration = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
                mm.`month`, 
                (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = mm.year AND month = mm.month) AS budget_cg, 
                (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = mm.year AND month = mm.month) AS budget_ob,
                (SELECT SUM(PRD_OUTLOOK_OB_TOT) / SUM(PRD_OUTLOOK_CG_TOT) FROM t_outlook_timesheet tot WHERE year = mm.year AND month = mm.month) AS outlook")
                ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget", "right")
                ->where("mm.year = $year")
                ->groupBy("mm.month, mm.year")
                ->get()->getResultArray();
            // CC
            $sum_cc = $db->query("SELECT SUM(cc_qty) AS total, mmc.`month` AS bulan, mc.crusher_description AS crusher
                FROM t_crushcoal tc
                INNER JOIN md_crusher mc ON mc.id = tc.id_crusher
                INNER JOIN md_monthlybudget_cc mmc  ON mmc.id_monthlybudgetcc  = tc.id_monthlybudgetcc 
                WHERE YEAR(production_date) = $year
                GROUP BY bulan, crusher
                ORDER BY bulan, crusher")->getResultArray();
            $sum_cc = $db->query("SELECT SUM(Net_Weigh)/1000 AS total, bulan, 'OFN' AS crusher FROM temp_transfer WHERE tahun = $year GROUP BY bulan ORDER BY bulan")->getResultArray();
            $grouped_cc = array();
            foreach ($sum_cc as $c) {
                $grouped_cc[$c['crusher']][] = $c;
            }
            $data['sum_cc'] = $grouped_cc;

            $plan_cc = $db->query("SELECT month AS bulan, year AS tahun, cc_mounthlybudget_qty FROM md_monthlybudget_cc 
                                    WHERE year = $year")->getResultArray();
            $data['plan_cc'] = $plan_cc;
            // sum hauling to port (tonase)

            $sum_hauling_to_tonase = $db->query("WITH temp_hauling AS (SELECT Net_Weigh, Transporter_Description AS transporter, Posting_Date,
                (CASE WHEN DAY(Posting_Date) > 25 THEN MONTH(Posting_Date) + 1 ELSE MONTH(Posting_Date) END) AS bulan
                FROM inquiry_transfer
                WHERE YEAR(Posting_Date) = $year)
                SELECT SUM(Net_Weigh)/1000 AS total, transporter, bulan
                FROM temp_hauling
                GROUP BY transporter, bulan")->getResultArray();
            $grouped_tonase = array();
            foreach ($sum_hauling_to_tonase as $c) {
                $grouped_tonase[$c['transporter']][$c['bulan']] = $c;
                for ($i = 1; $i <= 12; $i++) {
                    if (!array_key_exists($i, $grouped_tonase[$c['transporter']])) {
                        $grouped_tonase[$c['transporter']][$i] = array('bulan' => $i);
                    }
                }
            }
            $data['sum_tonase'] = $grouped_tonase;
            $hauling_plan = $db->query("SELECT COALESCE(AVG(hp_mounthlybudget_qty), 0) AS plan, month FROM md_monthlybudget_hp 
                WHERE year = $year GROUP BY month ORDER BY month")->getResultArray();
            $data['hauling_plan'] = $hauling_plan;

            // distance
            // TODO: hitung plan distance sesuai dengan actual
            $actual_distance = $db->query("SELECT SUM(prd_ob_total * prd_ob_distance) AS ob,
                SUM(prd_cg_total * prd_cg_distance) AS cg,
                MONTH(prd_date) AS bulan, YEAR(prd_date) AS tahun,
                SUM(prd_ob_total) AS ob_total,
                SUM(prd_cg_total) AS cg_total,
                (SELECT SUM(PRD_Outlook_discg) FROM t_outlook_timesheet tot WHERE year = tahun AND month = bulan) AS outlook_cg,
                (SELECT SUM(PRD_Outlook_disob) FROM t_outlook_timesheet tot WHERE year = tahun AND month = bulan) AS outlook_ob,
                (SELECT SUM(disob_monthlybudget_qty * mm.ob_monthlybudget_qt)/SUM(mm.ob_monthlybudget_qt) FROM md_monthly_disob mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = tahun AND mmd.month = bulan) AS target_ob,
                (SELECT SUM(discg_monthlybudget_qty * mm.cg_monthlybudget_qt)/SUM(mm.cg_monthlybudget_qt) FROM md_monthly_discg mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = tahun AND mmd.month = bulan) AS target_cg
                FROM timesheets
                WHERE YEAR(prd_date) = $year
                GROUP BY bulan, tahun
                ORDER BY bulan")->getResultArray();
        } else {
            $month_before = $month - 1;
            $date_before = "$year-$month_before-26";
            $date_target = "$year-$month-25";
            $date_target2 = "$year-$month-26";
            if ($month == 1) {
                $date_before = "$year-$month-1";
                $date_target = "$year-$month-25";
                $date_target2 = "$year-$month-26";
            }
            if ($month == 12) {
                // $date_before = "$year-$month-1";
                $date_target = "$year-$month-31";
                $year2 = $year + 1;
                $date_target2 = "$year2-1-1";
            }
            // $cg_lines = $db->query("WITH data_per_contractor AS (SELECT SUM(prd_cg_total) AS actual, prd_date AS month, id_monthlybudget AS ids,
            //     (SELECT SUM(cg_dailybudget_qt) FROM md_monthlybudget mms WHERE id_monthlybudget = ids) AS budget,
            //     (SELECT SUM(PRD_OUTLOOK_CG_TOT)/DATEDIFF('$date_target', '$date_before') FROM t_outlook_timesheet tot 
            //     WHERE tot.year = $year AND tot.month = $month) AS outlook	
            //     FROM timesheets t  WHERE prd_date BETWEEN '$date_before' AND '$date_target'
            //     GROUP BY prd_date, id_monthlybudget ORDER BY prd_date)
            //     SELECT SUM(actual) AS actual, month, SUM(budget) AS budget FROM data_per_contractor GROUP BY month")->getResultArray();
            $cg_lines = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, Posting_Date AS month, tahun, bulan,
                (SELECT SUM(mm.cg_dailybudget_qt) FROM md_monthlybudget mm WHERE mm.`year` = tahun AND mm.`month` = bulan) AS budget
                FROM temp_timbangan tt 
                INNER JOIN md_monthlybudget mms ON mms.project = tt.contractor AND mms.`month` = bulan AND mms.`year` = tahun
                WHERE tt.tahun = $year AND bulan = $month GROUP BY Posting_Date, tahun, bulan ORDER BY Posting_Date")->getResultArray();
            $ob_lines = $db->query("WITH data_per_contractor AS (SELECT SUM(prd_ob_total) AS actual, prd_date AS month, id_monthlybudget AS ids,
                (SELECT SUM(ob_dailybudget_qt) FROM md_monthlybudget mms WHERE id_monthlybudget = ids) AS budget,
                (SELECT SUM(PRD_OUTLOOK_OB_TOT)/DATEDIFF('$date_target', '$date_before') FROM t_outlook_timesheet tot 
                WHERE tot.year = $year AND tot.month = $month) AS outlook	
                FROM timesheets t  WHERE prd_date BETWEEN '$date_before' AND '$date_target'
                GROUP BY prd_date, id_monthlybudget ORDER BY prd_date)
                SELECT SUM(actual) AS actual, month, SUM(budget) AS budget FROM data_per_contractor GROUP BY month")->getResultArray();
            $stripping_ration = $db->query("WITH data_per_contractor AS (SELECT SUM(prd_cg_total) AS actual_cg, SUM(t.prd_ob_total) AS actual_ob, 
                prd_date AS month, id_monthlybudget AS ids, 
                (SELECT SUM(cg_dailybudget_qt) FROM md_monthlybudget mms WHERE id_monthlybudget = ids) AS budget_cg, 
                (SELECT SUM(ob_dailybudget_qt) FROM md_monthlybudget mms WHERE id_monthlybudget = ids) AS budget_ob 
                FROM timesheets t WHERE prd_date BETWEEN '$date_before' AND '$date_target' 
                GROUP BY prd_date, id_monthlybudget ORDER BY prd_date)
                SELECT SUM(actual_cg) AS actual_cg, SUM(actual_ob) AS actual_ob,
                month, SUM(budget_cg) AS budget_cg, SUM(budget_ob) AS budget_ob
                FROM data_per_contractor GROUP BY month")->getResultArray();
            // CC
            // $sum_cc = $db->query("SELECT SUM(cc_qty) AS total, (production_date) AS bulan, mc.crusher_description AS crusher
            //     FROM t_crushcoal tc
            //     INNER JOIN md_crusher mc ON mc.id = tc.id_crusher
            //     WHERE production_date BETWEEN '$date_before' AND '$date_target'
            //     GROUP BY bulan, crusher
            //     ORDER BY bulan, crusher")->getResultArray();
            $sum_cc = $db->query("SELECT SUM(Net_Weigh)/1000 AS total, DATE(Posting_Date) AS bulan, 'OFN' AS crusher FROM temp_transfer 
                WHERE bulan = $month GROUP BY Posting_Date ORDER BY Posting_Date")->getResultArray();
            $grouped_cc = array();
            foreach ($sum_cc as $c) {
                $grouped_cc[$c['crusher']][$c['bulan']] = $c['total'];
            }
            $data['sum_cc'] = $grouped_cc;

            $begin = new DateTime($date_before);
            $end = new DateTime($date_target2); // pakai date_target2 dikarenakan fungsi interval exclude target

            $interval = DateInterval::createFromDateString("1 day");
            $period = new DatePeriod($begin, $interval, $end);

            foreach ($period as $dt) {
                $data['date_period'][] = $dt->format("Y-m-d");
            }

            $plan_cc = $db->query("SELECT cc_dailybudget_qty AS plan FROM md_monthlybudget_cc WHERE year = $year AND month = $month")->getRowArray();
            $data['plan_cc'] = $plan_cc;

            // sum hauling tonase
            $sum_hauling_to_tonase = $db->query("SELECT SUM(Net_Weigh)/1000 AS total, Transporter_Description AS transporter,
                DATE(Posting_Date) AS bulan
                FROM inquiry_transfer
                WHERE Posting_Date BETWEEN '$date_before' AND '$date_target'
                GROUP BY transporter, bulan")->getResultArray();
            $grouped_tonase = array();
            foreach ($sum_hauling_to_tonase as $c) {
                $grouped_tonase[$c['transporter']][] = $c;
            }
            $data['sum_tonase'] = $grouped_tonase;

            $distinct_hauling_date = $db->query("SELECT DISTINCT DATE(Posting_Date) AS tanggal
                FROM inquiry_transfer
                WHERE Posting_Date BETWEEN '$date_before' AND '$date_target'")->getResultArray();
            $hauling_plan_arr = array();
            $hauling_plan = $db->query("SELECT COALESCE(AVG(hp_dailybudget_qty), 0) AS plan FROM md_monthlybudget_hp 
                mmh WHERE mmh.year = $year AND mmh.month = $month")->getRowArray();
            foreach ($distinct_hauling_date as $hp) {
                $hauling_plan_arr[$hp['tanggal']] = array("month" => $hp['tanggal'], "plan" => $hauling_plan['plan']);
            }
            $data['hauling_plan'] = $hauling_plan_arr;

            // distance
            $actual_distance = $db->query("SELECT SUM(prd_ob_total * prd_ob_distance) AS ob,
                SUM(prd_cg_total * prd_cg_distance) AS cg,
                prd_date AS bulan,
                SUM(prd_ob_total) AS ob_total,
                SUM(prd_cg_total) AS cg_total,
                (SELECT SUM(disob_dailybudget_qty * mm.ob_dailybudget_qt)/SUM(mm.ob_dailybudget_qt) FROM md_monthly_disob mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = $year AND mmd.month = $month) AS target_ob,
                (SELECT SUM(discg_dailybudget_qty * mm.cg_dailybudget_qt)/SUM(mm.cg_dailybudget_qt) FROM md_monthly_discg mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = $year AND mmd.month = $month) AS target_cg
                FROM timesheets
                WHERE prd_date BETWEEN '$date_before' AND '$date_target'
                GROUP BY bulan
                ORDER BY bulan")->getResultArray();
        }


        $data['cg_lines'] = $cg_lines;
        $data['ob_lines'] = $ob_lines;
        $data['stripping_ratio'] = $stripping_ration;
        $data['actual_distance'] = $actual_distance;

        // * SALES
        $SalePrice = new TSalPrice();
        $builder_sal = $SalePrice->builder();

        $average_price = $builder_sal->select("AVG(final_price) AS final_price")
            ->where("curr = 'IDR'")
            ->where("YEAR(date_final) = $year")
            ->get()->getRowArray();
        $data['average_price'] = $average_price;

        $total_shipment = $builder_shipment->select("SUM(bl_qty) AS total, 
            (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE year = $year) AS target")
            ->where("YEAR(bl_date) = $year")
            ->get()->getRowArray();
        $data['total_shipment'] = $total_shipment;

        $total_sales = $builder_sal->select("SUM(amount) AS total")
            ->where("curr = 'IDR'")
            ->where("YEAR(date_final) = $year")
            ->get()->getRowArray();
        $data['total_sales'] = $total_sales;

        // list data shipment

        $ship_type = $_GET['ship_type'] ?? 'qty';
        if ($ship_type == 'qty') {
            $selected_column = "discharging_qty";
        } elseif ($ship_type == 'price') {
            $selected_column = "final_price";
        } else {
            $selected_column = "amount";
        }
        if ($ship_type == 'qty') {
            $shipment_list = $builder_shipment->select("SUM($selected_column) AS total, MONTH(discharging_date) AS bulan, category, type")
                ->where("YEAR(discharging_date) = $year")
                ->groupBy("bulan")
                ->groupBy("category")
                ->groupBy("type")
                ->orderBy("bulan")
                ->get()->getResultArray();
        } else {
            $shipment_list = $db->query("SELECT SUM(amount) AS total, MONTH(discharging_date) AS bulan, 
                category, tsp.`type` FROM T_SAL_PRICE tsp 
                INNER JOIN T_SAL_SHIPMENT tss ON tss.shipment_id = tsp.shipment_id
                WHERE YEAR(tss.discharging_date) = $year
                GROUP BY bulan, category, type
                ORDER BY bulan")->getResultArray();
        }
        $grouped_sl = array();
        foreach ($shipment_list as $sl) {
            $grouped_sl[$sl['bulan']][$sl['type']][$sl['category']] = $sl['total'];
        };

        // hardcode
        if ($ship_type == 'qty') {
            $grouped_sl = [
                1 => ['Local' => ['FOB BARGE' => 8250, 'CIF' => 56831]],
                2 => ['Local' => ['FOB BARGE' => 41886, 'CIF' => 44684], 'Export' => ['CIF' => 10442, 'MV' => 50900]],
                3 => ['Local' => ['FOB BARGE' => 48609, 'CIF' => 47927], 'Export' => ['MV' => 54830]],
                4 => ['Local' => ['FOB BARGE' => 22676, 'CIF' => 65544], 'Export' => ['MV' => 29450]],
                5 => ['Local' => ['FOB BARGE' => 46999, 'CIF' => 33166], 'Export' => ['CIF' => 10431, 'MV' => 14400]],
                6 => ['Local' => ['FOB BARGE' => 31808, 'CIF' => 22640], 'Export' => ['MV' => 82051]],
                7 => ['Local' => ['FOB BARGE' => 61876, 'CIF' => 45894], 'Export' => ['MV' => 80000]],
                8 => ['Local' => ['FOB BARGE' => 16532, 'CIF' => 45932], 'Export' => ['MV' => 71285]],
                9 => ['Local' => ['CIF' => 22509], 'Export' => ['CIF' => 10412, 'MV' => 149000]],
                10 => ['Local' => ['FOB BARGE' => 88199, 'CIF' => 23274, 'FRANCO PABRIK' => 7511], 'Export' => ['MV' => 73200]]
            ];
        } elseif ($ship_type == 'price') {
            $grouped_sl = [
                1 => ['Local' => ['FOB BARGE' => 600000, 'CIF' => 657968]],
                2 => ['Local' => ['FOB BARGE' => 603583, 'CIF' => 684864], 'Export' => ['CIF' => 1050101, 'MV' => 797715]],
                3 => ['Local' => ['FOB BARGE' => 683240, 'CIF' => 861205], 'Export' => ['MV' => 993755]],
                4 => ['Local' => ['FOB BARGE' => 606663, 'CIF' => 709697], 'Export' => ['MV' => 1463561]],
                5 => ['Local' => ['FOB BARGE' => 603898, 'CIF' => 731638], 'Export' => ['CIF' => 1417866, 'MV' => 1463593]],
                6 => ['Local' => ['FOB BARGE' => 735410, 'CIF' => 700596], 'Export' => ['MV' => 1214715]],
                7 => ['Local' => ['FOB BARGE' => 786395, 'CIF' => 678518], 'Export' => ['MV' => 1200906]],
                8 => ['Local' => ['FOB BARGE' => 1021807, 'CIF' => 772617], 'Export' => ['MV' => 1179139]],
                9 => ['Local' => ['CIF' => 690565], 'Export' => ['CIF' => 1288622, 'MV' => 1074501]],
                10 => ['Local' => ['FOB BARGE' => 794751.953640453, 'CIF' => 748123.615744994, 'FRANCO PABRIK' => 1100000], 'Export' => ['MV' => 1156180.01]]
            ];
        } else {
            $grouped_sl = [
                1 => ['Local' => ['FOB BARGE' => 4950148800, 'CIF' => 37393092164]],
                2 => ['Local' => ['FOB BARGE' => 25281470869, 'CIF' => 30602445578], 'Export' => ['CIF' => 10944555310, 'MV' => 40603674397]],
                3 => ['Local' => ['FOB BARGE' => 33211383721, 'CIF' => 41274994033], 'Export' => ['MV' => 54487605374]],
                4 => ['Local' => ['FOB BARGE' => 13756509130, 'CIF' => 46516062938], 'Export' => ['MV' => 43101682846]],
                5 => ['Local' => ['FOB BARGE' => 28382681062, 'CIF' => 24265803847], 'Export' => ['CIF' => 14790442073, 'MV' => 21075913397]],
                6 => ['Local' => ['FOB BARGE' => 23392059941, 'CIF' => 15861437403], 'Export' => ['MV' => 99668570619]],
                7 => ['Local' => ['FOB BARGE' => 48659131363, 'CIF' => 31140230437], 'Export' => ['MV' => 96072499200]],
                8 => ['Local' => ['FOB BARGE' => 16892309092, 'CIF' => 35487471152], 'Export' => ['MV' => 84054933595]],
                9 => ['Local' => ['CIF' => 15544209180], 'Export' => ['CIF' => 13417133475, 'MV' => 160100625131]],
                10 => ['Local' => ['FOB BARGE' => 70096480152, 'CIF' => 17411825292, 'FRANCO PABRIK' => 8262122000], 'Export' => ['MV' => 84632376732]]
            ];
        }
        $data['shipment_list'] = $grouped_sl;

        // $export_vs_local = $builder_shipment->select("COUNT(CASE WHEN type = 'Local' THEN 1 ELSE NULL END) AS local, 
        // COUNT(CASE WHEN type = 'Export' THEN 1 ELSE NULL END) AS export")
        //     ->where("YEAR(receipt_date) = $year")
        //     ->get()->getRowArray();
        $export_vs_local = $db->query("SELECT COUNT(1) AS jumlah, type, MONTH(bl_date) AS bulan FROM T_SAL_SHIPMENT tss
            WHERE YEAR(bl_date) = $year AND `type` IN ('Local', 'Export')
            GROUP BY bulan, type
            ORDER BY bulan, type")->getResultArray();
        $grouped_export = array(
            "Local" => array(
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => 0,
                12 => 0
            ),
            "Export" => array(
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0,
                9 => 0,
                10 => 0,
                11 => 0,
                12 => 0,
            )
        );
        $total = 0;
        foreach ($export_vs_local as $x) {
            $grouped_export[$x['type']][$x['bulan']] = $x['jumlah'];
            $total += $x['jumlah'];
        }
        $grouped_export['total'] = $total;
        $data['export_vs_local'] = $grouped_export;
        $harga_jual = $builder_sal->select("SUM(final_price) AS price, MONTH(date_final) AS bulan")
            ->where("YEAR(date_final) = $year")
            ->groupBy("bulan")
            ->orderBy("bulan")
            ->get()->getResultArray();
        $data['harga_jual'] = $harga_jual;

        $barging = $builder_shipment->select("SUM(bl_qty) AS total, MONTH(bl_date) AS bulan,
            (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE `year` = $year AND `month` = bulan) AS target")
            ->where("YEAR(bl_date) = $year")
            ->groupBy("bulan")
            ->orderBy("bulan")
            ->get()->getResultArray();
        $data['barging'] = $barging;

        $CoalIndex = new TCoalIndex();
        $builder_coal = $CoalIndex->builder();
        $coal_a = $builder_coal->select("SUM(index_qty) AS total, MONTH(date_index) AS bulan")
            ->where("YEAR(date_index) = $year")
            ->where("index_type = 'IC'")
            ->groupBy("bulan")
            ->orderBy("bulan")
            ->get()->getResultArray();
        $coal_b = $builder_coal->select("SUM(index_qty) AS total, MONTH(date_index) AS bulan")
            ->where("YEAR(date_index) = $year")
            ->where("index_type = 'NEWC'")
            ->groupBy("bulan")
            ->orderBy("bulan")
            ->get()->getResultArray();
        $data['coal_ic'] = $coal_a;
        $data['coal_newc'] = $coal_b;

        $CostMining = new TCostmining();
        $builder_cost = $CostMining->builder();

        // list of cost types
        $cost_types = $db->query("SELECT id_costtype, cost_type FROM md_costtype")->getResultArray();
        $data['cost_types'] = $cost_types;

        $cost_mining_contractors = $db->query("SELECT DISTINCT id_contractor, contractor_name AS contractor 
            FROM t_costmining tc INNER JOIN md_contractors mc ON mc.id = tc.id_contractor")->getResultArray();
        $data['cost_mining_contractors'] = $cost_mining_contractors;
        // $data['cost_mining_contractors'] = [];
        $cc_type = $_GET['cc_type'] ?? 'total';
        if ($cc_type == 'total') {
            $selected_column = "tc.id_costtype > 0";
        } else {
            $selected_column = "tc.id_costtype = $cc_type";
        }
        $cost_minings = $db->query("SELECT month, SUM(cost) AS total_cost, contractor_name AS contractor FROM t_costmining tc 
            INNER JOIN md_costtype mc ON mc.id_costtype = tc.id_costtype
            INNER JOIN md_contractors mc2 ON mc2.id = tc.id_contractor 
            WHERE year = $year AND $selected_column
            GROUP BY month, id_contractor")->getResultArray();
        $grouped_cost_minings = array();
        foreach ($cost_minings as $c) {
            $grouped_cost_minings[$c['contractor']][$c['month']] = $c['total_cost'];
        }
        $data['cost_minings'] = $grouped_cost_minings;
        // $data['cost_minings'] = [];

        $unrestricted_stock = $db->query("SELECT isc.stock AS 'Crusher Stock', 
            isp.stock AS 'Port Stock', isr.stock AS 'ROM Stock'
            FROM im_stock_cc isc INNER JOIN im_stock_port isp ON isp.posting_date = isc.posting_date 
            INNER JOIN im_stock_raw isr ON isr.posting_date = isc.posting_date 
            WHERE YEAR(isc.posting_date) = $year
            ORDER BY isc.posting_date DESC LIMIT 1")
            ->getRowArray();
        $data['unrestricted_stock'] = $unrestricted_stock ?? [];

        $prd_sales_prod = $db->query("SELECT SUM(prd_cg_total) AS total,
            MONTH(prd_date) AS bulan
            FROM timesheets WHERE YEAR(prd_date) = $year
            GROUP BY bulan
            ORDER BY bulan")->getResultArray();
        $prd_sales_sales = $db->query("SELECT SUM(bl_qty) AS total,
            MONTH(bl_date) AS bulan
            FROM T_SAL_SHIPMENT WHERE YEAR(bl_date) = $year
            GROUP BY bulan
            ORDER BY bulan")->getResultArray();
        $prd_hauling = $db->query("SELECT SUM(Net_Weigh/1000) AS total, MONTH(Posting_Date) AS bulan
            FROM inquiry_transfer WHERE YEAR(Posting_Date) = $year
            GROUP BY bulan ORDER BY bulan")->getResultArray();
        $data['prd_prod'] = $prd_sales_prod;
        $data['prd_sales'] = $prd_sales_sales;
        $data['prd_hauling'] = $prd_hauling;

        // Inventory Closing Value
        $inventory_closing_value = $db->query("SELECT isc.stock AS crusher_stock, isp.stock AS port_stock, isr.stock AS rom_stock, DAY(isc.posting_date) AS `day`, MONTH(isc.posting_date) AS bulan 
            FROM im_stock_cc isc INNER JOIN im_stock_port isp ON isp.posting_date = isc.posting_date 
            INNER JOIN im_stock_raw isr ON isr.posting_date = isc.posting_date
            WHERE DAY(isc.posting_date) = 25 AND YEAR(isc.posting_date) = $year ORDER BY bulan")->getResultArray();
        $data["inventory_closing_value"] = $inventory_closing_value;

        // Stock vs Production
        $stock_vs_prod = $db->query("SELECT SUM(prd_cg_total) AS prod,
            MONTH(prd_date) AS bulan, YEAR(prd_date) AS tahun,
            (SELECT SUM(Weigh_In) FROM inquiry_transfer WHERE MONTH(Posting_Date) = bulan AND YEAR(Posting_Date) = tahun) AS stock
            FROM timesheets WHERE YEAR(prd_date) = $year
            GROUP BY bulan, tahun
            ORDER BY bulan")->getResultArray();
        $data['stock_vs_prod'] = $stock_vs_prod;

        // Contractor Performance
        if ($type == 'yearly') {
            // $contractor_cg = $db->query("SELECT mm.`year` AS bulan, mc.contractor_name AS contractor,
            //     SUM(prd_cg_total) AS actual_cg, SUM(prd_ob_total) AS actual_ob,
            //     (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 
            //         WHERE mm2.id_contractor = t.id_contractor AND mm2.`year` = bulan) AS budget_ob,
            //     (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 
            //         WHERE mm2.id_contractor = t.id_contractor AND mm2.`year` = bulan) AS budget_cg
            //     FROM timesheets t
            //     INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            //     INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            //     GROUP BY bulan, t.id_contractor
            //     ORDER BY bulan, contractor")->getResultArray();
            $contractor_cg = $db->query("SELECT
                mm.`year` AS bulan,
                mc.contractor_name AS contractor,
                SUBSTRING_INDEX(mc.contractor_name, ' ', -1) AS project1, 
                (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = mm.`year` AND tt.contractor = project1) AS actual_cg,
                SUM(prd_ob_total) AS actual_ob,
                (
                SELECT
                    SUM(mm2.ob_monthlybudget_qt)
                FROM
                    md_monthlybudget mm2
                WHERE
                    mm2.id_contractor = t.id_contractor
                    AND mm2.`year` = bulan) AS budget_ob,
                (
                SELECT
                    SUM(mm2.cg_monthlybudget_qt)
                FROM
                    md_monthlybudget mm2
                WHERE
                    mm2.project = project1
                    AND mm2.`year` = mm.`year`) AS budget_cg
            FROM
                timesheets t
            INNER JOIN md_contractors mc ON
                mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON
                mm.id_monthlybudget = t.id_monthlybudget
            WHERE t.status = 'approved'
            GROUP BY
                bulan,
                t.id_contractor
            ORDER BY
                bulan,
                contractor")->getResultArray();
            $grouped_contractors = array();
            foreach ($contractor_cg as $c) {
                $grouped_contractors[$c['contractor']][$c['bulan']] = $c;
            }
            $contractor_distance = $db->query("SELECT SUM(prd_ob_total * prd_ob_distance) AS ob,
                SUM(prd_cg_total * prd_cg_distance) AS cg, YEAR(prd_date) AS bulan,
                mc.contractor_name AS contractor,
                SUM(prd_ob_total) AS ob_total,
                SUM(prd_cg_total) AS cg_total,
                -- (SELECT SUM(PRD_Outlook_discg) FROM t_outlook_timesheet tot WHERE year = tahun AND month = bulan) AS outlook_cg,
                -- (SELECT SUM(PRD_Outlook_disob) FROM t_outlook_timesheet tot WHERE year = tahun AND month = bulan) AS outlook_ob,
                (SELECT SUM(disob_monthlybudget_qty * mm.ob_monthlybudget_qt)/SUM(mm.ob_monthlybudget_qt) FROM md_monthly_disob mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = bulan) AS target_ob,
                (SELECT SUM(discg_monthlybudget_qty * mm.cg_monthlybudget_qt)/SUM(mm.cg_monthlybudget_qt) FROM md_monthly_discg mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = bulan) AS target_cg
                FROM timesheets t
                INNER JOIN md_contractors mc ON mc.id = t.id_contractor
                GROUP BY bulan, mc.contractor_name
                ORDER BY bulan, contractor")->getResultArray();
            $grouped_distance = array();
            foreach ($contractor_distance as $c) {
                $grouped_distance[$c['contractor']][] = $c;
            }
        } elseif ($type == 'monthly') {
            // $contractor_cg = $db->query("SELECT mm.`month` AS bulan, mm.`year` AS tahun, mc.contractor_name AS contractor,
            //     SUM(prd_cg_total) AS actual_cg, SUM(prd_ob_total) AS actual_ob,
            //     (SELECT mm2.ob_monthlybudget_qt FROM md_monthlybudget mm2 
            //         WHERE mm2.id_contractor = t.id_contractor AND mm2.`month` = bulan 
            //         AND mm2.`year` = tahun) AS budget_ob,
            //     (SELECT mm2.cg_monthlybudget_qt FROM md_monthlybudget mm2 
            //         WHERE mm2.id_contractor = t.id_contractor AND mm2.`month` = bulan 
            //         AND mm2.`year` = tahun) AS budget_cg
            //     FROM timesheets t
            //     INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            //     INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            //     WHERE mm.`year` = $year
            //     GROUP BY bulan, tahun, t.id_contractor, mm.id_monthlybudget  
            //     ORDER BY bulan, contractor")->getResultArray();
            $contractor_cg = $db->query("SELECT mm.`month` AS bulan, mm.`year` AS tahun, mc.contractor_name AS contractor,
                SUBSTRING_INDEX(mc.contractor_name, ' ', -1) AS project1,
                (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = mm.`year` AND tt.contractor = project1 AND tt.bulan = mm.`month`) AS actual_cg,
                SUM(prd_ob_total) AS actual_ob,
                (SELECT mm2.ob_monthlybudget_qt FROM md_monthlybudget mm2 
                    WHERE mm2.id_contractor = t.id_contractor AND mm2.`month` = bulan 
                    AND mm2.`year` = tahun) AS budget_ob,
                (SELECT mm2.cg_monthlybudget_qt FROM md_monthlybudget mm2 
                    WHERE mm2.id_contractor = t.id_contractor AND mm2.`month` = bulan 
                    AND mm2.`year` = tahun) AS budget_cg
                FROM timesheets t
                INNER JOIN md_contractors mc ON mc.id = t.id_contractor
                INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
                WHERE mm.`year` = $year AND t.status = 'approved'
                GROUP BY bulan, tahun, t.id_contractor, mm.id_monthlybudget  
                ORDER BY bulan, contractor")->getResultArray();
            $grouped_contractors = array();
            foreach ($contractor_cg as $c) {
                $grouped_contractors[$c['contractor']][$c['bulan']] = $c;
            }
            $contractor_distance = $db->query("SELECT SUM(prd_ob_total * prd_ob_distance) AS ob,
                SUM(prd_cg_total * prd_cg_distance) AS cg,
                MONTH(prd_date) AS bulan, YEAR(prd_date) AS tahun,
                mc.contractor_name AS contractor,
                SUM(prd_ob_total) AS ob_total,
                SUM(prd_cg_total) AS cg_total,
                -- (SELECT SUM(PRD_Outlook_discg) FROM t_outlook_timesheet tot WHERE year = tahun AND month = bulan) AS outlook_cg,
                -- (SELECT SUM(PRD_Outlook_disob) FROM t_outlook_timesheet tot WHERE year = tahun AND month = bulan) AS outlook_ob,
                (SELECT SUM(disob_monthlybudget_qty * mm.ob_monthlybudget_qt)/SUM(mm.ob_monthlybudget_qt) FROM md_monthly_disob mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = tahun AND mmd.month = bulan) AS target_ob,
                (SELECT SUM(discg_monthlybudget_qty * mm.cg_monthlybudget_qt)/SUM(mm.cg_monthlybudget_qt) FROM md_monthly_discg mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = tahun AND mmd.month = bulan) AS target_cg
                FROM timesheets t
                INNER JOIN md_contractors mc ON mc.id = t.id_contractor
                WHERE YEAR(prd_date) = $year
                GROUP BY bulan, tahun, mc.contractor_name
                ORDER BY bulan, contractor")->getResultArray();
            $grouped_distance = array();
            foreach ($contractor_distance as $c) {
                $grouped_distance[$c['contractor']][$c['bulan']] = $c;
                for ($i = 1; $i <= 12; $i++) {
                    if (!array_key_exists($i, $grouped_distance[$c['contractor']])) {
                        $grouped_distance[$c['contractor']][$i] = array('bulan' => $i);
                    }
                }
            }
        } elseif ($type == 'daily') {
            // $contractor_cg = $db->query("SELECT
            //     SUM(prd_cg_total) AS actual_cg,
            //     SUM(prd_ob_total) AS actual_ob,
            //     prd_date AS date,
            //     id_monthlybudget AS ids, 
            //     contractor_name AS contractor,
            //     (
            //     SELECT
            //         cg_dailybudget_qt
            //     FROM
            //         md_monthlybudget mms
            //     WHERE
            //         mms.id_monthlybudget = ids) AS budget_cg,
            //     (
            //     SELECT
            //         ob_dailybudget_qt
            //     FROM
            //         md_monthlybudget mms
            //     WHERE
            //         mms.id_monthlybudget = ids) AS budget_ob
            // FROM
            //     timesheets t
            // INNER JOIN md_contractors mc ON
            //     mc.id = t.id_contractor
            // WHERE
            //     prd_date BETWEEN '$date_before' AND '$date_target'
            // GROUP BY
            //     id_monthlybudget,
            //     id_contractor,
            //     prd_date
            // ORDER BY
            //     prd_date, contractor")->getResultArray();
            $contractor_cg = $db->query("SELECT
                SUM(prd_ob_total) AS actual_ob,
                prd_date AS date,
                id_monthlybudget AS ids, 
                contractor_name AS contractor,
                SUBSTRING_INDEX(contractor_name, ' ', -1) AS project1,
                (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.contractor = project1 AND DATE(tt.Posting_Date) = date) AS actual_cg,
                (
                SELECT
                    cg_dailybudget_qt
                FROM
                    md_monthlybudget mms
                WHERE
                    mms.id_monthlybudget = ids) AS budget_cg,
                (
                SELECT
                    ob_dailybudget_qt
                FROM
                    md_monthlybudget mms
                WHERE
                    mms.id_monthlybudget = ids) AS budget_ob
            FROM
                timesheets t
            INNER JOIN md_contractors mc ON
                mc.id = t.id_contractor
            WHERE
                prd_date BETWEEN '$date_before' AND '$date_target'
                AND t.status = 'approved'
            GROUP BY
                id_monthlybudget,
                id_contractor,
                prd_date
            ORDER BY
                prd_date, contractor")->getResultArray();
            $grouped_contractors = array();
            foreach ($contractor_cg as $c) {
                $grouped_contractors[$c['contractor']][] = $c;
            }
            $contractor_distance = $db->query("SELECT SUM(prd_ob_total * prd_ob_distance) AS ob,
                SUM(prd_cg_total * prd_cg_distance) AS cg,
                prd_date AS bulan,
                mc.contractor_name AS contractor,
                SUM(prd_ob_total) AS ob_total,
                SUM(prd_cg_total) AS cg_total,
                -- (SELECT SUM(PRD_Outlook_discg) FROM t_outlook_timesheet tot WHERE year = tahun AND month = bulan) AS outlook_cg,
                -- (SELECT SUM(PRD_Outlook_disob) FROM t_outlook_timesheet tot WHERE year = tahun AND month = bulan) AS outlook_ob,
                (SELECT SUM(disob_dailybudget_qty * mm.ob_dailybudget_qt)/SUM(mm.ob_dailybudget_qt) FROM md_monthly_disob mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = $year AND mmd.month = $month) AS target_ob,
                (SELECT SUM(discg_dailybudget_qty * mm.cg_dailybudget_qt)/SUM(mm.cg_dailybudget_qt) FROM md_monthly_discg mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` AND mm.id_contractor = mmd.id_contractor WHERE mmd.year = $year AND mmd.month = $month) AS target_cg
                FROM timesheets t
                INNER JOIN md_contractors mc ON mc.id = t.id_contractor
                WHERE prd_date BETWEEN '$date_before' AND '$date_target'
                GROUP BY bulan, mc.contractor_name
                ORDER BY bulan, contractor")->getResultArray();
            $grouped_distance = array();
            foreach ($contractor_distance as $c) {
                $grouped_distance[$c['contractor']][] = $c;
            }
        }

        $data['contractor_cg_ob'] = $grouped_contractors;
        $data['contractor_performance'] = $contractor_cg;
        $data['contractor_distance'] = $grouped_distance;

        $coals_index = $db->query("SELECT month_index AS bulan,
            year_index AS tahun,
            SUM(CASE WHEN index_type = 'MV' THEN index_qty ELSE NULL END) AS mv,
            (SELECT SUM(index_qty) FROM T_CoalIndex WHERE year_index = tahun AND month_index = bulan AND index_type = 'Ici420' AND deletion_status = 0) as ic_total,
            (SELECT SUM(index_qty) FROM T_CoalIndex WHERE year_index = tahun AND month_index = bulan AND index_type = 'EBL' AND deletion_status = 0) as newc_total
            FROM T_CoalIndex
            WHERE year_index = $year AND deletion_status = 0
            GROUP BY bulan, tahun
            ORDER BY (bulan * 1)")->getResultArray();
        $data['coal_index'] = $coals_index;

        // ! TODO: tsal shipment, by bl_date
        $dmo_summary = $db->query("WITH temp_dmo AS (SELECT MONTH(tsco.`bl_date`) AS bulan, qty1, qty2, YEAR(tsco.`bl_date`) AS tahun 
            FROM T_SAL_DMO tsd
            INNER JOIN T_SAL_SHIPMENT tsco ON tsd.contract_no = tsco.contract_no AND tsd.contract_id = tsco.contract_id
            WHERE YEAR(tsco.`bl_date`) = $year
            GROUP BY bulan, qty1, qty2, tahun)
        SELECT SUM(qty1) AS listrik, SUM(qty2) AS nonlistrik, bulan, tahun,
            (SELECT SUM(prd_cg_total) * 0.25 FROM (
                SELECT (CASE WHEN DAY(prd_date) > 25 THEN MONTH(prd_date) + 1 ELSE MONTH(prd_date) END) AS b, prd_cg_total 
                FROM timesheets WHERE YEAR(prd_date) = $year) a
                WHERE a.b = bulan) AS target
        FROM temp_dmo GROUP BY bulan, tahun
        ORDER BY bulan")->getResultArray();
        $data['dmo_summary'] = $dmo_summary;

        // ! VIEWS
        echo view('pages/dashboard', $data);
    }

    // #Tempcode Malik ==
    public function production_report()
    {
        $data['title'] = "Production Report";
        echo view('pages/production-report', $data);
    }
    public function production_report_download()
    {
        $data['title'] = "Download Production Report";
        echo view('pages/production-report-download', $data);
    }
    public function peta()
    {
        $data['title'] = "Peta";
        echo view('pages/peta', $data);
    }
    // == #Tempcode Malik

    public function upload()
    {
        $validationRule = [
            'userfile' => [
                'label' => 'Image File',
                'rules' => 'uploaded[userfile]'
                    . '|is_image[userfile]'
                    . '|mime_in[userfile,image/jpg,image/jpeg,image/gif,image/png,image/webp]'
                    . '|max_size[userfile,100]'
                    . '|max_dims[userfile,1024,768]',
            ],
        ];
        // if (! $this->validate($validationRule)) {
        //     $data = ['errors' => $this->validator->getErrors()];

        //     return view('upload_form', $data);
        // }

        $file = $this->request->getFile('file');

        if (!$file->hasMoved()) {
            $filepath = WRITEPATH . 'uploads/' . $file->store();

            // $data = ['uploaded_flleinfo' => new File($filepath)];
            $res = array(
                "status" => "true",
                "Message" => "Berhasil Upload file",
                "path" => new File($filepath)
            );
            return $this->respond($res, 200);
        } else {
            $data = [
                'status' => "false",
                'message' => 'The file has already been moved.'
            ];
            return $this->respond($data, 200);
        }
    }
}
