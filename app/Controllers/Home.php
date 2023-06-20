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
            $date_target = "$year-$month-31";
            $year2 = $year + 1;
            $date_target2 = "$year2-1-1";
        }
        $begin = new DateTime($date_before);
        $end = new DateTime($date_target2); // pakai date_target2 dikarenakan fungsi interval exclude target

        $interval = DateInterval::createFromDateString("1 day");
        $period = new DatePeriod($begin, $interval, $end);

        foreach ($period as $dt) {
            $data['date_period'][] = $dt->format("Y-m-d");
        }

        // $data['total_shipment'] = ['target' => 0, 'total' => 0];
        //khusus sales and cost contractor
        if ($type == 'yearly') {
            // list data shipment
            $ship_type = $_GET['ship_type'] ?? 'qty';
            if ($ship_type == 'qty') {
                $selected_column = "discharging_qty";
            } elseif ($ship_type == 'price') {
                $selected_column = "final_price";
            } else {
                $selected_column = "amount";
            }
            $SalesShipment = new SalesShipment();
            $builder_shipment = $SalesShipment->builder();
            if ($ship_type == 'qty') {
                $shipment_list = $builder_shipment->select("SUM($selected_column) AS total, MONTH(discharging_date) AS bulan, category, type")
                    ->where("YEAR(discharging_date) = $year")
                    ->groupBy("bulan")
                    ->groupBy("category")
                    ->groupBy("type")
                    ->orderBy("bulan")
                    ->get()->getResultArray();
                // dd($shipment_list);
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
            $data['shipment_list'] = $grouped_sl;

            $SalesShipment = new SalesShipment();
            $builder_shipment = $SalesShipment->builder();
            $total_shipment = $builder_shipment->select("SUM(bl_qty) AS total, 
        (SELECT SUM(quantity) FROM T_SAL_TARGET) AS target")
                ->get()->getRowArray();
            $data['total_shipment'] = $total_shipment;

            $SalePrice = new TSalPrice();
            $builder_sal = $SalePrice->builder();

            $average_price = $builder_sal->select("AVG(final_price) AS final_price")
                ->where("curr = 'IDR'")
                ->get()->getRowArray();
            $data['average_price'] = $average_price;

            $total_sales = $builder_sal->select("SUM(amount) AS total")
                ->where("curr = 'IDR'")
                ->get()->getRowArray();
            $data['total_sales'] = $total_sales;

            // Inventory stock (MT)
            $unrestricted_stock = $db->query("SELECT isc.stock AS 'Crusher Stock', 
             isp.stock AS 'Port Stock', isr.stock AS 'ROM Stock'
             FROM im_stock_cc isc INNER JOIN im_stock_port isp ON isp.posting_date = isc.posting_date 
             INNER JOIN im_stock_raw isr ON isr.posting_date = isc.posting_date 
             WHERE YEAR(isc.posting_date) = $year
             ORDER BY isc.posting_date DESC LIMIT 1")->getRowArray();
            $data['unrestricted_stock'] = $unrestricted_stock ?? [];
        } else {
            $SalesShipment = new SalesShipment();
            $builder_shipment = $SalesShipment->builder();
            $total_shipment = $builder_shipment->select("SUM(bl_qty) AS total, 
            (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE year = $year) AS target")
                ->where("YEAR(bl_date) = $year")
                ->get()->getRowArray();
            $data['total_shipment'] = $total_shipment;

            $SalePrice = new TSalPrice();
            $builder_sal = $SalePrice->builder();

            $average_price = $builder_sal->select("AVG(final_price) AS final_price")
                ->where("curr = 'IDR'")
                ->where("YEAR(date_final) = $year")
                ->get()->getRowArray();
            $data['average_price'] = $average_price;

            $total_sales = $builder_sal->select("SUM(amount) AS total")
                ->where("curr = 'IDR'")
                ->where("YEAR(date_final) = $year")
                ->get()->getRowArray();
            $data['total_sales'] = $total_sales;

            // Inventory stock (MT)
            $unrestricted_stock = $db->query("SELECT isc.stock AS 'Crusher Stock', 
             isp.stock AS 'Port Stock', isr.stock AS 'ROM Stock'
             FROM im_stock_cc isc INNER JOIN im_stock_port isp ON isp.posting_date = isc.posting_date 
             INNER JOIN im_stock_raw isr ON isr.posting_date = isc.posting_date 
             WHERE YEAR(isc.posting_date) = $year
             ORDER BY isc.posting_date DESC LIMIT 1")->getRowArray();
            $data['unrestricted_stock'] = $unrestricted_stock ?? [];
        }
        //Cosh Contractor 
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
        $cost_types = $db->query("SELECT id_costtype, cost_type FROM md_costtype")->getResultArray();
        $data['cost_types'] = $cost_types;
        $cost_mining_contractors = $db->query("SELECT DISTINCT id_contractor, contractor_name AS contractor 
        FROM t_costmining tc INNER JOIN md_contractors mc ON mc.id = tc.id_contractor")->getResultArray();
        $data['cost_mining_contractors'] = $cost_mining_contractors;

        if ($type == 'yearly') { //filter by yearly
            //SUM overbuurden
            $data['ob_production'] = $db->query("SELECT (SELECT SUM(A.qty) FROM T_Adjustment A WHERE A.transaksi = 'Overburden') AS actual, 
            COALESCE((SELECT SUM(Mybudget) FROM (
                SELECT budget.year, 
                    IF(YEAR(NOW()) = budget.year,
                        SUM(CASE
                            WHEN budget.month < MONTH(NOW()) THEN budget.ob_monthlybudget_qt
                            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.ob_dailybudget_qt * DAY(NOW())
                            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                            ELSE 0 
                            END),
                        SUM(budget.ob_monthlybudget_qt)) AS Mybudget
                FROM md_monthlybudget budget
                GROUP BY budget.year) AS budget_total), 0) AS budget;")->getRowArray();

            // SUM COAL GETTING
            $data['cg_production'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'Coal Getting') AS actual, 
                COALESCE((SELECT SUM(Mybudget) FROM (
                SELECT budget.year, 
                    IF(YEAR(NOW()) = budget.year,
                        SUM(CASE
                            WHEN budget.month < MONTH(NOW()) THEN budget.cg_monthlybudget_qt
                            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cg_dailybudget_qt * DAY(NOW())
                            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                            ELSE 0 
                            END),
                        SUM(budget.cg_monthlybudget_qt)) AS Mybudget
                FROM md_monthlybudget budget
                GROUP BY budget.year) AS budget_total), 0) AS budget;")->getRowArray();

            // OB & CG Distance 
            //Overburden Distance - Actual
            $data['sum_actual_ob_distance'] = $db->query("WITH t_abc AS (SELECT `month`, `year`,
            SUM(CASE WHEN transaksi = 'Overburden' THEN qty ELSE 0 END) AS OB,
            SUM(CASE WHEN transaksi = 'Distance OB' THEN qty ELSE 0 END) AS DO
            FROM T_Adjustment ta
            -- WHERE year = $year AND month <= $month
            GROUP BY month, year, id_contractor)
            SELECT SUM(OB * DO)/SUM(OB) AS total FROM t_abc ORDER by year")->getRowArray();
            //Overburden Distance - Target
            $data['sum_plan_ob_distance'] = $db->query("WITH sum_distance AS
                (SELECT B.dibagi/B.pembagi AS DISTANCE,
                B.bulan, B.pembagi AS ob
                FROM (SELECT A.bulan,
                    SUM(A.ob * A.distance_ob) as dibagi,
                    SUM(A.ob) AS pembagi
                FROM (SELECT mmd.project ,mmd.`year` AS tahun, mmd.`month` AS bulan,
                    SUM(disob_monthlybudget_qty) AS distance_ob,
                    IF(YEAR(NOW()) = mm.year,
                            SUM(CASE
                                WHEN mm.month < MONTH(NOW()) THEN mm.ob_monthlybudget_qt
                                WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.ob_dailybudget_qt * DAY(NOW())
                                WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                ELSE 0 
                                END),
                            SUM(mm.ob_monthlybudget_qt)) AS ob
                    FROM md_monthly_disob mmd
                    INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                    -- WHERE mmd.`year` = 2023
                    -- AND mmd.`month` <= 5
                    GROUP BY tahun, bulan, project) A
                    GROUP BY A.bulan) B)
                SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance")->getRowArray();

            //Coal Getting Distance - Actual
            $data['sum_actual_cg_distance'] = $db->query("WITH t_abc AS (SELECT `month`, `year`,
            SUM(CASE WHEN transaksi = 'Coal Getting' THEN qty/1000 ELSE 0 END) AS CG,
            SUM(CASE WHEN transaksi = 'Distance CG' THEN qty ELSE 0 END) AS DC
            FROM T_Adjustment ta
        -- WHERE year = $year AND month <= $month
            GROUP BY month, year, id_contractor)
            SELECT SUM(CG * DC)/SUM(CG) AS total FROM t_abc ORDER by year")->getRowArray();
            //Coal Getting Distance - Target
            $data['sum_plan_cg_distance'] = $db->query("WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS cg FROM (SELECT SUM(A.cg * A.distance_cg) as dibagi, A.bulan, SUM(A.cg) AS pembagi
            FROM (SELECT mmd.project ,mmd.`year` AS tahun, mmd.`month` AS bulan,
                        SUM(discg_monthlybudget_qty) AS distance_cg,
                        IF(YEAR(NOW()) = mm.year,
                                SUM(CASE
                                    WHEN mm.month < MONTH(NOW()) THEN mm.cg_monthlybudget_qt
                                    WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.cg_dailybudget_qt * DAY(NOW())
                                    WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                    ELSE 0 
                                    END),
                                SUM(mm.cg_monthlybudget_qt)) AS cg
                        FROM md_monthly_discg mmd
                        INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                        GROUP BY tahun, bulan, project) A
            GROUP BY A.bulan) B)
            SELECT SUM(distance * cg) / SUM(cg) AS total FROM sum_distance")->getRowArray();

            // SUM CRUSH COAL
            $data['crush_coal_ytd'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal') AS total, 
            COALESCE((SELECT SUM(Mybudget) FROM (
                  SELECT budget.year, 
                      IF(YEAR(NOW()) = budget.year,
                          SUM(CASE
                              WHEN budget.month < MONTH(NOW()) THEN budget.cc_mounthlybudget_qty
                              WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cc_dailybudget_qty * DAY(NOW())
                              WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cc_dailybudget_qty * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                              ELSE 0 
                              END),
                          SUM(budget.cc_mounthlybudget_qty)) AS Mybudget
                  FROM md_monthlybudget_cc budget
                  WHERE budget.status = '1'
                  GROUP BY budget.year) AS budget_total), 0) AS budget")->getRowArray();

            //Sum Of Hauling to Port
            $data['inquiry_transfer'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'Hauling to Port') AS total, 
            COALESCE((SELECT SUM(Mybudget) FROM (
                 SELECT budget.year, 
                     IF(YEAR(NOW()) = budget.year,
                         SUM(CASE
                             WHEN budget.month < MONTH(NOW()) THEN budget.hp_mounthlybudget_qty
                             WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.hp_dailybudget_qty * DAY(NOW())
                             WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.hp_dailybudget_qty * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                             ELSE 0 
                             END),
                         SUM(budget.hp_mounthlybudget_qty)) AS Mybudget
                 FROM md_monthlybudget_hp budget
                 WHERE budget.status = '1'
                 GROUP BY budget.year) AS budget_total), 0) AS budget")->getRowArray();

            //Sum Of Stripping Ratio 
            $data['stripping_ytd'] = $db->query("SELECT (SELECT SUM(A1.qty) FROM T_Adjustment A1 WHERE A1.transaksi = 'Overburden') AS actual_ob, 
            COALESCE((SELECT SUM(Mybudget) FROM (
                   SELECT budget.year, 
                       IF(YEAR(NOW()) = budget.year,
                           SUM(CASE
                               WHEN budget.month < MONTH(NOW()) THEN budget.ob_monthlybudget_qt
                               WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.ob_dailybudget_qt * DAY(NOW())
                               WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                               ELSE 0 
                               END),
                           SUM(budget.ob_monthlybudget_qt)) AS Mybudget
                   FROM md_monthlybudget budget
                   GROUP BY budget.year) AS budget_total), 0) AS budget_ob,
               (SELECT SUM(A2.qty)/1000 FROM T_Adjustment A2 WHERE A2.transaksi = 'Coal Getting') AS actual_cg,
              COALESCE((SELECT SUM(Mybudget) FROM (
                   SELECT budget.year, 
                       IF(YEAR(NOW()) = budget.year,
                           SUM(CASE
                               WHEN budget.month < MONTH(NOW()) THEN budget.cg_monthlybudget_qt
                               WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cg_dailybudget_qt * DAY(NOW())
                               WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                               ELSE 0 
                               END),
                           SUM(budget.cg_monthlybudget_qt)) AS Mybudget
                   FROM md_monthlybudget budget
                   GROUP BY budget.year) AS budget_total), 0) AS budget_cg")->getRowArray();

            // Sum Of Barging | Yearly
            $SalesShipment = new SalesShipment();
            // $builder_shipment = $SalesShipment->builder();
            // $data['barging_ytd']  = $builder_shipment->select("COALESCE(SUM(bl_qty), 0) AS total, 
            // (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE year = $year AND month <= $month_sum) AS target")
            //     ->where("MONTH(bl_date) <= $month_sum")
            //     ->where("YEAR(bl_date) = $year")
            //     ->get()->getRowArray();

            //New Query Sum Of Barging | Yearly
            $data['barging_ytd'] = $db->query("SELECT COALESCE(SUM(a.bl_qty), 0) AS total, a.target, a.tahun FROM  
            (SELECT bl_qty, (SELECT SUM(quantity) FROM T_SAL_TARGET) AS target,
               (CASE WHEN DAY(bl_date) > 25 AND MONTH(bl_date) < 12 THEN MONTH(bl_date) + 1 
                WHEN MONTH(bl_date) = 12 THEN MONTH(bl_date) ELSE MONTH(bl_date) END) AS bulan,
               YEAR(bl_date) AS tahun
            FROM T_SAL_SHIPMENT
				WHERE YEAR(bl_date)) a
            GROUP BY a.tahun")->getRowArray();

            //Sum Overburden | Yearly Graphic
            $data['ob_lines'] = $db->query("SELECT actual, budget, tahun1 AS tahun, tahun1 AS month
            FROM (
                SELECT SUM(A.qty) AS actual, A.year AS tahun1
                FROM T_Adjustment A
                WHERE A.transaksi = 'Overburden'
                GROUP BY tahun1
            ) subquery1
            JOIN (
               SELECT B.year AS tahun2, 
  				  IF(YEAR(NOW()) = B.year,
		        SUM(CASE
		         	WHEN B.month < MONTH(NOW()) THEN B.ob_monthlybudget_qt
		            WHEN B.month = MONTH(NOW()) AND B.month = 1 THEN B.ob_dailybudget_qt * DAY(NOW())
		            WHEN B.month = MONTH(NOW()) AND B.month != 1 THEN B.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
		            ELSE 0 
		            END),
		        SUM(B.ob_monthlybudget_qt)) AS budget
					FROM md_monthlybudget B
					GROUP BY B.year
            ) subquery2
            ON subquery1.tahun1 = subquery2.tahun2
            ORDER BY tahun;")->getResultArray();
            //Sum Coal Getting (MT) | Yearly Graphic
            $data['cg_lines'] = $db->query("SELECT actual, budget, tahun1 AS tahun, tahun1 AS month
            FROM (
                SELECT SUM(A.qty)/1000 AS actual, A.year AS tahun1
                FROM T_Adjustment A
                WHERE A.transaksi = 'Coal Getting'
                GROUP BY tahun1
            ) subquery1
            JOIN (
                 SELECT B.year AS tahun2, 
  				  IF(YEAR(NOW()) = B.year,
		        SUM(CASE
		         	WHEN B.month < MONTH(NOW()) THEN B.cg_monthlybudget_qt
		            WHEN B.month = MONTH(NOW()) AND B.month = 1 THEN B.cg_dailybudget_qt * DAY(NOW())
		            WHEN B.month = MONTH(NOW()) AND B.month != 1 THEN B.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
		            ELSE 0 
		            END),
		        SUM(B.cg_monthlybudget_qt)) AS budget
					FROM md_monthlybudget B
					GROUP BY B.year
            ) subquery2
            ON subquery1.tahun1 = subquery2.tahun2
            ORDER BY tahun;")->getResultArray();


            // Contractor Performance Overburden & Contractor Performance Stripping Ratio  1
            $contractor_ob = $db->query("SELECT qmaster.*,
            (SELECT tctr.contractor_name from md_contractors tctr WHERE tctr.id = qmaster.id_contractor)AS contractor,
            (SELECT SUM(actual.qty)
                            FROM T_Adjustment actual
                            WHERE actual.transaksi = 'Overburden'
                            and actual.year = qmaster.bulan
                                 AND actual.id_contractor = qmaster.id_contractor) AS actual_ob,
            (SELECT IF(YEAR(NOW()) = qmaster.bulan,
									        SUM(CASE
									         	WHEN budget.month < MONTH(NOW()) THEN budget.ob_monthlybudget_qt
									            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.ob_dailybudget_qt * DAY(NOW())
									            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
									            ELSE 0 
									            END),
									        SUM(budget.ob_monthlybudget_qt)) AS budgetku
									FROM md_monthlybudget budget
                           WHERE budget.year = qmaster.bulan
                           AND budget.id_contractor = qmaster.id_contractor) AS budget_ob
            FROM(
            SELECT A.year AS bulan, A.id_contractor FROM T_Adjustment A WHERE A.transaksi = 'Overburden' AND A.id_contractor IS NOT NULL GROUP BY A.year, A.id_contractor
            UNION
            SELECT B.year AS bulan, B.id_contractor FROM md_monthlybudget B WHERE B.id_contractor IS NOT NULL GROUP BY B.year, B.id_contractor
            ) AS qmaster
            ORDER BY contractor, bulan")->getResultArray();
            $grouped_contractors = array();
            foreach ($contractor_ob as $c) {
                $grouped_contractors[$c['contractor']][$c['bulan']] = $c;
            }
            $data['contractor_ob'] = $grouped_contractors;

            // Contractor Performance Coal Getting  & Contractor Performance Stripping Ratio  2
            $contractor_cg = $db->query("SELECT qmaster.*,
            (SELECT tctr.contractor_name from md_contractors tctr WHERE tctr.id = qmaster.id_contractor)AS contractor,
            (SELECT SUM(actual.qty)/1000
                            FROM T_Adjustment actual
                            WHERE actual.transaksi = 'Coal Getting'
                            and actual.year = qmaster.bulan
                                 AND actual.id_contractor = qmaster.id_contractor) AS actual_cg,
            (SELECT IF(YEAR(NOW()) = qmaster.bulan,
						        SUM(CASE
						         	WHEN budget.month < MONTH(NOW()) THEN budget.cg_monthlybudget_qt
						            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cg_dailybudget_qt * DAY(NOW())
						            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
						            ELSE 0 
						            END),
						         SUM(budget.cg_monthlybudget_qt)) AS budgetku
									FROM md_monthlybudget budget
                           WHERE  budget.year = qmaster.bulan
                           AND budget.id_contractor = qmaster.id_contractor) AS budget_cg
            FROM(
            SELECT A.year AS bulan, A.id_contractor FROM T_Adjustment A WHERE A.transaksi = 'Coal Getting' AND A.id_contractor IS NOT NULL GROUP BY A.year, A.id_contractor
            UNION
            SELECT B.year AS bulan, B.id_contractor FROM md_monthlybudget B WHERE B.id_contractor IS NOT NULL GROUP BY B.year, B.id_contractor
            ) AS qmaster
            ORDER BY contractor, bulan;")->getResultArray();
            $grouped_contractorsC = array();
            foreach ($contractor_cg as $c) {
                $grouped_contractorsC[$c['contractor']][$c['bulan']] = $c;
            }
            $data['contractor_cg'] = $grouped_contractorsC;

            //Stripping Ratio Graphic
            $stripping_ratio = $db->query("SELECT qmaster.*,
            (SELECT SUM(actual.qty)/1000
                            FROM T_Adjustment actual
                            WHERE actual.transaksi = 'Coal Getting'
                            and actual.year = qmaster.month) AS actual_cg,
            (SELECT SUM(actual.qty)
                            FROM T_Adjustment actual
                            WHERE actual.transaksi = 'Overburden'
                            and actual.year = qmaster.month) AS actual_ob,
           (SELECT IF(YEAR(NOW()) = qmaster.month,
						        SUM(CASE
						         	WHEN budget.month < MONTH(NOW()) THEN budget.cg_monthlybudget_qt
						            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cg_dailybudget_qt * DAY(NOW())
						            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
						            ELSE 0 
						            END),
						         SUM(budget.cg_monthlybudget_qt)) AS budgetku
									FROM md_monthlybudget budget
                           WHERE  budget.year = qmaster.month) AS budget_cg,
      		(SELECT IF(YEAR(NOW()) = qmaster.month,
									        SUM(CASE
									         	WHEN budget.month < MONTH(NOW()) THEN budget.ob_monthlybudget_qt
									            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.ob_dailybudget_qt * DAY(NOW())
									            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
									            ELSE 0 
									            END),
									        SUM(budget.ob_monthlybudget_qt)) AS budgetku
									FROM md_monthlybudget budget
                           WHERE budget.year = qmaster.month) AS budget_ob
            FROM(
            SELECT A.year AS  month FROM T_Adjustment A WHERE A.transaksi = 'Coal Getting'  GROUP BY A.year
            UNION 
             SELECT A.year AS month  FROM T_Adjustment A WHERE A.transaksi = 'Overburden' GROUP BY A.year
            UNION 
            SELECT B.year AS month FROM md_monthlybudget B GROUP BY B.year) AS qmaster;")->getResultArray();
            $data['stripping_ratio'] = $stripping_ratio;

            // Sum Overburden Distance & Coal Getting Distance  - Graphic
            $data['actual_distance'] = $db->query("WITH t_abc AS (
                SELECT
                  YEAR AS bulan, 
                  id_contractor AS contractor, 
                  SUM(CASE WHEN transaksi = 'Overburden' THEN qty ELSE 0 END) AS OB,
                  SUM(CASE WHEN transaksi = 'Distance OB' THEN qty ELSE 0 END) AS DO,
                  SUM(CASE WHEN transaksi = 'Coal Getting' THEN qty/1000 ELSE 0 END) AS CG,
                  SUM(CASE WHEN transaksi = 'Distance CG' THEN qty ELSE 0 END) AS DC
                FROM T_Adjustment ta
                GROUP BY month, year, id_contractor
              ), 
              sum_distance_ob AS (
                SELECT B.dibagi/B.pembagi AS DISTANCE_myOB,
                       B.tahun,
                       B.pembagi AS ob
                FROM (
                  SELECT A.tahun,
                         A.bulan,
                         SUM(A.ob * A.distance_ob) as dibagi,
                         SUM(A.ob) AS pembagi
                  FROM (
                    SELECT mmd.project ,
                           mmd.`year` AS tahun, 
                           mmd.`month` AS bulan,
                           SUM(disob_monthlybudget_qty) AS distance_ob,
                           IF(YEAR(NOW()) = mm.year,
                              SUM(CASE
                                WHEN mm.month < MONTH(NOW()) THEN mm.ob_monthlybudget_qt
                                WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.ob_dailybudget_qt * DAY(NOW())
                                WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                ELSE 0 
                              END),
                              SUM(mm.ob_monthlybudget_qt)
                             ) AS ob
                    FROM md_monthly_disob mmd
                    INNER JOIN md_monthlybudget mm 
                    ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                    GROUP BY tahun, bulan, project
                  ) A
                  GROUP BY A.tahun, A.bulan
                ) B
              ),
              sum_distance_cg AS (
                SELECT B.dibagi/B.pembagi AS DISTANCE_myCG,
                       B.tahun,
                       B.pembagi AS cg
                FROM (
                  SELECT A.tahun,
                         A.bulan,
                         SUM(A.cg * A.distance_cg) as dibagi,
                         SUM(A.cg) AS pembagi
                  FROM (
                    SELECT mmd.project ,
                           mmd.`year` AS tahun, 
                           mmd.`month` AS bulan,
                           SUM(discg_monthlybudget_qty) AS distance_cg,
                           IF(YEAR(NOW()) = mm.year,
                              SUM(CASE
                                WHEN mm.month < MONTH(NOW()) THEN mm.cg_monthlybudget_qt
                                WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.cg_dailybudget_qt * DAY(NOW())
                                WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                ELSE 0 
                              END),
                              SUM(mm.cg_monthlybudget_qt)
                             ) AS cg
                    FROM md_monthly_discg mmd
                    INNER JOIN md_monthlybudget mm 
                    ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                    GROUP BY tahun, bulan, project
                  ) A
                  GROUP BY A.tahun, A.bulan
                ) B
              )
              SELECT t_abc.bulan,
                     SUM(OB * DO) / SUM(OB) AS distance_ob,
                     SUM(CG * DC) / SUM(CG) AS distance_cg,
                     (SELECT SUM(DISTANCE_myOB * ob) / SUM(ob) 
                      FROM sum_distance_ob 
                      WHERE tahun = t_abc.bulan) AS target_ob,
                     (SELECT SUM(DISTANCE_myCG * cg) / SUM(cg) 
                      FROM sum_distance_cg
                      WHERE tahun = t_abc.bulan) AS target_cg
                      
              FROM t_abc
              GROUP BY bulan 
              ORDER BY bulan;")->getResultArray();

            // Sum Overburden Distance & Coal Getting Distance  (Per Contractor) - Chart 
            $contractor_distance = $db->query("WITH t_abc AS (
                    SELECT
                      YEAR AS bulan, 
                      id_contractor, 
                      SUM(CASE WHEN transaksi = 'Overburden' THEN qty ELSE 0 END) AS OB,
                      SUM(CASE WHEN transaksi = 'Distance OB' THEN qty ELSE 0 END) AS DO,
                      SUM(CASE WHEN transaksi = 'Coal Getting' THEN qty/1000 ELSE 0 END) AS CG,
                      SUM(CASE WHEN transaksi = 'Distance CG' THEN qty ELSE 0 END) AS DC
                    FROM T_Adjustment ta
                    WHERE ta.transaksi = 'Overburden' OR ta.transaksi = 'Distance OB' OR ta.transaksi = 'Coal Getting' OR ta.transaksi = 'Distance CG' AND ta.id_contractor IS NOT NULL   
                    GROUP BY month, year, id_contractor
                  ), 
                  sum_distance_ob AS (
                    SELECT B.dibagi/B.pembagi AS DISTANCE_myOB,
                           B.tahun,
                                  B.id_contractor,
                           B.pembagi AS ob
                    FROM (
                      SELECT A.tahun,
                             A.bulan,
                             A.id_contractor,
                             SUM(A.ob * A.distance_ob) as dibagi,
                             SUM(A.ob) AS pembagi
                      FROM (
                        SELECT mmd.project ,
                               mmd.`year` AS tahun, 
                               mmd.`month` AS bulan,
                               mmd.id_contractor,
                               SUM(disob_monthlybudget_qty) AS distance_ob,
                               IF(YEAR(NOW()) = mm.year,
                                  SUM(CASE
                                    WHEN mm.month < MONTH(NOW()) THEN mm.ob_monthlybudget_qt
                                    WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.ob_dailybudget_qt * DAY(NOW())
                                    WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                    ELSE 0 
                                  END),
                                  SUM(mm.ob_monthlybudget_qt)
                                 ) AS ob
                        FROM md_monthly_disob mmd
                        INNER JOIN md_monthlybudget mm 
                        ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month`  AND mm.id_contractor = mmd.id_contractor 
                        GROUP BY tahun, bulan, project, id_contractor
                      ) A
                      GROUP BY A.tahun, A.bulan,  A.id_contractor
                    ) B
                  ),
                  sum_distance_cg AS (
                    SELECT B.dibagi/B.pembagi AS DISTANCE_myCG,
                           B.tahun,
                           B.id_contractor,
                           B.pembagi AS cg
                    FROM (
                      SELECT A.tahun,
                             A.bulan,
                             A.id_contractor,
                             SUM(A.cg * A.distance_cg) as dibagi,
                             SUM(A.cg) AS pembagi
                      FROM (
                        SELECT mmd.project ,
                               mmd.`year` AS tahun, 
                               mmd.`month` AS bulan,
                               mmd.id_contractor,
                               SUM(discg_monthlybudget_qty) AS distance_cg,
                               IF(YEAR(NOW()) = mm.year,
                                  SUM(CASE
                                    WHEN mm.month < MONTH(NOW()) THEN mm.cg_monthlybudget_qt
                                    WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.cg_dailybudget_qt * DAY(NOW())
                                    WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                    ELSE 0 
                                  END),
                                  SUM(mm.cg_monthlybudget_qt)
                                 ) AS cg
                        FROM md_monthly_discg mmd
                        INNER JOIN md_monthlybudget mm 
                        ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month`  AND mm.id_contractor = mmd.id_contractor 
                        GROUP BY tahun, bulan, project, id_contractor
                      ) A
                      GROUP BY A.tahun, A.bulan, A.id_contractor
                    ) B
                  )
                  SELECT t_abc.bulan, t_abc.id_contractor, 
                 (SELECT tctr.contractor_name from md_contractors tctr WHERE tctr.id = t_abc.id_contractor) AS contractor,
                         SUM(OB * DO) / SUM(OB) AS distance_ob,
                         SUM(CG * DC) / SUM(CG) AS distance_cg,
                         (SELECT SUM(DISTANCE_myOB * ob) / SUM(ob) 
                          FROM sum_distance_ob 
                          WHERE tahun = t_abc.bulan  AND id_contractor = t_abc.id_contractor) AS target_ob,
                         (SELECT SUM(DISTANCE_myCG * cg) / SUM(cg) 
                          FROM sum_distance_cg
                          WHERE tahun = t_abc.bulan AND id_contractor = t_abc.id_contractor) AS target_cg
                          
                  FROM t_abc
                  GROUP BY bulan, id_contractor
                  ORDER BY bulan;")->getResultArray();
            $grouped_contracDistance = array();
            foreach ($contractor_distance as $c) {
                $grouped_contracDistance[$c['contractor']][$c['bulan']] = $c;
            }
            $data['contractor_distance'] = $grouped_contracDistance;

            // // SUM CRUSH COAL
            // $data['crush_coal_ytd'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' AND year = $year AND month <= $month) AS total, 
            // COALESCE((SELECT SUM(B.cc_mounthlybudget_qty) FROM md_monthlybudget_cc B WHERE B.status = '1' AND year = $year AND month <= $month), 0) AS budget")->getRowArray();

            // //Sum Of Hauling to Port
            // $data['inquiry_transfer'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' AND year = $year AND month <= $month_sum) AS total, 
            // COALESCE((SELECT SUM(B.cc_mounthlybudget_qty) FROM md_monthlybudget_cc B WHERE B.status = '1'), 0) AS budget")->getRowArray();

            $sum_cc = $db->query("SELECT qmaster.*,
            'OFN' AS crusher,
            (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' AND YEAR = qmaster.bulan) AS total
            FROM(
             SELECT A.year AS bulan FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' GROUP BY A.year
             UNION 
             SELECT B.year AS bulan FROM md_monthlybudget_cc B GROUP BY B.year) AS qmaster")->getResultArray();
            $grouped_cc = array();
            foreach ($sum_cc as $c) {
                $grouped_cc[$c['crusher']][] = $c;
            }
            $data['sum_cc'] = $grouped_cc;

            $plan_cc = $db->query("SELECT qmaster.*,
             COALESCE((SELECT SUM(B.cc_mounthlybudget_qty) FROM md_monthlybudget_cc B WHERE B.status = '1' AND year = qmaster.bulan), 0) AS budget
             FROM(
             SELECT A.year AS bulan FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' GROUP BY A.year
             UNION 
             SELECT B.year AS bulan FROM md_monthlybudget_cc B GROUP BY B.year) AS qmaster")->getResultArray();
            $data['plan_cc'] = $plan_cc;


            //hauling graphic - total
            // $sum_hauling_to_tonase = $db->query("SELECT qmaster.*,
            // (SELECT tctr.contractor_name from md_contractors tctr WHERE tctr.id = qmaster.id_contractor)AS contractor,
            // (SELECT COALESCE(max(t.Transporter_Description), 'undefined') FROM T_Adjustment t WHERE t.id_contractor = qmaster.id_contractor) AS transporter,
            // (SELECT COALESCE(SUM(actual.qty), 0)
            //                 FROM T_Adjustment actual
            //                 WHERE actual.transaksi = 'Hauling to Port'
            //                 And actual.year = qmaster.bulan
            //                          AND actual.id_contractor = qmaster.id_contractor GROUP BY actual.id_contractor) AS total,
            // (SELECT COALESCE(SUM(hp_mounthlybudget_qty), 0) FROM md_monthlybudget_hp 
            // WHERE year = bulan) AS budget
            // FROM(
            // SELECT A.year AS bulan, A.id_contractor FROM T_Adjustment A WHERE A.transaksi = 'Hauling to Port' AND A.id_contractor IS NOT NULL GROUP BY A.year, A.id_contractor
            // UNION
            // SELECT B.year AS bulan, B.id_contractor FROM md_monthlybudget_hp  B WHERE B.id_contractor IS NOT NULL GROUP BY B.year, B.id_contractor
            // ) AS qmaster
            // ORDER BY bulan ,transporter;")->getResultArray();
            $sum_hauling_to_tonase = $db->query("SELECT SUM(qty)/1000 AS total, COALESCE(transporter_Description, 'undefined') AS transporter, 
            year AS bulan,
            (SELECT COALESCE(SUM(hp_mounthlybudget_qty), 0) FROM md_monthlybudget_hp 
            WHERE year = bulan) AS budget
            FROM T_Adjustment
            WHERE transaksi = 'Hauling to Port'
            GROUP BY transporter, bulan;")->getResultArray();
            $grouped_tonase = array();
            foreach ($sum_hauling_to_tonase as $c) {
                $grouped_tonase[$c['transporter']][$c['bulan']] = $c;
            }
            $tonase_years = $db->query("SELECT DISTINCT year FROM T_Adjustment WHERE transaksi = 'Hauling to Port'")->getResultArray();
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
            //hauling graphic - budget
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


            // // Local vs Export (%) Chart - by ferry
            // $export_vs_local = $db->query("SELECT COUNT(1) AS jumlah, type, YEAR(bl_date) AS tahun FROM T_SAL_SHIPMENT tss
            //      WHERE `type` IN ('Local', 'Export')
            //      GROUP BY tahun, type
            //      ORDER BY tahun, type")->getResultArray();
            // $data['export_vs_local'] = $export_vs_local;

            // Local vs Export (%) Chart
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




            // Coal Index Time Series (IDR) - Chart
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

            // Domestic Market Obligation(DMO) - Chart
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




            // Barging (MT) - Chart
            $barging = $builder_shipment->select("SUM(bl_qty) AS total, MONTH(bl_date) AS bulan,
        (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE `year` = $year AND `month` = bulan) AS target")
                ->where("YEAR(bl_date) = $year")
                ->groupBy("bulan")
                ->orderBy("bulan")
                ->get()->getResultArray();
            $data['barging'] = $barging;


            $data['harga_jual'] = [];

            $data['coal_ic'] = [];
            $data['coal_newc'] = [];



            $prd_sales_prod = $db->query("SELECT SUM(qty/1000) AS total,
            MONTH AS bulan
            FROM T_Adjustment WHERE year = $year AND transaksi = 'Coal Getting'
            GROUP BY bulan
            ORDER BY bulan")->getResultArray();
            $prd_sales_sales = $db->query("SELECT SUM(bl_qty) AS total,
            MONTH(bl_date) AS bulan
            FROM T_SAL_SHIPMENT WHERE YEAR(bl_date) = $year
            GROUP BY bulan
            ORDER BY bulan")->getResultArray();
            $prd_hauling = $db->query("SELECT SUM(qty/1000) AS total, month AS bulan
            FROM T_Adjustment WHERE year = $year AND transaksi = 'Hauling to Port'
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
        } elseif ($type == 'monthly') { //filter by monthly
            //SUM overbuurden | Monthly
            $data['ob_production'] = $db->query("SELECT (SELECT SUM(A.qty) FROM T_Adjustment A WHERE A.transaksi = 'Overburden' AND A.year = $year AND A.month <= $month) AS actual, 
            COALESCE((SELECT SUM(Mybudget) FROM (
                SELECT budget.year, 
                    IF(YEAR(NOW()) = budget.year,
                        SUM(CASE
                            WHEN budget.month < MONTH(NOW()) THEN budget.ob_monthlybudget_qt
                            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.ob_dailybudget_qt * DAY(NOW())
                            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                            ELSE 0 
                            END),
                        SUM(budget.ob_monthlybudget_qt)) AS Mybudget
                FROM md_monthlybudget budget
                WHERE budget.year = $year AND budget.month <= $month
                GROUP BY budget.year) AS budget_total), 0) AS budget;")->getRowArray();

            // SUM COAL GETTING | Monthly
            $data['cg_production'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'Coal Getting' AND A.year = $year AND A.month <= $month) AS actual, 
            COALESCE((SELECT SUM(Mybudget) FROM (
            SELECT budget.year, 
                IF(YEAR(NOW()) = budget.year,
                    SUM(CASE
                        WHEN budget.month < MONTH(NOW()) THEN budget.cg_monthlybudget_qt
                        WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cg_dailybudget_qt * DAY(NOW())
                        WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                        ELSE 0 
                        END),
                    SUM(budget.cg_monthlybudget_qt)) AS Mybudget
            FROM md_monthlybudget budget
            WHERE budget.year = $year AND budget.month <= $month
            GROUP BY budget.year) AS budget_total), 0) AS budget;")->getRowArray();

            // SUM CRUSH COAL | Monthly
            $data['crush_coal_ytd'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' AND year = $year AND month <= $month) AS total,
            COALESCE((SELECT SUM(Mybudget) FROM (
                              SELECT budget.year, 
                                  IF(YEAR(NOW()) = budget.year,
                                      SUM(CASE
                                          WHEN budget.month < MONTH(NOW()) THEN budget.cc_mounthlybudget_qty
                                          WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cc_dailybudget_qty * DAY(NOW())
                                          WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cc_dailybudget_qty * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                          ELSE 0 
                                          END),
                                      SUM(budget.cc_mounthlybudget_qty)) AS Mybudget
                              FROM md_monthlybudget_cc budget
                              WHERE budget.status = '1' AND budget.year = $year AND budget.month <= $month
                              GROUP BY budget.year) AS budget_total), 0) AS budget")->getRowArray();

            //Sum Of Hauling to Port | Monthly
            $data['inquiry_transfer'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'Hauling to Port' AND year = $year AND month <= $month) AS total,
            COALESCE((SELECT SUM(Mybudget) FROM (
                 SELECT budget.year, 
                     IF(YEAR(NOW()) = budget.year,
                         SUM(CASE
                             WHEN budget.month < MONTH(NOW()) THEN budget.hp_mounthlybudget_qty
                             WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.hp_dailybudget_qty * DAY(NOW())
                             WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.hp_dailybudget_qty * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                             ELSE 0 
                             END),
                         SUM(budget.hp_mounthlybudget_qty)) AS Mybudget
                 FROM md_monthlybudget_hp budget
                 WHERE budget.status = '1' AND budget.year = $year AND budget.month <= $month
                 GROUP BY budget.year) AS budget_total), 0) AS budget")->getRowArray();

            //Sum Of Stripping Ratio | Monthly
            $data['stripping_ytd'] = $db->query("SELECT (SELECT SUM(A1.qty) FROM T_Adjustment A1 WHERE A1.transaksi = 'Overburden' AND A1.year = $year AND A1.month <= $month) AS actual_ob, 
            COALESCE((SELECT SUM(Mybudget) FROM (
                SELECT budget.year, 
                    IF(YEAR(NOW()) = budget.year,
                        SUM(CASE
                            WHEN budget.month < MONTH(NOW()) THEN budget.ob_monthlybudget_qt
                            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.ob_dailybudget_qt * DAY(NOW())
                            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                            ELSE 0 
                            END),
                        SUM(budget.ob_monthlybudget_qt)) AS Mybudget
                FROM md_monthlybudget budget
                WHERE budget.year = $year AND budget.month <= $month
                GROUP BY budget.year) AS budget_total), 0) AS budget_ob,
            (SELECT SUM(A2.qty)/1000 FROM T_Adjustment A2 WHERE A2.transaksi = 'Coal Getting' AND A2.year = $year AND A2.month <= $month) AS actual_cg,
                COALESCE((SELECT SUM(Mybudget) FROM (
            SELECT budget.year, 
                IF(YEAR(NOW()) = budget.year,
                    SUM(CASE
                        WHEN budget.month < MONTH(NOW()) THEN budget.cg_monthlybudget_qt
                        WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cg_dailybudget_qt * DAY(NOW())
                        WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                        ELSE 0 
                        END),
                    SUM(budget.cg_monthlybudget_qt)) AS Mybudget
            FROM md_monthlybudget budget
            WHERE budget.year = $year AND budget.month <= $month
            GROUP BY budget.year) AS budget_total), 0) AS budget_cg;")->getRowArray();

            // Sum Of Barging | Monthly
            $builder_shipment = new SalesShipment();
            // $data['barging_ytd']   = $builder_shipment->select("COALESCE(SUM(bl_qty), 0) AS total, 
            // (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE year = $year AND month = $month) AS target")
            //     ->where("MONTH(bl_date) = $month")
            //     ->where("YEAR(bl_date) = $year")
            //     ->get()->getRowArray();

            //New Sum Of Barging | Monthly
            $data['barging_ytd'] = $db->query("SELECT COALESCE(SUM(a.bl_qty), 0) AS total, a.target, a.tahun FROM  
                (SELECT bl_qty, (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE YEAR = $year AND MONTH <= $month) AS target,
                   (CASE WHEN DAY(bl_date) > 25 AND MONTH(bl_date) < 12 THEN MONTH(bl_date) + 1 
                    WHEN MONTH(bl_date) = 12 THEN MONTH(bl_date) ELSE MONTH(bl_date) END) AS bulan,
                   YEAR(bl_date) AS tahun
                FROM T_SAL_SHIPMENT
                    WHERE MONTH(bl_date) <= $month AND YEAR(bl_date) = $year) a
                GROUP BY a.tahun")->getRowArray();

            //Overburden Distance - monthly
            $data['sum_actual_ob_distance'] = $db->query("WITH t_abc AS (SELECT `month`, `year`,
            SUM(CASE WHEN transaksi = 'Overburden' THEN qty/1000 ELSE 0 END) AS OB,
            SUM(CASE WHEN transaksi = 'Distance OB' THEN qty ELSE 0 END) AS DO
            FROM T_Adjustment ta
            WHERE year = $year AND month <= $month
            GROUP BY month, year, id_contractor)
            SELECT SUM(OB * DO)/SUM(OB) AS total FROM t_abc ORDER by year")->getRowArray();

            $data['sum_plan_ob_distance'] = $db->query("WITH sum_distance AS
			(SELECT B.dibagi/B.pembagi AS DISTANCE,
		  B.bulan, B.pembagi AS ob
		  FROM (SELECT A.bulan,
		  			SUM(A.ob * A.distance_ob) as dibagi,
		  			SUM(A.ob) AS pembagi
        FROM (SELECT mmd.project ,mmd.`year` AS tahun, mmd.`month` AS bulan,
					SUM(disob_monthlybudget_qty) AS distance_ob,
					IF(YEAR(NOW()) = mm.year,
					        SUM(CASE
					         	WHEN mm.month < MONTH(NOW()) THEN mm.ob_monthlybudget_qt
					            WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.ob_dailybudget_qt * DAY(NOW())
					            WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
					            ELSE 0 
					            END),
					        SUM(mm.ob_monthlybudget_qt)) AS ob
					FROM md_monthly_disob mmd
					INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
					 WHERE mmd.`year` = $year
					AND mmd.`month` <= $month
					GROUP BY tahun, bulan, project) A
        GROUP BY A.bulan) B)
        SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance")->getRowArray();

            //Coal Getting Distance  Actual - monthly
            $data['sum_actual_cg_distance'] = $db->query("WITH t_abc AS (SELECT `month`, `year`,
            SUM(CASE WHEN transaksi = 'Coal Getting' THEN qty/1000 ELSE 0 END) AS CG,
            SUM(CASE WHEN transaksi = 'Distance CG' THEN qty ELSE 0 END) AS DC
            FROM T_Adjustment ta
            WHERE year = $year AND month <= $month
            GROUP BY month, year, id_contractor)
            SELECT SUM(CG * DC)/SUM(CG) AS total FROM t_abc ORDER by year")->getRowArray();

            $data['sum_plan_cg_distance'] = $db->query("WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS cg FROM (SELECT SUM(A.cg * A.distance_cg) as dibagi, A.bulan, SUM(A.cg) AS pembagi
            FROM (SELECT mmd.project ,mmd.`year` AS tahun, mmd.`month` AS bulan,
                        SUM(discg_monthlybudget_qty) AS distance_cg,
                        IF(YEAR(NOW()) = mm.year,
                                SUM(CASE
                                     WHEN mm.month < MONTH(NOW()) THEN mm.cg_monthlybudget_qt
                                    WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.cg_dailybudget_qt * DAY(NOW())
                                    WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                    ELSE 0 
                                    END),
                                SUM(mm.cg_monthlybudget_qt)) AS cg
                        FROM md_monthly_discg mmd
                        INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                        WHERE mmd.`year` = $year
                        AND mmd.`month` <= $month
                        GROUP BY tahun, bulan, project) A
            GROUP BY A.bulan) B)
            SELECT SUM(distance * cg) / SUM(cg) AS total FROM sum_distance")->getRowArray();

            $data['ob_lines'] = $db->query("SELECT actual, budget, bulan1 AS tahun, bulan1 AS month
            FROM (
                SELECT SUM(A.qty) AS actual, A.month AS bulan1
                FROM T_Adjustment A
                WHERE A.transaksi = 'Overburden' AND A.year = '$year'
                GROUP BY bulan1
            ) subquery1
            JOIN (
					SELECT B.month AS bulan2, 
  				  IF(YEAR(NOW()) = B.year,
		        SUM(CASE
		         	WHEN B.month < MONTH(NOW()) THEN B.ob_monthlybudget_qt
		            WHEN B.month = MONTH(NOW()) AND B.month = 1 THEN B.ob_dailybudget_qt * DAY(NOW())
		            WHEN B.month = MONTH(NOW()) AND B.month != 1 THEN B.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
		            ELSE 0 
		            END),
		        SUM(B.ob_monthlybudget_qt)) AS budget
					FROM md_monthlybudget B
					WHERE B.year = '$year'
					GROUP BY B.year, B.month
            ) subquery2
            ON subquery1.bulan1 = subquery2.bulan2
            ORDER BY tahun;")->getResultArray();

            //Sum Coal Getting (MT) | monthly Graphic
            $data['cg_lines'] = $db->query("SELECT actual, budget, bulan1 AS tahun, bulan1 AS month
            FROM (
                SELECT SUM(A.qty)/1000 AS actual, A.month AS bulan1
                FROM T_Adjustment A
                WHERE A.transaksi = 'Coal Getting' AND A.year = '$year'
                GROUP BY bulan1
            ) subquery1
            JOIN (
				SELECT B.month AS bulan2, 
  				  IF(YEAR(NOW()) = B.year,
		        SUM(CASE
		         	WHEN B.month < MONTH(NOW()) THEN B.cg_monthlybudget_qt
		            WHEN B.month = MONTH(NOW()) AND B.month = 1 THEN B.cg_dailybudget_qt * DAY(NOW())
		            WHEN B.month = MONTH(NOW()) AND B.month != 1 THEN B.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
		            ELSE 0 
		            END),
		        SUM(B.cg_monthlybudget_qt)) AS budget
					FROM md_monthlybudget B
					WHERE B.year = '$year'
					GROUP BY B.year, B.month
            ) subquery2
            ON subquery1.bulan1 = subquery2.bulan2
            ORDER BY tahun;")->getResultArray();


            // Contractor Performance -  Overburden & Contractor Performance Stripping Ratio  1 - monhtly
            $contractor_ob = $db->query("SELECT qmaster.*,
            (SELECT tctr.contractor_name from md_contractors tctr WHERE tctr.id = qmaster.id_contractor)AS contractor,
            (SELECT SUM(actual.qty)
                            FROM T_Adjustment actual
                            WHERE actual.transaksi = 'Overburden'
                            AND actual.year = qmaster.tahun
                            AND actual.month = qmaster.bulan
                            AND actual.id_contractor = qmaster.id_contractor) AS actual_ob,
            (SELECT IF(YEAR(NOW()) = qmaster.tahun AND MONTH(NOW()) = qmaster.bulan,
									        SUM(CASE
									         	WHEN budget.month < MONTH(NOW()) THEN budget.ob_monthlybudget_qt
									            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.ob_dailybudget_qt * DAY(NOW())
									            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
									            ELSE 0 
									            END),
									        SUM(budget.ob_monthlybudget_qt)) AS budgetku
									FROM md_monthlybudget budget 
                           WHERE budget.year = qmaster.tahun AND budget.month = qmaster.bulan
                           AND budget.id_contractor = qmaster.id_contractor) AS budget_ob
            FROM(
            SELECT A.year AS tahun, A.month AS bulan, A.id_contractor FROM T_Adjustment A WHERE A.transaksi = 'Overburden' AND A.id_contractor IS NOT NULL AND A.year = $year GROUP BY A.year, A.month, A.id_contractor
            UNION
            SELECT B.year AS tahun, B.month AS bulan, B.id_contractor FROM md_monthlybudget B WHERE B.id_contractor IS NOT NULL AND B.year = $year GROUP BY B.year, B.month, B.id_contractor
            ) AS qmaster
            ORDER BY contractor, tahun, bulan;")->getResultArray();
            $grouped_contractors = array();
            foreach ($contractor_ob as $c) {
                $grouped_contractors[$c['contractor']][$c['bulan']] = $c;
            }
            $data['contractor_ob'] = $grouped_contractors;

            // Contractor Performance - Coal Getting  & Contractor Performance Stripping Ratio  2 - monhtly
            $contractor_cg = $db->query("SELECT qmaster.*,
            (SELECT tctr.contractor_name from md_contractors tctr WHERE tctr.id = qmaster.id_contractor)AS contractor,
            (SELECT SUM(actual.qty)/1000
                            FROM T_Adjustment actual
                            WHERE actual.transaksi = 'Coal Getting'
                                and actual.year = qmaster.tahun
                            and actual.month = qmaster.bulan
                                 AND actual.id_contractor = qmaster.id_contractor) AS actual_cg,
           (SELECT IF(YEAR(NOW()) = qmaster.tahun AND MONTH(NOW()) = qmaster.bulan,
									        SUM(CASE
									         	WHEN budget.month < MONTH(NOW()) THEN budget.cg_monthlybudget_qt
									            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cg_dailybudget_qt * DAY(NOW())
									            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
									            ELSE 0 
									            END),
									        SUM(budget.cg_monthlybudget_qt)) AS budgetku
                           FROM md_monthlybudget budget 
                           WHERE budget.year = qmaster.tahun AND budget.month = qmaster.bulan
                           AND budget.id_contractor = qmaster.id_contractor) AS budget_cg
            FROM(
            SELECT A.year AS tahun, A.month AS bulan, A.id_contractor FROM T_Adjustment A WHERE A.transaksi = 'Coal Getting' AND A.id_contractor IS NOT NULL AND A.year = $year GROUP BY A.year, A.month, A.id_contractor
            UNION
            SELECT B.year AS tahun, B.month AS bulan, B.id_contractor FROM md_monthlybudget B WHERE B.id_contractor IS NOT NULL AND B.year = $year GROUP BY B.year, B.month, B.id_contractor
            ) AS qmaster
            ORDER BY contractor, tahun, bulan;")->getResultArray();
            $grouped_contractorsC = array();
            foreach ($contractor_cg as $c) {
                $grouped_contractorsC[$c['contractor']][$c['bulan']] = $c;
            }
            $data['contractor_cg'] = $grouped_contractorsC;

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Stripping Ratio Graphic - monhtly
            $stripping_ratio = $db->query("SELECT qmaster.*,
            (SELECT SUM(actual.qty)/1000
                            FROM T_Adjustment actual
                            WHERE actual.transaksi = 'Coal Getting'
                            and actual.year = qmaster.tahun AND actual.month = qmaster.month) AS actual_cg,
            (SELECT SUM(actual.qty)
                            FROM T_Adjustment actual
                            WHERE actual.transaksi = 'Overburden'
                            and actual.year = qmaster.tahun AND actual.month = qmaster.month) AS actual_ob,
           (SELECT IF(YEAR(NOW()) = qmaster.tahun,
						        SUM(CASE
						         	WHEN budget.month < MONTH(NOW()) THEN budget.cg_monthlybudget_qt
						            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.cg_dailybudget_qt * DAY(NOW())
						            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
						            ELSE 0 
						            END),
						         SUM(budget.cg_monthlybudget_qt)) AS budgetku
									FROM md_monthlybudget budget
                           WHERE  budget.year = qmaster.tahun AND budget.month = qmaster.month) AS budget_cg,
      		(SELECT IF(YEAR(NOW()) = qmaster.tahun,
									        SUM(CASE
									         	WHEN budget.month < MONTH(NOW()) THEN budget.ob_monthlybudget_qt
									            WHEN budget.month = MONTH(NOW()) AND budget.month = 1 THEN budget.ob_dailybudget_qt * DAY(NOW())
									            WHEN budget.month = MONTH(NOW()) AND budget.month != 1 THEN budget.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
									            ELSE 0 
									            END),
									        SUM(budget.ob_monthlybudget_qt)) AS budgetku
									FROM md_monthlybudget budget
                           WHERE budget.year = qmaster.tahun AND budget.month = qmaster.month) AS budget_ob
            FROM(
            SELECT A.year AS tahun, A.month FROM T_Adjustment A WHERE A.transaksi = 'Coal Getting'  AND A.year = $year  GROUP BY A.year, A.month
            UNION 
             SELECT A.year AS tahun, A.month FROM T_Adjustment A WHERE A.transaksi = 'Overburden'  AND A.year = $year  GROUP BY A.year, A.month
            UNION 
            SELECT B.year AS tahun, B.month FROM md_monthlybudget B WHERE  B.year = $year  GROUP BY B.year, B.month) AS qmaster;")->getResultArray();
            $data['stripping_ratio'] = $stripping_ratio;

            // Sum Overburden Distance & Coal Getting Distance  - Graphic - monhtly
            $data['actual_distance'] = $db->query("WITH t_abc AS (
                SELECT
                  YEAR AS tahun,
                  MONTH AS bulan,
                  id_contractor AS contractor, 
                  SUM(CASE WHEN transaksi = 'Overburden' THEN qty ELSE 0 END) AS OB,
                  SUM(CASE WHEN transaksi = 'Distance OB' THEN qty ELSE 0 END) AS DO,
                  SUM(CASE WHEN transaksi = 'Coal Getting' THEN qty/1000 ELSE 0 END) AS CG,
                  SUM(CASE WHEN transaksi = 'Distance CG' THEN qty ELSE 0 END) AS DC
                FROM T_Adjustment ta
                GROUP BY month, year, id_contractor
              ), 
              sum_distance_ob AS (
                SELECT B.dibagi/B.pembagi AS DISTANCE_myOB,
                       B.tahun, B.bulan,
                       B.pembagi AS ob
                FROM (
                  SELECT A.tahun,
                         A.bulan,
                         SUM(A.ob * A.distance_ob) as dibagi,
                         SUM(A.ob) AS pembagi
                  FROM (
                    SELECT mmd.project ,
                           mmd.`year` AS tahun, 
                           mmd.`month` AS bulan,
                           SUM(disob_monthlybudget_qty) AS distance_ob,
                           IF(YEAR(NOW()) = mm.year,
                              SUM(CASE
                                WHEN mm.month < MONTH(NOW()) THEN mm.ob_monthlybudget_qt
                                WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.ob_dailybudget_qt * DAY(NOW())
                                WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                ELSE 0 
                              END),
                              SUM(mm.ob_monthlybudget_qt)
                             ) AS ob
                    FROM md_monthly_disob mmd
                    INNER JOIN md_monthlybudget mm 
                    ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                    GROUP BY tahun, bulan, project
                  ) A
                  GROUP BY A.tahun, A.bulan
                ) B
              ),
              sum_distance_cg AS (
                SELECT B.dibagi/B.pembagi AS DISTANCE_myCG,
                       B.tahun,B.bulan,
                       B.pembagi AS cg
                FROM (
                  SELECT A.tahun,
                         A.bulan,
                         SUM(A.cg * A.distance_cg) as dibagi,
                         SUM(A.cg) AS pembagi
                  FROM (
                    SELECT mmd.project ,
                           mmd.`year` AS tahun, 
                           mmd.`month` AS bulan,
                           SUM(discg_monthlybudget_qty) AS distance_cg,
                           IF(YEAR(NOW()) = mm.year,
                              SUM(CASE
                                WHEN mm.month < MONTH(NOW()) THEN mm.cg_monthlybudget_qt
                                WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.cg_dailybudget_qt * DAY(NOW())
                                WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                ELSE 0 
                              END),
                              SUM(mm.cg_monthlybudget_qt)
                             ) AS cg
                    FROM md_monthly_discg mmd
                    INNER JOIN md_monthlybudget mm 
                    ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                    GROUP BY tahun, bulan, project
                  ) A
                  GROUP BY A.tahun, A.bulan
                ) B
              )
              SELECT t_abc.tahun, t_abc.bulan, 
                     SUM(OB * DO) / SUM(OB) AS distance_ob,
                     SUM(CG * DC) / SUM(CG) AS distance_cg,
                     (SELECT SUM(DISTANCE_myOB * ob) / SUM(ob) 
                      FROM sum_distance_ob 
                      WHERE tahun = t_abc.tahun AND bulan = t_abc.bulan) AS target_ob,
                     (SELECT SUM(DISTANCE_myCG * cg) / SUM(cg) 
                      FROM sum_distance_cg
                      WHERE tahun = t_abc.tahun AND bulan = t_abc.bulan) AS target_cg
                      
              FROM t_abc
              WHERE tahun = $year
              GROUP BY tahun, bulan 
              ORDER BY tahun, bulan;")->getResultArray();

            // Sum Overburden Distance & Coal Getting Distance  (Per Contractor) - Chart - monthly
            $contractor_distance = $db->query("WITH t_abc AS (
                SELECT
                  YEAR AS tahun,
                  MONTH AS bulan,
                  id_contractor, 
                  SUM(CASE WHEN transaksi = 'Overburden' THEN qty ELSE 0 END) AS OB,
                  SUM(CASE WHEN transaksi = 'Distance OB' THEN qty ELSE 0 END) AS DO,
                  SUM(CASE WHEN transaksi = 'Coal Getting' THEN qty/1000 ELSE 0 END) AS CG,
                  SUM(CASE WHEN transaksi = 'Distance CG' THEN qty ELSE 0 END) AS DC
                FROM T_Adjustment ta
                WHERE ta.transaksi = 'Overburden' OR ta.transaksi = 'Distance OB' OR ta.transaksi = 'Coal Getting' OR ta.transaksi = 'Distance CG' AND ta.id_contractor IS NOT NULL   
                GROUP BY year, MONTH, id_contractor
              ), 
              sum_distance_ob AS (
                SELECT B.dibagi/B.pembagi AS DISTANCE_myOB,
                       B.tahun,
                       B.bulan,
							  B.id_contractor,
                       B.pembagi AS ob
                FROM (
                  SELECT A.tahun,
                         A.bulan,
                         A.id_contractor,
                         SUM(A.ob * A.distance_ob) as dibagi,
                         SUM(A.ob) AS pembagi
                  FROM (
                    SELECT mmd.project ,
                           mmd.`year` AS tahun, 
                           mmd.`month` AS bulan,
                           mmd.id_contractor,
                           SUM(disob_monthlybudget_qty) AS distance_ob,
                           IF(YEAR(NOW()) = mm.year,
                              SUM(CASE
                                WHEN mm.month < MONTH(NOW()) THEN mm.ob_monthlybudget_qt
                                WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.ob_dailybudget_qt * DAY(NOW())
                                WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.ob_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                ELSE 0 
                              END),
                              SUM(mm.ob_monthlybudget_qt)
                             ) AS ob
                    FROM md_monthly_disob mmd
                    INNER JOIN md_monthlybudget mm 
                    ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month`  AND mm.id_contractor = mmd.id_contractor 
                    GROUP BY tahun, bulan, project, id_contractor
                  ) A
                  GROUP BY A.tahun, A.bulan,  A.id_contractor
                ) B
              ),
              sum_distance_cg AS (
                SELECT B.dibagi/B.pembagi AS DISTANCE_myCG,
                       B.tahun,
                        B.bulan,
                       B.id_contractor,
                       B.pembagi AS cg
                FROM (
                  SELECT A.tahun,
                         A.bulan,
                         A.id_contractor,
                         SUM(A.cg * A.distance_cg) as dibagi,
                         SUM(A.cg) AS pembagi
                  FROM (
                    SELECT mmd.project ,
                           mmd.`year` AS tahun, 
                           mmd.`month` AS bulan,
                           mmd.id_contractor,
                           SUM(discg_monthlybudget_qty) AS distance_cg,
                           IF(YEAR(NOW()) = mm.year,
                              SUM(CASE
                                WHEN mm.month < MONTH(NOW()) THEN mm.cg_monthlybudget_qt
                                WHEN mm.month = MONTH(NOW()) AND mm.month = 1 THEN mm.cg_dailybudget_qt * DAY(NOW())
                                WHEN mm.month = MONTH(NOW()) AND mm.month != 1 THEN mm.cg_dailybudget_qt * (DAY(NOW()) + DAY(LAST_DAY(NOW() - INTERVAL 1 MONTH)) - 25)
                                ELSE 0 
                              END),
                              SUM(mm.cg_monthlybudget_qt)
                             ) AS cg
                    FROM md_monthly_discg mmd
                    INNER JOIN md_monthlybudget mm 
                    ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month`  AND mm.id_contractor = mmd.id_contractor 
                    GROUP BY tahun, bulan, project, id_contractor
                  ) A
                  GROUP BY A.tahun, A.bulan, A.id_contractor
                ) B
              )
              SELECT t_abc.tahun,  t_abc.bulan, t_abc.id_contractor, 
             (SELECT tctr.contractor_name from md_contractors tctr WHERE tctr.id = t_abc.id_contractor) AS contractor,
                     SUM(OB * DO) / SUM(OB) AS distance_ob,
                     SUM(CG * DC) / SUM(CG) AS distance_cg,
                     (SELECT SUM(DISTANCE_myOB * ob) / SUM(ob) 
                      FROM sum_distance_ob 
                      WHERE tahun = t_abc.tahun AND bulan = t_abc.bulan  AND id_contractor = t_abc.id_contractor) AS target_ob,
                     (SELECT SUM(DISTANCE_myCG * cg) / SUM(cg) 
                      FROM sum_distance_cg
                      WHERE tahun = t_abc.tahun AND bulan = t_abc.bulan AND id_contractor = t_abc.id_contractor) AS target_cg
                      
              FROM t_abc
              WHERE tahun = $year
              GROUP BY tahun, bulan, id_contractor
              ORDER BY tahun, bulan, id_contractor;")->getResultArray();
            $grouped_contracDistance = array();
            foreach ($contractor_distance as $c) {
                $grouped_contracDistance[$c['contractor']][$c['bulan']] = $c;
            }
            $data['contractor_distance'] = $grouped_contracDistance;


            // Sum Crushed Coal (MT)  - chart - monthly
            // $sum_cc = $db->query("SELECT qmaster.*,
            // 'OFN' AS crusher,
            // (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' AND YEAR = $year AND  MONTH = qmaster.bulan) AS total
            // FROM(
            //  SELECT A.year AS tahun, A.month AS bulan FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' GROUP BY A.year, A.month
            //  UNION 
            //  SELECT B.year AS tahun, B.month AS bulan FROM md_monthlybudget_cc B GROUP BY B.year, B.month) AS qmaster
            //  ORDER BY qmaster.tahun, qmaster.bulan ASC")->getResultArray();
            $sum_cc = $db->query("SELECT qmaster.*,
            'OFN' AS crusher,
            (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' AND MONTH = qmaster.bulan) AS total
            FROM(
             SELECT A.year AS tahun, A.month AS bulan FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' GROUP BY A.year, A.month
             UNION 
             SELECT B.year AS tahun, B.month AS bulan FROM md_monthlybudget_cc B GROUP BY B.year, B.month) AS qmaster
             WHERE qmaster.tahun = $year
             ORDER BY qmaster.tahun, qmaster.bulan ASC")->getResultArray();
            $grouped_cc = array();
            foreach ($sum_cc as $c) {
                $grouped_cc[$c['crusher']][] = $c;
            }
            $data['sum_cc'] = $grouped_cc;

            $plan_cc = $db->query("SELECT month AS bulan, year AS tahun, cc_mounthlybudget_qty AS budget FROM md_monthlybudget_cc 
            WHERE YEAR = $year")->getResultArray();
            $data['plan_cc'] = $plan_cc;


            //hauling graphic - Actual - chart - monthly 
            $sum_hauling_to_tonase = $db->query("SELECT SUM(qty)/1000 AS total, COALESCE(transporter_Description, 'undefined') AS transporter, 
            year AS tahun, month AS bulan,
            (SELECT COALESCE(SUM(hp_mounthlybudget_qty), 0) FROM md_monthlybudget_hp 
            WHERE YEAR = $year AND MONTH = bulan) AS budget
            FROM T_Adjustment
            WHERE YEAR = $year AND  transaksi = 'Hauling to Port'
            GROUP BY transporter, tahun, bulan;")->getResultArray();
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
            $hauling_plan_arr = array();

            //hauling graphic - Budget -chart - monthly
            $hauling_plan = $db->query("SELECT COALESCE(AVG(hp_mounthlybudget_qty), 0) AS plan, month FROM md_monthlybudget_hp 
            WHERE year = $year GROUP BY month ORDER BY month")->getResultArray();
            $data['hauling_plan'] = $hauling_plan;

            // Local vs Export (%) Chart
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
            $data['shipment_list'] = $grouped_sl;

            // Coal Index Time Series (IDR) - Chart
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

            // Domestic Market Obligation(DMO) - Chart
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




            // Barging (MT) - Chart
            $barging = $builder_shipment->select("SUM(bl_qty) AS total, MONTH(bl_date) AS bulan,
        (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE `year` = $year AND `month` = bulan) AS target")
                ->where("YEAR(bl_date) = $year")
                ->groupBy("bulan")
                ->orderBy("bulan")
                ->get()->getResultArray();
            $data['barging'] = $barging;


            $data['harga_jual'] = [];

            $data['coal_ic'] = [];
            $data['coal_newc'] = [];



            $prd_sales_prod = $db->query("SELECT SUM(qty/1000) AS total,
            MONTH AS bulan
            FROM T_Adjustment WHERE year = $year AND transaksi = 'Coal Getting'
            GROUP BY bulan
            ORDER BY bulan")->getResultArray();
            $prd_sales_sales = $db->query("SELECT SUM(bl_qty) AS total,
            MONTH(bl_date) AS bulan
            FROM T_SAL_SHIPMENT WHERE YEAR(bl_date) = $year
            GROUP BY bulan
            ORDER BY bulan")->getResultArray();
            $prd_hauling = $db->query("SELECT SUM(qty/1000) AS total, month AS bulan
            FROM T_Adjustment WHERE year = $year AND transaksi = 'Hauling to Port'
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

            $unrestricted_stock = $db->query("SELECT isc.stock AS 'Crusher Stock', 
            isp.stock AS 'Port Stock', isr.stock AS 'ROM Stock'
            FROM im_stock_cc isc INNER JOIN im_stock_port isp ON isp.posting_date = isc.posting_date 
            INNER JOIN im_stock_raw isr ON isr.posting_date = isc.posting_date 
            WHERE YEAR(isc.posting_date) = $year
            ORDER BY isc.posting_date DESC LIMIT 1")->getRowArray();
            $data['unrestricted_stock'] = $unrestricted_stock ?? [];

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } elseif ($type == 'daily') { //filter by daily
            //SUM overbuurden - Daily

            $Timesheet = new Timesheets();
            $builder = $Timesheet->builder();

            $data['ob_production'] = $builder->select("SUM(prd_ob_total) AS actual, 
                (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget")
                ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
                ->where("month = $month")
                ->where("mm.year = $year")
                ->where("timesheets.status = 'approved'")
                ->where("deleted_at IS NULL")
                ->get()->getRowArray();
            // SUM COAL GETTING - Daily
            // $data['cg_production'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'Coal Getting' AND A.year = $year AND A.month = $month) AS actual, 
            // COALESCE((SELECT SUM(B.cg_monthlybudget_qt) FROM md_monthlybudget B WHERE B.year = $year AND B.month = $month), 0) AS budget")->getRowArray();
            $data['cg_production'] = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
            (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget 
            FROM temp_timbangan WHERE tahun = $year AND bulan = $month")->getRowArray();
            // SUM CRUSH COAL - Daily
            // $data['crush_coal_ytd'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'CrushCoal' AND year = $year AND month = $month) AS total, 
            // COALESCE((SELECT SUM(B.cc_mounthlybudget_qty) FROM md_monthlybudget_cc B WHERE B.status = '1' AND year = $year AND month = $month), 0) AS budget")->getRowArray();
            $data['crush_coal_ytd'] = $db->query("SELECT SUM(Net_Weigh)/1000 AS total, (SELECT SUM(mmc2.cc_mounthlybudget_qty) FROM md_monthlybudget_cc mmc2 
            WHERE `year`= $year AND `month` = $month) AS budget FROM temp_transfer WHERE tahun = $year AND bulan = $month")->getRowArray();
            //Sum Of Hauling to Port
            // $data['inquiry_transfer'] = $db->query("SELECT (SELECT SUM(A.qty)/1000 FROM T_Adjustment A WHERE A.transaksi = 'Hauling to Port' AND year = $year AND month = $month) AS total, 
            // COALESCE((SELECT SUM(B.cc_mounthlybudget_qty) FROM md_monthlybudget_cc B WHERE B.status = '1'), 0) AS budget")->getRowArray();
            $data['inquiry_transfer']  = $db->query("WITH temp_hauling AS (SELECT Net_Weigh, Transporter_Description AS transporter, Posting_Date,
            (CASE WHEN DAY(Posting_Date) > 25 THEN MONTH(Posting_Date) + 1 ELSE MONTH(Posting_Date) END) AS bulan
            FROM inquiry_transfer
            WHERE YEAR(Posting_Date) = $year)
            SELECT SUM(Net_Weigh)/1000 AS total,
            (SELECT COALESCE(SUM(hp_mounthlybudget_qty), 0) FROM md_monthlybudget_hp 
                WHERE year = $year AND month = $month) AS budget
            FROM temp_hauling th
            WHERE bulan = $month")->getRowArray();

            //Sum Of Stripping Ratio 
            // $data['stripping_ytd'] = $db->query("SELECT (SELECT SUM(A1.qty) FROM T_Adjustment A1 WHERE A1.transaksi = 'Overburden' AND A1.year = $year AND A1.month = $month) AS actual_ob, 
            // COALESCE((SELECT SUM(B1.ob_monthlybudget_qt) FROM md_monthlybudget B1 WHERE B1.year = $year AND B1.month = $month), 0) AS budget_ob,
            // (SELECT SUM(A2.qty)/1000 FROM T_Adjustment A2 WHERE A2.transaksi = 'Coal Getting' AND A2.year = $year AND A2.month = $month) AS actual_cg,
            // COALESCE((SELECT SUM(B2.cg_monthlybudget_qt) FROM md_monthlybudget B2 WHERE B2.year = $year AND B2.month = $month), 0) AS budget_cg")->getRowArray();

            $data['stripping_ytd'] = $builder->select("COALESCE(SUM(timesheets.prd_cg_total), 0) AS actual_cg, 
            COALESCE(SUM(timesheets.prd_ob_total), 0) AS actual_ob, 
            (SELECT SUM(cg_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget_cg, 
            (SELECT SUM(ob_monthlybudget_qt) FROM md_monthlybudget mms WHERE year = $year AND month = $month) AS budget_ob")
                ->join("md_monthlybudget mm", "mm.id_monthlybudget = timesheets.id_monthlybudget")
                ->where("month = $month")
                ->where("mm.year = $year")
                ->where("timesheets.status = 'approved'")
                ->where("deleted_at IS NULL")
                ->get()->getRowArray();

            // Sum Of Barging | Monthly
            // $builder_shipment = new SalesShipment();
            $data['barging_ytd']   = $builder_shipment->select("COALESCE(SUM(bl_qty), 0) AS total, 
            (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE year = $year AND month = $month) AS target")
                ->where("MONTH(bl_date) = $month")
                ->where("YEAR(bl_date) = $year")
                ->get()->getRowArray();

            // $data['barging_ytd'] = $db->query("SELECT COALESCE(SUM(a.bl_qty), 0) AS total, a.target, a.tahun FROM  
            //     (SELECT bl_qty, (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE YEAR = $year AND MONTH = $month) AS target,
            //        (CASE WHEN DAY(bl_date) > 25 AND MONTH(bl_date) < 12 THEN MONTH(bl_date) + 1 
            //         WHEN MONTH(bl_date) = 12 THEN MONTH(bl_date) ELSE MONTH(bl_date) END) AS bulan,
            //        YEAR(bl_date) AS tahun
            //     FROM T_SAL_SHIPMENT
            //         WHERE MONTH(bl_date) = $month AND YEAR(bl_date) = $year) a
            //     GROUP BY a.tahun")->getRowArray();

            //Overburden Distance - monthly
            // $data['sum_actual_ob_distance'] = $db->query("WITH t_abc AS (SELECT `month`, `year`,
            // SUM(CASE WHEN transaksi = 'Overburden' THEN qty/1000 ELSE 0 END) AS OB,
            // SUM(CASE WHEN transaksi = 'Distance OB' THEN qty ELSE 0 END) AS DO
            // FROM T_Adjustment ta
            // WHERE year = $year AND month = $month
            // GROUP BY month, year, id_contractor)
            // SELECT SUM(OB * DO)/SUM(OB) AS total FROM t_abc ORDER by year")->getRowArray();

            $result_OB = $db->query("SELECT SUM(prd_ob_total * prd_ob_distance) AS OB,
            SUM(prd_ob_total) AS DO
            FROM timesheets
            WHERE YEAR(prd_date) = $year AND MONTH(prd_date) = $month")->getRowArray();
            $OB = $result_OB['OB'] ? $result_OB['OB'] : 0;
            $DO = $result_OB['DO'] ? $result_OB['DO'] : 0;
            // $total = ($OB * $DO) / ($OB);
            $total = $OB && $DO ? ($OB / $DO) : 0;
            $data['sum_actual_ob_distance']['total'] = $total;

            // $data['sum_plan_ob_distance'] = $db->query("WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
            // FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(disob_monthlybudget_qty) AS distance_ob, SUM(mm.ob_monthlybudget_qt) AS ob 
            // FROM md_monthly_disob mmd
            // INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
            // WHERE mmd.`year` = $year
            // AND mmd.`month` = $month
            // GROUP BY bulan, project) A
            // GROUP BY A.bulan) B)
            // SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance")->getRowArray();

            $data['sum_plan_ob_distance'] =  $db->query("WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
            FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(disob_monthlybudget_qty) AS distance_ob, SUM(mm.ob_monthlybudget_qt) AS ob 
            FROM md_monthly_disob mmd
            INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
            WHERE mmd.`year` = $year
            AND mmd.`month` = $month
            GROUP BY bulan, project) A
            GROUP BY A.bulan) B)
            SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance")->getRowArray();

            //Coal Getting Distance  Actual - monthly
            // $data['sum_actual_cg_distance'] = $db->query("WITH t_abc AS (SELECT `month`, `year`,
            // SUM(CASE WHEN transaksi = 'Coal Getting' THEN qty/1000 ELSE 0 END) AS CG,
            // SUM(CASE WHEN transaksi = 'Distance CG' THEN qty ELSE 0 END) AS DC
            // FROM T_Adjustment ta
            // WHERE year = $year AND month = $month
            // GROUP BY month, year, id_contractor)
            // SELECT SUM(CG * DC)/SUM(CG) AS total FROM t_abc ORDER by year")->getRowArray();
            $result_cg  = $db->query("SELECT SUM(prd_cg_total * prd_cg_distance) AS CG,
            SUM(prd_cg_total/1000) AS DC
            FROM timesheets
            WHERE YEAR(prd_date) = $year AND MONTH(prd_date) = $month")->getRowArray();
            $CG = $result_cg['CG'];
            $DC = $result_cg['DC'];
            // $total = ($CG * $DC) / ($CG);
            $total = $CG && $DC ?  ($CG / $DC) / 1000 : 0;
            $data['sum_actual_cg_distance']['total'] = $total;

            // $data['sum_plan_cg_distance'] = $db->query("WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
            // FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(discg_monthlybudget_qty) AS distance_ob, SUM(mm.cg_monthlybudget_qt) AS ob 
            // FROM md_monthly_discg mmd
            // INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
            // WHERE mmd.`year` = $year
            // AND mmd.`month` = $month
            // GROUP BY bulan, project) A
            // GROUP BY A.bulan) B)
            // SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance")->getRowArray();



            $data['sum_plan_cg_distance'] = $db->query("WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
                FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(discg_monthlybudget_qty) AS distance_ob, SUM(mm.cg_monthlybudget_qt) AS ob 
                FROM md_monthly_discg mmd
                INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
                WHERE mmd.`year` = $year
                AND mmd.`month` = $month
                GROUP BY bulan, project) A
                GROUP BY A.bulan) B)
            SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance")->getRowArray();

            // Sum Overburden (BCM) | Daily
            $data['ob_lines'] = $db->query("WITH data_per_contractor AS (SELECT SUM(prd_ob_total) AS actual, prd_date AS month, id_monthlybudget AS ids,
            (SELECT SUM(ob_dailybudget_qt) FROM md_monthlybudget mms WHERE id_monthlybudget = ids) AS budget,
            (SELECT SUM(PRD_OUTLOOK_OB_TOT)/DATEDIFF('$date_target', '$date_before') FROM t_outlook_timesheet tot 
            WHERE tot.year = $year AND tot.month = $month) AS outlook	
            FROM timesheets t  WHERE prd_date BETWEEN '$date_before' AND '$date_target'
            GROUP BY prd_date, id_monthlybudget ORDER BY prd_date)
            SELECT SUM(actual) AS actual, month, SUM(budget) AS budget FROM data_per_contractor GROUP BY month")->getResultArray();

            //Sum Coal Getting (MT) | Daily
            $data['cg_lines'] =  $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, Posting_Date AS month, tahun, bulan,
            (SELECT SUM(mm.cg_dailybudget_qt) FROM md_monthlybudget mm WHERE mm.`year` = tahun AND mm.`month` = bulan) AS budget
            FROM temp_timbangan tt 
            INNER JOIN md_monthlybudget mms ON mms.`month` = bulan AND mms.`year` = tahun
            WHERE tt.tahun = $year AND bulan = $month GROUP BY Posting_Date, tahun, bulan ORDER BY Posting_Date")->getResultArray();


            // Contractor Performance -  daily
            $contractor_cg_ob = $db->query("SELECT
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
            foreach ($contractor_cg_ob as $c) {
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

            $data['contractor_ob'] = [];
            $data['contractor_cg_ob'] = $grouped_contractors;
            $data['contractor_performance'] = $contractor_cg_ob;
            $data['contractor_distance'] = $grouped_distance;



            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //Stripping Ratio Graphic - daily
            $stripping_ration = $db->query("WITH data_per_contractor AS (SELECT SUM(prd_cg_total) AS actual_cg, SUM(t.prd_ob_total) AS actual_ob, 
            prd_date AS month, id_monthlybudget AS ids, 
            (SELECT SUM(cg_dailybudget_qt) FROM md_monthlybudget mms WHERE id_monthlybudget = ids) AS budget_cg, 
            (SELECT SUM(ob_dailybudget_qt) FROM md_monthlybudget mms WHERE id_monthlybudget = ids) AS budget_ob 
            FROM timesheets t WHERE prd_date BETWEEN '$date_before' AND '$date_target' 
            GROUP BY prd_date, id_monthlybudget ORDER BY prd_date)
            SELECT SUM(actual_cg) AS actual_cg, SUM(actual_ob) AS actual_ob,
            month, SUM(budget_cg) AS budget_cg, SUM(budget_ob) AS budget_ob
            FROM data_per_contractor GROUP BY month")->getResultArray();
            $data['stripping_ratio'] = $stripping_ration;

            // Sum Overburden Distance & Coal Getting Distance  - Graphic - Daily
            $data['actual_distance'] = $db->query("SELECT SUM(prd_ob_total * prd_ob_distance) AS ob,
            SUM(prd_cg_total * prd_cg_distance) AS cg,
            prd_date AS bulan,
            SUM(prd_ob_total) AS distance_ob,
            SUM(prd_cg_total) AS distance_cg,
            (SELECT SUM(disob_dailybudget_qty * mm.ob_dailybudget_qt)/SUM(mm.ob_dailybudget_qt) FROM md_monthly_disob mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` WHERE mmd.year = $year AND mmd.month = $month) AS target_ob,
            (SELECT SUM(discg_dailybudget_qty * mm.cg_dailybudget_qt)/SUM(mm.cg_dailybudget_qt) FROM md_monthly_discg mmd INNER JOIN md_monthlybudget mm ON mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` WHERE mmd.year = $year AND mmd.month = $month) AS target_cg
            FROM timesheets
            WHERE prd_date BETWEEN '$date_before' AND '$date_target'
            GROUP BY bulan
            ORDER BY bulan")->getResultArray();

            // Sum Overburden Distance & Coal Getting Distance  (Per Contractor) - Chart - Daily
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
            $data['contractor_distance'] = $grouped_distance;


            // Sum Crushed Coal (MT)  - chart - Daily
            $sum_cc = $db->query("SELECT SUM(Net_Weigh)/1000 AS total, DATE(Posting_Date) AS bulan, 'OFN' AS crusher FROM temp_transfer 
            WHERE bulan = $month GROUP BY Posting_Date ORDER BY Posting_Date")->getResultArray();
            $grouped_cc = array();
            foreach ($sum_cc as $c) {
                $grouped_cc[$c['crusher']][$c['bulan']] = $c['total'];
            }
            $data['sum_cc'] = $grouped_cc;

            $plan_cc = $db->query("SELECT cc_dailybudget_qty AS plan FROM md_monthlybudget_cc WHERE year = $year AND month = $month")->getRowArray();
            $data['plan_cc'] = $plan_cc;


            //hauling graphic - Actual - chart - Daily 
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

            //hauling graphic - Budget - chart - Daily 
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

            // Local vs Export (%) Chart
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
            $data['shipment_list'] = $grouped_sl;

            // Coal Index Time Series (IDR) - Chart
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

            // Domestic Market Obligation(DMO) - Chart
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




            // Barging (MT) - Chart
            $barging = $builder_shipment->select("SUM(bl_qty) AS total, MONTH(bl_date) AS bulan,
        (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE `year` = $year AND `month` = bulan) AS target")
                ->where("YEAR(bl_date) = $year")
                ->groupBy("bulan")
                ->orderBy("bulan")
                ->get()->getResultArray();
            $data['barging'] = $barging;


            $data['harga_jual'] = [];

            $data['coal_ic'] = [];
            $data['coal_newc'] = [];



            $prd_sales_prod = $db->query("SELECT SUM(qty/1000) AS total,
            MONTH AS bulan
            FROM T_Adjustment WHERE year = $year AND transaksi = 'Coal Getting'
            GROUP BY bulan
            ORDER BY bulan")->getResultArray();
            $prd_sales_sales = $db->query("SELECT SUM(bl_qty) AS total,
            MONTH(bl_date) AS bulan
            FROM T_SAL_SHIPMENT WHERE YEAR(bl_date) = $year
            GROUP BY bulan
            ORDER BY bulan")->getResultArray();
            $prd_hauling = $db->query("SELECT SUM(qty/1000) AS total, month AS bulan
            FROM T_Adjustment WHERE year = $year AND transaksi = 'Hauling to Port'
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

            $unrestricted_stock = $db->query("SELECT isc.stock AS 'Crusher Stock', 
            isp.stock AS 'Port Stock', isr.stock AS 'ROM Stock'
            FROM im_stock_cc isc INNER JOIN im_stock_port isp ON isp.posting_date = isc.posting_date 
            INNER JOIN im_stock_raw isr ON isr.posting_date = isc.posting_date 
            WHERE YEAR(isc.posting_date) = $year
            ORDER BY isc.posting_date DESC LIMIT 1")->getRowArray();
            $data['unrestricted_stock'] = $unrestricted_stock ?? [];
        }


        if ($date) {

            $parsed_date = Time::parse($date);
            $month_date = $parsed_date->getMonth();
            $year_date = $parsed_date->getYear();
            if ($parsed_date->getDay() > 25 && $month_date < 12) {
                $month_date++;
            }
            // Sum Of Overburden | Date
            $ob_production = $db->query("WITH temp_ts AS (SELECT prd_date, prd_cg_total, prd_ob_total, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mm.id_monthlybudget
                        FROM timesheets t
                        INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget 
                        WHERE prd_date = '$date' AND t.status = 'approved' AND deleted_at IS NULL)
                    SELECT SUM(prd_ob_total) AS actual, SUM(ob_dailybudget_qt) AS budget FROM temp_ts")->getRowArray();
            $data['ob_production'] = $ob_production;

            // Sum Of Coal Getting | Date
            $coal_getting_production = $db->query("SELECT SUM(Net_Weigh)/1000 AS actual, 
                (SELECT SUM(cg_dailybudget_qt) FROM md_monthlybudget mms WHERE year = YEAR('$date') AND month = MONTH('$date')) AS budget FROM temp_timbangan tt
                WHERE DATE(Posting_Date) = '$date';")->getRowArray();
            $data['cg_production'] = $coal_getting_production;

            // Sum Of Stripping Ratio | Date
            $stripping_today = $db->query("WITH temp_ts AS (SELECT prd_date, prd_cg_total, prd_ob_total, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mm.id_monthlybudget
                    FROM timesheets t
                    INNER JOIN md_monthlybudget mm ON mm.id_monthlybudget = t.id_monthlybudget 
                    WHERE prd_date = '$date' AND t.status = 'approved' AND deleted_at IS NULL)
                SELECT SUM(prd_ob_total) AS actual_ob, 
                    (SELECT SUM(ob_dailybudget_qt) FROM md_monthlybudget WHERE id_monthlybudget IN (SELECT id_monthlybudget FROM temp_ts)) AS budget_ob, 
                    (SELECT SUM(cg_dailybudget_qt) FROM md_monthlybudget WHERE id_monthlybudget IN (SELECT id_monthlybudget FROM temp_ts)) AS actual_cg,
                    SUM(cg_dailybudget_qt) AS budget_cg FROM temp_ts")->getRowArray();

            $data['stripping_ytd'] = $stripping_today;
            // Sum Of Crush Coal | Date
            $data['crush_coal_ytd'] = $db->query("SELECT SUM(Net_Weigh)/1000 AS total, (SELECT SUM(mmc2.cc_dailybudget_qty) FROM md_monthlybudget_cc mmc2 
            WHERE `year`= $year_date AND `month` = $month_date) AS budget FROM temp_transfer WHERE DATE(Posting_Date) = '$date'")->getRowArray();

            //Sum Of Hauling to Port - Date
            $data['inquiry_transfer'] = $db->query("SELECT SUM(Net_Weigh)/1000 AS total,
            (SELECT COALESCE(SUM(hp_mounthlybudget_qty), 0) FROM md_monthlybudget_hp 
                WHERE year = $year_date AND month = $month_date) AS budget
            FROM inquiry_transfer ip
            WHERE DATE(Posting_Date) = '$date'")->getRowArray();

            // Sum Of Barging | Date
            // $data['barging_ytd'] = $db->query("SELECT COALESCE(SUM(bl_qty), 0) AS total, 
            // (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE year = $year_date AND month = $month_date) AS target
            // FROM T_SAL_SHIPMENT WHERE bl_date = '$date'")->getRowArray();

            // New Sum Of Barging | Date
            $data['barging_ytd'] = $db->query("SELECT COALESCE(SUM(a.bl_qty), 0) AS total, a.target, a.tahun FROM  
            (SELECT bl_qty, (SELECT SUM(quantity) FROM T_SAL_TARGET WHERE YEAR = $year_date AND MONTH = $month_date) AS target,
               (CASE WHEN DAY(bl_date) > 25 AND MONTH(bl_date) < 12 THEN MONTH(bl_date) + 1 
                WHEN MONTH(bl_date) = 12 THEN MONTH(bl_date) ELSE MONTH(bl_date) END) AS bulan,
               YEAR(bl_date) AS tahun
            FROM T_SAL_SHIPMENT
                WHERE  bl_date = '$date') a
            GROUP BY a.tahun")->getRowArray();


            // // Overburden Distance | Actual | Date
            $data['sum_actual_ob_distance'] = $db->query("SELECT SUM(prd_ob_total * prd_ob_distance)/SUM(prd_ob_total) AS total
            FROM timesheets
            WHERE prd_date = '$date' AND status = 'approved' AND deleted_at IS NULL ")->getRowArray();
            // // ferr
            // Overburden Distance | Budget | Date
            $data['sum_plan_ob_distance'] = $db->query("WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
            FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(disob_monthlybudget_qty) AS distance_ob, SUM(mm.ob_monthlybudget_qt) AS ob 
            FROM md_monthly_disob mmd
            INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
            WHERE mmd.`year` = $year
            AND mmd.`month` = $month
            GROUP BY bulan, project) A
            GROUP BY A.bulan) B)
            SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance")->getRowArray();

            // // Coal Getting Distance | Actual| Date
            $data['sum_actual_cg_distance'] = $db->query("SELECT SUM(prd_cg_total * prd_cg_distance)/SUM(prd_cg_total) AS total
            FROM timesheets
            WHERE prd_date = '$date' AND status = 'approved' AND deleted_at IS NULL ")->getRowArray();

            // // Coal Getting Distance | Budget | Date
            $data['sum_plan_cg_distance'] = $db->query("WITH sum_distance AS (SELECT B.dibagi/B.pembagi AS distance, B.bulan, B.pembagi AS ob FROM (SELECT SUM(A.ob * A.distance_ob) as dibagi, A.bulan, SUM(A.ob) AS pembagi
            FROM (SELECT mmd.project, mmd.`month` AS bulan, SUM(discg_monthlybudget_qty) AS distance_ob, SUM(mm.cg_monthlybudget_qt) AS ob 
            FROM md_monthly_discg mmd
            INNER JOIN md_monthlybudget mm ON mm.project = mmd.project AND mm.`year` = mmd.`year` AND mm.`month` = mmd.`month` 
            WHERE mmd.`year` = $year
            AND mmd.`month` = $month
            GROUP BY bulan, project) A
            GROUP BY A.bulan) B)
            SELECT SUM(distance * ob) / SUM(ob) AS total FROM sum_distance")->getRowArray();
        }










        // * SALES










        // $data['cost_minings'] = [];



        $data['stock_vs_prod'] = [];



        $data['contractor_performance'] = [];


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
