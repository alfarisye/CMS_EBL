<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Timesheets;
use App\Models\TimesheetAdjustments;
use CodeIgniter\I18n\Time;

use Config\Database;

/** 
 * @author anggakawa
 * 
 * Untuk menampilkan data operasional di CMS
 * 
 * @info
 * 1. perhitungan per bulan dimulai dari > 25 bulan sebelumnya sampai dengan 25 bulan ini
 * 
 * 
 */
class OperationDashboard extends BaseController
{

    public function __construct()
    {
        $db = Database::connect();
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
    }

    // Tampilan data Today kecuali untuk graphic line yang langsung keseluruhan
    public function index()
    {
        $db = Database::connect();
        $data['title'] = "Operation Dashboard";
        $data['year'] = range(date('Y'), date('Y') - 4);

        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $coal_getting_production = $builder->select("SUM(prd_cg_total) AS actual, mm.cg_dailybudget_qt AS budget")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->where("mm.month = MONTH(NOW())")
            ->where("mm.year = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->groupBy("mm.id_monthlybudget")
            ->get()->getRowArray();
        $ob_production = $builder->select("SUM(prd_ob_total) AS actual, mm.ob_dailybudget_qt AS budget")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->where("mm.month = MONTH(NOW())")
            ->where("mm.year = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->groupBy("mm.id_monthlybudget")
            ->get()->getRowArray();
        // $coal_getting_line = $builder->select("SUM(prd_cg_total) AS actual, mm.`month`,
        //         (SELECT SUM(cg_adjustment) FROM timesheet_adjustments WHERE mm.`month` = MONTH(start_date) AND 
        //         mm.`month` = MONTH(end_date)) AS adjust,
        //         (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 WHERE mm2.`month` = mm.`month` AND mm2.`year` = mm.`year`)
        //         AS budget")
        //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
        //     ->where("YEAR(prd_date) = YEAR(NOW())")
        //     ->where("timesheets.status = 'approved'")
        //     ->where("deleted_at IS NULL")
        //     ->groupBy("mm.`month`, mm.`year`")
        //     ->orderBy("mm.`month`")
        //     ->get()->getResultArray();
        $coal_getting_line = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 0 AS adjust, bulan, (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = YEAR(NOW()) AND month = bulan) AS budget, 
            bulan AS month FROM temp_timbangan tt WHERE tt.tahun = YEAR(NOW()) GROUP BY bulan ORDER BY bulan")->getResultArray();
        $ob_line = $builder->select("SUM(prd_ob_total) AS actual, mm.`month`,
                (SELECT SUM(ob_adjustment) FROM timesheet_adjustments WHERE mm.`month` = MONTH(start_date) AND 
                mm.`month` = MONTH(end_date)) AS adjust,
                (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 WHERE mm2.`month` = mm.`month` AND mm2.`year` = mm.`year`)
                AS budget")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->where("YEAR(prd_date) = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->groupBy("mm.`month`, mm.`year`")
            ->orderBy("mm.`month`")
            ->get()->getResultArray();

        // $contractor_cg_production = $builder->select("SUM(prd_cg_total) AS actual, mm.cg_annualbudget_qt AS budget, mc.contractor_name, mc.id")
        //     ->join("md_annualbudget mm", "mm.id_annualbudget = timesheets.id_annualbudget")
        //     ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
        //     // ->where("prd_date = DATE(NOW())")
        //     ->where("timesheets.status = 'approved'")
        //     ->where("deleted_at IS NULL")
        //     ->groupBy("mc.id")
        //     ->groupBy("mm.id_annualbudget")
        //     ->orderBy("mc.contractor_name")
        //     ->get()->getResultArray();
        $contractor_cg_production = $db->query("SELECT mc.contractor_name AS contractor, mc.contractor_name, UUID() AS id,
            SUBSTRING_INDEX(mc.contractor_name, ' ', -1) AS project1,
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = YEAR(NOW()) AND tt.bulan <= MONTH(NOW()) AND tt.contractor = project1) AS actual,
            (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.id_contractor = t.id_contractor 
                AND mm2.`year` = YEAR(NOW())) AS budget
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE mm.`year` = YEAR(NOW())
            GROUP BY t.id_contractor
            ORDER BY contractor")->getResultArray();

        $contractor_ob_production = $builder->select("SUM(prd_ob_total) AS actual, mc.id AS idc,
            (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 WHERE mm2.year = YEAR(NOW()) AND mm2.id_contractor = mc.id AND mm2.month <= MONTH(NOW())) AS budget, 
            mc.contractor_name, mc.id")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
            ->where("YEAR(prd_date) = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->where('mm.status', 'active')
            ->groupBy("mc.id")
            ->orderBy("mc.contractor_name")
            ->get()->getResultArray();

        $contractor_stripping_ratio = $builder->select("SUM(prd_ob_total) AS actual_ob, mm.ob_annualbudget_qt AS budget_ob, 
            SUM(prd_cg_total) AS actual_cg, mm.cg_annualbudget_qt AS budget_cg, mc.id, mc.contractor_name")
            ->join("md_annualbudget mm", "mm.id_annualbudget = timesheets.id_annualbudget")
            ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
            ->where("mm.`year` = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->groupBy("mc.id")
            ->groupBy("mm.id_annualbudget")
            ->orderBy("mc.contractor_name")
            ->get()->getResultArray();
        $contractor_stripping_ratio = $db->query("SELECT mc.contractor_name AS contractor, mc.contractor_name, mc.id,
            SUBSTRING_INDEX(mc.contractor_name, ' ', -1) AS project1,
            SUM(prd_ob_total) AS actual_ob,
            (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.id_contractor = t.id_contractor
                AND mm2.`year` = YEAR(NOW()) AND mm2.month <= MONTH(NOW())) AS budget_ob,
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = YEAR(NOW()) AND tt.bulan <= MONTH(NOW()) AND tt.contractor = project1) AS actual_cg,
            (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.id_contractor = t.id_contractor 
                AND mm2.`year` = YEAR(NOW())) AS budget_cg
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE mm.`year` = YEAR(NOW())
            GROUP BY t.id_contractor
            ORDER BY contractor")->getResultArray();

        $contractor_ch_line = $builder->select("SUM(prd_cg_total) AS actual, mm.cg_dailybudget_qt AS budget, mm.`month`, mc.contractor_name")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
            ->where("YEAR(prd_date) = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->groupBy("mm.id_monthlybudget, mc.id")
            ->orderBy("mm.`month`")
            ->get()->getResultArray();
        $contractor_ob_line = $builder->select("SUM(prd_ob_total) AS actual, mm.ob_dailybudget_qt AS budget, mm.`month`, mc.contractor_name")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
            ->where("YEAR(prd_date) = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->groupBy("mm.id_monthlybudget, mc.id")
            ->orderBy("mm.`month`")
            ->get()->getResultArray();

        $grouped_contractor = array();

        foreach ($contractor_ch_line as $cg) {
            for ($i = 1; $i <= 12; $i++) {
                if (!isset($grouped_contractor['cg'][$cg['contractor_name']][$i])) {
                    $grouped_contractor['cg'][$cg['contractor_name']][$i] = ['actual' => 0, 'budget' => 0, 'month' => $i];
                }
            }
            $grouped_contractor['cg'][$cg['contractor_name']][$cg['month']] = $cg;
        }
        foreach ($contractor_ob_line as $cg) {
            for ($i = 1; $i <= 12; $i++) {
                if (!isset($grouped_contractor['ob'][$cg['contractor_name']][$i])) {
                    $grouped_contractor['ob'][$cg['contractor_name']][$i] = ['actual' => 0, 'budget' => 0, 'month' => $i];
                }
            }
            $grouped_contractor['ob'][$cg['contractor_name']][$cg['month']] = $cg;
        }

        // RATIO
        // $stripping_ration = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
        //     mm.`month`, 
        //     (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = mm.year AND month = mm.month) AS budget_cg, 
        //     (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = mm.year AND month = mm.month) AS budget_ob")
        //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget", "right")
        //     ->where("mm.year = YEAR(NOW())")
        //     ->groupBy("mm.month, mm.year")
        //     ->get()->getResultArray();
        $stripping_ration = $db->query("SELECT mm.`month` AS bulan, mm.`year` AS tahun,
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = mm.`year` AND tt.bulan = mm.`month`) AS actual_cg,
            SUM(prd_ob_total) AS actual_ob,  mm.`month`,
            (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`month` = bulan 
                AND mm2.`year` = tahun) AS budget_ob,
            (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`month` = bulan 
                AND mm2.`year` = tahun) AS budget_cg
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE mm.`year` = YEAR(NOW())
            GROUP BY bulan, tahun
            ORDER BY bulan")->getResultArray();

        $stripping_today = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
            SUM(mm.cg_monthlybudget_qt) AS budget_cg, SUM(mm.ob_monthlybudget_qt) AS budget_ob")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->where("mm.month = MONTH(NOW())")
            ->where("mm.year = YEAR(NOW())")
            ->get()->getRowArray();
        // $contractor_performance = $builder->select("SUM(timesheets.prd_cg_total) AS cg_total, SUM(timesheets.prd_ob_total) AS ob_total,
        //     ma.`year`, ma.cg_annualbudget_qt, ma.ob_annualbudget_qt, mc.contractor_name")
        //     ->join("md_annualbudget ma", "ma.id_annualbudget = timesheets.id_annualbudget")
        //     ->join("md_contractors mc", "mc.id = id_contractor")
        //     ->groupBy("mc.id, ma.id_annualbudget")
        //     ->get()->getResultArray();

        $contractor_performance = $db->query("SELECT mc.contractor_name AS contractor, mc.contractor_name, UUID() AS id,
            SUBSTRING_INDEX(mc.contractor_name, ' ', -1) AS project1,
            mm.year,
            SUM(prd_ob_total) AS ob_total,
            (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.id_contractor = t.id_contractor
                AND mm2.`year` = YEAR(NOW()) AND mm2.month <= MONTH(NOW())) AS ob_annualbudget_qt,
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = YEAR(NOW()) AND tt.bulan <= MONTH(NOW()) AND tt.contractor = project1) AS cg_total,
            (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.id_contractor = t.id_contractor 
                AND mm2.`year` = YEAR(NOW())) AS cg_annualbudget_qt
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE t.status = 'approved'
            GROUP BY t.id_contractor, mm.year
            ORDER BY mm.year")->getResultArray();

        $data['cg_sum_production'] = $coal_getting_production ?? ['actual' => 0, 'budget' => 0];
        $data['ob_sum_production'] = $ob_production ?? ['actual' => 0, 'budget' => 0];
        $data['cg_budget_vs_actual'] = $coal_getting_line;
        $data['ob_budget_vs_actual'] = $ob_line;
        $data['contractor_cg'] = $contractor_cg_production;
        $data['contractor_ob'] = $contractor_ob_production;
        $data['contractor_line'] = $grouped_contractor;
        $data['stripping_ratio'] = $stripping_ration;
        $data['stripping_ratio_today'] = $stripping_today;
        $data['stripping_contractor'] = $contractor_stripping_ratio;
        $data['contractor_performance'] = $contractor_performance;
        echo view('pages/operation-dashboard', $data);
    }

    public function getCGProductionThisMonth()
    {
        header('Content-Type: application/json');
        $db = Database::connect();
        $today = Time::now();
        $month = $today->getMonth();
        $year = $today->getYear();

        if ($today->getDay() > 25 && $month < 12) {
            $month++;
        }

        $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
            (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget 
            FROM temp_timbangan WHERE tahun = $year AND bulan = $month")->getRowArray();

        $res = array_values($coal_getting_production ?? [0, 0, 0]);
        return $this->response->setJSON($res);
    }

    public function getCGProductionThisYear()
    {
        $db = Database::connect();
        header('Content-Type: application/json');
        $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
            (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = YEAR(NOW()) AND month <= MONTH(NOW())) AS budget 
            FROM temp_timbangan WHERE tahun = YEAR(NOW())")->getRowArray();

        $res = array_values($coal_getting_production);
        return $this->response->setJSON($res);
    }

    public function getCGProductionToday()
    {
        $db = Database::connect();
        header('Content-Type: application/json');
        $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
            (SELECT SUM(cg_dailybudget_qt) FROM md_monthlybudget mms WHERE year = YEAR(NOW()) AND month = MONTH(NOW())) AS budget FROM temp_timbangan tt
            WHERE DATE(Posting_Date) = DATE(NOW());")->getRowArray();
        $res = array_values($coal_getting_production ?? [0, 0]);
        return $this->response->setJSON($res);
    }

    public function getOBProductionThisMonth()
    {
        header('Content-Type: application/json');
        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $today = Time::now();
        $month = $today->getMonth();
        $year = $today->getYear();
        if ($month == 1) {
            $month_before = 1;
        } else {
            $month_before = $month - 1;
        }
        $limit_start = "$year-$month_before-25";
        $limit_end = "$year-$month-25";
        $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual, 
            (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = YEAR(NOW()) AND month = MONTH(NOW()) AND status = 'active') AS budget, 
            COALESCE((SELECT SUM(ob_adjustment) FROM timesheet_adjustments WHERE start_date > $limit_start AND 
            end_date <= $limit_end), 0) AS adjust")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->where("mm.`month` = MONTH(NOW()) AND mm.`year` = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            // ->groupBy("mm.id_monthlybudget")
            ->get()->getRowArray();
        $res = array_values($coal_getting_production ?? [0, 0, 0]);
        return $this->response->setJSON($res);
    }

    public function getOBProductionThisYear()
    {
        header('Content-Type: application/json');
        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual, (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget WHERE `year` = YEAR(NOW()) AND `month` <= MONTH(NOW()) AND status = 'active') AS budget,
            COALESCE((SELECT SUM(ob_adjustment) FROM timesheet_adjustments WHERE YEAR(NOW()) = YEAR(start_date)), 0) AS adjust")
            ->where("YEAR(prd_date) = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->get()->getRowArray();
        $res = array_values($coal_getting_production);
        return $this->response->setJSON($res);
    }

    public function getOBProductionToday()
    {
        header('Content-Type: application/json');
        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual, 
        (SELECT SUM(ob_dailybudget_qt) FROM md_monthlybudget mms WHERE year = YEAR(NOW()) AND month = MONTH(NOW()) AND status = 'active')
            AS budget")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->where("prd_date = DATE(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            // ->groupBy("mm.id_monthlybudget")
            ->get()->getRowArray();
        $res = array_values($coal_getting_production ?? [0, 0]);
        return $this->response->setJSON($res);
    }

    public function getContractorCGProductionToday($id)
    {
        header('Content-Type: application/json');
        $db = Database::connect();

        // $Timesheet = new Timesheets();
        // $builder = $Timesheet->builder();
        // $coal_getting_production = $builder->select("COALESCE(SUM(prd_cg_total), 0) AS actual, COALESCE(mm.cg_dailybudget_qt, 0) AS budget, mc.contractor_name, mc.id")
        //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
        //     ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
        //     ->where("prd_date = DATE(NOW())")
        //     ->where("timesheets.status = 'approved'")
        //     ->where("deleted_at IS NULL")
        //     ->where("mc.id = $id")
        //     ->get()->getRowArray();

        $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
            (SELECT SUM(cg_dailybudget_qt) FROM md_monthlybudget mms WHERE year = YEAR(NOW()) AND month = MONTH(NOW()) AND project LIKE '%$id%') AS budget FROM temp_timbangan tt
            WHERE DATE(Posting_Date) = DATE(NOW()) AND contractor = '$id';")->getRowArray();
        $res = array_values($coal_getting_production ?? [0, 0]);
        return $this->response->setJSON($res);
    }

    public function getContractorCGProductionThisMonth($id)
    {
        header('Content-Type: application/json');
        $db = Database::connect();

        // $Timesheet = new Timesheets();
        // $builder = $Timesheet->builder();
        // $coal_getting_production = $builder->select("SUM(prd_cg_total) AS actual, mm.cg_monthlybudget_qt AS budget, mc.contractor_name, mc.id")
        //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
        //     ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
        //     ->where("mm.`month` = MONTH(NOW())")
        //     ->where("timesheets.status = 'approved'")
        //     ->where("deleted_at IS NULL")
        //     ->where("mc.id = $id")
        //     ->groupBy("mm.id_monthlybudget")
        //     ->get()->getRowArray();

        $today = Time::now();
        $month = $today->getMonth();
        $year = $today->getYear();

        if ($today->getDay() > 25 && $month < 12) {
            $month++;
        }

        $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
            (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month AND project = '$id') AS budget 
            FROM temp_timbangan WHERE tahun = $year AND bulan = $month AND contractor LIKE '%$id%'")->getRowArray();

        $res = array_values($coal_getting_production ?? [0, 0]);
        return $this->response->setJSON($res);
    }

    public function getContractorCGProductionThisYear($id)
    {
        $db = Database::connect();
        header('Content-Type: application/json');
        $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
            (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = YEAR(NOW()) AND month <= MONTH(NOW()) AND project LIKE '%$id%') AS budget 
            FROM temp_timbangan WHERE tahun = YEAR(NOW()) AND contractor = '$id'")->getRowArray();
        $res = array_values($coal_getting_production ?? [0, 0]);
        return $this->response->setJSON($res);
    }

    public function getContractorOBProductionToday($id)
    {
        header('Content-Type: application/json');
        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual, mm.ob_dailybudget_qt AS budget, mc.contractor_name, mc.id")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
            ->where("prd_date = DATE(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->where("mc.id = $id")
            ->groupBy("mm.id_monthlybudget")
            ->get()->getRowArray();
        $res = array_values($coal_getting_production ?? [0, 0]);
        return $this->response->setJSON($res);
    }

    public function getContractorOBProductionThisMonth($id)
    {
        header('Content-Type: application/json');
        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual, mm.ob_monthlybudget_qt AS budget, mc.contractor_name, mc.id")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
            ->where("mm.`month` = MONTH(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->where("mc.id = $id")
            ->groupBy("mm.id_monthlybudget")
            ->get()->getRowArray();
        $res = array_values($coal_getting_production ?? [0, 0]);
        return $this->response->setJSON($res);
    }

    public function getContractorOBProductionThisYear($id)
    {
        header('Content-Type: application/json');
        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual, 
            (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 WHERE mm2.year = YEAR(NOW()) AND mm2.id_contractor = mc.id AND mm2.month <= MONTH(NOW())) AS budget, 
            mc.contractor_name, mc.id")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
            ->where("mm.`year` = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->where("mc.id = $id")
            // ->groupBy("mm.id_annualbudget")
            ->get()->getRowArray();
        $res = array_values($coal_getting_production ?? [0, 0]);
        return $this->response->setJSON($res);
    }

    public function getCGProductionByDate()
    {
        $db = Database::connect();
        header('Content-Type: application/json');
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $parsed_start = Time::parse($start_date);
        $parsed_end = Time::parse($end_date);
        $start_month = $parsed_start->getMonth();
        $end_month = $parsed_end->getMonth();
        $start_year = $parsed_start->getYear();
        $end_year = $parsed_end->getYear();
        $start_day = $parsed_start->getDay();
        $end_day = $parsed_start->getDay();
        if ($start_day > 25 && $start_month < 12) {
            $start_month++;
        } else if ($end_day > 25 && $end_month < 12) {
            $end_month++;
        }

        $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
            (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE (year >= $start_year AND year <= $end_year) AND (month >= $start_month AND month <= $end_month)) AS budget,
            0 AS adjust 
            FROM temp_timbangan WHERE Posting_Date >= '$start_date' AND Posting_Date <= '$end_date'")->getRowArray();
        return $this->response->setJSON(['actual' => $coal_getting_production['actual'] + $coal_getting_production['adjust'], 'budget' => $coal_getting_production['budget']]);
    }

    public function getOBProductionByDate()
    {
        header('Content-Type: application/json');
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $parsed_start = Time::parse($start_date);
        $parsed_end = Time::parse($end_date);
        $start_month = $parsed_start->getMonth();
        $end_month = $parsed_end->getMonth();
        $start_year = $parsed_start->getYear();
        $end_year = $parsed_end->getYear();
        $start_day = $parsed_start->getDay();
        $end_day = $parsed_start->getDay();
        if ($start_day > 25) {
            $start_month++;
        } else if ($end_day > 25) {
            $end_month++;
        }

        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual, 
            (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE 
            (year >= $start_year AND year <= $end_year) 
            AND (month <= $end_month AND month >= $start_month)
            AND status = 'active') AS budget,
            COALESCE((SELECT SUM(ob_adjustment) FROM timesheet_adjustments WHERE start_date >= '$start_date' AND 
            end_date <= '$end_date'), 0) AS adjust")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->where("prd_date BETWEEN '$start_date' AND '$end_date'")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            // ->groupBy("mm.id_monthlybudget")
            ->get()->getResultArray();
        $res = array("actual" => 0, "budget" => 0, "adjust" => 0);
        foreach ($coal_getting_production as $cg) {
            $res['actual'] += $cg['actual'];
            $res['budget'] += $cg['budget'];
            $res['adjust'] = $cg['adjust'];
        }
        return $this->response->setJSON(['actual' => $res['actual'] + $res['adjust'], 'budget' => $res['budget']]);
    }

    public function getContractorOBProductionByDate($id)
    {
        header('Content-Type: application/json');
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');

        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual, mm.ob_monthlybudget_qt AS budget, mc.contractor_name, mc.id")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
            ->join("md_contractors mc", "mc.id = timesheets.id_contractor")
            ->where("prd_date BETWEEN '$start_date' AND '$end_date'")
            ->where("timesheets.status = 'approved'")
            ->where("deleted_at IS NULL")
            ->where("mc.id = $id")
            ->groupBy("mm.month")
            ->get()->getResultArray();
        $res = array("actual" => 0, "budget" => 0);
        foreach ($coal_getting_production as $cg) {
            $res['actual'] += $cg['actual'];
            $res['budget'] += $cg['budget'];
        }
        return $this->response->setJSON(['actual' => $res['actual'], 'budget' => $res['budget']]);
    }

    public function getContractorCGProductionByDate($id)
    {
        header('Content-Type: application/json');
        $db = Database::connect();
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');

        $parsed_start = Time::parse($start_date);
        $parsed_end = Time::parse($end_date);
        $start_month = $parsed_start->getMonth();
        $end_month = $parsed_end->getMonth();
        $start_year = $parsed_start->getYear();
        $end_year = $parsed_end->getYear();
        $start_day = $parsed_start->getDay();
        $end_day = $parsed_start->getDay();
        if ($start_day > 25 && $start_month < 12) {
            $start_month++;
        } else if ($end_day > 25 && $end_month < 12) {
            $end_month++;
        }

        $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
            (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE (year >= $start_year AND year <= $end_year) AND (month >= $start_month AND month <= $end_month) AND project LIKE '%$id%') AS budget,
            0 AS adjust 
            FROM temp_timbangan WHERE Posting_Date >= '$start_date' AND Posting_Date <= '$end_date' AND contractor = '$id'")->getRowArray();

        return $this->response->setJSON(['actual' => $coal_getting_production['actual'], 'budget' => $coal_getting_production['budget']]);
    }

    public function strippingRatio()
    {
        header('Content-Type: application/json');
        $Timesheet = new Timesheets();
        $builder = $Timesheet->builder();
        $stripping_ration = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
            mm.`month`, SUM(mm.cg_monthlybudget_qt) AS budget_cg, SUM(mm.ob_monthlybudget_qt) AS budget_ob")
            ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget", "right")
            ->where("mm.year = YEAR(NOW())")
            ->where("timesheets.status = 'approved'")
            ->groupBy("mm.month")
            ->get()->getResultArray();
        return $this->response->setJSON(['stripping_ratio' => $stripping_ration]);
    }

    public function strippingRatioToday()
    {
        $db = Database::connect();
        header('Content-Type: application/json');
        // $Timesheet = new Timesheets();
        // $builder = $Timesheet->builder();
        // $stripping_ration = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
        //     SUM(mm.cg_monthlybudget_qt) AS budget_cg, SUM(mm.ob_monthlybudget_qt) AS budget_ob")
        //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
        //     ->where("timesheets.prd_date = DATE(NOW())")
        //     ->where("timesheets.status = 'approved'")
        //     ->get()->getRowArray();
        $stripping_ration = $db->query("SELECT 
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE DATE(Posting_Date) = DATE(NOW())) AS actual_cg,
            COALESCE(SUM(prd_ob_total), 0) AS actual_ob,
            (SELECT SUM(mm2.ob_dailybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` = MONTH(NOW())) AS budget_ob,
            (SELECT SUM(mm2.cg_dailybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` = MONTH(NOW())) AS budget_cg, 
                0 AS ob_adjust,
                0 AS cg_adjust
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE t.prd_date = DATE(NOW())")->getRowArray();
        return $this->response->setJSON(['stripping_ratio' => $stripping_ration]);
    }

    public function strippingRatioThisMonth()
    {
        $db = Database::connect();
        header('Content-Type: application/json');
        // $Timesheet = new Timesheets();
        // $builder = $Timesheet->builder();
        $today = Time::now();
        $month = $today->getMonth();
        $year = $today->getYear();
        if ($month == 1) {
            $month_before = 1;
        } else {
            $month_before = $month - 1;
        }
        $limit_start = "$year-$month_before-25";
        $limit_end = "$year-$month-25";

        $stripping_ration = $db->query("SELECT 
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = YEAR(NOW()) AND tt.bulan = MONTH(NOW())) AS actual_cg,
            SUM(prd_ob_total) AS actual_ob,
            (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` = MONTH(NOW())) AS budget_ob,
            (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` = MONTH(NOW())) AS budget_cg, 
                COALESCE((SELECT SUM(ob_adjustment) FROM timesheet_adjustments WHERE start_date >= '$limit_start' AND end_date <= '$limit_end'), 0) AS ob_adjust,
                COALESCE((SELECT SUM(cg_adjustment) FROM timesheet_adjustments WHERE start_date >= '$limit_start' AND 
                end_date <= '$limit_end'), 0) AS cg_adjust
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE mm.`year` = YEAR(NOW()) AND mm.`month` = MONTH(NOW())")->getRowArray();

        return $this->response->setJSON(['stripping_ratio' => $stripping_ration]);
    }

    public function strippingRatioThisYear()
    {
        $db = Database::connect();

        header('Content-Type: application/json');
        // $Timesheet = new Timesheets();
        // $builder = $Timesheet->builder();
        // $stripping_ration = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
        //     (SELECT SUM(cg_annualbudget_qt) FROM md_annualbudget WHERE year = YEAR(NOW())) AS budget_cg, 
        //     (SELECT SUM(ob_annualbudget_qt) FROM md_annualbudget WHERE year = YEAR(NOW())) AS budget_ob,
        //     COALESCE((SELECT SUM(ob_adjustment) FROM timesheet_adjustments WHERE YEAR(NOW()) = YEAR(start_date) AND 
        //     YEAR(NOW()) = YEAR(end_date)), 0) AS adjust_ob,
        //     COALESCE((SELECT SUM(cg_adjustment) FROM timesheet_adjustments WHERE YEAR(NOW()) = YEAR(start_date) AND 
        //     YEAR(NOW()) = YEAR(end_date)), 0) AS adjust_cg")
        //     ->join("md_annualbudget mm", "mm.id_annualbudget = timesheets.id_annualbudget")
        //     // ->groupBy("mm.`year`")
        //     ->where("mm.`year` = YEAR(NOW())")
        //     ->where("timesheets.status = 'approved'")
        //     ->get()->getRowArray();

        $stripping_ration = $db->query("SELECT 
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = YEAR(NOW()) AND tt.bulan <= MONTH(NOW())) AS actual_cg,
            SUM(prd_ob_total) AS actual_ob,
            (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` <= MONTH(NOW())) AS budget_ob,
            (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` <= MONTH(NOW())) AS budget_cg, 0 AS adjust_ob, 0 AS adjust_cg
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE mm.`year` = YEAR(NOW())")->getRowArray();

        return $this->response->setJSON(['stripping_ratio' => $stripping_ration]);
    }

    public function strippingRatioByDate()
    {
        $db = Database::connect();
        header('Content-Type: application/json');
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $parsed_start = Time::parse($start_date);
        $parsed_end = Time::parse($end_date);
        $start_month = $parsed_start->getMonth();
        $end_month = $parsed_end->getMonth();
        $start_year = $parsed_start->getYear();
        $end_year = $parsed_end->getYear();
        $start_day = $parsed_start->getDay();
        $end_day = $parsed_start->getDay();
        if ($start_day > 25) {
            $start_month++;
        } else if ($end_day > 25) {
            $end_month++;
        }

        // $Timesheet = new Timesheets();
        // $builder = $Timesheet->builder();
        // $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual_ob, 
        //     (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE (year >= $start_year AND year <= $end_year) AND (month <= $end_month AND month >= $start_month)) AS budget_ob,
        //     SUM(prd_cg_total) AS actual_cg, 
        //     (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE (year >= $start_year AND year <= $end_year) AND (month <= $end_month AND month >= $start_month)) AS budget_cg,
        //     COALESCE((SELECT SUM(ob_adjustment) FROM timesheet_adjustments WHERE start_date >= '$start_date' AND 
        //         end_date <= '$end_date'), 0) AS ob_adjust, 
        //     COALESCE((SELECT SUM(cg_adjustment) FROM timesheet_adjustments WHERE start_date >= '$start_date' AND 
        //         end_date <= '$end_date'), 0) AS cg_adjust")
        //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
        //     ->where("prd_date BETWEEN '$start_date' AND '$end_date'")
        //     ->where("timesheets.status = 'approved'")
        //     ->where("deleted_at IS NULL")
        //     // ->groupBy("mm.month")
        //     ->get()->getResultArray();
        // $res = array("actual_cg" => 0, "budget_cg" => 0, "adjust_cg" => 0, "actual_ob" => 0, "budget_ob" => 0, "adjust_ob" => 0);
        // foreach ($coal_getting_production as $cg) {
        //     $res['actual_cg'] += $cg['actual_cg'];
        //     $res['budget_cg'] += $cg['budget_cg'];
        //     $res['adjust_cg'] = $cg['cg_adjust'];
        //     $res['actual_ob'] += $cg['actual_ob'];
        //     $res['budget_ob'] += $cg['budget_ob'];
        //     $res['adjust_ob'] = $cg['ob_adjust'];
        // }

        $res = $db->query("SELECT
                SUM(prd_ob_total) AS actual_ob,
                (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.Posting_Date BETWEEN '$start_date' AND '$end_date') AS actual_cg,
                (
                SELECT
                    SUM(cg_monthlybudget_qt)
                FROM
                    md_monthlybudget mms
                WHERE
                    (mms.month >= $start_month AND mms.month <= $end_month)
                AND 
                    (mms.year >= YEAR('$start_date') AND mms.year <= YEAR('$end_date'))
                ) AS budget_cg,
                (
                SELECT
                    SUM(ob_monthlybudget_qt)
                FROM
                    md_monthlybudget mms
                WHERE
                    (mms.month >= $start_month AND mms.month <= $end_month)
                AND 
                    (mms.year >= YEAR('$start_date') AND mms.year <= YEAR('$end_date'))) AS budget_ob
            FROM
                timesheets t
            WHERE
                prd_date BETWEEN '$start_date' AND '$end_date'")->getRowArray();

        return $this->response->setJSON([
            'actual_cg' => $res['actual_cg'],
            'budget_cg' => $res['budget_cg'],
            'actual_ob' => $res['actual_ob'],
            'budget_ob' => $res['budget_ob']
        ]);
    }

    public function getContractorStrippingRatioToday($id)
    {
        header('Content-Type: application/json');
        $db = Database::connect();

        $stripping_ration = $db->query("SELECT mc.contractor_name AS contractor, mc.contractor_name,
            SUBSTRING_INDEX(mc.contractor_name, ' ', -1) AS project1,
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE DATE(Posting_Date) = DATE(NOW()) AND contractor = project1) AS actual_cg,
            COALESCE(SUM(prd_ob_total), 0) AS actual_ob,
            (SELECT SUM(mm2.ob_dailybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` = MONTH(NOW()) AND project = project1) AS budget_ob,
            (SELECT SUM(mm2.cg_dailybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` = MONTH(NOW()) AND project = project1) AS budget_cg, 
                0 AS ob_adjust,
                0 AS cg_adjust
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE t.prd_date = DATE(NOW()) AND t.id_contractor = $id AND t.status = 'approved'")->getRowArray();
        return $this->response->setJSON(['stripping_ratio' => $stripping_ration]);
    }

    public function getContractorStrippingRatioThisMonth($id)
    {
        header('Content-Type: application/json');
        $db = Database::connect();

        $today = Time::now();
        $month = $today->getMonth();
        $year = $today->getYear();
        if ($month == 1) {
            $month_before = 1;
        } else {
            $month_before = $month - 1;
        }
        $limit_start = "$year-$month_before-25";
        $limit_end = "$year-$month-25";

        $stripping_ration = $db->query("SELECT mc.contractor_name AS contractor, mc.contractor_name,
            SUBSTRING_INDEX(mc.contractor_name, ' ', -1) AS project1,
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = YEAR(NOW()) AND tt.bulan = MONTH(NOW()) AND contractor = project1) AS actual_cg,
            SUM(prd_ob_total) AS actual_ob,
            (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` = MONTH(NOW()) AND project = project1) AS budget_ob,
            (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` = MONTH(NOW()) AND project = project1) AS budget_cg, 
                0 AS ob_adjust,
                0 AS cg_adjust
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE mm.`year` = YEAR(NOW()) AND mm.`month` = MONTH(NOW()) AND t.id_contractor = $id AND t.status = 'approved'")->getRowArray();
        return $this->response->setJSON(['stripping_ratio' => $stripping_ration]);
    }

    public function getContractorStrippingRatioThisYear($id)
    {
        header('Content-Type: application/json');

        $db = Database::connect();
        $stripping_ration = $db->query("SELECT mc.contractor_name AS contractor, mc.contractor_name,
            SUBSTRING_INDEX(mc.contractor_name, ' ', -1) AS project1,
            (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.tahun = YEAR(NOW()) AND tt.bulan <= MONTH(NOW()) AND contractor = project1) AS actual_cg,
            SUM(prd_ob_total) AS actual_ob,
            (SELECT SUM(mm2.ob_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` <= MONTH(NOW()) AND project = project1) AS budget_ob,
            (SELECT SUM(mm2.cg_monthlybudget_qt) FROM md_monthlybudget mm2 
                WHERE mm2.`year` = YEAR(NOW()) AND mm2.`month` <= MONTH(NOW()) AND project = project1) AS budget_cg, 
                0 AS ob_adjust,
                0 AS cg_adjust
            FROM timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget
            WHERE mm.`year` = YEAR(NOW()) AND mm.`month` <= MONTH(NOW()) AND t.id_contractor = $id AND t.status = 'approved'")->getRowArray();

        return $this->response->setJSON(['stripping_ratio' => $stripping_ration]);
    }

    public function getContractorStrippingRatioByDate($id)
    {
        header('Content-Type: application/json');
        $db = Database::connect();

        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $parsed_start = Time::parse($start_date);
        $parsed_end = Time::parse($end_date);
        $start_month = $parsed_start->getMonth();
        $end_month = $parsed_end->getMonth();
        $start_year = $parsed_start->getYear();
        $end_year = $parsed_end->getYear();
        $start_day = $parsed_start->getDay();
        $end_day = $parsed_start->getDay();
        if ($start_day > 25 && $start_month < 12) {
            $start_month++;
        } else if ($end_day > 25 && $end_month < 12) {
            $end_month++;
        }

        // $Timesheet = new Timesheets();
        // $builder = $Timesheet->builder();
        // $coal_getting_production = $builder->select("SUM(prd_ob_total) AS actual_ob, 
        //     SUM(mm.ob_monthlybudget_qt) AS budget_ob,
        //     SUM(prd_cg_total) AS actual_cg, 
        //     SUM(mm.cg_monthlybudget_qt) AS budget_cg")
        //     ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
        //     ->where("prd_date BETWEEN '$start_date' AND '$end_date'")
        //     ->where("timesheets.id_contractor = $id")
        //     ->where("timesheets.status = 'approved'")
        //     ->where("deleted_at IS NULL")
        //     ->groupBy("mm.month")
        //     ->get()->getResultArray();
        // $res = array("actual_cg" => 0, "budget_cg" => 0, "actual_ob" => 0, "budget_ob" => 0);
        // foreach ($coal_getting_production as $cg) {
        //     $res['actual_cg'] += $cg['actual_cg'];
        //     $res['budget_cg'] += $cg['budget_cg'];
        //     $res['actual_ob'] += $cg['actual_ob'];
        //     $res['budget_ob'] += $cg['budget_ob'];
        // }

        $res = $db->query("SELECT mc.contractor_name AS contractor, mc.contractor_name,
                SUBSTRING_INDEX(mc.contractor_name, ' ', -1) AS project1,
                SUM(prd_ob_total) AS actual_ob,
                (SELECT SUM(Net_Weigh)/1000 FROM temp_timbangan tt WHERE tt.Posting_Date BETWEEN '$start_date' AND '$end_date' AND contractor = project1) AS actual_cg,
                (
                SELECT
                    SUM(cg_monthlybudget_qt)
                FROM
                    md_monthlybudget mms
                WHERE
                    (mms.month >= $start_month AND mms.month <= $end_month)
                AND 
                    (mms.year >= YEAR('$start_date') AND mms.year <= YEAR('$end_date'))
                AND project = project1
                ) AS budget_cg,
                (
                SELECT
                    SUM(ob_monthlybudget_qt)
                FROM
                    md_monthlybudget mms
                WHERE
                    (mms.month >= $start_month AND mms.month <= $end_month)
                AND 
                    (mms.year >= YEAR('$start_date') AND mms.year <= YEAR('$end_date'))
                AND project = project1) AS budget_ob
            FROM
                timesheets t
            INNER JOIN md_contractors mc ON mc.id = t.id_contractor
            WHERE
                prd_date BETWEEN '$start_date' AND '$end_date' AND t.id_contractor = $id AND t.status = 'approved'")->getRowArray();

        return $this->response->setJSON([
            'actual_cg' => $res['actual_cg'],
            'budget_cg' => $res['budget_cg'],
            'actual_ob' => $res['actual_ob'],
            'budget_ob' => $res['budget_ob']
        ]);
    }
}
