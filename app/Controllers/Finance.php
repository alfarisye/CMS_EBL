<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\FiActBalance;
use App\Models\FiActTrs;
use App\Models\FiInRkps;
use App\Models\FiMdBudget;
use App\Models\Sales\TSalPrice;
use App\Models\FiSalesInv;
use App\Models\TCostmining;
use App\Models\TSalCoa;
use App\Models\MDCustomers;

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
            ->where("FI = $month")
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
            ->where("FI = $month")
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
            ->where("FI = $month")
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
            ->where("FI = $month")
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
            ->where("FI = $month")
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
        $document_aging = $db->query("SELECT SUM(
                CASE WHEN SHKZG = 'S' THEN DMBTR * -1/10000
                WHEN SHKZG = 'H' THEN DMBTR * 1/10000 END) AS total, C.CATEGORY AS category
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
                when (`B`.`TODAY` = `B`.ZFBDT AND `B`.`TODAY` < `B`.FAEDT) THEN 'Current'
            end
            ) AS CATEGORY
            FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
                DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
                FROM (
                SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                    INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
                WHERE HKONT IN ('2100110202', '2100110201', '2100110204') AND GJAHR = YEAR('$date') AND AUGBL = ''
            ) A ) B) C WHERE C.GJAHR = $year GROUP BY C.category ORDER BY C.category")->getResultArray();
        $grouped_array = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0);
        $sum = 0;
        foreach ($document_aging as $d) {
            $grouped_array[$d['category']] = $d['total'];
            if ($d['category'] != null) {
                $sum += $d['total'];
            }
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
                when (`B`.`TODAY` = `B`.ZFBDT AND `B`.`TODAY` < `B`.FAEDT) THEN 'Current'
            end
            ) AS CATEGORY
            FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
                DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
                FROM (
                SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                    INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
                WHERE HKONT IN ('2100110202', '2100110201', '2100110204') AND GJAHR = YEAR('$date') AND AUGBL = ''
            ) A ) B");
        for ($i = 2; $i <= 6; $i++) {
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
                    WHERE HKONT IN ('2100110202', '2100110201', '2100110204') AND GJAHR = YEAR('$date') AND AUGBL = ''
                ) A ) B");
        }
        $document_aging_utang = $db->query("SELECT SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * -1/10000
            WHEN SHKZG = 'H' THEN DMBTR * 1/10000 END) AS total, C.CATEGORY AS category
            FROM (SELECT * FROM zxc) C GROUP BY C.category ORDER BY C.category")->getResultArray();
        $document_aging_ppn = $db->query("SELECT 
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1/10000
                    WHEN SHKZG = 'H' THEN DMBTR * -1/10000 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc WHERE CATEGORY = '0-30')) AS '0-30',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1/10000
                    WHEN SHKZG = 'H' THEN DMBTR * -1/10000 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc2 WHERE CATEGORY = '31-60')) AS '31-60',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1/10000
                    WHEN SHKZG = 'H' THEN DMBTR * -1/10000 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc3 WHERE CATEGORY = '61-90')) AS '61-90',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1/10000
                    WHEN SHKZG = 'H' THEN DMBTR * -1/10000 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc4 WHERE CATEGORY = '>180')) AS '>180',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1/10000
                    WHEN SHKZG = 'H' THEN DMBTR * -1/10000 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc5 WHERE CATEGORY = '91-180')) AS '91-180',
            (SELECT 
                SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1/10000
                    WHEN SHKZG = 'H' THEN DMBTR * -1/10000 END) 
                    FROM FI_ACT_TRS WHERE HKONT = '1100610008' 
                    AND GJAHR = YEAR('$date') AND BELNR IN (SELECT BELNR FROM zxc6 WHERE CATEGORY = 'Current')) AS 'Current'")->getRowArray();
        $grouped_array = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0);
        $sum = 0;
        foreach ($document_aging_utang as $d) {
            $temp = $d['total'] - $document_aging_ppn[$d['category']];
            if ($d['category'] != null) {
                $sum += $d['total'];
            }
            $grouped_array[$d['category']] = $temp;
        }
        $grouped_array['total'] = $sum;
        return $grouped_array;
    }

    /**
     * @author Angga Kawa
     * @return Array of AR Net Invoice
     */
    private function agingAccountNetInvoiceReceive($year, $date)
    {
        $db = Database::connect();
        $temp = $db->query("CREATE TEMPORARY TABLE aair SELECT B.*, (case
            when (`B`.`DIFF` > 180) then '>180'
            when ((`B`.`DIFF` >= 91)
            and (`B`.`DIFF` <= 180)) then '91-180'
            when ((`B`.`DIFF` >= 61)
            and (`B`.`DIFF` <= 90)) then '61-90'
            when ((`B`.`DIFF` >= 31)
            and (`B`.`DIFF` <= 60)) then '31-60'
            when ((`B`.`DIFF` >= 0)
            and (`B`.`DIFF` <= 30)) then '0-30'
            when (`B`.`TODAY` = `B`.ZFBDT AND `B`.`TODAY` < `B`.FAEDT) THEN 'Current'
            when (`B`.`TODAY` < `B`.FAEDT) then 'Not Yet Due'
        end
        ) AS CATEGORY
        FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
            DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
            FROM (
            SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
            WHERE HKONT = '1100210101' AND GJAHR = YEAR('$date') AND AUGBL = ''
        ) A ) B");
        $nilai_piutang = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR * 1/10000
            WHEN fat.SHKZG = 'H' THEN fat.DMBTR * -1/10000 END) AS total, CATEGORY
            FROM FI_ACT_TRS fat INNER JOIN aair aa ON aa.BELNR = fat.BELNR AND aa.GJAHR = fat.GJAHR WHERE fat.HKONT = '1100210101'
            AND fat.GJAHR = YEAR('$date')
            GROUP BY CATEGORY")->getResultArray();
        $grouped_array_piutang = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0, 'Not Yet Due' => 0);
        $sum = 0;
        foreach ($nilai_piutang as $d) {
            $grouped_array_piutang[$d['CATEGORY']] = $d['total'];
            if ($d['CATEGORY'] != null) {
                $sum += $d['total'];
            }
        }
        $grouped_array_piutang['total'] = $sum;
        return $grouped_array_piutang;
    }

    /**
     * @author Angga kawa
     * @description Perhitungan Aging Account Receivable parameter = Invoice
     * @return Array of AR Invoice
     */
    private function agingAccountInvoiceReceivable($year, $date)
    {
        $db = Database::connect();
        $temp = $db->query("CREATE TEMPORARY TABLE aair SELECT B.*, (case
            when (`B`.`DIFF` > 180) then '>180'
            when ((`B`.`DIFF` >= 91)
            and (`B`.`DIFF` <= 180)) then '91-180'
            when ((`B`.`DIFF` >= 61)
            and (`B`.`DIFF` <= 90)) then '61-90'
            when ((`B`.`DIFF` >= 31)
            and (`B`.`DIFF` <= 60)) then '31-60'
            when ((`B`.`DIFF` >= 0)
            and (`B`.`DIFF` <= 30)) then '0-30'
            when (`B`.`TODAY` = `B`.ZFBDT AND `B`.`TODAY` < `B`.FAEDT) THEN 'Current'
            when (`B`.`TODAY` < `B`.FAEDT) then 'Not Yet Due'
        end
        ) AS CATEGORY
        FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
            DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
            FROM (
            SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
            WHERE HKONT = '1100210101' AND GJAHR = YEAR('$date') AND AUGBL = ''
        ) A ) B");
        $nilai_dpp = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR * -1/10000
            WHEN fat.SHKZG = 'H' THEN fat.DMBTR * 1/10000 END) AS total, CATEGORY
            FROM FI_ACT_TRS fat INNER JOIN aair aa ON aa.BELNR = fat.BELNR AND aa.GJAHR = fat.GJAHR WHERE fat.HKONT = '4395100104'
            AND fat.GJAHR = YEAR('$date')
            GROUP BY CATEGORY")->getResultArray();
        $nilai_ppn = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR * -1/10000
            WHEN fat.SHKZG = 'H' THEN fat.DMBTR * 1/10000 END) AS total, CATEGORY
            FROM FI_ACT_TRS fat INNER JOIN aair aa ON aa.BELNR = fat.BELNR AND aa.GJAHR = fat.GJAHR WHERE fat.HKONT = '2100420111'
            AND fat.GJAHR = YEAR('$date')
            GROUP BY CATEGORY")->getResultArray();
        $nilai_potong = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR * -1/10000
            WHEN fat.SHKZG = 'H' THEN fat.DMBTR * 1/10000 END) AS total, CATEGORY
            FROM FI_ACT_TRS fat INNER JOIN aair aa ON aa.BELNR = fat.BELNR AND aa.GJAHR = fat.GJAHR WHERE fat.HKONT = '4395109104'
            AND fat.GJAHR = YEAR('$date')
            GROUP BY CATEGORY")->getResultArray();
        $grouped_array_dpp = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0, 'Not Yet Due' => 0);
        $sum = 0;
        foreach ($nilai_dpp as $d) {
            $grouped_array_dpp[$d['CATEGORY']] = $d['total'];
            if ($d['CATEGORY'] != null) {
                $sum += $d['total'];
            }
        }
        $grouped_array_ppn = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0, 'Not Yet Due' => 0);
        $sum = 0;
        foreach ($nilai_ppn as $d) {
            $grouped_array_ppn[$d['CATEGORY']] = $d['total'];
            if ($d['CATEGORY'] != null) {
                $sum += $d['total'];
            }
        }
        $grouped_array_potong = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0, 'Not Yet Due' => 0);
        $sum = 0;
        foreach ($nilai_potong as $d) {
            $grouped_array_potong[$d['CATEGORY']] = $d['total'];
            if ($d['CATEGORY'] != null) {
                $sum += $d['total'];
            }
        }
        $grouped_array = array(
            '>180' => $grouped_array_dpp['>180'] + $grouped_array_ppn['>180'] + $grouped_array_potong['>180'],
            '91-180' => $grouped_array_dpp['91-180'] + $grouped_array_ppn['91-180'] + $grouped_array_potong['91-180'],
            '61-90' => $grouped_array_dpp['61-90'] + $grouped_array_ppn['61-90'] + $grouped_array_potong['61-90'],
            '31-60' => $grouped_array_dpp['31-60'] + $grouped_array_ppn['31-60'] + $grouped_array_potong['31-60'],
            '0-30' => $grouped_array_dpp['0-30'] + $grouped_array_ppn['0-30'] + $grouped_array_potong['0-30'],
            'Current' => $grouped_array_dpp['Current'] + $grouped_array_ppn['Current'] + $grouped_array_potong['Current'],
            'Not Yet Due' => $grouped_array_dpp['Not Yet Due'] + $grouped_array_ppn['Not Yet Due'] + $grouped_array_potong['Not Yet Due']
        );
        $grouped_array['total'] = $grouped_array['>180'] + $grouped_array['91-180'] + $grouped_array['61-90']
            + $grouped_array['31-60'] + $grouped_array['0-30'] + $grouped_array['Current'] + $grouped_array['Not Yet Due'];
        return $grouped_array;
    }

    private function agingARBuktiPotong($year, $date)
    {
        $db = Database::connect();
        $temp = $db->query("CREATE TEMPORARY TABLE aair SELECT B.*, (case
            when (`B`.`DIFF` > 180) then '>180'
            when ((`B`.`DIFF` >= 91)
            and (`B`.`DIFF` <= 180)) then '91-180'
            when ((`B`.`DIFF` >= 61)
            and (`B`.`DIFF` <= 90)) then '61-90'
            when ((`B`.`DIFF` >= 31)
            and (`B`.`DIFF` <= 60)) then '31-60'
            when ((`B`.`DIFF` >= 0)
            and (`B`.`DIFF` <= 30)) then '0-30'
            when (`B`.`TODAY` = `B`.ZFBDT AND `B`.`TODAY` < `B`.FAEDT) THEN 'Current'
            when (`B`.`TODAY` < `B`.FAEDT) then 'Not Yet Due'
        end
        ) AS CATEGORY
        FROM (SELECT BELNR, ZFBDT, DATE_ADD(ZFBDT, INTERVAL ZBDIT day) AS FAEDT, '$date' AS TODAY,
            DATEDIFF('$date', DATE_ADD(ZFBDT, INTERVAL ZBDIT day)) AS DIFF, SHKZG, DMBTR, GJAHR
            FROM (
            SELECT fat.*, z.ZBDIT FROM FI_ACT_TRS fat 
                INNER JOIN ZTERM z ON z.ZTERM = fat.ZTERM 
            WHERE HKONT = '1100610053' AND GJAHR = YEAR('$date') AND AUGBL = ''
        ) A ) B");
        $nilai_potong = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR * 1/10000
            WHEN fat.SHKZG = 'H' THEN fat.DMBTR * -1/10000 END) AS total, CATEGORY
            FROM FI_ACT_TRS fat INNER JOIN aair aa ON aa.BELNR = fat.BELNR AND aa.GJAHR = fat.GJAHR WHERE fat.HKONT = '1100610053'
            AND fat.GJAHR = YEAR('$date')
            GROUP BY CATEGORY")->getResultArray();
        $nilai_potong_terbit = $db->query("SELECT SUM(DMBTR)/1000000 AS total
            FROM FI_BKT_PTG WHERE NBKP != '' AND GJAHR = YEAR('$date')")->getRowArray();
        $grouped_array = array('>180' => 0, '91-180' => 0, '61-90' => 0, '31-60' => 0, '0-30' => 0, 'Current' => 0, 'Not Yet Due' => 0);
        $sum = 0;
        foreach ($nilai_potong as $d) {
            $temp = $d['total'] - $nilai_potong_terbit['total'];
            $sum += $temp;
            $grouped_array[$d['category']] = $temp;
        }
        $grouped_array['total'] = $sum;
        return $grouped_array;
    }

    private function getCashflowReportTransaction($db, $hkont, $start, $end, $kurzt, $pengali = "DEFAULT")
    {
        // Conditions: DEFAULT untuk Pengali menggunakan case SHKZG = S THEN * 1;
        // SHKZG  untuk pengali khusus parameter tertentu
        if ($pengali == 'DEFAULT') {
            $query = "SELECT SUM(
                    CASE WHEN SHKZG = 'S' THEN DMBTR * 1
                    WHEN SHKZG = 'H' THEN DMBTR * -1
                    END
                ) AS total FROM FI_ACT_TRS fat INNER JOIN FI_MD_GL fmg
                ON fmg.BUKRS = fat.BUKRS AND fmg.SAKNR = fat.HKONT
                WHERE fmg.FDLEV IN ('ZC', 'ZB') AND HKONT LIKE '$hkont' AND SGTXT LIKE '$kurzt' 
                AND BUDAT BETWEEN '$start' AND '$end'";
        } else {
            $query = "SELECT SUM(CASE WHEN SHKZG = '$pengali' THEN DMBTR * 1 ELSE NULL END) AS total FROM FI_ACT_TRS fat INNER JOIN FI_MD_GL fmg
                ON fmg.BUKRS = fat.BUKRS AND fmg.SAKNR = fat.HKONT
                WHERE fmg.FDLEV IN ('ZC', 'ZB') AND HKONT LIKE '$hkont' AND SGTXT LIKE '$kurzt' 
                AND BUDAT BETWEEN '$start' AND '$end'";
        }

        $res = $db->query($query)->getRowArray();
        return $res['total'];
    }

    private function getBeginningBalanceCashflow($db, $gl_account, $fisc, $fi)
    {
        $query = "SELECT SUM(BALANCE) AS total
        FROM FI_ACT_BAL fat
        INNER JOIN FI_MD_GL fmg
        ON fmg.BUKRS = fat.COMP AND fmg.SAKNR = fat.GL_ACCOUNT
        WHERE FISC = $fisc AND FI = $fi AND GL_ACCOUNT LIKE '$gl_account' AND fmg.FDLEV IN ('ZC', 'ZB')";

        $res = $db->query($query)->getRowArray();
        return $res['total'];
    }

    // cashflow
    public function index()
    {

        $db = Database::connect();

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
        $start_date = $_GET['start'] ?? Time::createFromDate($year, $month - 1, 1)->format('Y-m-d');
        $end_date = $_GET['end'] ?? Time::createFromDate($year, $month - 1, 1)->format('Y-m-t');

        $data['selectedParams'] = ['month' => $month, 'year' => $year, "sdate" => $start_date, "edate" => $end_date];

        $data['todayDate'] = ['month' => $parsed->getMonth(), 'year' => $parsed->getYear()];

        // only for balance
        $month_balance = $month;
        $year_balance = $year - 1;
        $year_balance_mtd = $year;

        // cashflow report by date
        if ($start_date && $end_date) {
            $sd = Time::parse($start_date);
            $ed = Time::parse($end_date);
            $diff_date = $sd->difference($ed)->getDays() + 1;

            // operating
            $step_1_op = $this->getCashflowReportTransaction($db, "11%", $start_date, $end_date, "1___;%");
            $step_2_op = $this->getCashflowReportTransaction($db, "11%", $start_date, $end_date, "2___;%");
            $data['operating_range'] = ($step_1_op + $step_2_op) / 1000000;

            // investing
            $step_1_invest = $this->getCashflowReportTransaction($db, "11%", $start_date, $end_date, "3___;%");
            $step_2_invest = $this->getCashflowReportTransaction($db, "11%", $start_date, $end_date, "4___;%");
            $data['investing_range'] = ($step_1_invest + $step_2_invest) / 1000000;

            $data['free_cash_range'] = $data['operating_range'] + $data['investing_range'];

            // financing
            $step_1_fi = $this->getCashflowReportTransaction($db, "11%", $start_date, $end_date, "5___;%");
            $step_2_fi = $this->getCashflowReportTransaction($db, "11%", $start_date, $end_date, "%Valuation on%", 'S');
            $step_3_fi = $this->getCashflowReportTransaction($db, "11%", $start_date, $end_date, "6___;%");
            $step_4_fi = $this->getCashflowReportTransaction($db, "11%", $start_date, $end_date, "%Valuation on%", 'H');


            $financing = ($step_1_fi + $step_2_fi) + ($step_3_fi + $step_4_fi);
            $data['financing_range'] = $financing / 1000000;

            $data['net_cash_range'] = $data['free_cash_range'] + $data['financing_range'];

            // balance
            $start_balance_date = Time::createFromDate($sd->getYear(), $sd->getMonth(), 1)->format('Y-m-d');
            $end_balance_date = Time::createFromDate($sd->getYear(), $sd->getMonth(), $sd->getDay() - 1)->format('Y-m-d');
            $step_1_bal = $this->getBeginningBalanceCashflow($db, "11%", $sd->getYear(), $sd->getMonth() - 1);
            $step_2_bal = $this->getCashflowReportTransaction($db, "11%", $start_balance_date, $end_balance_date, "%");

            $data['beginning_range'] = ($step_1_bal + $step_2_bal) / 1000000;

            $data['ending_balance_range'] = $data['beginning_range'] + $data['net_cash_range'];
        }

        // BEGINNING BALANCE
        $data['beginning_balance_ytd'] = $builder_bal->select("SUM(BALANCE)/1000000 AS BALANCE")
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


        $data['beginning_balance_mtd'] = $builder_bal->select("SUM(BALANCE)/1000000 AS BALANCE")
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
            ->where("MONAT <= $month")
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
            ->where("MONAT <= $month")
            ->where("SGTXT LIKE '2___;%'")
            ->get()->getRowArray();
        $data['operating_ytd'] = ($operating_step_1_ytd['total'] + $operating_step_2_ytd['total']) / 1000000;

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
        $data['operating_mtd'] = ($operating_step_1_mtd['total'] + $operating_step_2_mtd['total']) / 1000000;

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
        $data['investing_mtd'] = ($investing_step_1_mtd['total'] + $investing_step_2_mtd['total']) / 1000000;
        $investing_step_1_ytd = $builder->select("SUM(
            CASE WHEN SHKZG = 'S' THEN DMBTR * 1
            WHEN SHKZG = 'H' THEN DMBTR * -1
            END
        ) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT <= $month")
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
            ->where("MONAT <= $month")
            ->where("SGTXT LIKE '4___;%'")
            ->get()->getRowArray();
        $data['investing_ytd'] = ($investing_step_1_ytd['total'] + $investing_step_2_ytd['total']) / 1000000;

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
            ->where("MONAT <= $month")
            ->where("SGTXT LIKE '5___;%'")
            ->get()->getRowArray();
        $financing_step_2_ytd = $builder->select("SUM(DMBTR * 1) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT <= $month")
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
            ->where("MONAT <= $month")
            ->where("fmg.BUKRS = 'HH10'")
            ->where("SGTXT LIKE '6___;%'")
            ->get()->getRowArray();
        $financing_step_4_ytd = $builder->select("SUM(DMBTR * -1) AS total")
            ->join("FI_MD_GL fmg", "fmg.BUKRS = FI_ACT_TRS.BUKRS AND fmg.SAKNR = FI_ACT_TRS.HKONT")
            ->where("fmg.FDLEV IN ('ZC', 'ZB')")
            ->where("HKONT LIKE '11%'")
            ->where("GJAHR = $year")
            ->where("MONAT <= $month")
            ->where("SHKZG = 'H'")
            ->where("fmg.BUKRS = 'HH10'")
            ->where("SGTXT LIKE '%Valuation on%'")
            ->get()->getRowArray();
        // dd($financing_step_1_ytd['total'], $financing_step_2_ytd['total'], $financing_step_3_ytd['total'], $financing_step_4_ytd['total']);
        $financing_ytd = (($financing_step_1_ytd['total'] + $financing_step_2_ytd['total']) + ($financing_step_3_ytd['total'] + $financing_step_4_ytd['total']));
        $data['financing_ytd'] = $financing_ytd / 1000000;
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
        $financing_mtd = (($financing_step_1_mtd['total'] + $financing_step_2_mtd['total']) + ($financing_step_3_mtd['total'] + $financing_step_4_mtd['total']));
        $data['financing_mtd'] = $financing_mtd / 1000000;

        // Free cash flow
        $data['free_cash_ytd'] = $data['operating_ytd'] + $data['investing_ytd'];
        $data['free_cash_mtd'] = $data['operating_mtd'] + $data['investing_mtd'];

        // net cash flow
        // $data['net_cash_ytd'] = $data['free_cash_ytd'] + $data['financing_ytd'];
        // $data['net_cash_mtd'] = $data['free_cash_mtd'] + $data['financing_mtd'];
        $data['net_cash_ytd'] = $data['free_cash_ytd'] + $data['financing_ytd'];
        $data['net_cash_mtd'] = $data['free_cash_mtd'] + $data['financing_mtd'];

        // ending balance
        $data['ending_balance_ytd'] = $data['beginning_balance_ytd']['BALANCE'] + $data['net_cash_ytd'];
        $data['ending_balance_mtd'] = $data['beginning_balance_mtd']['BALANCE'] + $data['net_cash_mtd'];

        // * -------- Cash Convertion Cycle ------------

        // by year
        $temp_total_day_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $start_d = Time::createFromDate($year, 1, 1);
        if ($year == $parsed->getYear() && $month == $parsed->getMonth()) {
            $end_d = Time::createFromDate($year, $month);
            $diff = $start_d->difference($end_d);
            $total_days = $diff->days + 1;
        } else {
            $end_d = Time::createFromDate($year, $month, $temp_total_day_in_month);
            $diff = $start_d->difference($end_d);
            $total_days = $diff->days;
        }

        // by month
        if ($year == $parsed->getYear() && $month == $parsed->getMonth()) {
            $total_days_month = $parsed->getDay();
        } else {
            // get total days of the selected year
            $total_days_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        // by date range
        if ($start_date && $end_date) {
            $sd = Time::parse($start_date);
            $ed = Time::parse($end_date);
            $diff_date = $sd->difference($ed)->getDays() + 1;

            // days inventory
            $step_1_date = $db->query("SELECT SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1 
                WHEN SHKZG = 'H' THEN DMBTR * -1 ELSE NULL END) AS total 
                FROM FI_ACT_TRS fat
                INNER JOIN ZFIT_CMSGL zc ON zc.GL = fat.HKONT
                WHERE BUKRS = 'HH10' AND zc.ID = 'DAYS_INVENTORY'
                AND BUDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();
            $step_2_date = $db->query("SELECT SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1 
                WHEN SHKZG = 'H' THEN DMBTR * -1 ELSE NULL END) AS total 
                FROM FI_ACT_TRS fat
                WHERE BUKRS = 'HH10' AND HKONT LIKE '5%'
                AND BUDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();
            $days_inventory_date = handleDivision($step_1_date['total'], $step_2_date['total']) * $diff_date;
            $data['days_inventory_date'] = $days_inventory_date;

            // days sales
            $step_1_date = $db->query("SELECT SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1 
                WHEN SHKZG = 'H' THEN DMBTR * -1 ELSE NULL END) AS total 
                FROM FI_ACT_TRS fat
                INNER JOIN ZFIT_CMSGL zc ON zc.GL = fat.HKONT
                WHERE BUKRS = 'HH10' AND zc.ID = 'DAYS_SALES'
                AND BUDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();
            $step_2_date = $db->query("SELECT SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * -1 
                WHEN SHKZG = 'H' THEN DMBTR * 1 ELSE NULL END) AS total 
                FROM FI_ACT_TRS fat
                WHERE BUKRS = 'HH10' AND HKONT LIKE '4%'
                AND BUDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();
            $days_sales_date = handleDivision($step_1_date['total'], $step_2_date['total']) * $diff_date;
            $data['days_sales_date'] = $days_sales_date;

            // days payable
            $step_1_date = $db->query("SELECT SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * -1 
                WHEN SHKZG = 'H' THEN DMBTR * 1 ELSE NULL END) AS total 
                FROM FI_ACT_TRS fat
                INNER JOIN ZFIT_CMSGL zc ON zc.GL = fat.HKONT
                WHERE BUKRS = 'HH10' AND zc.ID = 'DAYS_PAYABLE'
                AND BUDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();
            $step_2_date = $db->query("SELECT SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1 
                WHEN SHKZG = 'H' THEN DMBTR * -1 ELSE NULL END) AS total 
                FROM FI_ACT_TRS fat
                WHERE BUKRS = 'HH10' AND HKONT LIKE '5%'
                AND BUDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();
            $days_payable_date = handleDivision($step_1_date['total'], $step_2_date['total']) * $diff_date;
            $data['days_payable_date'] = $days_payable_date;

            $cash_convertion_cycle_date = $days_inventory_date + $days_sales_date - $days_payable_date;
            $data['cash_convertion_cycle_date'] = $cash_convertion_cycle_date;
        }

        // Days Inventory

        $step_1_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DAYS_INVENTORY'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_2_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("GL_ACCOUNT LIKE '5%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_1_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DAYS_INVENTORY'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_2_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("(GL_ACCOUNT LIKE '5%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // (step 1 / step 2) * jumlah hari dari tanggal 1 januari sampai sekarang
        $days_inventory_ytd = handleDivision($step_1_ytd['BALANCE'], $step_2_ytd['BALANCE']) * $total_days;
        $days_inventory_mtd = handleDivision($step_1_mtd['BALANCE'], $step_2_mtd['BALANCE']) * $total_days_month;
        $data['days_inventory_ytd'] = $days_inventory_ytd;
        $data['days_inventory_mtd'] = $days_inventory_mtd;

        // Days Sales

        $step_1_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DAYS_SALES'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_2_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '4%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_1_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DAYS_SALES'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_2_mtd = $builder_bal->select("SUM(PER_SALES) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '4%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // days sales
        // (step 1 / step 2) * jumlah hari dari tanggal 1 januari sampai sekarang
        $days_sales_ytd = handleDivision($step_1_ytd['BALANCE'], $step_2_ytd['BALANCE']) * $total_days;
        $days_sales_mtd = handleDivision($step_1_mtd['BALANCE'], $step_2_mtd['BALANCE']) * $total_days_month;
        $data['days_sales_ytd'] = $days_sales_ytd;
        $data['days_sales_mtd'] = $days_sales_mtd;


        // days Payable

        $step_1_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DAYS_PAYABLE'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_2_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '5%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $step_1_mtd = $builder_bal->select("SUM(PER_SALES) * -1 AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DAYS_PAYABLE'")
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
        $days_payable_ytd = handleDivision($step_1_ytd['BALANCE'], $step_2_ytd['BALANCE']) * $total_days;
        $days_payable_mtd = handleDivision($step_1_mtd['BALANCE'], $step_2_mtd['BALANCE']) * $total_days_month;
        $data['days_payable_ytd'] = $days_payable_ytd;
        $data['days_payable_mtd'] = $days_payable_mtd;

        $cash_convertion_cycle_ytd = $days_inventory_ytd + $days_sales_ytd - $days_payable_ytd;
        $cash_convertion_cycle_mtd = $days_inventory_mtd + $days_sales_mtd - $days_payable_mtd;

        $data['cash_convertion_cycle_ytd'] = $cash_convertion_cycle_ytd;
        $data['cash_convertion_cycle_mtd'] = $cash_convertion_cycle_mtd;

        // * -------- Cash Cost ------------

        // total cost

        $total_cost_ytd = $db->query("WITH total_cost AS (SELECT sum(BALANCE/1000000) AS total FROM FI_ACT_BAL fab 
            WHERE 
            (GL_ACCOUNT LIKE '5%' 
                OR (GL_ACCOUNT LIKE '6%')
                OR (GL_ACCOUNT >= 9100010000 AND GL_ACCOUNT <= 9100019999)
            OR (GL_ACCOUNT >= 9200020000 AND GL_ACCOUNT <= 9200029999))
            AND FISC = $year
            AND FI = 16
            UNION ALL SELECT SUM(BALANCE/1000000) AS total FROM FI_ACT_BAL fab2 
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab2.GL_ACCOUNT 
            WHERE FISC = $year AND FI = $month AND zc.ID = 'INTEREST')
            SELECT SUM(total) AS BALANCE FROM total_cost")->getRowArray();
        $total_cost_mtd = $db->query("WITH total_cost AS (SELECT sum(PER_SALES/1000000) AS total FROM FI_ACT_BAL fab 
            WHERE 
            (GL_ACCOUNT LIKE '5%' 
                OR (GL_ACCOUNT LIKE '6%')
                OR (GL_ACCOUNT >= 9100010000 AND GL_ACCOUNT <= 9100019999)
            OR (GL_ACCOUNT >= 9200020000 AND GL_ACCOUNT <= 9200029999))
            AND FISC = $year
            AND FI = $month
            UNION ALL SELECT SUM(PER_SALES/1000000) AS total FROM FI_ACT_BAL fab2 
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab2.GL_ACCOUNT 
            WHERE FISC = $year AND FI = $month AND zc.ID = 'INTEREST')
            SELECT SUM(total) AS BALANCE FROM total_cost")->getRowArray();
        $data['total_cost_ytd'] = $total_cost_ytd['BALANCE'];
        $data['total_cost_mtd'] = $total_cost_mtd['BALANCE'];

        $start_mtd_date = Time::createFromDate($year, $month, 1);
        $start_ytd_date = Time::createFromDate($year, 1, 1);
        $mtd_start = $start_mtd_date->format('Y-m-d');
        $ytd_start = $start_ytd_date->format('Y-m-d');
        $mtd_end = $now->format('Y-m-d');

        $mtd_mt = $db->query("SELECT SUM(FNL_QTY) AS qty FROM FI_SALES_INV WHERE BLDAT BETWEEN '$mtd_start' AND '$mtd_end'")->getRowArray();
        $ytd_mt = $db->query("SELECT SUM(FNL_QTY) AS qty FROM FI_SALES_INV WHERE BLDAT BETWEEN '$ytd_start' AND '$mtd_end'")->getRowArray();

        $data['mtd_mt'] = $mtd_mt['qty'];
        $data['ytd_mt'] = $ytd_mt['qty'];

        // cash cost

        $forex_unrelease_ytd = $builder_bal->select("SUM(BALANCE) / 1000000 AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'FOR_UN'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $forex_unrelease_mtd = $builder_bal->select("SUM(PER_SALES) / 1000000 AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'FOR_UN'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $depreciation_ytd = $builder_bal->select("SUM(BALANCE) / 1000000 AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DEP'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $depreciation_mtd = $builder_bal->select("SUM(PER_SALES) / 1000000 AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'DEP'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $amortization_ytd = $builder_bal->select("SUM(BALANCE) / 1000000 AS BALANCE")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->where("zc.ID = 'AMRT'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        $amortization_mtd = $builder_bal->select("SUM(PER_SALES) / 1000000 AS BALANCE")
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
        // $data['cash_cost_ytd'] = 778237822504;
        // $data['cash_cost_mtd'] = 13106003781;

        // variace = total cost - cash cost
        $variance_ytd = $total_cost_ytd['BALANCE'] - $cash_cost_ytd;
        $variance_mtd = $total_cost_mtd['BALANCE'] - $cash_cost_mtd;

        $data['variance_ytd'] = $variance_ytd;
        $data['variance_mtd'] = $variance_mtd;

        // by date range
        if ($start_date && $end_date) {
            $sd = Time::parse($start_date);
            $ed = Time::parse($end_date);
            $diff_date = $sd->difference($ed)->getDays() + 1;

            // total cost
            $total_cost = $db->query("WITH total_cost AS (
                SELECT
                  SUM(
                    CASE
                      WHEN SHKZG = 'S' THEN DMBTR * 1 / 10000
                      WHEN SHKZG = 'H' THEN DMBTR * -1 / 10000
                      ELSE NULL
                    END
                  ) AS total
                FROM
                  FI_ACT_TRS fat
                  WHERE BUDAT BETWEEN '$start_date' AND '$end_date'
                  AND (
                    HKONT LIKE '5%'
                    OR (HKONT LIKE '6%')
                    OR (
                      HKONT >= 9100010000
                      AND HKONT <= 9100019999
                    )
                    OR (
                      HKONT >= 9200020000
                      AND HKONT <= 9200029999
                    )
                  )
                UNION ALL
                SELECT
                  SUM(
                    CASE
                      WHEN SHKZG = 'S' THEN DMBTR * 1 / 10000
                      WHEN SHKZG = 'H' THEN DMBTR * -1 / 10000
                      ELSE NULL
                    END
                  ) AS total
                FROM
                  FI_ACT_TRS fat
                  INNER JOIN ZFIT_CMSGL zc ON zc.GL = fat.HKONT
                WHERE
                  BUKRS = 'HH10'
                  AND zc.ID = 'INTEREST'
                  AND BUDAT BETWEEN '$start_date' AND '$end_date'
              )
              SELECT
                SUM(total) AS total
              FROM
                total_cost")->getRowArray();
            $data['total_cost_range'] = $total_cost['total'] / 100;

            // cash cost
            $forex_unrelease = $db->query("SELECT SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1/10000 
                WHEN SHKZG = 'H' THEN DMBTR * -1/10000 ELSE NULL END) AS total  
                FROM FI_ACT_TRS fat
                INNER JOIN ZFIT_CMSGL zc ON zc.GL = fat.HKONT
                WHERE BUKRS = 'HH10'
                AND zc.ID = 'FOR_UN'
                AND BUDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();
            $depreciation = $db->query("SELECT SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1/10000 
                WHEN SHKZG = 'H' THEN DMBTR * -1/10000 ELSE NULL END) AS total  
                FROM FI_ACT_TRS fat
                INNER JOIN ZFIT_CMSGL zc ON zc.GL = fat.HKONT
                WHERE BUKRS = 'HH10'
                AND zc.ID = 'DEP'
                AND BUDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();
            $amortization = $db->query("SELECT SUM(CASE WHEN SHKZG = 'S' THEN DMBTR * 1/10000 
                WHEN SHKZG = 'H' THEN DMBTR * -1/10000 ELSE NULL END) AS total  
                FROM FI_ACT_TRS fat
                INNER JOIN ZFIT_CMSGL zc ON zc.GL = fat.HKONT
                WHERE BUKRS = 'HH10'
                AND zc.ID = 'AMRT'
                AND BUDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();
            $cash_cost_ranged = $total_cost['total'] - $forex_unrelease['total'] - $depreciation['total'] - $amortization['total'];
            $data['cash_cost_range'] = $cash_cost_ranged / 100;

            // variance = total cost - cash cost
            $variance_ranged = $data['total_cost_range'] - $data['cash_cost_range'];

            $range_mt = $db->query("SELECT SUM(FNL_QTY) AS qty FROM FI_SALES_INV WHERE BLDAT BETWEEN '$start_date' AND '$end_date'")->getRowArray();

            $data['variance_range'] = $variance_ranged;
            $data['range_mt'] = $range_mt['qty'] / 100;
        }

        echo view("pages/finance/cashflow", $data);
    }

    public function profitability()
    {

        $db = Database::connect();

        $data['title'] = "Profitability";

        // filter month and date
        // $month = $_GET['month'] ?? false;
        $year = $_GET['year'] ?? false;
        $now = Time::now();
        $parsed = Time::parse($now);
        if (!$year) {
            $year = $parsed->getYear();
        }
        $month = $parsed->getMonth();
        $startPeriode = $_GET['startPeriode'] ?? 1;
        $endPeriode = $_GET['endPeriode'] ?? $month;
        $startPeriode2 = $_GET['startPeriode2'] ?? 1;
        $endPeriode2 = $_GET['endPeriode2'] ?? $month;

        // $startPeriode2 = $_GET['start2'] ?? Time::createFromDate($parsed->getYear(), $parsed->getMonth() - 1, 1)->format('Y-m-d');
        // $endPeriode2 = $_GET['end2'] ?? Time::createFromDate($parsed->getYear(), $parsed->getMonth() - 1, 1)->format('Y-m-t');

        // $time_actual_start2 = Time::parse($startPeriode2);
        // $time_actual_end2   = Time::parse($endPeriode2);
        $time_actual_start2 = $startPeriode2;
        $time_actual_end2   = $endPeriode2;


        $start_date = $_GET['start'] ?? Time::createFromDate($parsed->getYear(), $parsed->getMonth() - 1, 1)->format('Y-m-d');
        $end_date = $_GET['end'] ?? Time::createFromDate($parsed->getYear(), $parsed->getMonth() - 1, 1)->format('Y-m-t');

        $time_actual_start = Time::parse($start_date);
        $time_actual_end   = Time::parse($end_date);

        $data['selectedParams'] = ['year' => $year, 'startPeriode2' => $startPeriode2, 'endPeriode2' => $endPeriode2, "start_date" => $start_date, "end_date" => $end_date];
        $data['selectedParams'] = ['year' => $year, 'startPeriode2' => $startPeriode2, 'endPeriode2' => $endPeriode2, "start_date" => $start_date, "end_date" => $end_date];
        $data['todayDate'] = ['year' => $parsed->getYear(), 'startPeriode' => $startPeriode, 'endPeriode' => $endPeriode, 'startPeriode2' => $startPeriode2, 'endPeriode2' => $endPeriode2, "start_date" => $start_date, "end_date" => $end_date];
        // $data['selectedParams2'] = ['year' => $year, 'startPeriode2' => $startPeriode2, 'endPeriode2' => $endPeriode2];
        // $data['todayDate2'] = ['year' => $parsed->getYear(), 'startPeriode2' => $startPeriode2, 'endPeriode2' => $endPeriode2];


        $FiActBal = new FiActBalance();
        $builder = $FiActBal->builder();

        $FiMdBudget = new FiMdBudget();
        $builderBudget = $FiMdBudget->builder();

        // Profit and Loss
        $start_date_profit = $_GET['start_profit'] ?? Time::createFromDate($parsed->getYear(), $parsed->getMonth() - 1, 1)->format('Y-m-d');
        $end_date_profit = $_GET['end_profit'] ?? Time::createFromDate($parsed->getYear(), $parsed->getMonth() - 1, 1)->format('Y-m-t');
        $data['years_profit'] = $builderBudget->select("DISTINCT(GJAHR)")->where("GJAHR BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")->orderBy('GJAHR', 'desc')->get()->getResultArray();
        $data['selectedParamsProfit'] = ["start_date_profit" => $start_date_profit, "end_date_profit" => $end_date_profit];


        // list of years
        $data['years'] = $builder->select("DISTINCT(FISC)")->where("GL_ACCOUNT LIKE '4%'")->orderBy('FISC', 'desc')->get()->getResultArray();


        // Revenue new
        // budget mtd
        $data['revenue_budget_mtd'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET, SUM(DMBTR) / 1 AS BUDGET_EBITDA")
            ->where("SAKNR LIKE '4%'")
            ->where("GJAHR BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("MONAT BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // dd($data['revenue_budget_mtd']['BUDGET'], $data['revenue_budget_mtd']['BUDGET_EBITDA']);
        // actual mtd
        $data['revenue_actual_mtd'] = $builder->select("SUM(PER_SALES) / -1000000 AS ACTUAL, SUM(PER_SALES) / -1 AS ACTUAL_EBITDA")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '4%'")
            ->where("FISC BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("FI BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // dd($data['revenue_actual_mtd']);
        // variance 
        $data['revenue_variance_mtd'] = handleDivision($data['revenue_actual_mtd']['ACTUAL'],$data['revenue_budget_mtd']['BUDGET']);
        // dd($data['revenue_variance_mtd']);

        // cogs new
        // budget mtd
        $data['cogs_budget_mtd'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET, SUM(DMBTR) / 1 AS BUDGET_EBITDA")
            ->where("SAKNR LIKE '5%'")
            ->where("GJAHR BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("MONAT BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // actual mtd
        $data['cogs_actual_mtd'] = $builder->select("SUM(PER_SALES) / 1000000 AS ACTUAL, SUM(PER_SALES) / 1 AS ACTUAL_EBITDA")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '5%'")
            ->where("FISC BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("FI BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // variance 
        $data['cogs_variance_mtd'] = handleDivision($data['cogs_actual_mtd']['ACTUAL'],$data['cogs_budget_mtd']['BUDGET']);
        // dd($data['cogs_variance_mtd']);


        // GP new
        // budget mtd
        $data['gp_budget_mtd'] = $data['revenue_budget_mtd']['BUDGET'] - $data['cogs_budget_mtd']['BUDGET'];
        // actual mtd
        $data['gp_actual_mtd'] = $data['revenue_actual_mtd']['ACTUAL'] - $data['cogs_actual_mtd']['ACTUAL'];
        // variance
        $data['gp_variance_mtd'] = handleDivision($data['gp_actual_mtd'],$data['gp_budget_mtd']);


        // GAE new
        // budget mtd
        $data['gae_budget_mtd'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET, SUM(DMBTR) / 1 AS BUDGET_EBITDA")
            ->where("SAKNR LIKE '6%'")
            ->where("GJAHR BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("MONAT BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // actual mtd
        $data['gae_actual_mtd'] = $builder->select("SUM(PER_SALES) / 1000000 AS ACTUAL, SUM(PER_SALES) / 1 AS ACTUAL_EBITDA")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '6%'")
            ->where("FISC BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("FI BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // variance
        $data['gae_variance_mtd'] = handleDivision($data['gae_actual_mtd']['ACTUAL'], $data['gae_budget_mtd']['BUDGET']);

        // op new
        // budget mtd
        $data['op_budget_mtd'] = $data['gp_budget_mtd'] - $data['gae_budget_mtd']['BUDGET'];
        // actual mtd
        $data['op_actual_mtd'] = $data['gp_actual_mtd'] - $data['gae_actual_mtd']['ACTUAL'];
        // variance
        $data['op_variance_mtd'] = handleDivision($data['op_actual_mtd'], $data['op_budget_mtd']);


        // oie new 
        // budget mtd
        $data['oie_budget_mtd'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET, SUM(DMBTR) / 1 AS BUDGET_EBITDA")
            ->where("(SAKNR BETWEEN '7100000000' AND '7100999999' OR SAKNR BETWEEN '7200000000' AND '7200999999')")
            ->where("GJAHR BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("MONAT BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // actual mtd
        $data['oie_actual_mtd'] = $builder->select("SUM(PER_SALES) / 1000000 AS ACTUAL, SUM(PER_SALES) / 1 AS ACTUAL_EBITDA")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT BETWEEN '7100000000' AND '7100999999' OR GL_ACCOUNT BETWEEN '7200000000' AND '7200999999')")
            ->where("FISC BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("FI BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // variance
        $data['oie_variance_mtd'] = handleDivision($data['oie_actual_mtd']['ACTUAL'], $data['oie_budget_mtd']['BUDGET']);


        // EBT new
        // budget mtd
        $data['ebt_budget_mtd'] = $data['op_budget_mtd'] - $data['oie_budget_mtd']['BUDGET'];
        // actual mtd
        $data['ebt_actual_mtd'] = $data['op_actual_mtd'] - $data['oie_actual_mtd']['ACTUAL'];
        // variance
        $data['ebt_variance_mtd'] = handleDivision($data['ebt_actual_mtd'], $data['ebt_budget_mtd']);


        // TAX new
        // budget mtd
        $data['tax_budget_mtd'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET, SUM(DMBTR) / 1 AS BUDGET_EBITDA")
            ->where("(SAKNR LIKE '910001%' OR SAKNR LIKE '920002%')")
            ->where("GJAHR BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("MONAT BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // actual mtd
        $data['tax_actual_mtd'] = $builder->select("SUM(BALANCE) / 1000000 AS ACTUAL, SUM(PER_SALES) / 1 AS ACTUAL_EBITDA")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '910001%' OR GL_ACCOUNT LIKE '920002%')")
            ->where("FISC BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')")
            ->where("FI BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')")
            ->get()->getRowArray();
        // variance
        $data['tax_variance_mtd'] = handleDivision($data['tax_actual_mtd']['ACTUAL'], $data['tax_budget_mtd']['BUDGET']);


        // EAT new
        // budget mtd
        $data['eat_budget_mtd'] = $data['ebt_budget_mtd'] - $data['tax_budget_mtd']['BUDGET'];
        // actual mtd
        $data['eat_actual_mtd'] = $data['ebt_actual_mtd'] - $data['tax_actual_mtd']['ACTUAL'];
        // variance
        $data['eat_variance_mtd'] = handleDivision($data['eat_actual_mtd'], $data['eat_budget_mtd']);

        // EBITDA new
        // budget mtd
        // $ebitda_budget_mtd = $db->query("SELECT SUM(CASE WHEN SAKNR LIKE '4%' THEN DMBTR ELSE NULL END) / 1000000 AS kolom_1,
        //     SUM(CASE WHEN SAKNR LIKE '5%' THEN DMBTR ELSE NULL END) / 1000000 AS kolom_2,
        //     SUM(CASE WHEN SAKNR LIKE '6%' THEN DMBTR ELSE NULL END) / 1000000 AS kolom_3,
        //     SUM(CASE WHEN SAKNR LIKE '7%' THEN DMBTR ELSE NULL END) / 1000000 AS kolom_4,
        //     SUM(CASE WHEN (SAKNR LIKE '910001%' OR SAKNR LIKE '920002%') THEN DMBTR ELSE NULL END) / 1000000 AS tax
        // FROM FI_MD_BUDG
        // WHERE GJAHR = $year
        // AND MONAT BETWEEN $startPeriode AND $endPeriode")->getRowArray();
        // depreciantion
        $depreciation_budget_mtd = $builderBudget->select("(SELECT SUM(DMBTR) FROM FI_MD_BUDG fab
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab.SAKNR 
            WHERE zc.ID = 'DEP' AND (GJAHR BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')) AND MONAT BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')) AS depreciation")
            ->join("ZFIT_CMSGL zc", "zc.GL = SAKNR")
            ->get()->getRowArray();
        // dd($depreciation_budget_mtd);
        // amortization
        $amortization_budget_mtd = $builderBudget->select("(SELECT SUM(DMBTR) FROM FI_MD_BUDG fab
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab.SAKNR 
            WHERE zc.ID = 'AMRT' AND (GJAHR BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')) AND MONAT BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')) AS amortization")
            ->join("ZFIT_CMSGL zc", "zc.GL = SAKNR")
            ->get()->getRowArray();
        // dd($amortization_budget_mtd);
        // interest
        $interest_budget_mtd = $builderBudget->select("(SELECT SUM(DMBTR) FROM FI_MD_BUDG fab
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab.SAKNR 
            WHERE zc.ID = 'INTEREST' AND (GJAHR BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')) AND MONAT BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')) AS interest")
            ->join("ZFIT_CMSGL zc", "zc.GL = SAKNR")
            ->get()->getRowArray();
        // addition and subtraction
        $ebitda_budget_handle_subtraction_mtd = ($data['revenue_budget_mtd']['BUDGET_EBITDA'] ?? 0) - ($data['cogs_budget_mtd']['BUDGET_EBITDA'] ?? 0) - ($data['gae_budget_mtd']['BUDGET_EBITDA'] ?? 0) - ($data['oie_budget_mtd']['BUDGET_EBITDA'] ?? 0);
        $ebitda_budget_handle_addition_mtd = ($depreciation_budget_mtd['depresiasi'] ?? 0) + ($amortization_budget_mtd['amortisai'] ?? 0) + ($interest_budget_mtd['interest'] ?? 0) + ($ebitda_budget_mtd['tax'] ?? 0);
        $data['ebitda_budget_fix_mtd'] = ($ebitda_budget_handle_subtraction_mtd + $ebitda_budget_handle_addition_mtd) / 1000000;
        // dd($data['revenue_budget_mtd']['BUDGET_EBITDA']);

        // Actual mtd
        // $ebitda_actual_mtd = $db->query("SELECT SUM(CASE WHEN GL_ACCOUNT LIKE '4%' THEN PER_SALES ELSE NULL END) / 1000000 AS kolom_1,
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '5%' THEN BALANCE ELSE NULL END) / 1000000 AS kolom_2,
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '6%' THEN BALANCE ELSE NULL END) / 1000000 AS kolom_3,
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '7%' THEN BALANCE ELSE NULL END) / 1000000 AS kolom_4,
        //     SUM(CASE WHEN (GL_ACCOUNT LIKE '910001%' OR GL_ACCOUNT LIKE '920002%') THEN BALANCE ELSE NULL END) / 1000000 AS tax
        // FROM FI_ACT_BAL
        // WHERE FISC = $year
        // AND FI BETWEEN $startPeriode AND $endPeriode")->getRowArray();
        // dd($ebitda_actual_mtd['tax'], $data['tax_actual_mtd']);
        // depreciation
        $depreciation_actual_mtd = $builder->select("(SELECT SUM(PER_SALES) FROM FI_ACT_BAL fab
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab.GL_ACCOUNT 
            WHERE zc.ID = 'DEP' AND (FISC BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')) AND FI BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')) AS depreciation")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->get()->getRowArray();
        // dd($depreciation_actual_mtd);
        // amortization
        $amortization_actual_mtd = $builder->select("(SELECT SUM(PER_SALES) FROM FI_ACT_BAL fab
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab.GL_ACCOUNT 
            WHERE zc.ID = 'AMRT' AND (FISC BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')) AND FI BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')) AS amortization")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->get()->getRowArray();
        // interest
        $interest_actual_mtd = $builder->select("(SELECT SUM(PER_SALES) FROM FI_ACT_BAL fab
            INNER JOIN ZFIT_CMSGL zc ON zc.GL = fab.GL_ACCOUNT 
            WHERE zc.ID = 'INTEREST' AND (FISC BETWEEN YEAR('$start_date_profit') AND YEAR('$end_date_profit')) AND FI BETWEEN MONTH('$start_date_profit') AND MONTH('$end_date_profit')) AS interest")
            ->join("ZFIT_CMSGL zc", "zc.GL = GL_ACCOUNT")
            ->get()->getRowArray();
        // addition and subtraction
        $ebitda_actual_handle_subtraction_mtd = ($data['revenue_actual_mtd']['ACTUAL_EBITDA'] ?? 0) - ($data['cogs_actual_mtd']['ACTUAL_EBITDA'] ?? 0) - ($data['gae_actual_mtd']['ACTUAL_EBITDA'] ?? 0) - ($data['oie_actual_mtd']['ACTUAL_EBITDA'] ?? 0);
        $ebitda_actual_handle_addition_mtd = ($depreciation_actual_mtd['depreciation'] ?? 0) + ($amortization_actual_mtd['amortization'] ?? 0) + ($interest_actual_mtd['interest'] ?? 0) + ($data['tax_actual_mtd']['ACTUAL_EBITDA'] ?? 0);
        $data['ebitda_actual_fix_mtd'] = ($ebitda_actual_handle_subtraction_mtd + $ebitda_actual_handle_addition_mtd) / 1000000;
        // dd($data['ebitda_budget_fix_mtd']);
        // variance
        $data['ebitda_variance_fix_mtd'] = handleDivision($data['ebitda_actual_fix_mtd'], $data['ebitda_budget_fix_mtd']);
        // dd($data['ebitda_variance_fix_mtd']);

        //Query Untuk Cost Volume Profit 

        $CVP_a_price = $db->query("SELECT AVG(tsp.final_price) as rata_price FROM T_SAL_PRICE tsp
                                   WHERE MONTH(tsp.date_final) BETWEEN '$startPeriode2' AND '$endPeriode2'
                                   AND YEAR(tsp.date_final) = '$year'")->getRowArray();



        $CVPq_a_FC = $db->query("SELECT SUM(fab.PER_SALES)/1000000 AS balance  FROM FI_ACT_BAL fab
                                WHERE fab.GL_ACCOUNT LIKE '6%'
                                AND fab.FISC = '$year'
                                AND fab.FI BETWEEN $startPeriode2 AND $endPeriode2")->getRowArray();

        $CVPq_a_r_FC = $db->query("SELECT SUM(fab.PER_SALES) AS balance  FROM FI_ACT_BAL fab
                                 WHERE fab.GL_ACCOUNT LIKE '6%'
                                 AND fab.FISC = '$year'
                                 AND fab.FI BETWEEN $startPeriode2 AND $endPeriode2")->getRowArray();

        //dd($CVPq_a_FC);

        $CVPq_b_FC = $db->query("SELECT SUM(fab.PER_SALES)/1000000 AS per_sales FROM FI_ACT_BAL fab
                                WHERE fab.GL_ACCOUNT IN ('2200010158', '2200010159') 
                                AND fab.FISC = '$year'
                                AND fab.FI BETWEEN $startPeriode2 AND $endPeriode2")->getRowArray();

        $CVPq_b_r_FC = $db->query("SELECT SUM(fab.PER_SALES) AS per_sales FROM FI_ACT_BAL fab
                                WHERE fab.GL_ACCOUNT IN ('2200010158', '2200010159') 
                                AND fab.FISC = '$year'
                                AND fab.FI BETWEEN $startPeriode2 AND $endPeriode2")->getRowArray();

        //dd($CVPq_b_FC);

        $CVPq_c_FC = $db->query("SELECT SUM(fac.DMBTR)/1000000 AS dmbtr FROM FI_ADD_CVP fac
                                WHERE fac.CTGRY = 'C (B + Profit)'
                                AND fac.RKMRK = 'FC'
                                AND fac.GJAHR = '$year'
                                AND fac.MONAT BETWEEN $startPeriode2 AND $endPeriode2")->getRowArray();

        $CVPq_c_r_FC = $db->query("SELECT SUM(fac.DMBTR) AS dmbtr FROM FI_ADD_CVP fac
                                WHERE fac.CTGRY = 'C (B + Profit)'
                                AND fac.RKMRK = 'FC'
                                AND fac.GJAHR = '$year'
                                AND fac.MONAT BETWEEN $startPeriode2 AND $endPeriode2")->getRowArray();

        //dd($CVPq_c_FC);

        $CVPq_d_FC = $db->query("SELECT SUM(fac.DMBTR)/1000000 AS dmbtr FROM FI_ADD_CVP fac
                                WHERE fac.CTGRY = 'D (C & Qty)'
                                AND fac.RKMRK = 'FC'
                                AND fac.GJAHR = '$year'
                                AND fac.MONAT BETWEEN $startPeriode2 AND $endPeriode2")->getRowArray();

        if ($time_actual_start2 < 11 && $time_actual_end2 < 11) {
            $CVP_jml_DQ = $db->query("SELECT SUM(tpq.quantity) AS jml_dq FROM temp_price_quantity tpq
                                      WHERE tpq.month BETWEEN $startPeriode2 AND $endPeriode2
                                      AND tpq.`year` = '$year'")->getRowArray();
        } else {
            $CVP_jml_DQ = $db->query("SELECT SUM(tss.discharging_qty) AS jml_dq FROM T_SAL_SHIPMENT tss
                                  WHERE MONTH(tss.discharging_date) BETWEEN '$startPeriode2' and '$endPeriode2'
                                  AND YEAR(tss.discharging_date) = '$year'")->getRowArray();
        }

        //dd($CVP_jml_DQ, $startPeriode2, $endPeriode2);
        //query untuk field d BE QTY 

        //step 1
        $be_qty_1 = $db->query("SELECT SUM(fac.DMBTR) AS dmbtr FROM FI_ADD_CVP fac
                                WHERE fac.CTGRY = 'D (C & Qty)'
                                AND fac.RKMRK = 'BE'
                                AND fac.GJAHR = '$year'
                                AND fac.MONAT BETWEEN 1 AND 12 ")->getRowArray();
        // //step 2
        // $be_qty_2 = $db->query("SELECT SUM(tsco.quantity) AS quantity FROM T_SAL_CONTRACT_ORDER tsco
        //                         WHERE MONTH(tsco.date) BETWEEN '$startPeriode2' AND '$endPeriode2'
        //                         AND YEAR(tsco.date) = '$year'")->getRowArray();
        //step 3
        if ($time_actual_start2 < 11 && $time_actual_end2 < 11) {
            $be_qty_3 = $db->query("SELECT SUM(tpq.quantity) AS discharging FROM temp_price_quantity tpq
                                      WHERE tpq.month BETWEEN $startPeriode2 AND $endPeriode2
                                      AND tpq.`year` = '$year'")->getRowArray();
        } else {
            $be_qty_3 = $db->query("SELECT SUM(tss.discharging_qty) AS discharging FROM T_SAL_SHIPMENT tss
                                WHERE MONTH(tss.discharging_date) BETWEEN $startPeriode2 AND $endPeriode2   
                                AND YEAR(tss.discharging_date) = '$year'")->getRowArray();
        }
        //step 4
        // $be_qty_4 = $db->query("SELECT SUM(tss.discharging_qty) AS discharging FROM T_SAL_SHIPMENT tss
        //                         WHERE MONTH(tss.discharging_date)  BETWEEN 11 and 12
        //                         AND YEAR(tss.discharging_date) = '$year'")->getRowArray();

        $be_qty_4 = 12 - $endPeriode2;

        // dd($be_qty_1, $be_qty_2, $be_qty_3, $be_qty_4 , $startPeriode2, $endPeriode2);

        //Query FIELD A

        $data['CVP_a_price'] = $CVP_a_price;
        $data['CVP_a_FC'] = $CVPq_a_FC;
        $data['CVPq_a_r_FC'] = $CVPq_a_r_FC;
        $data['CVP_jml_DQ'] = $CVP_jml_DQ;
        $CVP_a_VC =  ($CVP_a_price['rata_price'] ?? 0) - handleDivision(($CVPq_a_r_FC['balance'] ?? 0), ($CVP_jml_DQ['jml_dq'] ?? 0));
        $data['CVP_a_VC'] = $CVP_a_VC;
        $a_be_qty = handleDivision(($CVPq_a_r_FC['balance'] ?? 0), (($CVP_a_price['rata_price'] ?? 0) - ($CVP_a_VC ?? 0)));
        $data['a_be_qty'] = $a_be_qty;

        //dd($data['CVP_a_price'], $data['CVP_a_FC'], $data['CVP_a_VC'], $data['CVP_jml_DQ'], $data['a_be_qty'], $year, $startPeriode2, $endPeriode2);


        //END QUERY FIELD A 

        //QUERY FEILD B 
        $data['CVP_b_price'] = $CVP_a_price;
        $data['CVP_b_FC'] = ($data['CVP_a_FC']['balance'] ?? 0) + ($CVPq_b_FC['per_sales'] ?? 0);
        $data['CVP_b_r_FC'] = ($data['CVPq_a_r_FC']['balance'] ?? 0) + ($CVPq_b_r_FC['per_sales'] ?? 0);
        $data['CVP_b_VC'] = $CVP_a_VC;
        $data['b_be_qty'] = handleDivision(($data['CVP_b_r_FC'] ?? 0), (($data['CVP_b_price']['rata_price'] ?? 0) - ($data['CVP_b_VC'] ?? 0)));

        //dd($data['CVP_b_price'], $data['CVP_b_FC'], $data['CVP_b_VC'], $data['b_be_qty']);

        //END FIELD B 

        //field c                                                                                                                                                                        
        $data['CVP_c_price'] = $CVP_a_price;
        $data['CVP_c_FC'] = ($data['CVP_b_FC'] ?? 0) + ($CVPq_c_FC['dmbtr'] ?? 0);
        $data['CVP_c_r_FC'] = ($data['CVP_b_r_FC'] ?? 0) + ($CVPq_c_r_FC['dmbtr'] ?? 0);
        $data['CVP_c_VC'] = $CVP_a_VC;
        $data['c_be_qty'] = handleDivision(($data['CVP_c_r_FC'] ?? 0), (($data['CVP_c_price']['rata_price'] ?? 0) - ($data['CVP_c_VC'] ?? 0)));

        //dd($data['CVP_c_price'], $data['CVP_c_FC'], $data['CVP_c_VC'], $data['c_be_qty']);
        //end field c

        //field d
        $data['CVP_d_price'] = $CVP_a_price;
        $data['CVP_d_FC'] = $data['CVP_c_FC'] + $CVPq_d_FC['dmbtr'];
        $data['CVP_d_VC'] = $CVP_a_VC;
        $data['d_be_qty'] =  handleDivision((($be_qty_1['dmbtr'] ?? 0) - ($be_qty_2['quantity'] ?? 0) - ($be_qty_3['discharging'] ?? 0)), ($be_qty_4 ?? 0));

        //dd($data['CVP_d_price'], $data['CVP_d_FC'], $data['CVP_d_VC'], $data['d_be_qty']);
        //end field d

        //END untuk QUERY CVP 

        // dd($start_date);


        //Query Untuk FS Profit Pershipment
        $local_fobb_rkap_price = $db->query("SELECT AVG(fir.PRC) as price FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'FOBB'
                                            AND fir.`TYPE`= 'LOCAL'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['local_fobb_rkap_price'] = $local_fobb_rkap_price;
        // dd($data['local_fobb_rkap_price']);
        $local_fobb_rkap_cost = $db->query("SELECT AVG(fir.COST) as cost FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'FOBB'
                                            AND fir.`TYPE`= 'LOCAL'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['local_fobb_rkap_cost'] = $local_fobb_rkap_cost;

        $data['local_fobb_rkap_earning'] = $local_fobb_rkap_price['price'] - $local_fobb_rkap_cost['cost'];

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $local_fobb_actual_price = $db->query("SELECT AVG(ts.price) as price FROM them_ship ts
                                                    WHERE ts.shipment = 'FOBB'
                                                    AND ts.`type`= 'Local'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $local_fobb_actual_price = $db->query("SELECT AVG(tsp.final_price) as price FROM T_SAL_PRICE tsp
                                                INNER JOIN T_SAL_SHIPMENT tss ON tsp.shipment_id = tss.shipment_id
                                                WHERE tsp.date_final BETWEEN '$start_date' AND '$end_date'
                                                AND tss.category = 'FOB BARGE'
                                                AND tss.`type`= 'Local'")->getRowArray();
        }
        $data['local_fobb_actual_price'] = $local_fobb_actual_price;

        //dd($data['local_fobb_actual_price'], $start_date, $end_date);

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $local_fobb_actual_cost3 = $db->query("SELECT AVG(ts.cost) AS total FROM them_ship ts
                                                    WHERE ts.shipment = 'FOBB'
                                                    AND ts.`type`= 'Local'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $local_fobb_actual_cost1 = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR *100
                                                WHEN fat.SHKZG = 'H' THEN fat.DMBTR *-100 END ) AS total  FROM FI_ACT_TRS fat
                                                WHERE fat.HKONT IN ('5%', '6%', '7200010001', '7200010002')
                                                AND fat.PSPNR = 'AB3.11-00.00.00.00' 
                                                AND fat.GJAHR = '$year'
                                                AND fat.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")->getRowArray();
            $local_fobb_actual_cost2 = $db->query("SELECT SUM(fsi.FNL_QTY) AS total FROM  FI_SALES_INV fsi 
                                                INNER JOIN T_SAL_SHIPMENT tss ON tss.shipment_id = fsi.SHIPMENT_ID
                                                where fsi.BUDAT BETWEEN '$start_date' AND '$end_date'
                                                AND tss.category = 'FOB BARGE'
                                                AND tss.`type` = 'Local'")->getRowArray();

            //kalau ada datanya bakal di hapus handleDivisionnya 
            $local_fobb_actual_cost3 = handleDivision($local_fobb_actual_cost1['total'], $local_fobb_actual_cost2['total']);
        }

        $data['local_fobb_actual_cost3'] = $local_fobb_actual_cost3;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['local_fobb_actual_earning'] = $data['local_fobb_actual_price']['price'] - $data['local_fobb_actual_cost3']['total'] ?? 0;
        } else {

            $data['local_fobb_actual_earning'] = $data['local_fobb_actual_price']['price'] - $data['local_fobb_actual_cost3'] ?? 0;
        }

        //dd($data['local_fobb_actual_cost3'], $data['local_fobb_actual_earning']);


        $local_cif_rkap_price = $db->query("SELECT AVG(fir.PRC) AS price FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'CIF'
                                            AND fir.`TYPE`= 'Local'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['local_cif_rkap_price'] = $local_cif_rkap_price;

        $local_cif_rkap_cost = $db->query("SELECT AVG(fir.COST) as cost FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'CIF'
                                            AND fir.`TYPE`= 'LOCAL'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['local_cif_rkap_cost'] = $local_cif_rkap_cost;

        $data['local_cif_rkap_earning'] = $local_cif_rkap_price['price'] - $local_cif_rkap_cost['cost'];

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $local_cif_actual_price = $db->query("SELECT AVG(ts.price) as price FROM them_ship ts
                                                    WHERE ts.shipment = 'CIF'
                                                    AND ts.`type`= 'Local'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $local_cif_actual_price = $db->query("SELECT AVG(tsp.final_price) as price FROM T_SAL_PRICE tsp
                                            INNER JOIN T_SAL_SHIPMENT tss ON tsp.shipment_id = tss.shipment_id
                                            WHERE tsp.date_final BETWEEN '$start_date' AND '$end_date'
                                            AND tss.category = 'CIF'
                                            AND tss.`type` = 'Local'")->getRowArray();
        }
        $data['local_cif_actual_price'] = $local_cif_actual_price;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $local_cif_actual_cost3 = $db->query("SELECT AVG(ts.cost) AS total FROM them_ship ts
                                                    WHERE ts.shipment = 'CIF'
                                                    AND ts.`type`= 'Local'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $local_cif_actual_cost1 = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR *100
                                            WHEN fat.SHKZG = 'H' THEN fat.DMBTR *-100 END ) AS total  FROM FI_ACT_TRS fat
                                            WHERE fat.HKONT IN ('5%', '6%', '7200010001', '7200010002')
                                            AND fat.PSPNR IN ('AB3.11-00.00.00.00', 'AB3.12-00.00.00.00') 
                                            AND fat.GJAHR = '$year'
                                            AND fat.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")->getRowArray();
            $local_cif_actual_cost2 = $db->query("SELECT SUM(fsi.FNL_QTY) AS total FROM  FI_SALES_INV fsi 
                                            INNER JOIN T_SAL_SHIPMENT tss ON tss.shipment_id = fsi.SHIPMENT_ID
                                            AND fsi.BUDAT BETWEEN '$start_date' AND '$end_date'
                                            AND tss.category = 'CIF'
                                            AND tss.`type` = 'Local'")->getRowArray();


            $local_cif_actual_cost3 = handleDivision($local_cif_actual_cost1['total'], $local_cif_actual_cost2['total']);
        }
        $data['local_cif_actual_cost3'] = $local_cif_actual_cost3;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['local_cif_actual_earning'] = $data['local_cif_actual_price']['price'] - $data['local_cif_actual_cost3']['total'] ?? 0;
        } else {
            $data['local_cif_actual_earning'] = $data['local_cif_actual_price']['price'] - $data['local_cif_actual_cost3'] ?? 0;
        }

        $local_fb_rkap_price = $db->query("SELECT AVG(fir.PRC) AS price FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'Franco Pabrik'
                                            AND fir.`TYPE`= 'Local'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['local_fb_rkap_price'] = $local_fb_rkap_price;

        $local_fb_rkap_cost = $db->query("SELECT AVG(fir.COST) as cost FROM FI_IN_RKP fir
                                        WHERE fir.SHPMN = 'Franco Pabrik'
                                        AND fir.`TYPE`= 'LOCAL'
                                        AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                        AND fir.GJAHR = '$year'")->getRowArray();
        $data['local_fb_rkap_cost'] = $local_fb_rkap_cost;

        $data['local_fb_rkap_earning'] = $local_fb_rkap_price['price'] - $local_fb_rkap_cost['cost'];

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $local_fb_actual_price = $db->query("SELECT AVG(ts.price) as price FROM them_ship ts
                                                    WHERE ts.shipment = 'Franco Pabrik'
                                                    AND ts.`type`= 'Local'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $local_fb_actual_price = $db->query("SELECT AVG(tsp.final_price) as price FROM T_SAL_PRICE tsp
                                            INNER JOIN T_SAL_SHIPMENT tss ON tsp.shipment_id = tss.shipment_id
                                            WHERE tsp.date_final BETWEEN '$start_date' AND '$end_date'
                                            AND tss.category = 'Franco Pabrik'
                                            AND tss.`type` = 'Local'")->getRowArray();
        }
        $data['local_fb_actual_price'] = $local_fb_actual_price;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $local_fb_actual_cost3 = $db->query("SELECT AVG(ts.cost) AS total FROM them_ship ts
                                                    WHERE ts.shipment = 'Franco Pabrik'
                                                    AND ts.`type`= 'Local'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $local_fb_actual_cost1 = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR *100
                                            WHEN fat.SHKZG = 'H' THEN fat.DMBTR *-100 END ) AS total  FROM FI_ACT_TRS fat
                                            WHERE fat.HKONT IN ('5%', '6%', '7200010001', '7200010002')
                                            AND fat.PSPNR IN ('AB3.11-00.00.00.00',  'AB3.13-00.00.00.00') 
                                            AND fat.GJAHR = '$year'
                                            AND fat.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")->getRowArray();
            $local_fb_actual_cost2 = $db->query("SELECT SUM(fsi.FNL_QTY) AS total FROM  FI_SALES_INV fsi 
                                            INNER JOIN T_SAL_SHIPMENT tss ON tss.shipment_id = fsi.SHIPMENT_ID
                                            AND fsi.BUDAT BETWEEN '$start_date' AND '$end_date'
                                            AND tss.category = 'Franco Pabrik'
                                            AND tss.`type` = 'Local'")->getRowArray();

            //kalau ada datanya bakal di hapus handleDivisionnya 
            $local_fb_actual_cost3 = handleDivision($local_fb_actual_cost1['total'], $local_fb_actual_cost2['total']);
        }
        $data['local_fb_actual_cost3'] = $local_fb_actual_cost3;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['local_fb_actual_earning'] = $data['local_fb_actual_price']['price'] - $data['local_fb_actual_cost3']['total'] ?? 0;
        } else {
            $data['local_fb_actual_earning'] = $data['local_fb_actual_price']['price'] - $data['local_fb_actual_cost3'] ?? 0;
        }


        $export_fobb_rkap_price = $db->query("SELECT AVG(fir.PRC) AS price FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'FOBB'
                                            AND fir.`TYPE`= 'Export'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['export_fobb_rkap_price'] = $export_fobb_rkap_price;
        $export_fobb_rkap_cost = $db->query("SELECT AVG(fir.COST) as cost FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'FOBB'
                                            AND fir.`TYPE`= 'Export'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['export_fobb_rkap_cost'] = $export_fobb_rkap_cost;

        $data['export_fobb_rkap_earning'] = $export_fobb_rkap_price['price'] - $export_fobb_rkap_cost['cost'];

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $export_fobb_actual_price = $db->query("SELECT AVG(ts.price) as price FROM them_ship ts
                                                    WHERE ts.shipment = 'FOBB'
                                                    AND ts.`type`= 'Export'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $export_fobb_actual_price = $db->query("SELECT AVG(tsp.final_price) as price FROM T_SAL_PRICE tsp
                                                INNER JOIN T_SAL_SHIPMENT tss ON tsp.shipment_id = tss.shipment_id
                                                WHERE tsp.date_final BETWEEN '$start_date' AND '$end_date'
                                                AND tss.category = 'FOBB'
                                                AND tss.`type` = 'Export'")->getRowArray();
        }
        $data['export_fobb_actual_price'] = $export_fobb_actual_price;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $export_fobb_actual_cost3 = $db->query("SELECT AVG(ts.cost) AS total FROM them_ship ts
                                                    WHERE ts.shipment = 'FOBB'
                                                    AND ts.`type`= 'Export'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $export_fobb_actual_cost1 = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR *100
                                            WHEN fat.SHKZG = 'H' THEN fat.DMBTR *-100 END ) AS total  FROM FI_ACT_TRS fat
                                            WHERE fat.HKONT IN ('5%', '6%', '7200010001', '7200010002')
                                            AND fat.PSPNR = 'AB3.21-00.00.00.00' 
                                            AND fat.GJAHR = '$year'
                                            AND fat.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")->getRowArray();
            $export_fobb_actual_cost2 = $db->query("SELECT SUM(fsi.FNL_QTY) AS total FROM  FI_SALES_INV fsi 
                                            INNER JOIN T_SAL_SHIPMENT tss ON tss.shipment_id = fsi.SHIPMENT_ID
                                            AND fsi.BUDAT BETWEEN '$start_date' AND '$end_date'
                                            AND tss.category = 'FOB BARGE'
                                            AND tss.`type` = 'Local'")->getRowArray();

            //kalau ada datanya bakal di hapus handleDivisionnya di ganti bagi
            $export_fobb_actual_cost3 = handleDivision($export_fobb_actual_cost1['total'], $export_fobb_actual_cost2['total']);
        }
        $data['export_fobb_actual_cost3'] = $export_fobb_actual_cost3;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_fobb_actual_earning'] = $data['export_fobb_actual_price']['price'] - $data['export_fobb_actual_cost3']['total'] ?? 0;
        } else {
            $data['export_fobb_actual_earning'] = $data['export_fobb_actual_price']['price'] - $data['export_fobb_actual_cost3'] ?? 0;
        }
        $export_cif_rkap_price = $db->query("SELECT AVG(fir.PRC) AS price FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'CIF'
                                            AND fir.`TYPE`= 'Export'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['export_cif_rkap_price'] = $export_cif_rkap_price;
        $export_cif_rkap_cost = $db->query("SELECT AVG(fir.COST) as cost FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'CIF'
                                            AND fir.`TYPE`= 'Export'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['export_cif_rkap_cost'] = $export_cif_rkap_cost;

        $data['export_cif_rkap_earning'] = $export_cif_rkap_price['price'] - $export_cif_rkap_cost['cost'];

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $export_cif_actual_price = $db->query("SELECT AVG(ts.price) as price FROM them_ship ts
                                                    WHERE ts.shipment = 'CIF'
                                                    AND ts.`type`= 'Export'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $export_cif_actual_price = $db->query("SELECT AVG(tsp.final_price) as price FROM T_SAL_PRICE tsp
                                                INNER JOIN T_SAL_SHIPMENT tss ON tsp.shipment_id = tss.shipment_id
                                                WHERE tsp.date_final BETWEEN '$start_date' AND '$end_date'
                                                AND tss.category = 'CIF'
                                                AND tss.`type` = 'Export'")->getRowArray();
        }
        $data['export_cif_actual_price'] = $export_cif_actual_price;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $export_cif_actual_cost3 = $db->query("SELECT AVG(ts.cost) As total FROM them_ship ts
                                                    WHERE ts.shipment = 'CIF'
                                                    AND ts.`type`= 'Export'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $export_cif_actual_cost1 = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR *100
                                            WHEN fat.SHKZG = 'H' THEN fat.DMBTR *-100 END ) AS total  FROM FI_ACT_TRS fat
                                            WHERE fat.HKONT IN ('5%', '6%', '7200010001', '7200010002')
                                            AND fat.PSPNR IN ('AB3.21-00.00.00.00', 'AB3.22-00.00.00.00') 
                                            AND fat.GJAHR = '$year'
                                            AND fat.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")->getRowArray();
            $export_cif_actual_cost2 = $db->query("SELECT SUM(fsi.FNL_QTY) AS total FROM  FI_SALES_INV fsi 
                                            INNER JOIN T_SAL_SHIPMENT tss ON tss.shipment_id = fsi.SHIPMENT_ID
                                            AND fsi.BUDAT BETWEEN '$start_date' AND '$end_date'
                                            AND tss.category = 'CIF'
                                            AND tss.`type` = 'Local'")->getRowArray();

            //kalau ada datanya bakal di hapus handleDivisionnya di ganti bagi
            $export_cif_actual_cost3 = handleDivision($export_cif_actual_cost1['total'], $export_cif_actual_cost2['total']);
        }
        $data['export_cif_actual_cost3'] = $export_cif_actual_cost3;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_cif_actual_earning'] = $data['export_cif_actual_price']['price'] - $data['export_cif_actual_cost3']['total'] ?? 0;
        } else {
            $data['export_cif_actual_earning'] = $data['export_cif_actual_price']['price'] - $data['export_cif_actual_cost3'] ?? 0;
        }

        $export_fas_rkap_price = $db->query("SELECT AVG(fir.PRC) AS price FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'FAS'
                                            AND fir.`TYPE`= 'Export'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['export_fas_rkap_price'] = $export_fas_rkap_price;
        $export_fas_rkap_cost = $db->query("SELECT AVG(fir.COST) as cost FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'FAS'
                                            AND fir.`TYPE`= 'Export'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['export_fas_rkap_cost'] = $export_fas_rkap_cost;

        $data['export_fas_rkap_earning'] = $export_fas_rkap_price['price'] - $export_fas_rkap_cost['cost'];

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $export_fas_actual_price = $db->query("SELECT AVG(ts.price) as price FROM them_ship ts
                                                    WHERE ts.shipment = 'FAS'
                                                    AND ts.`type`= 'Export'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $export_fas_actual_price = $db->query("SELECT AVG(tsp.final_price) as price FROM T_SAL_PRICE tsp
                                                INNER JOIN T_SAL_SHIPMENT tss ON tsp.shipment_id = tss.shipment_id
                                                WHERE tsp.date_final BETWEEN '$start_date' AND '$end_date'
                                                AND tss.category = 'FAS'
                                                AND tss.`type` = 'Export'")->getRowArray();
        }
        $data['export_fas_actual_price'] = $export_fas_actual_price;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $export_fas_actual_cost3 = $db->query("SELECT AVG(ts.cost) As total FROM them_ship ts
                                                    WHERE ts.shipment = 'FAS'
                                                    AND ts.`type`= 'Export'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $export_fas_actual_cost1 = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR *100
                                            WHEN fat.SHKZG = 'H' THEN fat.DMBTR *-100 END ) AS total  FROM FI_ACT_TRS fat
                                            WHERE fat.HKONT IN ('5%', '6%', '7200010001', '7200010002')
                                            AND fat.PSPNR IN ('AB3.21-00.00.00.00', 'AB3.24-00.00.00.00') 
                                            AND fat.GJAHR = '$year'
                                            AND fat.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")->getRowArray();
            $export_fas_actual_cost2 = $db->query("SELECT SUM(fsi.FNL_QTY) AS total FROM  FI_SALES_INV fsi 
                                            INNER JOIN T_SAL_SHIPMENT tss ON tss.shipment_id = fsi.SHIPMENT_ID
                                            AND fsi.BUDAT BETWEEN '$start_date' AND '$end_date'
                                            AND tss.category = 'FAS'
                                            AND tss.`type` = 'Local'")->getRowArray();

            //kalau ada datanya bakal di hapus handleDivisionnya di ganti bagi
            $export_fas_actual_cost3 = handleDivision($export_fas_actual_cost1['total'], $export_fas_actual_cost2['total']);
        }
        $data['export_fas_actual_cost3'] = $export_fas_actual_cost3;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_fas_actual_earning'] = $data['export_fas_actual_price']['price'] - $data['export_fas_actual_cost3']['total'] ?? 0;
        } else {
            $data['export_fas_actual_earning'] = $data['export_fas_actual_price']['price'] - $data['export_fas_actual_cost3'] ?? 0;
        }

        $export_mv_rkap_price = $db->query("SELECT AVG(fir.PRC) AS price FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'MV'
                                            AND fir.`TYPE`= 'Export'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['export_mv_rkap_price'] = $export_mv_rkap_price;
        $export_mv_rkap_cost = $db->query("SELECT AVG(fir.COST) as cost FROM FI_IN_RKP fir
                                            WHERE fir.SHPMN = 'MV'
                                            AND fir.`TYPE`= 'Export'
                                            AND fir.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')
                                            AND fir.GJAHR = '$year'")->getRowArray();
        $data['export_mv_rkap_cost'] = $export_mv_rkap_cost;

        $data['export_mv_rkap_earning'] = $export_mv_rkap_price['price'] - $export_mv_rkap_cost['cost'];

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $export_mv_actual_price = $db->query("SELECT AVG(ts.price) as price FROM them_ship ts
                                                    WHERE ts.shipment = 'MV'
                                                    AND ts.`type`= 'Export'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $export_mv_actual_price = $db->query("SELECT AVG(tsp.final_price) as price FROM T_SAL_PRICE tsp
                                                INNER JOIN T_SAL_SHIPMENT tss ON tsp.shipment_id = tss.shipment_id
                                                WHERE tsp.date_final BETWEEN '$start_date' AND '$end_date'
                                                AND tss.category = 'MV'
                                                AND tss.`type` = 'Export'")->getRowArray();
        }
        $data['export_mv_actual_price'] = $export_mv_actual_price;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $export_mv_actual_cost3 = $db->query("SELECT AVG(ts.cost) As total FROM them_ship ts
                                                    WHERE ts.shipment = 'MV'
                                                    AND ts.`type`= 'Export'
                                                    AND ts.`month`BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {

            $export_mv_actual_cost1 = $db->query("SELECT SUM(CASE WHEN fat.SHKZG = 'S' THEN fat.DMBTR *100
                                            WHEN fat.SHKZG = 'H' THEN fat.DMBTR *-100 END ) AS total  FROM FI_ACT_TRS fat
                                            WHERE fat.HKONT IN ('5%', '6%', '7200010001', '7200010002')
                                            AND fat.PSPNR IN ('AB3.21-00.00.00.00', 'AB3.23-00.00.00.00') 
                                            AND fat.GJAHR = '$year'
                                            AND fat.MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")->getRowArray();
            $export_mv_actual_cost2 = $db->query("SELECT SUM(fsi.FNL_QTY) AS total FROM  FI_SALES_INV fsi 
                                            INNER JOIN T_SAL_SHIPMENT tss ON tss.shipment_id = fsi.SHIPMENT_ID
                                            AND fsi.BUDAT BETWEEN '$start_date' AND '$end_date'
                                            AND tss.category = 'MV'
                                            AND tss.`type` = 'Local'")->getRowArray();

            //kalau ada datanya bakal di hapus handleDivisionnya di ganti bagi
            $export_mv_actual_cost3 = handleDivision($export_mv_actual_cost1['total'], $export_mv_actual_cost2['total']);
        }
        $data['export_mv_actual_cost3'] = $export_mv_actual_cost3;

        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_mv_actual_earning'] = $data['export_mv_actual_price']['price'] - $data['export_mv_actual_cost3']['total'] ?? 0;
        } else {
            $data['export_mv_actual_earning'] = $data['export_mv_actual_price']['price'] - $data['export_mv_actual_cost3'] ?? 0;
        }



        //dd($data['local_fobb_rkap_price'],  $data['local_fobb_rkap_cost'], $data['local_fobb_rkap_earning'], $data['local_fobb_actual_price'], $data['local_fobb_actual_cost3'], $data['local_fobb_actual_eraning']);

        $data['COGS_margin'] = $this->COGS_margin($year);
        $data['COGS_margin_YTD'] = $this->COGS_margin_YTD($year);
        $data['Gross_Profit_Margin'] = $this->Gross_Profit_Margin($year);
        $data['Gross_Profit_MarginYTD'] = $this->Gross_Profit_MarginYTD($year);
        $data['Opr_Profit_Margin'] = $this->Opr_Profit_Margin($year);
        $data['Opr_Profit_MarginYTD'] = $this->Opr_Profit_MarginYTD($year);
        $data['EBT_Margin'] = $this->EBT_Margin($year);
        $data['EBT_MarginYTD'] = $this->EBT_MarginYTD($year);
        $data['eatMargin'] = $this->eatMargin($year);
        $data['eatMarginYTD'] = $this->eatMarginYTD($year);
        $data['ebitdaMargin'] = $this->ebitdaMargin($year);
        $data['ebitdaMarginYTD'] = $this->ebitdaMarginYTD($year);
        $data['ebdaMargin'] = $this->ebdaMargin($year);
        $data['ebdaMarginYTD'] = $this->ebdaMarginYTD($year);

        //dd($data['COGS_margin'], $data['Gross_Profit_Margin'], $data['Opr_Profit_Margin'],  $data['EBT_Margin'], $data['eatMargin'], $data['ebitdaMargin'],  $data['ebdaMargin'], $year);
        echo view("pages/finance/profitability", $data);
    }

    public function COGS_margin($year)
    {
        function getQuery($from, $to, $year)
        {
            return "SELECT (F1.sales/F2.sales)*100 AS PER_SALES, F1.FI AS MONTH FROM
            (
                SELECT SUM(fab.PER_SALES) AS sales, FI FROM FI_ACT_BAL fab 
                where fab.GL_ACCOUNT LIKE '5%'
                AND fab.FI BETWEEN $from AND $to
                AND fab.COMP = 'HH10'
                AND fab.FISC LIKE '%$year%'
                GROUP BY FI
            ) AS F1,
            (
                SELECT SUM(fab.PER_SALES)*-1 AS sales , FI FROM FI_ACT_BAL fab 
                where fab.GL_ACCOUNT LIKE '4%'
                AND fab.FI BETWEEN $from AND $to
                AND fab.COMP = 'HH10'
                AND fab.FISC LIKE '%$year%'
                GROUP BY FI
            ) AS F2
            where F1.FI = F2.FI;";
        };
        $db = Database::connect();

        $data = $db->query(getQuery('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQuery('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['PER_SALES'];
        }
        $arr = [
            'PER_SALES' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function COGS_margin_YTD($year)
    {
        function getQueryYTD($from, $to, $year)
        {
            return "SELECT (F1.balance/F2.balance)*100 AS Hasil, F1.FI AS MONTH FROM
            (
                SELECT SUM(fab.BALANCE) AS balance, FI FROM FI_ACT_BAL fab 
                where fab.GL_ACCOUNT LIKE '5%'
                AND fab.FI BETWEEN $from AND $to
                AND fab.COMP = 'HH10'
                AND fab.FISC LIKE '%$year%'
                GROUP BY FI
            ) AS F1,
            (
                SELECT SUM(fab.BALANCE)*-1 AS balance , FI FROM FI_ACT_BAL fab 
                where fab.GL_ACCOUNT LIKE '4%'
                AND fab.FI BETWEEN $from AND $to
                AND fab.COMP = 'HH10'
                AND fab.FISC LIKE '%$year%'
                GROUP BY FI
            ) AS F2
            where F1.FI = F2.FI;";
        };
        $db = Database::connect();

        $data = $db->query(getQueryYTD('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryYTD('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['Hasil'];
        }
        $arr = [
            'Hasil' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function Gross_Profit_Margin($year)
    {
        function getQuerygross($from, $to, $year)
        {
            return "SELECT ((F1.sales-F2.sales)/F1.sales)*100 AS PER_SALES, F1.FI AS MONTH FROM
        (
            SELECT SUM(fab.PER_SALES)*-1 AS sales, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '4%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F1,
        (
            SELECT SUM(fab.PER_SALES) AS sales, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '5%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F2
        where F1.FI = F2.FI;";
        }

        $db = Database::connect();


        $data = $db->query(getQuerygross('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQuerygross('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['PER_SALES'];
        }
        $arr = [
            'PER_SALES' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function Gross_Profit_MarginYTD($year)
    {
        function getQuerygrossYTD($from, $to, $year)
        {
            return "SELECT ((F1.balance-F2.balance)/F1.balance)*100 AS Hasil, F1.FI AS MONTH FROM
        (
            SELECT SUM(fab.BALANCE)*-1 AS balance, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '4%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F1,
        (
            SELECT SUM(fab.BALANCE) AS balance, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '5%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F2
        where F1.FI = F2.FI;";
        }

        $db = Database::connect();


        $data = $db->query(getQuerygrossYTD('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQuerygrossYTD('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['Hasil'];
        }
        $arr = [
            'Hasil' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function Opr_Profit_Margin($year)
    {
        function getQueryOpr($from, $to, $year)
        {
            return "SELECT ((F1.sales-F2.sales-F3.sales)/F1.sales)*100 AS PER_SALES, F1.FI AS MONTH FROM
        (
            SELECT SUM(fab.PER_SALES)*-1 AS sales, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '4%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F1,
        (
            SELECT SUM(fab.PER_SALES) AS sales, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '5%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F2,
        (
            SELECT SUM(fab.PER_SALES) AS sales, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '6%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F3
        where F1.FI = F2.FI AND F2.FI = F3.FI;";
        }

        $db = Database::connect();


        $data = $db->query(getQueryOpr('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryOpr('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['PER_SALES'];
        }
        $arr = [
            'PER_SALES' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function Opr_Profit_MarginYTD($year)
    {
        function getQueryOprYTD($from, $to, $year)
        {
            return "SELECT ((F1.balance-F2.balance-F3.balance)/F1.balance)*100 AS Hasil, F1.FI AS MONTH FROM
        (
            SELECT SUM(fab.BALANCE)*-1 AS balance, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '4%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F1,
        (
            SELECT SUM(fab.BALANCE) AS balance, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '5%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F2,
        (
            SELECT SUM(fab.BALANCE) AS balance, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '6%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F3
        where F1.FI = F2.FI AND F2.FI = F3.FI;";
        }

        $db = Database::connect();


        $data = $db->query(getQueryOprYTD('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryOprYTD('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['Hasil'];
        }
        $arr = [
            'Hasil' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function EBT_Margin($year)
    {

        function getQueryEBT($from, $to, $year)
        {
            return "SELECT((F1.sales-F2.sales-F3.sales-F4.sales)/F1.sales)*100 AS PER_SALES, F1.FI AS MONTH FROM
        (
            SELECT SUM(fab.PER_SALES)*-1 AS sales, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '4%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F1,
        (
            SELECT SUM(fab.PER_SALES) AS sales, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '5%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F2,
        (
            SELECT SUM(fab.PER_SALES) AS sales, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '6%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F3,
        (
            SELECT SUM(fab.PER_SALES) AS sales, FI FROM FI_ACT_BAL fab 
            where (fab.GL_ACCOUNT BETWEEN '7100000000' AND '7100999999' OR fab.GL_ACCOUNT BETWEEN '7200000000' AND '7200999999')
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F4
        where F1.FI = F2.FI AND F2.FI = F3.FI AND F3.FI = F4.FI;";
        }

        $db = Database::connect();


        $data = $db->query(getQueryEBT('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryEBT('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['PER_SALES'];
        }
        $arr = [
            'PER_SALES' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function EBT_MarginYTD($year)
    {

        function getQueryEBTYTD($from, $to, $year)
        {
            return "SELECT((F1.balance-F2.balance-F3.balance-F4.balance)/F1.balance)*100 AS Hasil, F1.FI AS MONTH FROM
        (
            SELECT SUM(fab.BALANCE)*-1 AS balance, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '4%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F1,
        (
            SELECT SUM(fab.BALANCE) AS balance, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '5%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F2,
        (
            SELECT SUM(fab.BALANCE) AS balance, FI FROM FI_ACT_BAL fab 
            where fab.GL_ACCOUNT LIKE '6%'
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F3,
        (
            SELECT SUM(fab.BALANCE) AS balance, FI FROM FI_ACT_BAL fab 
            where (fab.GL_ACCOUNT BETWEEN '7100000000' AND '7100999999' OR fab.GL_ACCOUNT BETWEEN '7200000000' AND '7200999999')
            AND fab.FI BETWEEN $from AND $to
            AND fab.COMP = 'HH10'
            AND fab.FISC LIKE '%$year%'
            GROUP BY FI
        ) AS F4
        where F1.FI = F2.FI AND F2.FI = F3.FI AND F3.FI = F4.FI;";
        }

        $db = Database::connect();


        $data = $db->query(getQueryEBTYTD('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryEBTYTD('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['Hasil'];
        }
        $arr = [
            'Hasil' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function eatMargin($year)
    {
        function getQueryeat($from, $to, $year)
        {
            return "SELECT ((f1.per_sale-f2.per_sale-f3.per_sale-f4.per_sale-f5.per_sale)/f1.per_sale)*100 AS per_saless, f1.fi AS MONTH from
        (
            SELECT sum(PER_SALES)*-1 AS per_sale, fi
            FROM FI_ACT_BAL
            WHERE COMP='HH10'
            AND GL_ACCOUNT BETWEEN 4000000000 AND 4999999999
            AND fisc like '%$year%'
            AND fi BETWEEN $from AND $to
            GROUP BY fi
        ) AS f1,
        (
            SELECT sum(PER_SALES) AS per_sale, fi
            FROM FI_ACT_BAL
            WHERE COMP='HH10'
            AND GL_ACCOUNT BETWEEN 5000000000 AND 5999999999
            AND fisc like '%$year%'
            AND fi BETWEEN $from AND $to
            GROUP BY fi
        ) AS f2,
        (
            SELECT sum(PER_SALES) AS per_sale, fi
            FROM FI_ACT_BAL
            WHERE COMP='HH10'
            AND GL_ACCOUNT BETWEEN 6000000000 AND 6999999999
            AND fisc like '%$year%'
            AND fi BETWEEN $from AND $to
            GROUP BY fi
        ) AS f3,
        (
            SELECT SUM(PER_SALES) AS per_sale, fi
            FROM FI_ACT_BAL
            WHERE COMP='HH10'
            AND (GL_ACCOUNT BETWEEN 7100000000 AND 7100999999 or GL_ACCOUNT BETWEEN 7200000000 AND 7200999999)
            AND fisc like '%$year%'
            AND fi BETWEEN $from AND $to
            GROUP BY fi
        ) AS f4,
        (
            (
                SELECT SUM(PER_SALES) AS per_sale, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND (GL_ACCOUNT BETWEEN 9100010000 AND 9100019999 or GL_ACCOUNT BETWEEN 9200020000 AND 9200029999)
                AND fisc LIKE '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            )
                UNION
            (
                SELECT SUM(PER_SALES) AS per_sale , fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND (GL_ACCOUNT BETWEEN 9100010000 AND 9100019999 or GL_ACCOUNT BETWEEN 9200020000 AND 9200029999)
                AND fisc LIKE '%%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            )
        ) AS f5
        where f1.fi=f2.fi AND f2.fi=f3.fi AND f3.fi=f4.fi AND f4.fi=f5.fi;";
        }
        $db = \Config\Database::connect();


        $data = $db->query(getQueryeat('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryeat('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['per_saless'];
        }
        $arr = [
            'per_saless' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function eatMarginYTD($year)
    {
        function getQueryeatYTD($from, $to, $year)
        {
            return "SELECT ((f1.balance-f2.balance-f3.balance-f4.balance-f5.balance)/f1.balance)*100 AS Hasil, f1.fi AS MONTH from
        (
            SELECT sum(BALANCE)*-1 AS balance, fi
            FROM FI_ACT_BAL
            WHERE COMP='HH10'
            AND GL_ACCOUNT BETWEEN 4000000000 AND 4999999999
            AND fisc like '%$year%'
            AND fi BETWEEN $from AND $to
            GROUP BY fi
        ) AS f1,
        (
            SELECT sum(BALANCE) AS balance, fi
            FROM FI_ACT_BAL
            WHERE COMP='HH10'
            AND GL_ACCOUNT BETWEEN 5000000000 AND 5999999999
            AND fisc like '%$year%'
            AND fi BETWEEN $from AND $to
            GROUP BY fi
        ) AS f2,
        (
            SELECT sum(BALANCE) AS balance, fi
            FROM FI_ACT_BAL
            WHERE COMP='HH10'
            AND GL_ACCOUNT BETWEEN 6000000000 AND 6999999999
            AND fisc like '%$year%'
            AND fi BETWEEN $from AND $to
            GROUP BY fi
        ) AS f3,
        (
            SELECT SUM(BALANCE) AS balance, fi
            FROM FI_ACT_BAL
            WHERE COMP='HH10'
            AND (GL_ACCOUNT BETWEEN 7100000000 AND 7100999999 or GL_ACCOUNT BETWEEN 7200000000 AND 7200999999)
            AND fisc like '%$year%'
            AND fi BETWEEN $from AND $to
            GROUP BY fi
        ) AS f4,
        (
            (
                SELECT SUM(BALANCE) AS balance, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND (GL_ACCOUNT BETWEEN 9100010000 AND 9100019999 or GL_ACCOUNT BETWEEN 9200020000 AND 9200029999)
                AND fisc LIKE '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            )
                UNION
            (
                SELECT SUM(BALANCE) AS balance , fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND (GL_ACCOUNT BETWEEN 9100010000 AND 9100019999 or GL_ACCOUNT BETWEEN 9200020000 AND 9200029999)
                AND fisc LIKE '%%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            )
        ) AS f5
        where f1.fi=f2.fi AND f2.fi=f3.fi AND f3.fi=f4.fi AND f4.fi=f5.fi;";
        }
        $db = \Config\Database::connect();


        $data = $db->query(getQueryeatYTD('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryeatYTD('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['Hasil'];
        }
        $arr = [
            'Hasil' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }



    public function ebitdaMargin($year)
    {
        function getQueryebit($from, $to, $year)
        {

            return "SELECT (((f1.per_sale - f2.per_sale - f3.per_sale - f4.per_sale) + (f5.per_sale + f6.per_sale + f7.per_sale + f8.per_sale))/f1.per_sale)*100 AS per_saless,
            f1.fi AS MONTH from
            (
                SELECT sum(PER_SALES)*-1 AS per_sale, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 4000000000 AND 4999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f1,
            (
                SELECT sum(PER_SALES) AS per_sale, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 5000000000 AND 5999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f2,
            (
                SELECT sum(PER_SALES) AS per_sale, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 6000000000 AND 6999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f3,
            (
                SELECT SUM(PER_SALES) AS per_sale, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND (GL_ACCOUNT BETWEEN 7100000000 AND 7100999999 or GL_ACCOUNT BETWEEN 7200000000 AND 7200999999)
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f4,
            (
                SELECT SUM(a1.PER_SALES) AS per_sale, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='interest'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f5,
            (
                (
                    SELECT SUM(PER_SALES) AS per_sale, fi
                    FROM FI_ACT_BAL
                    WHERE COMP='HH10'
                    AND (GL_ACCOUNT BETWEEN 9100010000 AND 9100019999 or GL_ACCOUNT BETWEEN 9200020000 AND 9200029999)
                    AND fisc LIKE '%$year%'
                    AND fi BETWEEN $from AND $to
                    GROUP BY fi
                )
                    UNION
                (
                    SELECT SUM(PER_SALES) AS per_sale , fi
                    FROM FI_ACT_BAL
                    WHERE COMP='HH10'
                    AND (GL_ACCOUNT BETWEEN 9100010000 AND 9100019999 or GL_ACCOUNT BETWEEN 9200020000 AND 9200029999)
                    AND fisc LIKE '%%'
                    AND fi BETWEEN $from AND $to
                    GROUP BY fi
                )
            ) AS f6,
            (
                SELECT SUM(a1.PER_SALES) AS per_sale, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='DEP'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f7,
            (
                SELECT SUM(a1.PER_SALES) AS per_sale, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='AMRT'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f8
            where f1.fi=f2.fi 
            AND f2.fi=f3.fi 
            AND f3.fi=f4.fi 
            AND f4.fi=f5.fi 
            AND f5.fi=f6.fi
            AND f6.fi=f7.fi
            AND f7.fi=f8.fi;";
        }
        $db = \Config\Database::connect();

        $data = $db->query(getQueryebit('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryebit('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['per_saless'];
        }
        $arr = [
            'per_saless' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function ebitdaMarginYTD($year)
    {
        function getQueryebitYTD($from, $to, $year)
        {

            return "SELECT (((f1.balance - f2.balance - f3.balance - f4.balance) + (f5.balance + f6.balance + f7.balance + f8.balance))/f1.balance)*100 AS Hasil,
            f1.fi AS MONTH from
            (
                SELECT sum(BALANCE)*-1 AS balance, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 4000000000 AND 4999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f1,
            (
                SELECT sum(BALANCE) AS balance, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 5000000000 AND 5999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f2,
            (
                SELECT sum(BALANCE) AS balance, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 6000000000 AND 6999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f3,
            (
                SELECT SUM(BALANCE) AS balance, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND (GL_ACCOUNT BETWEEN 7100000000 AND 7100999999 or GL_ACCOUNT BETWEEN 7200000000 AND 7200999999)
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f4,
            (
                SELECT SUM(a1.BALANCE) AS balance, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='interest'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f5,
            (
                (
                    SELECT SUM(BALANCE) AS balance, fi
                    FROM FI_ACT_BAL
                    WHERE COMP='HH10'
                    AND (GL_ACCOUNT BETWEEN 9100010000 AND 9100019999 or GL_ACCOUNT BETWEEN 9200020000 AND 9200029999)
                    AND fisc LIKE '%$year%'
                    AND fi BETWEEN $from AND $to
                    GROUP BY fi
                )
                    UNION
                (
                    SELECT SUM(BALANCE) AS balance , fi
                    FROM FI_ACT_BAL
                    WHERE COMP='HH10'
                    AND (GL_ACCOUNT BETWEEN 9100010000 AND 9100019999 or GL_ACCOUNT BETWEEN 9200020000 AND 9200029999)
                    AND fisc LIKE '%%'
                    AND fi BETWEEN $from AND $to
                    GROUP BY fi
                )
            ) AS f6,
            (
                SELECT SUM(a1.BALANCE) AS balance, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='DEP'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f7,
            (
                SELECT SUM(a1.BALANCE) AS balance, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='AMRT'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f8
            where f1.fi=f2.fi 
            AND f2.fi=f3.fi 
            AND f3.fi=f4.fi 
            AND f4.fi=f5.fi 
            AND f5.fi=f6.fi
            AND f6.fi=f7.fi
            AND f7.fi=f8.fi;";
        }
        $db = \Config\Database::connect();

        $data = $db->query(getQueryebitYTD('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryebitYTD('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['Hasil'];
        }
        $arr = [
            'Hasil' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function ebdaMargin($year)
    {
        function getQueryebda($from, $to, $year)
        {
            return "SELECT (((f1.per_sale-f2.per_sale-f3.per_sale-f4.per_sale)+(f5.per_sale+f6.per_sale))/f1.per_sale)*100 AS per_saless,
            f1.fi AS MONTH from
            (
                SELECT sum(PER_SALES)*-1 AS per_sale, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 4000000000 AND 4999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f1,
            (
                SELECT sum(PER_SALES) AS per_sale, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 5000000000 AND 5999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f2,
            (
                SELECT sum(PER_SALES) AS per_sale, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 6000000000 AND 6999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f3,
            (
                SELECT SUM(PER_SALES) AS per_sale, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND (GL_ACCOUNT BETWEEN 7100000000 AND 7100999999 or GL_ACCOUNT BETWEEN 7200000000 AND 7200999999)
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f4,
            (
                SELECT SUM(a1.PER_SALES) AS per_sale, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='DEP'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f5,
            (
                SELECT SUM(a1.PER_SALES) AS per_sale, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='AMRT'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f6
            where f1.fi=f2.fi 
            AND f2.fi=f3.fi 
            AND f3.fi=f4.fi 
            AND f4.fi=f5.fi 
            AND f5.fi=f6.fi;";
        }
        $db = \Config\Database::connect();

        $data = $db->query(getQueryebda('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryebda('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['per_saless'];
        }
        $arr = [
            'per_saless' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
    }

    public function ebdaMarginYTD($year)
    {
        function getQueryebdaYTD($from, $to, $year)
        {
            return "SELECT (((f1.balance-f2.balance-f3.balance-f4.balance)+(f5.balance+f6.balance))/f1.balance)*100 AS Hasil,
            f1.fi AS MONTH from
            (
                SELECT sum(BALANCE)*-1 AS balance, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 4000000000 AND 4999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f1,
            (
                SELECT sum(BALANCE) AS balance, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 5000000000 AND 5999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f2,
            (
                SELECT sum(BALANCE) AS balance, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND GL_ACCOUNT BETWEEN 6000000000 AND 6999999999
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f3,
            (
                SELECT SUM(BALANCE) AS balance, fi
                FROM FI_ACT_BAL
                WHERE COMP='HH10'
                AND (GL_ACCOUNT BETWEEN 7100000000 AND 7100999999 or GL_ACCOUNT BETWEEN 7200000000 AND 7200999999)
                AND fisc like '%$year%'
                AND fi BETWEEN $from AND $to
                GROUP BY fi
            ) AS f4,
            (
                SELECT SUM(a1.BALANCE) AS balance, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='DEP'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f5,
            (
                SELECT SUM(a1.BALANCE) AS balance, a1.fi
                FROM FI_ACT_BAL a1, ZFIT_CMSGL a2
                WHERE a1.GL_ACCOUNT = a2.GL
                and a1.COMP='HH10'
                AND a2.COMP='HH10'
                AND a2.id='AMRT'
                AND a1.fi BETWEEN $from AND $to
                AND fisc LIKE '%$year%'
                GROUP BY fi
            ) AS f6
            where f1.fi=f2.fi 
            AND f2.fi=f3.fi 
            AND f3.fi=f4.fi 
            AND f4.fi=f5.fi 
            AND f5.fi=f6.fi;";
        }
        $db = \Config\Database::connect();

        $data = $db->query(getQueryebdaYTD('01', '11', $year))->getResultArray();
        $data2 = $db->query(getQueryebdaYTD('12', '16', $year))->getResultArray();
        $bulan12 = 0;
        foreach ($data2 as $row) {
            $bulan12 = $bulan12 + $row['Hasil'];
        }
        $arr = [
            'Hasil' => $bulan12,
            'MONTH' => '12'
        ];
        array_push($data, $arr);
        //dd($data);
        return $data;
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

        if ($month == 16) {
            $search_balance_sheet = "FI = 16";
        } else {
            $search_balance_sheet = "FI = $month";
        }

        $month_awl = 1;
        $month_akr = $month;

        $db = Database::connect();

        $data['title'] = "Balance Sheet";
        $FiActBal = new FiActBalance();
        $builder_bal = $FiActBal->builder();

        $data['years'] = $builder_bal->select("DISTINCT(FISC)")->orderBy('FISC', 'desc')->get()->getResultArray();

        // current asset
        $current_asset_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '11%'")
            ->where("FISC = $year")
            ->where("$search_balance_sheet")
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
            ->where("$search_balance_sheet")
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
            ->where("$search_balance_sheet")
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
            ->where("GL_ACCOUNT LIKE '22%' AND GL_ACCOUNT NOT LIKE '220052%'")
            // ->where("GL_ACCOUNT LIKE '22%'")
            ->where("FISC = $year")
            ->where("$search_balance_sheet")
            ->get()->getRowArray();
        $non_current_liabilities_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '22%' AND GL_ACCOUNT NOT LIKE '220052%'")
            // ->where("GL_ACCOUNT LIKE '22%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        // Saldo pada Non Current Liabilities tidak termasuk 
        // GL 2200520000 – 2200529999 karena GL tersebut sudah ada di Non Controlling Interest.
        $gl_except_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '220052%'")
            ->where("FISC = $year")
            ->where("$search_balance_sheet")
            ->get()->getRowArray();
        $gl_except_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '220052%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        // $data['non_current_liabilities_ytd'] = ($non_current_liabilities_ytd['BALANCE'] - $gl_except_ytd['BALANCE']);
        // $data['non_current_liabilities_mtd'] = $non_current_liabilities_mtd['BALANCE'] - $gl_except_mtd['BALANCE'];
        $data['non_current_liabilities_ytd'] = ($non_current_liabilities_ytd['BALANCE']);
        $data['non_current_liabilities_mtd'] = $non_current_liabilities_mtd['BALANCE'];

        // total liabilities
        $data['total_liabilities_ytd'] = $data['current_liabilities_ytd'] + $data['non_current_liabilities_ytd'];
        $data['total_liabilities_mtd'] = $data['current_liabilities_mtd'] + $data['non_current_liabilities_mtd'];

        // capital stock
        $capital_stock_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '31%'")
            ->where("FISC = $year")
            ->where("$search_balance_sheet")
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
            ->where("$search_balance_sheet")
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
            ->where("$search_balance_sheet")
            ->get()->getRowArray();
        $oci_mtd = $builder_bal->select("SUM(PER_SALES) * -1 AS BALANCE")
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
            ->where("$search_balance_sheet")
            ->get()->getRowArray();

        $penambah_retained_ytd2 = $db->query("SELECT SUM(fe.amount) AS BALANCE FROM FI_EAT fe
                                            WHERE fe.company_code = 'HH10'
                                            AND fe.fiscal_year = '$year'
                                            AND fe.period BETWEEN '$month_awl' AND '$month_akr'")->getRowArray();

        $penambah_retained_mtd = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '39%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $penambah_retained_ytd3 = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '34%'")
            ->where("FISC = $year")
            ->where("$search_balance_sheet")
            ->get()->getRowArray();
        // $penambah_retained_ytd4 = $db->query("SELECT SUM(fe.amount) AS BALANCE FROM FI_EAT fe
        //                                     WHERE fe.company_code = 'HH10'
        //                                     AND fe.fiscal_year = '$year'
        //                                     AND fe.period BETWEEN '$month_awl' AND '$month_akr'")->getRowArray();

        $penambah_retained_mtd2 = $builder_bal->select("SUM(PER_SALES) AS BALANCE")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '34%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $eat = $this->getEat($year, $month);

        $data['retained_earning_ytd'] = $penambah_retained_ytd['BALANCE'] + $penambah_retained_ytd2['BALANCE'] + $penambah_retained_ytd3['BALANCE'] +  $eat['ytd'];
        $data['retained_earning_mtd'] = $penambah_retained_mtd2['BALANCE'] + $penambah_retained_mtd['BALANCE'] + $eat['mtd'];
        //dd($data['retained_earning_ytd'], $penambah_retained_ytd2['BALANCE'], $penambah_retained_ytd['BALANCE'], $eat['ytd']);

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
            ->where("$search_balance_sheet")
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
        // $data['total_equity_ytd'] = $data['stockholder_equity_ytd'] + $data['nc_interest_ytd'];
        // $data['total_equity_mtd'] = $data['stockholder_equity_mtd'] + $data['nc_interest_mtd'];
        $data['total_equity_ytd'] = $data['capital_stock_ytd'] + $data['additional_capital_stock_ytd'] + $data['oci_ytd'] + $data['retained_earning_ytd'];
        $data['total_equity_mtd'] = $data['capital_stock_mtd'] + $data['additional_capital_stock_mtd'] + $data['oci_mtd'] + $data['retained_earning_mtd'];

        // total liabilities & equity
        $data['total_liaequ_ytd'] = $data['total_liabilities_ytd'] + $data['total_equity_ytd'];
        $data['total_liaequ_mtd'] = $data['total_liabilities_mtd'] + $data['total_equity_mtd'];

        $data['selectedParams'] = ['month' => $month, 'year' => $year];

        $data['todayDate'] = ['month' => $parsed->getMonth(), 'year' => $parsed->getYear()];

        // hardcoded the result
        switch ($year) {
            case 2021:
                $data['current_asset_ytd'] = 533309.01 * 1000000;
                $data['non_current_asset_ytd'] = 527137.35 * 1000000;
                $data['total_asset_ytd'] = 1060446.36 * 1000000;
                $data['current_liabilities_ytd'] = 524617.627878 * 1000000;
                $data['non_current_liabilities_ytd'] = 141619.903667 * 1000000;
                $data['total_liabilities_ytd'] = 666237.531545 * 1000000;
                $data['capital_stock_ytd'] = 239925 * 1000000;
                $data['oci_ytd'] = -5130.569052 * 1000000;
                $data['retained_earning_ytd'] = 159414.397241 * 1000000;
                $data['total_equity_ytd'] = 394208.828189 * 1000000;
                $data['total_liaequ_ytd'] = 1060446.359734 * 1000000;
                break;
            case 2020:
                $data['current_asset_ytd'] = 384836.765665 * 1000000;
                $data['non_current_asset_ytd'] = 704104.358553 * 1000000;
                $data['total_asset_ytd'] = 1088941.124218 * 1000000;
                $data['current_liabilities_ytd'] = 630734.111321 * 1000000;
                $data['non_current_liabilities_ytd'] = 180934.858515 * 1000000;
                $data['total_liabilities_ytd'] = 811668.969836 * 1000000;
                $data['capital_stock_ytd'] = 239925 * 1000000;
                $data['oci_ytd'] = -5801.14318 * 1000000;
                $data['retained_earning_ytd'] = 43148.297562 * 1000000;
                $data['total_equity_ytd'] = 277272.154382 * 1000000;
                $data['total_liaequ_ytd'] = 1088941.124218 * 1000000;
                break;
            case 2019:
                $data['current_asset_ytd'] = 336607.301116 * 1000000;
                $data['non_current_asset_ytd'] = 775048.230593 * 1000000;
                $data['total_asset_ytd'] = 1111655.531709 * 1000000;
                $data['current_liabilities_ytd'] = 561257.470946 * 1000000;
                $data['non_current_liabilities_ytd'] = 289174.212687 * 1000000;
                $data['total_liabilities_ytd'] = 850431.683633 * 1000000;
                $data['capital_stock_ytd'] = 239925 * 1000000;
                $data['oci_ytd'] = -5271.744354 * 1000000;
                $data['retained_earning_ytd'] = 26570.59243 * 1000000;
                $data['total_equity_ytd'] = 261223.848076 * 1000000;
                $data['total_liaequ_ytd'] = 1111655.531709 * 1000000;
                break;
            default:
                # code...
                break;
        }

        // !----------------------- CHARTS ---------------------------------

        $total_aset_lancar_ytd = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'ASSET_LANCAR_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();

        $total_liabilitas_jangka_pendek_ytd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'LIABILITAS_LANCAR_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();

        // dd($total_aset_lancar_ytd);

        $grouped_array = array();
        foreach (range(0, 11) as $i) {
            if ($i == 11) {
                $total_aset_lancar = ($total_aset_lancar_ytd[11]['BALANCE'] ?? 0) + ($total_aset_lancar_ytd[12]['BALANCE'] ?? 0)
                    + ($total_aset_lancar_ytd[13]['BALANCE'] ?? 0) + ($total_aset_lancar_ytd[14]['BALANCE'] ?? 0) + ($total_aset_lancar_ytd[15]['BALANCE'] ?? 0);
                $total_liabilitas = ($total_liabilitas_jangka_pendek_ytd[11]['BALANCE'] ?? 0) + ($total_liabilitas_jangka_pendek_ytd[12]['BALANCE'] ?? 0)
                    + ($total_liabilitas_jangka_pendek_ytd[13]['BALANCE'] ?? 0) + ($total_liabilitas_jangka_pendek_ytd[14]['BALANCE'] ?? 0) + ($total_liabilitas_jangka_pendek_ytd[15]['BALANCE'] ?? 0);
                $ratio = handleDivision($total_aset_lancar, $total_liabilitas);
                $bulan = 12;
            } else {
                $ratio = handleDivision(($total_aset_lancar_ytd[$i]['BALANCE'] ?? 0), ($total_liabilitas_jangka_pendek_ytd[$i]['BALANCE'] ?? 0));
                $bulan = $i + 1;
            }
            $grouped_array[$bulan]["ratio"] = $ratio;
        }

        $current_ratio_ytd = $grouped_array;
        $data['current_ratio_ytd'] = $current_ratio_ytd;

        // Quick Ratio
        // $quick_ratio = $db->query("SELECT FI, SUM(CASE WHEN GL_ACCOUNT LIKE '11%' THEN PER_SALES ELSE NULL END) AS aset_lancar, 
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '11005%' THEN PER_SALES ELSE NULL END) AS persediaan,
        //     SUM(CASE WHEN GL_ACCOUNT BETWEEN '1194500000' AND '1195599999' THEN PER_SALES ELSE NULL END) AS bahan_baku,
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '110062%' THEN PER_SALES ELSE NULL END) AS uang_muka,
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '110071%' THEN PER_SALES ELSE NULL END) AS beban_muka,
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '21%' THEN PER_SALES * -1 ELSE NULL END) AS liabilitas
        // FROM FI_ACT_BAL 
        // WHERE FISC = $year
        // GROUP BY FI")->getResultArray();

        $quick_ratio_lancar = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'ASSET_LANCAR_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();

        $quick_ratio_persedian = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'PERSEDIAAN_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->get()->getResultArray();

        $quick_ratio_bahan_baku = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'BB_BJ_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();

        $quick_ratio_uang_muka = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'UM_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();

        $quick_ratio_beban_muka = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'BEBAN_DBDM_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();


        $quick_ratio_liabilitas = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'LIABILITAS_LANCAR_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();

        $grouped_quick_ratio = array();

        foreach (range(0, 11) as $i) {
            // if ($i == 11) {
            //     $bulan = 12;
            //     $qr11 = handleDivision(($quick_ratio[11]['aset_lancar'] - $quick_ratio[11]['persediaan']
            //         - $quick_ratio[11]['bahan_baku'] - $quick_ratio[11]['uang_muka'] - $quick_ratio[11]['beban_muka']), $quick_ratio[11]['liabilitas']);
            //     $qr12 = handleDivision(($quick_ratio[12]['aset_lancar'] - $quick_ratio[12]['persediaan']
            //         - $quick_ratio[12]['bahan_baku'] - $quick_ratio[12]['uang_muka'] - $quick_ratio[12]['beban_muka']), $quick_ratio[12]['liabilitas']);
            //     $qr13 = handleDivision(($quick_ratio[13]['aset_lancar'] - $quick_ratio[13]['persediaan']
            //         - $quick_ratio[13]['bahan_baku'] - $quick_ratio[13]['uang_muka'] - $quick_ratio[13]['beban_muka']), $quick_ratio[13]['liabilitas']);
            //     $qr14 = handleDivision(($quick_ratio[14]['aset_lancar'] - $quick_ratio[14]['persediaan']
            //         - $quick_ratio[14]['bahan_baku'] - $quick_ratio[14]['uang_muka'] - $quick_ratio[14]['beban_muka']), $quick_ratio[14]['liabilitas']);
            //     $qr15 = handleDivision(($quick_ratio[15]['aset_lancar'] - $quick_ratio[15]['persediaan']
            //         - $quick_ratio[15]['bahan_baku'] - $quick_ratio[15]['uang_muka'] - $quick_ratio[15]['beban_muka']), $quick_ratio[15]['liabilitas']);
            //     $qr = ($qr11 + $qr12 + $qr13 + $qr14 + $qr15) / 5;
            // } else {
            //     $bulan = $i + 1;
            //     $qr = handleDivision(($quick_ratio[$i]['aset_lancar'] - $quick_ratio[$i]['persediaan']
            //         - $quick_ratio[$i]['bahan_baku'] - $quick_ratio[$i]['uang_muka'] - $quick_ratio[$i]['beban_muka']), $quick_ratio[$i]['liabilitas']);
            // }
            if ($i == 11) {
                $bulan = 12;
                $qr11 = handleDivision((($quick_ratio_lancar[11]['BALANCE'] ?? 0) - ($quick_ratio_persedian[11]['BALANCE'] ?? 0)
                    - ($quick_ratio_bahan_baku[11]['BALANCE'] ?? 0) - ($quick_ratio_uang_muka[11]['BALANCE'] ?? 0) - ($quick_ratio_beban_muka[11]['BALANCE'] ?? 0)), ($quick_ratio_liabilitas[11]['BALANCE'] ?? 0));
                $qr12 = handleDivision((($quick_ratio_lancar[12]['BALANCE'] ?? 0) - ($quick_ratio_persedian[12]['BALANCE'] ?? 0)
                    - ($quick_ratio_bahan_baku[12]['BALANCE'] ?? 0) - ($quick_ratio_uang_muka[12]['BALANCE'] ?? 0) - ($quick_ratio_beban_muka[12]['BALANCE'] ?? 0)), ($quick_ratio_liabilitas[12]['BALANCE'] ?? 0));
                $qr13 = handleDivision((($quick_ratio_lancar[13]['BALANCE'] ?? 0) - ($quick_ratio_persedian[13]['BALANCE'] ?? 0)
                    - ($quick_ratio_bahan_baku[13]['BALANCE'] ?? 0) - ($quick_ratio_uang_muka[13]['BALANCE'] ?? 0) - ($quick_ratio_beban_muka[13]['BALANCE'] ?? 0)), ($quick_ratio_liabilitas[13]['BALANCE'] ?? 0));
                $qr14 = handleDivision((($quick_ratio_lancar[14]['BALANCE'] ?? 0) - ($quick_ratio_persedian[14]['BALANCE'] ?? 0)
                    - ($quick_ratio_bahan_baku[14]['BALANCE'] ?? 0) - ($quick_ratio_uang_muka[14]['BALANCE'] ?? 0) - ($quick_ratio_beban_muka[14]['BALANCE'] ?? 0)), ($quick_ratio_liabilitas[14]['BALANCE'] ?? 0));
                $qr15 = handleDivision((($quick_ratio_lancar[15]['BALANCE'] ?? 0) - ($quick_ratio_persedian[15]['BALANCE'] ?? 0)
                    - ($quick_ratio_bahan_baku[15]['BALANCE'] ?? 0) - ($quick_ratio_uang_muka[15]['BALANCE'] ?? 0) - ($quick_ratio_beban_muka[15]['BALANCE'] ?? 0)), ($quick_ratio_liabilitas[15]['BALANCE'] ?? 0));
                $qr = ($qr11 + $qr12 + $qr13 + $qr14 + $qr15) / 5;
            } else {
                $bulan = $i + 1;
                $qr = handleDivision((($quick_ratio_lancar[$i]['BALANCE'] ?? 0) - ($quick_ratio_persedian[$i]['BALANCE'] ?? 0)
                    - ($quick_ratio_bahan_baku[$i]['BALANCE'] ?? 0) - ($quick_ratio_uang_muka[$i]['BALANCE'] ?? 0) - ($quick_ratio_beban_muka[$i]['BALANCE'] ?? 0)), ($quick_ratio_liabilitas[$i]['BALANCE'] ?? 0));
            }
            $grouped_quick_ratio[$bulan]["ratio"] = $qr;
        }

        $data['quick_ratio_ytd'] = $grouped_quick_ratio;

        // cash ratio
        // $cash_ratio = $db->query("SELECT FI, SUM(CASE WHEN GL_ACCOUNT BETWEEN '1100010101' AND '1100010799' THEN PER_SALES ELSE NULL END) AS kas, 
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '111001%' THEN PER_SALES ELSE NULL END) AS bank,
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '21%' THEN PER_SALES * -1 ELSE NULL END) AS liabilitas
        // FROM FI_ACT_BAL 
        // WHERE FISC = $year
        // GROUP BY FI")->getResultArray();

        $cash_ratio_kas = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'KAS_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();

        $cash_ratio_bank = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'BANK_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();

        $cash_ratio_liabilitas = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'LIABILITAS_LANCAR_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray();

        $grouped_cash = array();
        foreach (range(0, 11) as $i) {
            // if ($i == 11) {
            //     $kas_bank = $cash_ratio[11]['kas'] + $cash_ratio[11]['bank'] + $cash_ratio[12]['kas'] + $cash_ratio[12]['bank']
            //         + $cash_ratio[13]['kas'] + $cash_ratio[13]['bank'] + $cash_ratio[14]['kas'] + $cash_ratio[14]['bank']
            //         + $cash_ratio[15]['kas'] + $cash_ratio[15]['bank'];
            //     $liabilitas = $cash_ratio[11]['liabilitas'] + $cash_ratio[12]['liabilitas'] + $cash_ratio[13]['liabilitas']
            //         + $cash_ratio[14]['liabilitas'] + $cash_ratio[15]['liabilitas'];
            //     $ratio = handleDivision($kas_bank, $liabilitas);
            //     $bulan = 12;
            // } else {
            //     $kas_bank = $cash_ratio[$i]['kas'] + $cash_ratio[$i]['bank'];
            //     $ratio = handleDivision($kas_bank, $cash_ratio[$i]['liabilitas']);
            //     $bulan = $i + 1;
            // }
            if ($i == 11) {
                $kas_bank = ($cash_ratio_kas[11]['BALANCE'] ?? 0) + ($cash_ratio_bank[11]['BALANCE'] ?? 0) + ($cash_ratio_kas[12]['BALANCE'] ?? 0) + ($cash_ratio_bank[12]['BALANCE'] ?? 0)
                    + ($cash_ratio_kas[13]['BALANCE'] ?? 0) + ($cash_ratio_bank[13]['BALANCE'] ?? 0) + ($cash_ratio_kas[14]['BALANCE'] ?? 0) + ($cash_ratio_bank[14]['BALANCE'] ?? 0)
                    + ($cash_ratio_kas[15]['BALANCE'] ?? 0) + ($cash_ratio_bank[15]['BALANCE'] ?? 0);
                $liabilitas = ($cash_ratio_liabilitas[11]['BALANCE'] ?? 0) + ($cash_ratio_liabilitas[12]['BALANCE'] ?? 0) + ($cash_ratio_liabilitas[13]['BALANCE'] ?? 0)
                    + ($cash_ratio_liabilitas[14]['BALANCE'] ?? 0) + ($cash_ratio_liabilitas[15]['BALANCE'] ?? 0);
                $ratio = handleDivision($kas_bank, $liabilitas);
                $bulan = 12;
            } else {
                $kas_bank = ($cash_ratio_kas[$i]['BALANCE'] ?? 0) + ($cash_ratio_bank[$i]['BALANCE'] ?? 0);
                $ratio = handleDivision($kas_bank, ($cash_ratio_liabilitas[$i]['BALANCE'] ?? 0));
                $bulan = $i + 1;
            }
            $grouped_cash[$bulan]["ratio"] = $ratio;
        }
        $data['cash_ratio_ytd'] = $grouped_cash;

        // ------------------------- LEVERAGE RATIO ----------------------------

        // $kepala_gl = $db->query("SELECT FI, SUM(CASE WHEN GL_ACCOUNT LIKE '4%' THEN PER_SALES * -1 ELSE 0 END) AS gl_4, 
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '5%' THEN PER_SALES ELSE 0 END) AS gl_5,
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '6%' THEN PER_SALES ELSE 0 END) AS gl_6,
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '7%' THEN PER_SALES * -1 ELSE 0 END) AS gl_7
        // FROM FI_ACT_BAL 
        // WHERE FISC = $year
        // GROUP BY FI")->getResultArray();

        // $selain_gl = $db->query("WITH t_bukan_gl AS (SELECT FI, FI AS bulan, SUM(CASE WHEN zc.ID = 'INTEREST' THEN PER_SALES ELSE 0 END) AS interest,
        //     (SELECT SUM(PER_SALES) FROM FI_ACT_BAL WHERE GL_ACCOUNT LIKE '910001%' AND FISC = $year AND FI = bulan) AS tax_91,
        //     (SELECT SUM(PER_SALES) FROM FI_ACT_BAL WHERE GL_ACCOUNT LIKE '920002%' AND FISC = $year AND FI = bulan) AS tax_92,
        //     SUM(CASE WHEN zc.ID = 'DEP' THEN PER_SALES ELSE 0 END) AS dep,
        //     SUM(CASE WHEN zc.ID = 'AMRT' THEN PER_SALES ELSE 0 END) AS amrt,
        //     SUM(CASE WHEN zc.ID = 'INTERESTEXPENSE' THEN PER_SALES ELSE 0 END) AS ie,
        //     SUM(CASE WHEN zc.ID = 'CPLTD' THEN PER_SALES * -1 ELSE 0 END) AS cpltd
        //     FROM FI_ACT_BAL fat
        //     INNER JOIN ZFIT_CMSGL zc ON zc.GL = fat.GL_ACCOUNT 
        //     WHERE FISC = $year
        //     GROUP BY FI
        //     ORDER BY FI)
        //     SELECT FI, interest, (tax_91 - tax_92) AS tax, dep, amrt, ie, cpltd FROM t_bukan_gl")->getResultArray();

        $kepala_gl_4 = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'REVENUE_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $kepala_gl_5 = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'COGS_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $kepala_gl_6 = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'BUA_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $kepala_gl_7 = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'OTHER_REV_EXP_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $interest_ratio = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'INTEREST_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $tax_ratio = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'PAJAK_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $depreciation_ratio = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'DEP_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $amortization_ratio = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'AMRT_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $interest_expense = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'INT_EXP_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $cpltd = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'CPLTD_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $grouped_debt_service = array();
        foreach (range(0, 11) as $i) {
            if ($i == 11) {
                $bulan = 12;
                $total_ds_12 = 0;
                for ($x = $i; $x <= 15; $x++) {
                    $pengurangan_gl = ($kepala_gl_4[$i]['BALANCE'] ?? 0) - ($kepala_gl_5[$i]['BALANCE'] ?? 0) - ($kepala_gl_6[$i]['BALANCE'] ?? 0) - ($kepala_gl_7[$i]['BALANCE'] ?? 0);
                    $total_lain_gl = ($interest_ratio[$i]['BALANCE'] ?? 0) + ($tax_ratio[$i]['BALANCE'] ?? 0) + ($depreciation_ratio[$i]['BALANCE'] ?? 0) + ($amortization_ratio[$i]['BALANCE'] ?? 0);
                    $op1 = handleDivision(($pengurangan_gl - $total_lain_gl), ($kepala_gl_4[$i]['BALANCE'] ?? 0));
                    $op2 = handleDivision($op1, (($cpltd[$i]['BALANCE'] ?? 0) + ($interest_expense[$i]['BALANCE'] ?? 0)));
                    $total_ds_12 += $op2;
                }
                $op2 = handleDivision($total_ds_12, 5);
            } else {
                $bulan = $i + 1;
                $pengurangan_gl = ($kepala_gl_4[$i]['BALANCE'] ?? 0) - ($kepala_gl_5[$i]['BALANCE'] ?? 0) - ($kepala_gl_6[$i]['BALANCE'] ?? 0) - ($kepala_gl_7[$i]['BALANCE'] ?? 0);
                $total_lain_gl = ($interest_ratio[$i]['BALANCE'] ?? 0) + ($tax_ratio[$i]['BALANCE'] ?? 0) + ($depreciation_ratio[$i]['BALANCE'] ?? 0) + ($amortization_ratio[$i]['BALANCE'] ?? 0);
                $op1 = handleDivision(($pengurangan_gl - $total_lain_gl), ($kepala_gl_4[$i]['BALANCE'] ?? 0));
                $op2 = handleDivision($op1, (($cpltd[$i]['BALANCE'] ?? 0) + ($interest_expense[$i]['BALANCE'] ?? 0)));
            }
            $grouped_debt_service[$bulan]['ratio'] = $op2;
        }
        $data['debt_service_ytd'] = $grouped_debt_service;
        // dd($data['debt_service_ytd']);
        // $data['debt_service_ytd'] = 8.28120409478657 * 100;
        // $data['debt_service_mtd'] = 27774.3675685421 * 100;

        // DER

        // $der_data = $db->query("SELECT FI, SUM(CASE WHEN zc.ID = 'LBL' THEN PER_SALES * -1 ELSE 0 END) AS liability,
        //     SUM(CASE WHEN zc.ID = 'EQT' THEN PER_SALES ELSE 0 END) AS equity
        //     FROM FI_ACT_BAL fat
        //     INNER JOIN ZFIT_CMSGL zc ON zc.GL = fat.GL_ACCOUNT 
        //     WHERE FISC = $year
        //     GROUP BY FI
        //     ORDER BY FI")->getResultArray();
        $der_liability = $builder_bal->select("SUM(BALANCE) * -1 AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'LBL_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $der_equity = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'TOT_EKT_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $grouped_debt_equity = array();
        foreach (range(0, 11) as $i) {
            if ($i == 11) {
                $bulan = 12;
                $total = 0;
                for ($x = $i; $x <= 15; $x++) {
                    $pengurangan_gl = ($kepala_gl_4[$i]['BALANCE'] ?? 0) - ($kepala_gl_5[$i]['BALANCE'] ?? 0) - ($kepala_gl_6[$i]['BALANCE'] ?? 0) - ($kepala_gl_7[$i]['BALANCE'] ?? 0);
                    $op = handleDivision(($der_liability[$i]['BALANCE'] ?? 0), ($der_equity[$i]['BALANCE'] ?? 0));
                    $total += $op;
                }
                $op = handleDivision($total, 5);
            } else {
                $bulan = $i + 1;
                $op = handleDivision(($der_liability[$i]['BALANCE'] ?? 0), ($der_equity[$i]['BALANCE'] ?? 0));
            }
            $grouped_debt_equity[$bulan]['ratio'] = $op;
        }

        $data['der_ytd'] = $grouped_debt_equity;


        // equity ratio
        // $kepala_gl_x = $db->query("SELECT FI, SUM(CASE WHEN GL_ACCOUNT LIKE '3%' THEN PER_SALES ELSE 0 END) AS gl_3, 
        //     SUM(CASE WHEN GL_ACCOUNT LIKE '1%' THEN PER_SALES ELSE 0 END) AS gl_1
        // FROM FI_ACT_BAL 
        // WHERE FISC = $year
        // GROUP BY FI")->getResultArray();
        $kepala_gl_3 = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'TOT_EKT_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $kepala_gl_1 = $builder_bal->select("SUM(BALANCE) AS BALANCE, FI")
            ->join("ZFIT_CMSGL zc", "zc.GL = FI_ACT_BAL.GL_ACCOUNT")
            ->where("zc.ID = 'TOT_AST_RASIO'")
            ->where("FI_ACT_BAL.COMP = 'HH10'")
            ->where("FISC = $year")
            ->groupBy("FI")
            ->orderBy("FI")
            ->get()->getResultArray(); 

        $grouped_equity = array();
        foreach (range(0, 11) as $i) {
            if ($i == 11) {
                $bulan = 12;
                $total = 0;
                for ($x = $i; $x <= 15; $x++) {
                    $op = handleDivision(($kepala_gl_3[$i]['BALANCE'] ?? 0), ($kepala_gl_1[$i]['BALANCE'] ?? 0));
                    $total += $op;
                }
                $op = handleDivision($total, 5);
            } else {
                $bulan = $i + 1;
                $op = handleDivision(($kepala_gl_3[$i]['BALANCE'] ?? 0), ($kepala_gl_1[$i]['BALANCE'] ?? 0));
            }
            $grouped_equity[$bulan]['ratio'] = $op;
        }
        $data['equ_ytd'] = $grouped_equity;

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
        // $trade_payable = $builder_bal->select("SUM(BALANCE) AS previous, FISC, SUM(DEBIT_PER) AS increase, 
        //     SUM(CREDIT_PER) AS decrease, (SUM(BALANCE) + SUM(DEBIT_PER) + SUM(CREDIT_PER)) AS os_today")
        //     ->where("COMP = 'HH10'")
        //     ->where("GL_ACCOUNT = '$indicator'")
        //     // ->where("FI = 16")
        //     ->groupBy('FISC')
        //     ->orderBy('FISC', 'DESC')
        //     ->limit(3)
        //     ->get()->getResultArray();
        $trade_payable = $db->query("WITH ar_ap  AS (SELECT GL_ACCOUNT AS gl, FISC AS tahun, SUM(DEBIT_PER)/1000000 AS increase, SUM(CREDIT_PER)/1000000 AS decrease,
            (SELECT COALESCE(SUM(BALANCE), 0)/1000000 FROM FI_ACT_BAL fab2 WHERE fab2.FISC = tahun-1  AND GL_ACCOUNT = gl AND FI = 16) AS previous
            FROM FI_ACT_BAL fab
            WHERE GL_ACCOUNT = '$indicator'
            GROUP BY FISC)
        SELECT previous, increase, decrease, (previous + increase + decrease) AS os_today, tahun AS FISC FROM ar_ap WHERE tahun > 2019 ORDER BY tahun DESC LIMIT 3")->getResultArray();
        $data['trade_payable'] = $trade_payable;

        // dummy data below
        // if($indicator == '2100110201') {
        //     $data['trade_payable'] = array(
        //         array("FISC" => 2022, 'previous' => -63484512736, 
        //             'increase' => 802129854525, 'decrease' => 840393706311, 'os_today' => -101748364522.00),
        //         array("FISC" => 2021, 'previous' => -121672547967, 
        //             'increase' => 988181972606, 'decrease' => 929993937375, 'os_today' => -63484512736),
        //         array("FISC" => 2020, 'previous' => -74922976245, 
        //             'increase' => 651071836793, 'decrease' => 697821408515, 'os_today' => -121672547967),
        //     );
        // } elseif($indicator == '2200110001') {
        //     $data['trade_payable'] = array(
        //         array("FISC" => 2022, 'previous' => -20732130564, 
        //             'increase' => 14999280000, 'decrease' => 7999280000, 'os_today' => -13732130564),
        //         array("FISC" => 2021, 'previous' => -35379975350, 
        //             'increase' => 22196834786, 'decrease' => 7548990000, 'os_today' => -20732130564),
        //         array("FISC" => 2020, 'previous' => -185384756592, 
        //             'increase' => 179980517978, 'decrease' => 29975736736, 'os_today' => -35379975350),
        //     );
        // } else {
        //     $data['trade_payable'] = array(
        //         array("FISC" => 2022, 'previous' => -98616000000, 
        //             'increase' => 210330135625, 'decrease' => 111714135625, 'os_today' => 0),
        //     );
        // }
        // Account receivable

        // $account_receivable = $builder_bal->select("FISC AS year, (SELECT COALESCE(SUM(BALANCE), 0) FROM FI_ACT_BAL 
        //     WHERE FISC = year - 1 AND FI = 16 AND COMP = 'HH10' AND GL_ACCOUNT = '1100210101') AS previous, SUM(DEBIT_PER) AS increase, 
        //     SUM(CREDIT_PER) AS decrease, ((SELECT COALESCE(SUM(BALANCE), 0) FROM FI_ACT_BAL
        //     WHERE FISC = year - 1 AND FI = 16 AND COMP = 'HH10' AND GL_ACCOUNT = '1100210101') + SUM(DEBIT_PER) + SUM(CREDIT_PER)) AS os_today")
        //     ->where("COMP = 'HH10'")
        //     ->where("GL_ACCOUNT = '1100210101'")
        //     ->where("FI < 16")
        //     ->groupBy('FISC')
        //     ->orderBy('FISC', 'DESC')
        //     ->limit(3)
        //     ->get()->getResultArray();

        $qaccountreceivable  = "SELECT t1.fisc AS year, t2.balance AS previous, t1.debit_per as increase, t1.credit_per as decrease
        FROM 
        (
            SELECT fisc, sum(abs(debit_per/1000000)) AS debit_per, sum(abs(credit_per/1000000)) as credit_per FROM FI_ACT_BAL
            WHERE comp = 'HH10'
            AND GL_ACCOUNT	= 1100210101
            GROUP BY fisc
        ) t1
        LEFT JOIN
        (
            SELECT fisc,balance/1000000 as balance FROM FI_ACT_BAL
            WHERE fi = 16
            and comp = 'HH10'
            AND GL_ACCOUNT	= 1100210101
        ) t2
        ON (t1.fisc=t2.fisc);";
        $qresultaccountreceivable = $db->query($qaccountreceivable);
        $qresultarray = $qresultaccountreceivable->getResultArray();

        $data['account_receivable'] = $qresultarray;
        //dd($data['account_receivable']);


        // $data['account_receivable'] = $account_receivable;
        // $data['account_receivable'] = array(
        //     array(
        //         "year" => 2022, 'previous' => 223,
        //         'increase' => 723989059365, 'decrease' => 798430530697, 'os_today' => 51067346402
        //     ),
        //     array(
        //         "year" => 2021, 'previous' => 59637504591,
        //         'increase' => 1415325938109, 'decrease' => 1349454624966, 'os_today' => 125508817734
        //     ),
        //     array(
        //         "year" => 2020, 'previous' => 29609596011,
        //         'increase' => 854767777312, 'decrease' => -824739868732, 'os_today' => 59637504591
        //     ),
        // );

        // aging account
        $group_account_payable = $_GET['account_type'] ?? 'invoice';
        $group_account_receivable = $_GET['receive_type'] ?? 'invoice';
        $as_of_date = $_GET['as_of_date'] ?? date('Y-m-d');
        $as_date_rec = $_GET['as_date_rec'] ?? date('Y-m-d');
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

        $year = $_GET['cof'] ?? false;
        $data['costOfFund'] = $this->costOfFund($month, $year);
        //dd($data['costOfFund']);
        echo view("pages/finance/balance", $data);
    }

    public function costOfFund($month, $year)
    {
        $kl = 7200010008;
        $kmk = 7200010007;
        $leasing = 7200010001;
        $scf = 6200990003;
        $gl = array($kl, $kmk, $leasing, $scf);
        $costOfFund = array();
        $k = 0;
        $l = 1;
        $data = '';
        $curentmonth = 0;
        $field = "BALANCE";
        for ($i = 0; $i <= 15; $i++) {
            $j = 0;
            if ($i % 2 == 0) {
                $j = 1;
            } else {
                $j = 2;
            } //pergantian mode tiap iterasi
            if ($i % 4 == 0) {
                $k++;
            } //pergantian gl tiap 4x iterasi
            if ($l == 3 or $l == 4) {
                $curentmonth = 16;
                $field = "BALANCE";
                if ($l == 4) $l = 0;
            } else {
                $curentmonth = $month;
                $field = "PER_SALES";
            } //pergantian bulan

            //echo $j.$gl[$k - 1].$year.$curentmonth.$month.$field."<br>"; 
            // if($i == 15){
            //     dd($j, $gl[$k - 1], $year, $curentmonth, $month,$field); //untuk debugging
            // }
            $data = $this->costOfFundExceptMU($j, $gl[$k - 1], $year, $curentmonth, $month, $field);

            array_push($costOfFund, $data);
            $l++;
        }
        $data = $this->costOfFundMU($year, '');
        array_push($costOfFund, $data);
        array_push($costOfFund, $data);
        $data = $this->costOfFundMU($year, $month);
        array_push($costOfFund, $data);
        array_push($costOfFund, $data);

        $total = $costOfFund[0][0]['result'] + $costOfFund[4][0]['result'] + $costOfFund[8][0]['result'] + $costOfFund[12][0]['result'] + $costOfFund[16][0]['RpJtYtd'];
        array_push($costOfFund, $total);
        $total = $costOfFund[1][0]['result'] + $costOfFund[5][0]['result'] + $costOfFund[9][0]['result'] + $costOfFund[13][0]['result'] + $costOfFund[17][0]['RpMtYtd'];
        array_push($costOfFund, $total);
        $total = $costOfFund[2][0]['result'] + $costOfFund[6][0]['result'] + $costOfFund[10][0]['result'] + $costOfFund[14][0]['result'] + $costOfFund[18][0]['RpJtYtd'];
        array_push($costOfFund, $total);
        $total = $costOfFund[3][0]['result'] + $costOfFund[7][0]['result'] + $costOfFund[11][0]['result'] + $costOfFund[15][0]['result'] + $costOfFund[18][0]['RpMtYtd'];
        array_push($costOfFund, $total);
        //dd($costOfFund);
        return $costOfFund;
    }

    public function costOfFundExceptMU($mode, $gl, $year, $month, $monthfilter, $field)
    {
        if ($year == "")
            $year = 2022;
        if ($year != 2022) {
            $field2 = "fnl_qty";
            $table = "FI_SALES_INV";
            $con = ", T_SAL_SHIPMENT t2
            WHERE t1.SHIPMENT_ID=t2.SHIPMENT_ID AND t1.CONTRACT_NO = t2.contract_no
            AND MONTH(budat) LIKE '%$monthfilter%'";
        } else {
            $field2 = "quantity";
            $table = "temp_price_quantity";
            $con = "";
        }
        $db = \Config\Database::connect();
        $sql = '';
        if ($mode == 1) {
            $sql = "SELECT COALESCE((SELECT sum($field/1000000) AS result
            FROM FI_ACT_BAL
            WHERE COMP = 'HH10'
            AND GL_ACCOUNT = $gl
            AND FISC LIKE '%$year%'
            AND FI > '%$month%'), 0) AS result";
        } else {
            $sql = "SELECT COALESCE((SELECT (s2.$field/s1.fnl_qty) AS result from
            (
                SELECT sum($field2) AS fnl_qty
                FROM  $table t1 $con
            ) AS s1,
            (
                SELECT sum($field) as $field
                FROM FI_ACT_BAL
                WHERE COMP = 'HH10'
                AND GL_ACCOUNT = $gl
                AND FISC LIKE '%$year%'
                AND FI > '%$month%'
            ) as s2), 0) AS result";
            if ($mode == 2 and $gl == "7200010008" and $year == "2022" and $month == "16") {
                //dd($sql);//$mode, $gl, $year, $month, $monthfilter, $field
            }
        }
        if($mode==2){
            //dd($sql);
        }
        $query = $db->query($sql);

        $data = $query->getResultArray();
        return $data;
    }

    private function costOfFundMU($year, $month)
    {
        if ($year == "")
            $year = 2022;
        $db = \Config\Database::connect();
        $sql = "SELECT mitsui.hasil+itochu.hasil AS RpJtYtd ,
        ((mitsui.hasil+itochu.hasil)/mitsui.hasil)+((mitsui.hasil+itochu.hasil)/itochu.hasil) AS RpMtYtd
            from
        (
            SELECT (sum(t1.fnl_qty)*1.83*sum(t1.ukurs)) AS hasil FROM FI_SALES_INV t1, T_SAL_CONTRACT_ORDER t2
            WHERE t2.customer_code = 4000046
            and YEAR(t1.budat) LIKE '%$year%'
            and MONTH(t1.budat) LIKE '%$month%'
        ) as mitsui,
        (
            SELECT (sum(t1.fnl_qty)*0.48*sum(t1.ukurs)) AS hasil FROM FI_SALES_INV t1, T_SAL_CONTRACT_ORDER t2
            WHERE t2.customer_code = 4000047
            and YEAR(t1.budat) LIKE '%$year%'
            and MONTH(t1.budat) LIKE '%$month%'
        ) as itochu";
        //dd($sql);
        $query = $db->query($sql);
        $data = $query->getResultArray();
        return $data;
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

    // public function addcostindex()
    // {
    //     $data['title'] = "Add Cost Mining";

    //     $cost = new TCostmining();
    //     $builder = $cost->builder();
    //     $builder->select('t_costmining.*');
    //     $data['cost'] = $builder->get()->getResultArray();

    //     echo view("pages/finance/addcost", $data);
    // }

    // public function addcost() 
    // {
    //     $year = $this->request->getVar('year');
    //     $month = $this->request->getVar('month');
    //     $contractor = $this->request->getVar('contractor');
    //     $costOb = $this->request->getVar('costOb');
    //     $costCg = $this->request->getVar('costCg');
    //     $costHauling = $this->request->getVar('costHauling');

    //     $model = new TCostmining();
    //     $model->save([
    //         'year' => $year,
    //         'month' => $month,
    //         'contractor' => $contractor,
    //         'cost_ob' => $costOb,
    //         'cost_cg' => $costCg,
    //         'cost_hauling' => $costHauling,
    //     ]);
    //     return redirect()->to("/finance/addcost/")->with('message', 'A cost contractor has been created');
    // }

    /* Get the data for Sales and Production and show it on Modal View */
    public function salesandproduction()
    {
        // Sales and Cost
        $data['title'] = "salesandproduction";

        $db = Database::connect();
        $FiInRkp = new FiInRkps();
        $builder_rkp = $FiInRkp->builder();

        $TSalPrice = new TSalPrice();
        $builder_price = $TSalPrice->builder();

        $FiSalesInv = new FiSalesInv();
        $builder_inv = $FiSalesInv->builder();

        $data['years'] = $builder_rkp->select("DISTINCT(GJAHR)")->orderBy('GJAHR', 'desc')->get()->getResultArray();

        $now = Time::now();
        $parsed = Time::parse($now);
        $start_date = $_GET['start'] ?? Time::createFromDate($parsed->getYear(), $parsed->getMonth() - 1, 1)->format('Y-m-d');
        $end_date = $_GET['end'] ?? Time::createFromDate($parsed->getYear(), $parsed->getMonth() - 1, 1)->format('Y-m-t');
        $data['selectedParams'] = ["start_date" => $start_date, "end_date" => $end_date];

        $time_actual_start = Time::parse($start_date);
        $time_actual_end   = Time::parse($end_date);

        // LOCAL
        // Fobb
        // Fobb Price RKAP
        $data['fobb_price_rkap'] = $builder_rkp->select("SUM(PRC) AS RKAP")
            ->where("TYPE = 'LOCAL'")
            ->where("SHPMN = 'FOBB'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // Fobb Price Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['fobb_price_actual'] = $db->query("SELECT AVG(price) AS ACTUAL FROM temp_price_quantity
                                                    WHERE shipment = 'FOBB'
                                                    AND `type` = 'Local'
                                                    AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['fobb_price_actual'] = $builder_price->select("(SELECT SUM(final_price) FROM T_SAL_PRICE price 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = price.shipment_id 
                WHERE shipment.category = 'FOBB' AND shipment.type = 'Local' AND date_final BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // Fobb Quantity RKAP
        $data['fobb_quantity_rkap'] = $builder_rkp->select("SUM(QTY) AS RKAP")
            ->where("TYPE = 'LOCAL'")
            ->where("SHPMN = 'FOBB'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // Fobb Quantity Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['fobb_quantity_actual'] = $db->query("SELECT AVG(quantity) AS ACTUAL FROM temp_price_quantity
                                                        WHERE shipment = 'FOBB'
                                                        AND `type` = 'Local'
                                                        AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['fobb_quantity_actual'] = $builder_inv->select("(SELECT SUM(FNL_QTY) FROM FI_SALES_INV inv 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = inv.SHIPMENT_ID 
                WHERE shipment.category = 'FOBB' AND shipment.type = 'Local' AND BUDAT BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // Fobb Revenue RKAP
        $data['fobb_revenue_rkap'] = ($data['fobb_price_rkap']['RKAP'] * $data['fobb_quantity_rkap']['RKAP']) / 1000000;
        // Fobb Quantity Actual
        $data['fobb_revenue_actual'] = ($data['fobb_price_actual']['ACTUAL'] * $data['fobb_quantity_actual']['ACTUAL']) / 1000000;

        // CIF
        // CIF Price RKAP
        $data['cif_price_rkap'] = $builder_rkp->select("SUM(PRC) AS RKAP")
            ->where("TYPE = 'LOCAL'")
            ->where("SHPMN = 'CIF'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // CIF Price Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['cif_price_actual'] = $db->query("SELECT AVG(price) AS ACTUAL FROM temp_price_quantity
                                                        WHERE shipment = 'CIF'
                                                        AND `type` = 'Local'
                                                        AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['cif_price_actual'] = $builder_price->select("(SELECT SUM(final_price) FROM T_SAL_PRICE price 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = price.shipment_id 
                WHERE shipment.category = 'CIF' AND shipment.type = 'Local' AND date_final BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // CIF Quantity RKAP
        $data['cif_quantity_rkap'] = $builder_rkp->select("SUM(QTY) AS RKAP")
            ->where("TYPE = 'LOCAL'")
            ->where("SHPMN = 'CIF'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // CIF Quantity Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['cif_quantity_actual'] = $db->query("SELECT AVG(quantity) AS ACTUAL FROM temp_price_quantity
                                                        WHERE shipment = 'CIF'
                                                        AND `type` = 'Local'
                                                        AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['cif_quantity_actual'] = $builder_inv->select("(SELECT SUM(FNL_QTY) FROM FI_SALES_INV inv 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = inv.SHIPMENT_ID 
                WHERE shipment.category = 'CIF' AND shipment.type = 'Local' AND BUDAT BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // CIF Revenue RKAP
        $data['cif_revenue_rkap'] = ($data['cif_price_rkap']['RKAP'] * $data['cif_quantity_rkap']['RKAP']) / 1000000;
        // CIF Revenue Actual
        $data['cif_revenue_actual'] = ($data['cif_price_actual']['ACTUAL'] * $data['cif_quantity_actual']['ACTUAL']) / 1000000;

        // Franco Pabrik
        // Franco Pabrik Price RKAP
        $data['pabrik_price_rkap'] = $builder_rkp->select("SUM(PRC) AS RKAP")
            ->where("TYPE = 'LOCAL'")
            ->where("SHPMN = 'Franco Pabrik'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // Franco Pabrik Price Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['pabrik_price_actual'] = $db->query("SELECT AVG(price) AS ACTUAL FROM temp_price_quantity
                                                    WHERE shipment = 'Franco Pabrik'
                                                    AND `type` = 'Local'
                                                    AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['pabrik_price_actual'] = $builder_price->select("(SELECT SUM(final_price) FROM T_SAL_PRICE price 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = price.shipment_id 
                WHERE shipment.category = 'Franco Pabrik' AND shipment.type = 'Local' AND date_final BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // Franco Pabrik Quantity RKAP
        $data['pabrik_quantity_rkap'] = $builder_rkp->select("SUM(QTY) AS RKAP")
            ->where("TYPE = 'LOCAL'")
            ->where("SHPMN = 'Franco Pabrik'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // Franco Pabrik Quantity Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['pabrik_quantity_actual'] = $db->query("SELECT AVG(quantity) AS ACTUAL FROM temp_price_quantity
                                                    WHERE shipment = 'Franco Pabrik'
                                                    AND `type` = 'Local'
                                                    AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['pabrik_quantity_actual'] = $builder_inv->select("(SELECT SUM(FNL_QTY) FROM FI_SALES_INV inv 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = inv.SHIPMENT_ID 
                WHERE shipment.category = 'Franco Pabrik' AND shipment.type = 'Local' AND BUDAT BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // Franco Pabrik Revenue RKAP
        $data['pabrik_revenue_rkap'] = ($data['pabrik_price_rkap']['RKAP'] * $data['pabrik_quantity_rkap']['RKAP']) / 1000000;
        // Franco Pabrik Revenue Actual
        $data['pabrik_revenue_actual'] = ($data['pabrik_price_actual']['ACTUAL'] * $data['pabrik_quantity_actual']['ACTUAL']) / 1000000;

        // EXPORT
        // Fobb
        // Fobb Price RKAP
        $data['export_fobb_price_rkap'] = $builder_rkp->select("SUM(PRC) AS RKAP")
            ->where("TYPE = 'Export'")
            ->where("SHPMN = 'FOBB'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // Fobb Price Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_fobb_price_actual'] = $db->query("SELECT AVG(price) AS ACTUAL FROM temp_price_quantity
                                                    WHERE shipment = 'FOBB'
                                                    AND `type` = 'Export'
                                                    AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['export_fobb_price_actual'] = $builder_price->select("(SELECT SUM(final_price) FROM T_SAL_PRICE price 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = price.shipment_id 
                WHERE shipment.category = 'FOBB' AND shipment.type = 'Export' AND date_final BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }

        // Fobb Quantity RKAP
        $data['export_fobb_quantity_rkap'] = $builder_rkp->select("SUM(QTY) AS RKAP")
            ->where("TYPE = 'Export'")
            ->where("SHPMN = 'FOBB'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // Fobb Quantity Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_fobb_quantity_actual'] = $db->query("SELECT AVG(quantity) AS ACTUAL FROM temp_price_quantity
                                                        WHERE shipment = 'FOBB'
                                                        AND `type` = 'Export'
                                                        AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['export_fobb_quantity_actual'] = $builder_inv->select("(SELECT SUM(FNL_QTY) FROM FI_SALES_INV inv 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = inv.SHIPMENT_ID 
                WHERE shipment.category = 'FOBB' AND shipment.type = 'Export' AND BUDAT BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // Fobb Revenue RKAP
        $data['export_fobb_revenue_rkap'] = ($data['export_fobb_price_rkap']['RKAP'] * $data['export_fobb_quantity_rkap']['RKAP']) / 1000000;
        // Fobb Quantity Actual
        $data['export_fobb_revenue_actual'] = ($data['export_fobb_price_actual']['ACTUAL'] * $data['export_fobb_quantity_actual']['ACTUAL']) / 1000000;

        // CIF
        // CIF Price RKAP
        $data['export_cif_price_rkap'] = $builder_rkp->select("SUM(PRC) AS RKAP")
            ->where("TYPE = 'Export'")
            ->where("SHPMN = 'CIF'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // CIF Price Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_cif_price_actual'] = $db->query("SELECT AVG(price) AS ACTUAL FROM temp_price_quantity
                                                            WHERE shipment = 'CIF'
                                                            AND `type` = 'Export'
                                                            AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['export_cif_price_actual'] = $builder_price->select("(SELECT SUM(final_price) FROM T_SAL_PRICE price 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = price.shipment_id 
                WHERE shipment.category = 'CIF' AND shipment.type = 'Export' AND date_final BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // CIF Quantity RKAP
        $data['export_cif_quantity_rkap'] = $builder_rkp->select("SUM(QTY) AS RKAP")
            ->where("TYPE = 'Export'")
            ->where("SHPMN = 'CIF'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // CIF Quantity Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_cif_quantity_actual'] = $db->query("SELECT AVG(quantity) AS ACTUAL FROM temp_price_quantity
                                                        WHERE shipment = 'CIF'
                                                        AND `type` = 'Export'
                                                        AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['export_cif_quantity_actual'] = $builder_inv->select("(SELECT SUM(FNL_QTY) FROM FI_SALES_INV inv 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = inv.SHIPMENT_ID 
                WHERE shipment.category = 'CIF' AND shipment.type = 'Export' AND  BUDAT BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // CIF Revenue RKAP
        $data['export_cif_revenue_rkap'] = ($data['export_cif_price_rkap']['RKAP'] * $data['export_cif_quantity_rkap']['RKAP']) / 1000000;
        // CIF Revenue Actual
        $data['export_cif_revenue_actual'] = ($data['export_cif_price_actual']['ACTUAL'] * $data['export_cif_quantity_actual']['ACTUAL']) / 1000000;

        // FAS
        // FAS Price RKAP
        $data['export_fas_price_rkap'] = $builder_rkp->select("SUM(PRC) AS RKAP")
            ->where("TYPE = 'Export'")
            ->where("SHPMN = 'FAS'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // FAS Price Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_fas_price_actual'] = $db->query("SELECT AVG(price) AS ACTUAL FROM temp_price_quantity
                                                            WHERE shipment = 'FAS'
                                                            AND `type` = 'Export'
                                                            AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['export_fas_price_actual'] = $builder_price->select("(SELECT SUM(final_price) FROM T_SAL_PRICE price 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = price.shipment_id 
                WHERE shipment.category = 'FAS' AND shipment.type = 'Export' AND date_final BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // FAS Quantity RKAP
        $data['export_fas_quantity_rkap'] = $builder_rkp->select("SUM(QTY) AS RKAP")
            ->where("TYPE = 'Export'")
            ->where("SHPMN = 'FAS'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // FAS Quantity Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_fas_quantity_actual'] = $db->query("SELECT AVG(quantity) AS ACTUAL FROM temp_price_quantity
                                                        WHERE shipment = 'FAS'
                                                        AND `type` = 'Export'
                                                        AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['export_fas_quantity_actual'] = $builder_inv->select("(SELECT SUM(FNL_QTY) FROM FI_SALES_INV inv 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = inv.SHIPMENT_ID 
                WHERE shipment.category = 'FAS' AND shipment.type = 'Export' AND  BUDAT BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // FAS Revenue RKAP
        $data['export_fas_revenue_rkap'] = ($data['export_fas_price_rkap']['RKAP'] * $data['export_fas_quantity_rkap']['RKAP']) / 1000000;
        // FAS Revenue Actual
        $data['export_fas_revenue_actual'] = ($data['export_fas_price_actual']['ACTUAL'] * $data['export_fas_quantity_actual']['ACTUAL']) / 1000000;

        // MV
        // MV Price RKAP
        $data['export_mv_price_rkap'] = $builder_rkp->select("SUM(PRC) AS RKAP")
            ->where("TYPE = 'Export'")
            ->where("SHPMN = 'MV'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // MV Price Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_mv_price_actual'] = $db->query("SELECT AVG(price) AS ACTUAL FROM temp_price_quantity
                                                            WHERE shipment = 'MV'
                                                            AND `type` = 'Export'
                                                            AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['export_mv_price_actual'] = $builder_price->select("(SELECT SUM(final_price) FROM T_SAL_PRICE price 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = price.shipment_id 
                WHERE shipment.category = 'MV' AND shipment.type = 'Export' AND date_final BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // MV Quantity RKAP
        $data['export_mv_quantity_rkap'] = $builder_rkp->select("SUM(QTY) AS RKAP")
            ->where("TYPE = 'Export'")
            ->where("SHPMN = 'MV'")
            ->where("MONAT BETWEEN MONTH('$start_date') AND MONTH('$end_date')")
            ->where("GJAHR BETWEEN YEAR('$start_date') AND YEAR('$end_date')")
            ->get()->getRowArray();
        // MV Quantity Actual
        if ($time_actual_start->getMonth() < 11 && $time_actual_end->getMonth() < 11 && $time_actual_start->getYear() == 2022 && $time_actual_end->getYear() == 2022) {
            $data['export_mv_quantity_actual'] = $db->query("SELECT AVG(quantity) AS ACTUAL FROM temp_price_quantity
                                                        WHERE shipment = 'MV'
                                                        AND `type` = 'Export'
                                                        AND `month` BETWEEN MONTH('$time_actual_start') AND MONTH('$time_actual_end')")->getRowArray();
        } else {
            $data['export_mv_quantity_actual'] = $builder_inv->select("(SELECT SUM(FNL_QTY) FROM FI_SALES_INV inv 
                INNER JOIN T_SAL_SHIPMENT shipment ON shipment.shipment_id = inv.SHIPMENT_ID 
                WHERE shipment.category = 'MV' AND shipment.type = 'Export' AND BUDAT BETWEEN '$start_date' AND '$end_date') AS ACTUAL")
                ->get()->getRowArray();
        }
        // MV Revenue RKAP
        $data['export_mv_revenue_rkap'] = ($data['export_mv_price_rkap']['RKAP'] * $data['export_mv_quantity_rkap']['RKAP']) / 1000000;
        // MV Revenue Actual
        $data['export_mv_revenue_actual'] = ($data['export_mv_price_actual']['ACTUAL'] * $data['export_mv_quantity_actual']['ACTUAL']) / 1000000;

        $qstr_sales_inv = "SELECT DISTINCT TI.*, TMD.NAME1 AS BUYER,
        IFNULL(TSCO.contract_price,0) AS CONTRACT_PRICE,
        (CONTRACT_PRICE - FNL_QTY1) AS VAR_PRICE1,
        (CONTRACT_PRICE - FNL_QTY2) AS VAR_PRICE2,
        (CONTRACT_PRICE - FNL_QTY3) AS VAR_PRICE3,
        (CONTRACT_PRICE - FNL_QTY4) AS VAR_PRICE4,
        (CONTRACT_PRICE - FNL_QTY5) AS VAR_PRICE5,
        (CONTRACT_PRICE - FNL_QTY6) AS VAR_PRICE6,
        (CONTRACT_PRICE - FNL_QTY7) AS VAR_PRICE7,
        (CONTRACT_PRICE - FNL_QTY8) AS VAR_PRICE8,
        (CONTRACT_PRICE - FNL_QTY9) AS VAR_PRICE9,
        (CONTRACT_PRICE - FNL_QTY10) AS VAR_PRICE10,
        IFNULL(TDI.DSPTCH,0) AS DESPATCH,
        IFNULL(TDM.DEMURAGE,0) AS DEMURAGE,
        (SELECT CONTRACT_PRICE - FNL_QTY1 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE1,
        (SELECT CONTRACT_PRICE - FNL_QTY2 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE2,
        (SELECT CONTRACT_PRICE - FNL_QTY3 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE3,
        (SELECT CONTRACT_PRICE - FNL_QTY4 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE4,
        (SELECT CONTRACT_PRICE - FNL_QTY5 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE5,
        (SELECT CONTRACT_PRICE - FNL_QTY6 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE6,
        (SELECT CONTRACT_PRICE - FNL_QTY7 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE7,
        (SELECT CONTRACT_PRICE - FNL_QTY8 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE8,
        (SELECT CONTRACT_PRICE - FNL_QTY9 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE9,
        (SELECT CONTRACT_PRICE - FNL_QTY10 + DESPATCH - DEMURAGE) AS TOTAL_NET_PRICE10
        FROM FI_SALES_INV TI
        LEFT JOIN T_MDCUSTOMER TMD ON TI.KUNNR = TMD.KUNNR AND TMD.BUKRS = 'HH10'
        LEFT JOIN T_SAL_CONTRACT_ORDER TSCO ON TI.CONTRACT_NO = TSCO.contract_no
        LEFT JOIN FI_DSPTCH_INV TDI ON TI.CONTRACT_NO = TDI.CONTRCT_NO AND TI.SHIPMENT_ID = TDI.SHIPMENT_ID
        LEFT JOIN FI_DEMURAGE_INV TDM ON TI.CONTRACT_NO = TDM.CONTRCT_NO AND TI.SHIPMENT_ID = TDM.SHIPMENT_ID
        LEFT JOIN T_SAL_COA TSC ON TI.CONTRACT_NO = TSC.contract_no WHERE TI.deleted_at = '' ";
        $q = $db->query($qstr_sales_inv);
        $data['sales'] = $q->getResultArray();

        // dd($this->respond($q->getResultArray(), 200));
        //return $this->respond($q->getResult(), 200);

        echo view("pages/finance/salesandproduction", $data);
    }

    public function salesCOA()
    {
        $id = $_GET['id'];
        $data['title'] = "Parameter COA";
        $db = Database::connect();
        $ParamCOA = "SELECT DISTINCT TSC.*, TI.CONTRACT_NO AS ContNo, TMD.NAME1 AS BUYER
        FROM FI_SALES_INV TI
        LEFT JOIN T_MDCUSTOMER TMD ON TI.KUNNR = TMD.KUNNR AND TMD.BUKRS = 'HH10'
        LEFT JOIN T_SAL_CONTRACT_ORDER TSCO ON TI.CONTRACT_NO = TSCO.contract_no
        LEFT JOIN FI_DSPTCH_INV TDI ON TI.CONTRACT_NO = TDI.CONTRCT_NO AND TI.SHIPMENT_ID = TDI.SHIPMENT_ID
        LEFT JOIN FI_DEMURAGE_INV TDM ON TI.CONTRACT_NO = TDM.CONTRCT_NO AND TI.SHIPMENT_ID = TDM.SHIPMENT_ID
        LEFT JOIN T_SAL_COA TSC ON TI.CONTRACT_NO = TSC.contract_no WHERE TI.deleted_at = '' AND TI.id = " . $id;
        $q = $db->query($ParamCOA);
        $data['ParamCOA'] = $q->getResultArray();

        $activityCOA = "SELECT * FROM T_SAL_MASTER_COA";
        $activityCOA = $db->query($activityCOA);
        $data['activityCOA'] = $activityCOA->getResultArray();


        echo view("pages/finance/salesCOA", $data);
    }

    /* End of Sales and Production */

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
