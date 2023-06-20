<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\FiActBalance;
use App\Models\FiActTrs;

use CodeIgniter\I18n\Time;
use Config\Database;

class Finance extends BaseController
{

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

    private function agingAccountInvoice($year, $date)
    {
        $db = Database::connect();
        if ($date == date('Y-m-d')) {
            $document_aging = $db->query("SELECT SUM(
                CASE WHEN SHKZG = 'S' THEN DMBTR * -1
                WHEN SHKZG = 'H' THEN DMBTR * 1 END) AS total, aa.CATEGORY AS category
                FROM aging_account aa 
                WHERE aa.GJAHR = $year
                GROUP BY category
                ORDER BY category")->getResultArray();
        } else {
            $document_aging = $db->query("SELECT SUM(
                CASE WHEN SHKZG = 'S' THEN DMBTR * -1
                WHEN SHKZG = 'H' THEN DMBTR * 1 END) AS total, C.CATEGORY AS category
                FROM (SELECT B.*, (case
                when (`B`.`DIFF` > 180) then '>180'
                when ((`B`.`DIFF` >= 91)
                and (`B`.`DIFF` <= 180)) then '91-180'
                when ((`B`.`DIFF` >= 61)
                and (`B`.`DIFF` <= 90)) then '61-90'
                when ((`B`.`DIFF` >= 31)
                and (`B`.`DIFF` <= 60)) then '31-60'
                when ((`B`.`DIFF` >= 0)
                and (`B`.`DIFF` <= 30)) then '0-30'
                else 'Current'
            end
            ) AS CATEGORY
            FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
                DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
                FROM (
                SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                    INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
                WHERE HKONT IN ('2100110202', '2100110201', '2100110204') AND GJAHR = YEAR('$date')
            ) A ) B) C WHERE C.GJAHR = $year GROUP BY C.category ORDER BY C.category")->getResultArray();
        }
        $grouped_array = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0);
        $sum = 0;
        foreach ($document_aging as $d) {
            $grouped_array[$d['category']] = $d['total'];
            $sum += $d['total'];
        }
        $grouped_array['total'] = $sum;
        return $grouped_array;
    }

    private function agingAccountNetInvoice($year, $date)
    {
        $db = Database::connect();
        $temp_table = $db->query("CREATE TEMPORARY TABLE zxc 
            SELECT B.*, (case
                when (`B`.`DIFF` > 180) then '>180'
                when ((`B`.`DIFF` >= 91)
                and (`B`.`DIFF` <= 180)) then '91-180'
                when ((`B`.`DIFF` >= 61)
                and (`B`.`DIFF` <= 90)) then '61-90'
                when ((`B`.`DIFF` >= 31)
                and (`B`.`DIFF` <= 60)) then '31-60'
                when ((`B`.`DIFF` >= 0)
                and (`B`.`DIFF` <= 30)) then '0-30'
                else 'Current'
            end
            ) AS CATEGORY
            FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
                DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
                FROM (
                SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                    INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
                WHERE HKONT IN ('2100110202', '2100110201', '2100110204') AND GJAHR = YEAR('$date')
            ) A ) B");
        for($i=2; $i <= 6; $i++) {
            $temp_table = $db->query("CREATE TEMPORARY TABLE zxc$i
                SELECT B.*, (case
                    when (`B`.`DIFF` > 180) then '>180'
                    when ((`B`.`DIFF` >= 91)
                    and (`B`.`DIFF` <= 180)) then '91-180'
                    when ((`B`.`DIFF` >= 61)
                    and (`B`.`DIFF` <= 90)) then '61-90'
                    when ((`B`.`DIFF` >= 31)
                    and (`B`.`DIFF` <= 60)) then '31-60'
                    when ((`B`.`DIFF` >= 0)
                    and (`B`.`DIFF` <= 30)) then '0-30'
                    else 'Current'
                end
                ) AS CATEGORY
                FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
                    DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
                    FROM (
                    SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                        INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
                    WHERE HKONT IN ('2100110202', '2100110201', '2100110204') AND GJAHR = YEAR('$date')
                ) A ) B");
        }
        $document_aging_utang = $db->query("SELECT SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * -1
            WHEN SHKZG = 'H' THEN DMBTR * 1 END) AS total, C.CATEGORY AS category
            FROM (SELECT * FROM zxc) C GROUP BY C.category ORDER BY C.category")->getResultArray();
        $document_aging_ppn = $db->query("SELECT 
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc WHERE CATEGORY = '0-30')) AS '0-30',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc2 WHERE CATEGORY = '31-60')) AS '31-60',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc3 WHERE CATEGORY = '61-90')) AS '61-90',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc4 WHERE CATEGORY = '>180')) AS '>180',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc5 WHERE CATEGORY = '91-180')) AS '91-180',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc6 WHERE CATEGORY = 'Current')) AS 'Current'")->getRowArray();
        $grouped_array = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0);
        $sum = 0;
        foreach ($document_aging_utang as $d) {
            $temp = $d['total'] - $document_aging_ppn[$d['category']];
            $sum += $temp;
            $grouped_array[$d['category']] = $temp;
        }
        $grouped_array['total'] = $sum;
        return $grouped_array;
    }

    private function agingAccountNetInvoiceReceive($year, $date)
    {
        $db = Database::connect();
        for($i=1; $i <= 6; $i++) {
            $temp_table = $db->query("CREATE TEMPORARY TABLE xyz$i
                SELECT B.*, (case
                    when (`B`.`DIFF` > 180) then '>180'
                    when ((`B`.`DIFF` >= 91)
                    and (`B`.`DIFF` <= 180)) then '91-180'
                    when ((`B`.`DIFF` >= 61)
                    and (`B`.`DIFF` <= 90)) then '61-90'
                    when ((`B`.`DIFF` >= 31)
                    and (`B`.`DIFF` <= 60)) then '31-60'
                    when ((`B`.`DIFF` >= 0)
                    and (`B`.`DIFF` <= 30)) then '0-30'
                    else 'Current'
                end
                ) AS CATEGORY
                FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
                    DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
                    FROM (
                    SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                        INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
                    WHERE HKONT IN ('1100210101') AND GJAHR = YEAR('$date')
                ) A ) B");
        }
        $document_aging_utang = $db->query("SELECT SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1 END) AS total, C.CATEGORY AS category
            FROM (SELECT * FROM xyz1) C GROUP BY C.category ORDER BY C.category")->getResultArray();
        $document_aging_ppn = $db->query("SELECT 
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '2100420111' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM xyz1 WHERE CATEGORY = '0-30')) AS '0-30',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '2100420111' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM xyz2 WHERE CATEGORY = '31-60')) AS '31-60',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '2100420111' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM xyz3 WHERE CATEGORY = '61-90')) AS '61-90',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '2100420111' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM xyz4 WHERE CATEGORY = '>180')) AS '>180',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '2100420111' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM xyz5 WHERE CATEGORY = '91-180')) AS '91-180',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 11
                    WHEN SHKZG = 'H' THEN DMBTR * -1 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '2100420111' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM xyz6 WHERE CATEGORY = 'Current')) AS 'Current'")->getRowArray();
        $grouped_array = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0);
        $sum = 0;
        foreach ($document_aging_utang as $d) {
            $temp = $d['total'] - $document_aging_ppn[$d['category']];
            $sum += $temp;
            $grouped_array[$d['category']] = $temp;
        }
        $grouped_array['total'] = $sum;
        return $grouped_array;
    }

    private function agingAccountInvoiceReceivable($year, $date)
    {
        $db = Database::connect();
        $document_aging = $db->query("SELECT SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * -1
            WHEN SHKZG = 'H' THEN DMBTR * -1 END) AS total, C.CATEGORY AS category
            FROM (SELECT B.*, (case
            when (`B`.`DIFF` > 180) then '>180'
            when ((`B`.`DIFF` >= 91)
            and (`B`.`DIFF` <= 180)) then '91-180'
            when ((`B`.`DIFF` >= 61)
            and (`B`.`DIFF` <= 90)) then '61-90'
            when ((`B`.`DIFF` >= 31)
            and (`B`.`DIFF` <= 60)) then '31-60'
            when ((`B`.`DIFF` >= 0)
            and (`B`.`DIFF` <= 30)) then '0-30'
            else 'Current'
        end
        ) AS CATEGORY
        FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
            DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
            FROM (
            SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
            WHERE HKONT IN ('1100210101') AND GJAHR = YEAR('$date')
        ) A ) B) C WHERE C.GJAHR = $year GROUP BY C.category ORDER BY C.category")->getResultArray();
        $grouped_array = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0);
        $sum = 0;
        foreach ($document_aging as $d) {
            $grouped_array[$d['category']] = $d['total'];
            $sum += $d['total'];
        }
        $grouped_array['total'] = $sum;
        return $grouped_array;
    }

    private function agingARBuktiPotong($year, $date)
    {
        $db = Database::connect();
        for($i=1; $i <= 6; $i++) {
            $temp_table = $db->query("CREATE TEMPORARY TABLE qwe$i 
            SELECT
                B.*,
                (case
                    when (`B`.`DIFF` > 180) then '>180'
                    when ((`B`.`DIFF` >= 91)
                    and (`B`.`DIFF` <= 180)) then '91-180'
                    when ((`B`.`DIFF` >= 61)
                    and (`B`.`DIFF` <= 90)) then '61-90'
                    when ((`B`.`DIFF` >= 31)
                    and (`B`.`DIFF` <= 60)) then '31-60'
                    when ((`B`.`DIFF` >= 0)
                    and (`B`.`DIFF` <= 30)) then '0-30'
                    else 'Current'
                end ) AS CATEGORY
            FROM
                (
                SELECT
                    BELNR,
                    ZFBDT,
                    DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT,
                    '$date' AS TODAY,
                    DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF,
                    SHKZG,
                    DMBTR,
                    GJAHR,
                    BUKRS
                FROM
                    (
                    SELECT
                        fat2.*,
                        z.ZBDIT
                    FROM
                        FI_ACT_TRS fat2
                    INNER JOIN ZTERM z ON
                        z.ZTERM = fat2.ZTERM
                    WHERE
                        BELNR IN (
                        SELECT
                            BELNR
                        FROM
                            FI_ACT_TRS fat
                        WHERE
                            HKONT = '1100610053')
                        AND HKONT = '1100210101'
                        AND GJAHR = YEAR('$date') AND BUKRS = 'HH10') A ) B");
        }
        $bukti_potong = $db->query("SELECT SUM(
                CASE WHEN SHKZG = 'S' THEN DMBTR * 1
                WHEN SHKZG = 'H' THEN DMBTR * -1 END) AS total, C.CATEGORY AS category
            FROM (SELECT * FROM qwe1) C GROUP BY C.category ORDER BY C.category")
            ->getResultArray();
        $fi_bkt_potong = $db->query("SELECT 
            (SELECT 
                COALESCE(SUM(DMBTR), 0) 
                    FROM FI_BKT_PTG WHERE BKP = 1 AND (NBKP != '' OR NBKP IS NOT NULL) 
                    AND BELNR IN (SELECT BELNR FROM qwe1 WHERE CATEGORY = '0-30')) AS '0-30',
            (SELECT 
                COALESCE(SUM(DMBTR), 0) 
                    FROM FI_BKT_PTG WHERE BKP = 1 AND (NBKP != '' OR NBKP IS NOT NULL) 
                    AND BELNR IN (SELECT BELNR FROM qwe2 WHERE CATEGORY = '31-60')) AS '31-60',
            (SELECT 
                COALESCE(SUM(DMBTR), 0) 
                    FROM FI_BKT_PTG WHERE BKP = 1 AND (NBKP != '' OR NBKP IS NOT NULL) 
                    AND BELNR IN (SELECT BELNR FROM qwe3 WHERE CATEGORY = '61-90')) AS '61-90',
            (SELECT 
                COALESCE(SUM(DMBTR), 0) 
                    FROM FI_BKT_PTG WHERE BKP = 1 AND (NBKP != '' OR NBKP IS NOT NULL) 
                    AND BELNR IN (SELECT BELNR FROM qwe4 WHERE CATEGORY = '91-180')) AS '91-180',
            (SELECT 
                COALESCE(SUM(DMBTR), 0) 
                    FROM FI_BKT_PTG WHERE BKP = 1 AND (NBKP != '' OR NBKP IS NOT NULL) 
                    AND BELNR IN (SELECT BELNR FROM qwe5 WHERE CATEGORY = '>180')) AS '>180',
            (SELECT 
                COALESCE(SUM(DMBTR), 0) 
                    FROM FI_BKT_PTG WHERE BKP = 1 AND (NBKP != '' OR NBKP IS NOT NULL) 
                    AND BELNR IN (SELECT BELNR FROM qwe6 WHERE CATEGORY = 'Current')) AS 'Current'")->getRowArray();
        $grouped_array = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0);
        $sum = 0;
        foreach ($bukti_potong as $d) {
            $temp = $d['total'] - $fi_bkt_potong[$d['category']];
            $sum += $temp;
            $grouped_array[$d['category']] = $temp;
        }
        $grouped_array['total'] = $sum;
        return $grouped_array;
    }

    // cashflow
    public function index()
    {
        $data['title'] = "Cash Flow";
        $FiActBal = new FiActBalance();
        $builder_bal = $FiActBal->builder();

        $data['years'] = $builder_bal->select("DISTINCT(FISC)")->orderBy('FISC', 'desc')->get()->getResultArray();

        // filter month and date
        $month = $_GET['month'] ?? false;
        $year = $_GET['year'] ?? false;

        $now = Time::now();
        $parsed = Time::parse($now);
        if (!$month && !$year) {
            $month = $parsed->getMonth();
            $year = $parsed->getYear();
        }
        $data['selectedParams'] = ['month' => $month, 'year' => $year];

        $data['todayDate'] = ['month' => $parsed->getMonth(), 'year' => $parsed->getYear()];

        // only for balance
        $month_balance = $month;
        $year_balance = $year - 1;
        $year_balance_mtd = $year;

        // BEGINNING BALANCE
        $data['beginning_balance_ytd'] = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_BAL.COMP AND fmg.SAKNR = FI_ACT_BAL.GL_ACCOUNT")
            ->where("COMP = 'HH10'")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("GL_ACCOUNT LIKE '11%'")
            ->where("FISC = $year_balance")
            ->where("FI = 16")
            ->get()->getRowArray();

        if ($month == 1) {
            $month_balance = 16;
            $year_balance_mtd--;
        } else {
            $month_balance--;
        }


        $data['beginning_balance_mtd'] = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = COMP AND fmg.SAKNR = GL_ACCOUNT")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11%'")
            ->where("FDLEV IN ('ZC', 'ZB')")
            ->where("FISC = $year_balance_mtd")
            ->where("FI = $month_balance")
            ->get()->getRowArray();


        $FiActTrs = new FiActTrs();
        $builder = $FiActTrs->builder();

        // OPERATING
        $operating_step_1_ytd = $builder->select("SUM(
                CASE WHEN SHKZG = 'S' THEN DMBTR * 1
                WHEN SHKZG = 'H' THEN DMBTR * -1
                END
            ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("HKONT LIKE '11%'")
            ->where("fmg.BUKRS = 'HH10'")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("GJAHR = $year")
            ->where("SGTXT LIKE '1___;%'")
            ->get()->getRowArray();
        $operating_step_2_ytd = $builder->select("SUM(
                CASE WHEN SHKZG = 'S' THEN DMBTR * 1
                WHEN SHKZG = 'H' THEN DMBTR * -1
                END
            ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("HKONT LIKE '11%'")
            ->where("fmg.BUKRS = 'HH10'")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("GJAHR = $year")
            ->where("SGTXT LIKE '2___;%'")
            ->get()->getRowArray();
        $data['operating_ytd'] = $operating_step_1_ytd['total'] + $operating_step_2_ytd['total'];
        $operating_step_1_mtd = $builder->select("SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1
            END
        ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->where("SGTXT LIKE '1___;%'")
            ->get()->getRowArray();
        $operating_step_2_mtd = $builder->select("SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1
            END
        ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->where("SGTXT LIKE '2___;%'")
            ->get()->getRowArray();
        $data['operating_mtd'] = $operating_step_1_mtd['total'] + $operating_step_2_mtd['total'];

        // INVESTING
        $investing_step_1_mtd = $builder->select("SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1
            END
        ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->where("SGTXT LIKE '3___;%'")
            ->get()->getRowArray();
        $investing_step_2_mtd = $builder->select("SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1
            END
        ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->where("SGTXT LIKE '4___;%'")
            ->get()->getRowArray();
        $data['investing_mtd'] = $investing_step_1_mtd['total'] + $investing_step_2_mtd['total'];
        $investing_step_1_ytd = $builder->select("SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1
            END
        ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("SGTXT LIKE '3___;%'")
            ->get()->getRowArray();
        $investing_step_2_ytd = $builder->select("SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1
            END
        ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("SGTXT LIKE '4___;%'")
            ->get()->getRowArray();
        $data['investing_ytd'] = $investing_step_1_ytd['total'] + $investing_step_2_ytd['total'];

        // FINANCING
        $financing_step_1_ytd = $builder->select("SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1
            END
        ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("fmg.BUKRS = 'HH10'")
            ->where("GJAHR = $year")
            ->where("SGTXT LIKE '5___;%'")
            ->get()->getRowArray();
        $financing_step_2_ytd = $builder->select("SUM(DMBTR * 1) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("SHKZG = 'S'")
            ->where("fmg.BUKRS = 'HH10'")
            ->where("SGTXT LIKE '%Valuation on%'")
            ->get()->getRowArray();
        $financing_step_3_ytd = $builder->select("SUM(
                CASE WHEN SHKZG = 'S' THEN DMBTR * 1
                WHEN SHKZG = 'H' THEN DMBTR * -1
                END
            ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("fmg.BUKRS = 'HH10'")
            ->where("SGTXT LIKE '6___;%'")
            ->get()->getRowArray();
        $financing_step_4_ytd = $builder->select("SUM(DMBTR * -1) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("SHKZG = 'H'")
            ->where("fmg.BUKRS = 'HH10'")
            ->where("SGTXT LIKE '%Valuation on%'")
            ->get()->getRowArray();
        // dd($financing_step_1_ytd['total'], $financing_step_2_ytd['total'], $financing_step_3_ytd['total'], $financing_step_4_ytd['total']);
        $data['financing_ytd'] = (($financing_step_1_ytd['total'] + $financing_step_2_ytd['total']) + ($financing_step_3_ytd['total'] + $financing_step_4_ytd['total']));
        $financing_step_1_mtd = $builder->select("SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1
            END
        ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->where("SGTXT LIKE '5___;%'")
            ->get()->getRowArray();
        $financing_step_2_mtd = $builder->select("SUM(DMBTR * 1) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->where("SHKZG = 'S'")
            ->where("SGTXT LIKE '%Valuation on%'")
            ->get()->getRowArray();
        $financing_step_3_mtd = $builder->select("SUM(
                CASE WHEN SHKZG = 'S' THEN DMBTR * 1
                WHEN SHKZG = 'H' THEN DMBTR * -1
                END
            ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->where("SGTXT LIKE '6___;%'")
            ->get()->getRowArray();
        $financing_step_4_mtd = $builder->select("SUM(DMBTR * 1) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->where("SHKZG = 'H'")
            ->where("SGTXT LIKE '%Valuation on%'")
            ->get()->getRowArray();
        $data['financing_mtd'] = (($financing_step_1_mtd['total'] + $financing_step_2_mtd['total']) + ($financing_step_3_mtd['total'] + $financing_step_4_mtd['total']));
        // Free cash flow
        $data['free_cash_ytd'] = $data['operating_ytd'] - $data['investing_ytd'];
        $data['free_cash_mtd'] = $data['operating_mtd'] - $data['investing_mtd'];

        // net cash flow
        // $data['net_cash_ytd'] = $data['free_cash_ytd'] + $data['financing_ytd'];
        // $data['net_cash_mtd'] = $data['free_cash_mtd'] + $data['financing_mtd'];
        $data['net_cash_ytd'] = $data['operating_ytd'] + $data['financing_ytd'] + $data['investing_ytd'];
        $data['net_cash_mtd'] = $data['operating_mtd'] + $data['financing_mtd'] + $data['investing_mtd'];

        // ending balance
        $data['ending_balance_ytd'] = $data['beginning_balance_ytd']['BALANCE'] + $data['net_cash_ytd'];
        $data['ending_balance_mtd'] = $data['beginning_balance_mtd']['BALANCE'] + $data['net_cash_mtd'];

        // * -------- Cash Convertion Cycle ------------

        // by year
        if ($year == $parsed->getYear()) {
            $total_days = $parsed->getDayOfYear();
        } else {
            // get total days of the selected year
            $start_d = Time::createFromDate($year, 1, 1);
            $end_d = Time::createFromDate($year, 12, 31);
            $diff = $start_d->difference($end_d);
            $total_days = $diff->days;
        }

        // by month
        if ($year == $parsed->getYear() && $month = $parsed->getMonth()) {
            $total_days_month = $parsed->getDay();
        } else {
            // get total days of the selected year
            $total_days_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }
        // Days Inventory

        $step_1_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT >= 1194500000 AND GL_ACCOUNT <= 1195599999) OR GL_ACCOUNT LIKE '11005%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $step_2_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '5%')")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $step_1_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT >= 1194500000 AND GL_ACCOUNT <= 1195599999) OR GL_ACCOUNT LIKE '11005%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_2_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '5%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // days inventory
        // (step 1 / step 2) * jumlah hari dari tanggal 1 januari sampai sekarang
        $days_inventory_ytd = $step_2_ytd['BALANCE'] != 0 ? ($step_1_ytd['BALANCE'] / $step_2_ytd['BALANCE']) * $total_days : 0;
        $days_inventory_mtd = $step_2_mtd['BALANCE'] != 0 ? ($step_1_mtd['BALANCE'] / $step_2_mtd['BALANCE']) * $total_days_month : 0;
        $data['days_inventory_ytd'] = $days_inventory_ytd;
        $data['days_inventory_mtd'] = $days_inventory_mtd;

        // days sales

        $step_1_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT >= 1100210000 AND GL_ACCOUNT <= 1100219999) 
                OR (GL_ACCOUNT >= 1100290000 AND GL_ACCOUNT <= 1100299999)
                OR (GL_ACCOUNT >= 1200010000 AND GL_ACCOUNT <= 1200019999)")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $step_2_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '4%')")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $step_1_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT >= 1100210000 AND GL_ACCOUNT <= 1100219999) 
                OR (GL_ACCOUNT >= 1100290000 AND GL_ACCOUNT <= 1100299999)
                OR (GL_ACCOUNT >= 1200010000 AND GL_ACCOUNT <= 1200019999)")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_2_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '4%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // days sales
        // (step 1 / step 2) * jumlah hari dari tanggal 1 januari sampai sekarang
        $days_sales_ytd = $step_2_ytd['BALANCE'] != 0 ? ($step_1_ytd['BALANCE'] / $step_2_ytd['BALANCE']) * $total_days : 0;
        $days_sales_mtd = $step_2_mtd['BALANCE'] != 0 ? ($step_1_mtd['BALANCE'] / $step_2_mtd['BALANCE']) * $total_days_month : 0;
        $data['days_sales_ytd'] = $days_sales_ytd;
        $data['days_sales_mtd'] = $days_sales_mtd;


        // days Payable

        $step_1_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT >= 2100110000 AND GL_ACCOUNT <= 2100110220) 
                OR (GL_ACCOUNT >= 2200110000 AND GL_ACCOUNT <= 2200119999)
                OR (GL_ACCOUNT >= 2100110221 AND GL_ACCOUNT <= 2100110250)
                OR (GL_ACCOUNT >= 2100590000 AND GL_ACCOUNT <= 2100599999)")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $step_2_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '5%')")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $step_1_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT >= 2100110000 AND GL_ACCOUNT <= 2100110220) 
                OR (GL_ACCOUNT >= 2200110000 AND GL_ACCOUNT <= 2200119999)
                OR (GL_ACCOUNT >= 2100110221 AND GL_ACCOUNT <= 2100110250)
                OR (GL_ACCOUNT >= 2100590000 AND GL_ACCOUNT <= 2100599999)")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_2_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '5%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // days payable
        // (step 1 / step 2) * jumlah hari dari tanggal 1 januari sampai sekarang
        $days_payable_ytd = $step_2_ytd['BALANCE'] != 0 ? ($step_1_ytd['BALANCE'] / $step_2_ytd['BALANCE']) * $total_days : 0;
        $days_payable_mtd = $step_2_mtd['BALANCE'] != 0 ? ($step_1_mtd['BALANCE'] / $step_2_mtd['BALANCE']) * $total_days_month : 0;
        $data['days_payable_ytd'] = $days_payable_ytd;
        $data['days_payable_mtd'] = $days_payable_mtd;

        $cash_convertion_cycle_ytd = $days_inventory_ytd + $days_sales_ytd + $days_payable_ytd;
        $cash_convertion_cycle_mtd = $days_inventory_mtd + $days_sales_mtd + $days_payable_mtd;

        $data['cash_convertion_cycle_ytd'] = $cash_convertion_cycle_ytd;
        $data['cash_convertion_cycle_mtd'] = $cash_convertion_cycle_mtd;


        // * -------- Cash Cost ------------

        // total cost

        $total_cost_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '5%' 
                OR (GL_ACCOUNT LIKE '6%')
                OR (GL_ACCOUNT >= 9100010000 AND GL_ACCOUNT <= 9100019999)
                OR (GL_ACCOUNT >= 9200020000 AND GL_ACCOUNT <= 9200029999)")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->where("zc.ID = 'INTEREST'")
            ->get()->getRowArray();
        $total_cost_mtd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '5%' 
                OR (GL_ACCOUNT LIKE '6%')
                OR (GL_ACCOUNT >= 9100010000 AND GL_ACCOUNT <= 9100019999)
                OR (GL_ACCOUNT >= 9200020000 AND GL_ACCOUNT <= 9200029999)")
            ->where("zc.ID = 'INTEREST'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['total_cost_ytd'] = $total_cost_ytd['BALANCE'];
        $data['total_cost_mtd'] = $total_cost_mtd['BALANCE'];

        // cash cost

        $forex_unrelease_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'FOR_UN'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $forex_unrelease_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'FOR_UN'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $depreciation_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DEP'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $depreciation_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DEP'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $amortization_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'AMRT'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $amortization_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'AMRT'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // cash cost
        // Total Cost – Forex Unrelease – Depreciation  – Amortization Deff. Cost
        $cash_cost_ytd = $total_cost_ytd['BALANCE'] - $forex_unrelease_ytd['BALANCE'] - $depreciation_ytd['BALANCE']
            - $amortization_ytd['BALANCE'];
        $cash_cost_mtd = $total_cost_mtd['BALANCE'] - $forex_unrelease_mtd['BALANCE'] - $depreciation_mtd['BALANCE']
            - $amortization_mtd['BALANCE'];
        $data['cash_cost_ytd'] = $cash_cost_ytd;
        $data['cash_cost_mtd'] = $cash_cost_mtd;

        // variace = total cost - cash cost
        $variance_ytd = $total_cost_ytd['BALANCE'] - $cash_cost_ytd;
        $variance_mtd = $total_cost_mtd['BALANCE'] - $cash_cost_mtd;

        $data['variance_ytd'] = $variance_ytd;
        $data['variance_mtd'] = $variance_mtd;

        echo view("pages/finance/cashflow", $data);
    }

    public function profitability()
    {
        $data['title'] = "Profitability";

        // filter month and date
        $month = $_GET['month'] ?? false;
        $year = $_GET['year'] ?? false;

        $now = Time::now();
        $parsed = Time::parse($now);
        if (!$month && !$year) {
            $month = $parsed->getMonth();
            $year = $parsed->getYear();
        }
        $data['selectedParams'] = ['month' => $month, 'year' => $year];

        $data['todayDate'] = ['month' => $parsed->getMonth(), 'year' => $parsed->getYear()];

        $FiActBal = new FiActBalance();
        $builder = $FiActBal->builder();

        // list of years
        $data['years'] = $builder->select("DISTINCT(FISC)")->where("GL_ACCOUNT LIKE '4%'")->orderBy('FISC', 'desc')->get()->getResultArray();

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
        // untuk gl account 71 interest kali -1
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
            ->where("(GL_ACCOUNT LIKE '910001%' OR GL_ACCOUNT LIKE '920002%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // EAT
        $data['eat_ytd'] = $data['ebt_ytd'] - $data['tax_ytd']['BALANCE'];
        $data['eat_mtd'] = $data['ebt_mtd'] - $data['tax_mtd']['BALANCE'];

        // COGS MARGIN
        $cogs = $builder->select("(SELECT SUM(BALANCE) FROM FI_ACT_BAL fab 
            WHERE GL_ACCOUNT LIKE '5%' AND FISC = $year AND fi = 16 AND COMP = 'HH10') AS year, SUM(PER_SALES) AS month")
            ->where("GL_ACCOUNT LIKE '5%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->where("COMP = 'HH10'")
            ->get()->getRowArray();
        $revenue = $builder->select("ABS(SUM(PER_SALES)) AS month, (SELECT ABS(SUM(BALANCE)) FROM FI_ACT_BAL fab 
            WHERE GL_ACCOUNT LIKE '4%' AND FISC = $year AND fi = 16 AND COMP = 'HH10') AS year")
            ->where("GL_ACCOUNT LIKE '4%'")
            ->where("FISC = $year")
            ->where("COMP = 'HH10'")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['cogs_ratio_ytd'] = $revenue['year'] != 0 ? ($cogs['year'] / $revenue['year']) * 100 : 0;
        $data['cogs_ratio_mtd'] = $revenue['month'] != 0 ? ($cogs['month'] / $revenue['month']) * 100 : 0;

        $data['gross_profit_margin_ytd'] = $revenue['year'] != 0 ? (($revenue['year'] - $cogs['year']) / $revenue['year']) * 100 : 0;
        $data['gross_profit_margin_mtd'] = $revenue['month'] != 0 ? (($revenue['month'] - $cogs['month']) / $revenue['month']) * 100 : 0;

        $operating_expense = $builder->select("SUM(PER_SALES) AS month, (SELECT SUM(BALANCE) FROM FI_ACT_BAL fab 
            WHERE GL_ACCOUNT LIKE '6%' AND FISC = $year AND fi = 16) AS year")
            ->where("GL_ACCOUNT LIKE '6%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['opr_profit_margin_ytd'] = $revenue['year'] != 0 ? (($revenue['year'] - $cogs['year'] - $operating_expense['year']) / $revenue['year']) * 100 : 0;
        $data['opr_profit_margin_mtd'] = $revenue['month'] != 0 ? (($revenue['month'] - $cogs['month'] - $operating_expense['month']) / $revenue['month']) * 100 : 0;

        $other_expense = $builder->select("SUM(PER_SALES) AS month, (SELECT SUM(BALANCE) FROM FI_ACT_BAL fab 
            WHERE GL_ACCOUNT LIKE '7%' AND FISC = $year AND fi = 16) AS year")
            ->where("GL_ACCOUNT LIKE '7%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['ebt_margin_ytd'] = $revenue['year'] != 0 ? (($revenue['year'] - $cogs['year'] - $operating_expense['year'] - $other_expense['year']) / $revenue['year']) * 100 : 0;
        $data['ebt_margin_mtd'] = $revenue['month'] != 0 ? (($revenue['month'] - $cogs['month'] - $operating_expense['month'] - $other_expense['month']) / $revenue['month']) * 100 : 0;

        // eat margin
        $tax = $builder->select("SUM(PER_SALES) AS month, (SELECT SUM(BALANCE) FROM FI_ACT_BAL fab 
            WHERE GL_ACCOUNT LIKE '910001%' AND FISC = $year AND fi = 16) AS year")
            ->where("GL_ACCOUNT LIKE '910001%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['eat_margin_ytd'] = $revenue['year'] != 0 ? (($revenue['year'] - $cogs['year'] - $operating_expense['year'] - $other_expense['year'] - $tax['year']) / $revenue['year']) * 100 : 0;
        $data['eat_margin_mtd'] = $revenue['month'] != 0 ? (($revenue['month'] - $cogs['month'] - $operating_expense['month'] - $other_expense['month'] - $tax['month']) / $revenue['month']) * 100 : 0;

        $interest = $builder->select("SUM(PER_SALES) AS month, (SELECT SUM(BALANCE) FROM FI_ACT_BAL fab
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab.GL_ACCOUNT 
            WHERE zc.ID = 'INTEREST' AND FISC = $year AND fi = 16) AS year")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'INTEREST'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $tax_2 = $builder->select("SUM(PER_SALES) AS month, (SELECT SUM(BALANCE) FROM FI_ACT_BAL fab 
            WHERE (GL_ACCOUNT LIKE '910001%' OR GL_ACCOUNT LIKE '920002%') AND FISC = $year AND fi = 16) AS year")
            ->where("(GL_ACCOUNT LIKE '910001%' OR GL_ACCOUNT LIKE '920002%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $depreciation = $builder->select("SUM(PER_SALES) AS month, (SELECT SUM(BALANCE) FROM FI_ACT_BAL fab
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab.GL_ACCOUNT 
            WHERE zc.ID = 'DEP' AND FISC = $year AND fi = 16) AS year")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DEP'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $amortization = $builder->select("SUM(PER_SALES) AS month, (SELECT SUM(BALANCE) FROM FI_ACT_BAL fab
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab.GL_ACCOUNT 
            WHERE zc.ID = 'AMRT' AND FISC = $year AND fi = 16) AS year")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'AMRT'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['ebitda_margin_ytd'] = $revenue['year'] != 0 ? ((($revenue['year'] - $cogs['year'] - $operating_expense['year'] - $other_expense['year']) + ($interest['year'] + $tax_2['year'] + $depreciation['year'] + $amortization['year'])) / $revenue['year']) * 100 : 0;
        $data['ebitda_margin_mtd'] = $revenue['month'] != 0 ? ((($revenue['month'] - $cogs['month'] - $operating_expense['month'] - $other_expense['month']) + ($interest['month'] + $tax_2['month'] + $depreciation['month'] + $amortization['month'])) / $revenue['month']) * 100 : 0;

        $data['ebda_margin_ytd'] = $revenue['year'] != 0 ? ((($revenue['year'] - $cogs['year'] - $operating_expense['year'] - $other_expense['year']) + ($depreciation['year'] + $amortization['year'])) / $revenue['year']) * 100 : 0;
        $data['ebda_margin_mtd'] = $revenue['month'] != 0 ? ((($revenue['month'] - $cogs['month'] - $operating_expense['month'] - $other_expense['month']) + ($depreciation['month'] + $amortization['month'])) / $revenue['month']) * 100 : 0;

        $other_expense_2 = $builder->select("SUM(PER_SALES) AS month, (SELECT SUM(BALANCE) FROM FI_ACT_BAL fab 
            WHERE (GL_ACCOUNT LIKE '71%' OR GL_ACCOUNT LIKE '72%') AND FISC = $year AND fi = 16) AS year")
            ->where("(GL_ACCOUNT LIKE '71%' OR GL_ACCOUNT LIKE '72%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['net_profit_margin_ytd'] = $revenue['year'] != 0 ? (($revenue['year'] - $cogs['year'] - $operating_expense['year'] - $other_expense_2['year'] - $tax['year']) / $revenue['year']) * 100 : 0;
        $data['net_profit_margin_mtd'] = $revenue['month'] != 0 ? (($revenue['month'] - $cogs['month'] - $operating_expense['month'] - $other_expense_2['month'] - $tax['month']) / $revenue['month']) * 100 : 0;

        echo view("pages/finance/profitability", $data);
    }

    public function balanceSheet()
    {
        // filter month and date
        $month = $_GET['month'] ?? false;
        $year = $_GET['year'] ?? false;

        $now = Time::now();
        $parsed = Time::parse($now);
        if (!$month && !$year) {
            $month = $parsed->getMonth();
            $year = $parsed->getYear();
        }

        $data['title'] = "Cash Flow";
        $FiActBal = new FiActBalance();
        $builder_bal = $FiActBal->builder();

        $data['years'] = $builder_bal->select("DISTINCT(FISC)")->orderBy('FISC', 'desc')->get()->getResultArray();

        // current asset
        $current_asset_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $current_asset_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['current_asset_ytd'] = $current_asset_ytd['BALANCE'];
        $data['current_asset_mtd'] = $current_asset_mtd['BALANCE'];

        // non current asset
        $non_current_asset_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '12%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $non_current_asset_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '12%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['non_current_asset_ytd'] = $non_current_asset_ytd['BALANCE'];
        $data['non_current_asset_mtd'] = $non_current_asset_mtd['BALANCE'];

        // total asset
        $data['total_asset_ytd'] = $data['current_asset_ytd'] + $data['non_current_asset_ytd'];
        $data['total_asset_mtd'] = $data['current_asset_mtd'] + $data['non_current_asset_mtd'];

        // current liabilities
        $current_liabilities_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '21%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $current_liabilities_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '21%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['current_liabilities_ytd'] = $current_liabilities_ytd['BALANCE'];
        $data['current_liabilities_mtd'] = $current_liabilities_mtd['BALANCE'];

        // non current liabilities
        $non_current_liabilities_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '22%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $non_current_liabilities_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '22%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // Saldo pada Non Current Liabilities tidak termasuk 
        // GL 2200520000 – 2200529999 karena GL tersebut sudah ada di Non Controlling Interest.
        $gl_except_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '220052%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $gl_except_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '220052%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['non_current_liabilities_ytd'] = ($non_current_liabilities_ytd['BALANCE'] - $gl_except_ytd['BALANCE']);
        $data['non_current_liabilities_mtd'] = $non_current_liabilities_mtd['BALANCE'] - $gl_except_mtd['BALANCE'];

        // total liabilities
        $data['total_liabilities_ytd'] = $data['current_liabilities_ytd'] + $data['non_current_liabilities_ytd'];
        $data['total_liabilities_mtd'] = $data['current_liabilities_mtd'] + $data['non_current_liabilities_mtd'];

        // capital stock
        $capital_stock_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '31%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $capital_stock_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '31%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['capital_stock_ytd'] = $capital_stock_ytd['BALANCE'];
        $data['capital_stock_mtd'] = $capital_stock_mtd['BALANCE'];

        // additional paid-in capital
        $additional_paid_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '32%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $additional_paid_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '32%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['additional_capital_stock_ytd'] = $additional_paid_ytd['BALANCE'];
        $data['additional_capital_stock_mtd'] = $additional_paid_mtd['BALANCE'];

        // other comprehensive income
        $oci_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '33%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $oci_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '33%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['oci_ytd'] = $oci_ytd['BALANCE'];
        $data['oci_mtd'] = $oci_mtd['BALANCE'];

        // retained earning
        // additional paid-in capital + 34
        $penambah_retained_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '39%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $penambah_retained_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '39%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $penambah_retained_ytd2 = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '34%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $penambah_retained_mtd2 = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '34%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $eat = $this->getEat($year, $month);

        $data['retained_earning_ytd'] = $penambah_retained_ytd2['BALANCE'] + $penambah_retained_ytd['BALANCE'] + $eat['ytd'];
        $data['retained_earning_mtd'] = $penambah_retained_mtd2['BALANCE'] + $penambah_retained_mtd['BALANCE'] + $eat['mtd'];

        // stockholder equity
        $data['stockholder_equity_ytd'] = $capital_stock_ytd['BALANCE'] + $additional_paid_ytd['BALANCE'] + $oci_ytd['BALANCE']
            + $data['retained_earning_ytd'];
        $data['stockholder_equity_mtd'] = $capital_stock_mtd['BALANCE'] + $additional_paid_mtd['BALANCE'] + $oci_mtd['BALANCE']
            + $data['retained_earning_mtd'];
        // dd($capital_stock_ytd['BALANCE'], $additional_paid_ytd['BALANCE'], $oci_ytd['BALANCE'], $data['retained_earning_ytd']);
        // non controlling interest
        $nc_interest_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '220052%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $nc_interest_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '220052%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $data['nc_interest_ytd'] = $nc_interest_ytd['BALANCE'];
        $data['nc_interest_mtd'] = $nc_interest_mtd['BALANCE'];

        // totals equity
        $data['total_equity_ytd'] = $data['stockholder_equity_ytd'] + $data['nc_interest_ytd'];
        $data['total_equity_mtd'] = $data['stockholder_equity_mtd'] + $data['nc_interest_mtd'];

        // total liabilities & equity
        $data['total_liaequ_ytd'] = $data['total_liabilities_ytd'] + $data['total_equity_ytd'];
        $data['total_liaequ_mtd'] = $data['total_liabilities_mtd'] + $data['total_equity_mtd'];

        $data['selectedParams'] = ['month' => $month, 'year' => $year];

        $data['todayDate'] = ['month' => $parsed->getMonth(), 'year' => $parsed->getYear()];

        // ----------------------- CHARTS ---------------------------------

        $total_aset_lancar_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $total_aset_lancar_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $total_liabilitas_jangka_pendek_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '21%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $total_liabilitas_jangka_pendek_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '21%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $current_ratio_ytd = $total_liabilitas_jangka_pendek_ytd['BALANCE'] != 0 ? ($total_aset_lancar_ytd['BALANCE'] / $total_liabilitas_jangka_pendek_ytd['BALANCE']) * 100 : 0;
        $current_ratio_mtd = $total_liabilitas_jangka_pendek_mtd['BALANCE'] != 0 ? ($total_aset_lancar_mtd['BALANCE'] / $total_liabilitas_jangka_pendek_mtd['BALANCE']) * 100 : 0;
        $data['current_ratio_ytd'] = $current_ratio_ytd;
        $data['current_ratio_mtd'] = $current_ratio_mtd;

        // Quick Ratio

        // persediaan
        $persediaan_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11005%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $persediaan_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11005%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // bahan baku dan barang jadi
        $bahan_baku_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11005%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $bahan_baku_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11005%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // uang muka
        $uang_muka_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '110062%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $uang_muka_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '110062%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // total liabilitas jangka pendek
        $total_liabilitas_pendek_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '21%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $total_liabilitas_pendek_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '21%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // beban muka
        $beban_muka_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '110071%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $beban_muka_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '110071%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $quick_ratio_ytd = $total_liabilitas_pendek_ytd['BALANCE'] != 0 ? (($total_aset_lancar_ytd['BALANCE'] - $persediaan_ytd['BALANCE']
            - $bahan_baku_ytd['BALANCE'] - $uang_muka_ytd['BALANCE'] - $beban_muka_ytd['BALANCE']) / $total_liabilitas_pendek_ytd['BALANCE']) * 100 : 0;
        $quick_ratio_mtd = $total_liabilitas_pendek_mtd['BALANCE'] != 0 ? (($total_aset_lancar_mtd['BALANCE'] - $persediaan_mtd['BALANCE']
            - $bahan_baku_mtd['BALANCE'] - $uang_muka_mtd['BALANCE'] - $beban_muka_mtd['BALANCE']) / $total_liabilitas_pendek_mtd['BALANCE']) * 100 : 0;
        $data['quick_ratio_ytd'] = $quick_ratio_ytd;
        $data['quick_ratio_mtd'] = $quick_ratio_mtd;

        // cash ratio

        // kas
        $kas_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT BETWEEN 1100010101 AND 1100010799")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $kas_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT BETWEEN 1100010101 AND 1100010799")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $bank_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '111001%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $bank_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '111001%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $cash_ratio_ytd = $total_liabilitas_pendek_ytd['BALANCE'] != 0 ? (($kas_ytd['BALANCE'] + $bank_ytd['BALANCE']) / $total_liabilitas_pendek_ytd['BALANCE']) * 100 : 0;
        $cash_ratio_mtd = $total_liabilitas_pendek_mtd['BALANCE'] != 0 ? (($kas_mtd['BALANCE'] + $bank_mtd['BALANCE']) / $total_liabilitas_pendek_mtd['BALANCE']) * 100 : 0;
        $data['cash_ratio_ytd'] = $cash_ratio_ytd;
        $data['cash_ratio_mtd'] = $cash_ratio_mtd;

        // ------------------------- LEVERAGE RATIO ----------------------------

        // saldo gl kepala 4
        $saldo_gl_4_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '43%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $saldo_gl_4_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '43%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // saldo gl kepala 5
        $saldo_gl_5_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '5%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $saldo_gl_5_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '5%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // saldo gl kepala 6
        $saldo_gl_6_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT BETWEEN 6100000000 AND 6399999999")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $saldo_gl_6_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT BETWEEN 6100000000 AND 6399999999")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // saldo gl kepala 7
        $saldo_gl_7_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT BETWEEN 7100000000 AND 7999999999")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $saldo_gl_7_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT BETWEEN 7100000000 AND 7999999999")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // interest
        $interest_mtd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'INTEREST'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $interest_ytd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'INTEREST'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();

        // tax
        // gl 91 - gl 92
        $saldo_gl_91_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '910001%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $saldo_gl_91_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '910001%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $saldo_gl_92_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '920002%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $saldo_gl_92_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '920002%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $tax_ytd = $saldo_gl_91_ytd['BALANCE'] - $saldo_gl_92_ytd['BALANCE'];
        $tax_mtd = $saldo_gl_91_mtd['BALANCE'] - $saldo_gl_92_mtd['BALANCE'];

        // Depreciation
        $depreciation_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DEP'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $depreciation_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DEP'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();

        // Amortization
        $amortization_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'AMRT'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $amortization_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'AMRT'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();

        // Interest Expense
        $interest_exp_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'INTEREST_EXPENSE'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $interest_exp_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'INTEREST_EXPENSE'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();

        // Interest Expense
        $cpltd_exp_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'CPLTD'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $cpltd_exp_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'CPLTD'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();

        // debt service coverage ratio
        if ($interest_exp_ytd['BALANCE'] + $cpltd_exp_ytd['BALANCE'] != 0 && $interest_exp_mtd['BALANCE'] + $cpltd_exp_mtd['BALANCE'] != 0) {
            $debt_service_ytd = (((($saldo_gl_4_ytd['BALANCE'] - $saldo_gl_5_ytd['BALANCE'] - $saldo_gl_6_ytd['BALANCE'] - $saldo_gl_7_ytd['BALANCE'])
                - ($interest_ytd['BALANCE'] + $tax_ytd + $depreciation_ytd['BALANCE'] + $amortization_ytd['BALANCE'])) / $saldo_gl_4_ytd['BALANCE'])
                / ($interest_exp_ytd['BALANCE'] + $cpltd_exp_ytd['BALANCE'])) * 100;
            $debt_service_mtd = (((($saldo_gl_4_mtd['BALANCE'] - $saldo_gl_5_mtd['BALANCE'] - $saldo_gl_6_mtd['BALANCE'] - $saldo_gl_7_mtd['BALANCE'])
                - ($interest_mtd['BALANCE'] + $tax_mtd + $depreciation_mtd['BALANCE'] + $amortization_mtd['BALANCE'])) / $saldo_gl_4_mtd['BALANCE'])
                / ($interest_exp_mtd['BALANCE'] + $cpltd_exp_mtd['BALANCE'])) * 100;
        } else {
            $debt_service_ytd = 0;
            $debt_service_mtd = 0;
        }
        // $data['debt_service_ytd'] = $debt_service_ytd;
        // $data['debt_service_mtd'] = $debt_service_mtd;
        $data['debt_service_ytd'] = 8.28120409478657 * 100;
        $data['debt_service_mtd'] = 27774.3675685421 * 100;

        // DER

        // total liability
        $total_liability_mtd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'LBL'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $total_liability_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'LBL'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();

        // total equity
        $total_equity_mtd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'EQT'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $total_equity_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'EQT'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();

        $der_ytd = $total_liability_ytd['BALANCE'] - $total_equity_ytd['BALANCE'];
        $der_mtd = $total_liability_mtd['BALANCE'] - $total_equity_mtd['BALANCE'];
        // $data['der_ytd'] = $der_ytd;
        // $data['der_mtd'] = $der_mtd;
        $data['der_ytd'] = 0.889173619043731 * 100;
        $data['der_mtd'] = 1.10779490951993 * 100;

        // equity ratio

        // saldo kepala gl 3
        $saldo_gl_3_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '3%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $saldo_gl_3_mtd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '3%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // saldo gl kepala 1
        $saldo_gl_1_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '1%'")
            ->where("FISC = $year")
            ->where("FI = 16")
            ->get()->getRowArray();
        $saldo_gl_1_mtd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '1%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $equity_ratio_ytd = ($saldo_gl_3_ytd['BALANCE'] / $saldo_gl_1_ytd['BALANCE']) * 100;
        $equity_ratio_mtd = ($saldo_gl_3_mtd['BALANCE'] / $saldo_gl_1_mtd['BALANCE']) * 100;
        $data['equ_ytd'] = $equity_ratio_ytd;
        $data['equ_mtd'] = $equity_ratio_mtd;

        // ------------ movement account payable ------------------

        // trade payable = 2100110201
        //  Non Trade Payable = 2200110001
        // Bank/Leasing Payable = 2200010601

        $indicator = $_GET['indicator'] ?? false;

        if (!$indicator) {
            $indicator = '2100110201';
        }

        $data['type_ap'] = $indicator;

        // Account payable

        // nama variable harusnya ap biar lebih jelas 
        // tapi karna sudah terlanjur jadi dibiarkan saja.
        // mohon maaf
        $trade_payable = $builder_bal->select("SUM(BALANCE) AS previous, FISC, SUM(DEBIT_PER) AS increase, 
            SUM(CREDIT_PER) AS decrease, (SUM(BALANCE) + SUM(DEBIT_PER) + SUM(CREDIT_PER)) AS os_today")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT = '$indicator'")
            ->where("FI = 16")
            ->groupBy('FISC')
            ->orderBy('FISC', 'DESC')
            ->limit(3)
            ->get()->getResultArray();
        // $data['trade_payable'] = $trade_payable;
        // dummy data below
        if($indicator == '2100110201') {
            $data['trade_payable'] = array(
                array("FISC" => 2022, 'previous' => -63484512736, 
                    'increase' => 802129854525, 'decrease' => 840393706311, 'os_today' => -101748364522.00),
                array("FISC" => 2021, 'previous' => -121672547967, 
                    'increase' => 988181972606, 'decrease' => 929993937375, 'os_today' => -63484512736),
                array("FISC" => 2020, 'previous' => -74922976245, 
                    'increase' => 651071836793, 'decrease' => 697821408515, 'os_today' => -121672547967),
            );
        } elseif($indicator == '2200110001') {
            $data['trade_payable'] = array(
                array("FISC" => 2022, 'previous' => -20732130564, 
                    'increase' => 14999280000, 'decrease' => 7999280000, 'os_today' => -13732130564),
                array("FISC" => 2021, 'previous' => -35379975350, 
                    'increase' => 22196834786, 'decrease' => 7548990000, 'os_today' => -20732130564),
                array("FISC" => 2020, 'previous' => -185384756592, 
                    'increase' => 179980517978, 'decrease' => 29975736736, 'os_today' => -35379975350),
            );
        } else {
            $data['trade_payable'] = array(
                array("FISC" => 2022, 'previous' => -98616000000, 
                    'increase' => 210330135625, 'decrease' => 111714135625, 'os_today' => 0),
            );
        }
        

        // Account receivable

        $account_receivable = $builder_bal->select("FISC AS year, (SELECT COALESCE(SUM(BALANCE), 0) FROM FI_ACT_BAL 
            WHERE FISC = year - 1 AND FI = 16 AND COMP = 'HH10' AND GL_ACCOUNT = '1100210101') AS previous, SUM(DEBIT_PER) AS increase, 
            SUM(CREDIT_PER) AS decrease, ((SELECT COALESCE(SUM(BALANCE), 0) FROM FI_ACT_BAL
            WHERE FISC = year - 1 AND FI = 16 AND COMP = 'HH10' AND GL_ACCOUNT = '1100210101') + SUM(DEBIT_PER) + SUM(CREDIT_PER)) AS os_today")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT = '1100210101'")
            ->where("FI < 16")
            ->groupBy('FISC')
            ->orderBy('FISC', 'DESC')
            ->limit(3)
            ->get()->getResultArray();
        // $data['account_receivable'] = $account_receivable;
        $data['account_receivable'] = array(
            array("year" => 2022, 'previous' => 125508817734, 
                'increase' => 723989059365, 'decrease' => 798430530697, 'os_today' => 51067346402),
            array("year" => 2021, 'previous' => 59637504591, 
                'increase' => 1415325938109, 'decrease' => 1349454624966, 'os_today' => 125508817734),
            array("year" => 2020, 'previous' => 29609596011, 
                'increase' => 854767777312, 'decrease' => -824739868732, 'os_today' => 59637504591),
        );

        // aging account
        $group_account_payable = $_GET['account_type'] ?? 'invoice';
        $group_account_receivable = $_GET['receive_type'] ?? 'invoice';
        $as_of_date = $_GET['as_of_date'] ?? date('Y-m-d');
        $as_date_rec = $_GET['as_of_date'] ?? date('Y-m-d');
        $aging_account = [];
        $aging_account_receive = [];
        
        // payable
        if ($group_account_payable == 'invoice') {
            $aging_account = $this->agingAccountInvoice($year, $as_of_date);
        } elseif ($group_account_payable == 'net_invoice') {
            $aging_account = $this->agingAccountNetInvoice($year, $as_of_date);
        }

        // receive
        if ($group_account_receivable == 'net_invoice') {
            $aging_account_receive = $this->agingAccountNetInvoiceReceive($year, $as_date_rec);
        } elseif ($group_account_receivable == 'invoice') {
            $aging_account_receive = $this->agingAccountInvoiceReceivable($year, $as_date_rec);
        } elseif ($group_account_receivable == 'bukti_potong') {
            $aging_account_receive = $this->agingARBuktiPotong($year, $as_date_rec);
        }

        $data['aging_account'] = $aging_account;
        $data['aging_receivable'] = $aging_account_receive;

        echo view("pages/finance/balance", $data);
    }

    public function profitpershipment()
    {
        $data['title'] = "profitpershipment";
        echo view("pages/finance/profitpershipment", $data);
    }

    public function updatedata()
    {
        $data['title'] = "Update Data";
        echo view("pages/finance/updatedata", $data);
    }
    public function salesandproduction()
    {
        $data['title'] = "salesandproduction";
        echo view("pages/finance/salesandproduction", $data);
    }
    public function pph22()
    {
        $data['title'] = "Bukti Potong PPh22";
        echo view("pages/finance/pph22", $data);
    }

    public function cvpanlysis()
    {
        $data['title'] = "Additional for CVP Analysis Report";
        echo view("pages/finance/cvpanlysis", $data);
    }

    public function rkap()
    {
        $data['title'] = "Input RKAP";
        echo view("pages/finance/rkap", $data);
    }

    public function updateproductiondata()
    {
        $data['title'] = "Update Production Data";
        echo view("pages/finance/updateproductiondata", $data);
    }

    public function updateproductionvendor()
    {
        $data['title'] = "Update Production Vendor";
        echo view("pages/finance/updateproductionvendor", $data);
    }
}
