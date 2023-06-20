<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\FiActBalance;
use App\Models\FiMdBudget;

use CodeIgniter\I18n\Time;
use Config\Database;

class ProfitAndLoss extends BaseController
{
    public function profitability()
    {
        $db = Database::connect();
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

        $FiMdBudget = new FiMdBudget();
        $builderBudget = $FiMdBudget->builder();

        // list of years
        $data['years'] = $builder->select("DISTINCT(FISC)")->where("GL_ACCOUNT LIKE '4%'")->orderBy('FISC', 'desc')->get()->getResultArray();

        // Revenue new
        $data['revenue_budget'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET")
            ->where("SAKNR LIKE '4%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->get()->getRowArray();

        $data['revenue_actual'] = $builder->select("SUM(BALANCE) / 1000000 AS ACTUAL")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '4%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();
        
        $data['revenue_variance'] = $data['revenue_budget']['BUDGET'] - $data['revenue_actual']['ACTUAL'];

        // cogs new
        $data['cogs_budget'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET")
            ->where("SAKNR LIKE '5%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->get()->getRowArray();

        $data['cogs_actual'] = $builder->select("SUM(BALANCE) / 1000000 AS ACTUAL")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '5%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $data['cogs_variance'] = $data['cogs_budget']['BUDGET'] - $data['cogs_actual']['ACTUAL'];


        // GP new
        $data['gp_budget'] = $data['revenue_budget']['BUDGET'] - $data['cogs_budget']['BUDGET'];
        $data['gp_actual'] = $data['revenue_actual']['ACTUAL'] - $data['cogs_actual']['ACTUAL'];
        $data['gp_variance'] = $data['gp_budget'] - $data['gp_actual'];

        // GAE new
        $data['gae_budget'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET")
            ->where("SAKNR LIKE '6%'")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->get()->getRowArray();

        $data['gae_actual'] = $builder->select("SUM(BALANCE) / 1000000 AS ACTUAL")
            ->where("COMP = 'HH10'")
            ->where("GL_ACCOUNT LIKE '6%'")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $data['gae_variance'] = $data['gae_budget']['BUDGET'] - $data['gae_actual']['ACTUAL'];

        // op new
        $data['op_budget'] = $data['gp_budget'] - $data['gae_budget']['BUDGET'];
        $data['op_actual'] = $data['gp_actual'] - $data['gae_actual']['ACTUAL'];
        $data['op_variance'] = $data['op_budget'] - $data['op_actual'];

        // OTHER I/E new
        $data['oie_budget'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET")
            ->where("(SAKNR BETWEEN '7100000000' AND '7100999999' OR SAKNR BETWEEN '7200000000' AND '7200999999')")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->get()->getRowArray();

        $data['oie_actual'] = $builder->select("SUM(BALANCE) / 1000000 AS ACTUAL")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT BETWEEN '7100000000' AND '7100999999' OR GL_ACCOUNT BETWEEN '7200000000' AND '7200999999')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $data['oie_variance'] = $data['oie_budget']['BUDGET'] - $data['oie_actual']['ACTUAL'];

        // EBT new
        $data['ebt_budget'] = $data['op_budget'] - $data['oie_budget']['BUDGET'];
        $data['ebt_actual'] = $data['op_actual'] - $data['oie_actual']['ACTUAL'];
        $data['ebt_variance'] = $data['ebt_budget'] - $data['ebt_actual'];

        // TAX new
        $data['tax_budget'] = $builderBudget->select("SUM(DMBTR) / 1000000 AS BUDGET")
            ->where("(SAKNR LIKE '910001%' OR SAKNR LIKE '920002%')")
            ->where("GJAHR = $year")
            ->where("MONAT = $month")
            ->get()->getRowArray();

        $data['tax_actual'] = $builder->select("SUM(BALANCE) / 1000000 AS ACTUAL")
            ->where("COMP = 'HH10'")
            ->where("(GL_ACCOUNT LIKE '910001%' OR GL_ACCOUNT LIKE '920002%')")
            ->where("FISC = $year")
            ->where("FI = $month")
            ->get()->getRowArray();

        $data['tax_variance'] = $data['tax_budget']['BUDGET'] - $data['tax_actual']['ACTUAL'];

        // EAT new
        $data['eat_budget'] = $data['ebt_budget'] - $data['tax_budget']['BUDGET'];
        $data['eat_actual'] = $data['ebt_actual'] - $data['tax_actual']['ACTUAL'];
        $data['eat_variance'] = $data['eat_budget'] - $data['eat_actual'];

        // EBITDA new
        // Budget
        $ebitda_budget = $db->query("SELECT SUM(CASE WHEN SAKNR LIKE '4%' THEN DMBTR ELSE NULL END) AS kolom_1,
            SUM(CASE WHEN SAKNR LIKE '5%' THEN DMBTR ELSE NULL END) AS kolom_2,
            SUM(CASE WHEN SAKNR LIKE '6%' THEN DMBTR ELSE NULL END) AS kolom_3,
            SUM(CASE WHEN SAKNR LIKE '7%' THEN DMBTR ELSE NULL END) AS kolom_4,
            SUM(CASE WHEN (SAKNR LIKE '910001%' OR SAKNR LIKE '920002%') THEN DMBTR ELSE NULL END) AS tax
        FROM FI_MD_BUDG
        WHERE GJAHR = $year
        AND MONAT = $month")->getRowArray();

        $ebitda_budget_dep_amor_inte = $db->query("SELECT SUM(CASE WHEN ID='DEP' THEN GL ELSE NULL END) AS depresiasi,
            SUM(CASE WHEN ID='AMRT' THEN GL ELSE NULL END) AS amortisai,
            SUM(CASE WHEN ID='INTEREST' THEN GL ELSE NULL END) AS interest
        FROM ZFIT_CMSGL")->getRowArray();

        $ebitda_budget_handle_subtraction = $ebitda_budget['kolom_1'] - $ebitda_budget['kolom_2'] - $ebitda_budget['kolom_3'] - $ebitda_budget['kolom_4'];
        $ebitda_budget_handle_addition = $ebitda_budget_dep_amor_inte['depresiasi'] + $ebitda_budget_dep_amor_inte['amortisai'] + $ebitda_budget_dep_amor_inte['interest'] + $ebitda_budget['tax'];
        $data['ebitda_budget_fix'] = $ebitda_budget_handle_subtraction + $ebitda_budget_handle_addition;
        
        // Actual
        $ebitda_actual = $db->query("SELECT SUM(CASE WHEN GL_ACCOUNT LIKE '4%' THEN BALANCE ELSE NULL END) AS kolom_1,
            SUM(CASE WHEN GL_ACCOUNT LIKE '5%' THEN BALANCE ELSE NULL END) AS kolom_2,
            SUM(CASE WHEN GL_ACCOUNT LIKE '6%' THEN BALANCE ELSE NULL END) AS kolom_3,
            SUM(CASE WHEN GL_ACCOUNT LIKE '7%' THEN BALANCE ELSE NULL END) AS kolom_4,
            SUM(CASE WHEN (GL_ACCOUNT LIKE '910001%' OR GL_ACCOUNT LIKE '920002%') THEN BALANCE ELSE NULL END) AS tax
        FROM FI_ACT_BAL
        WHERE FISC = $year
        AND FI = $month")->getRowArray();

        $ebitda_actual_dep_amor_inte = $db->query("SELECT SUM(CASE WHEN ID='DEP' THEN GL ELSE NULL END) AS depresiasi,
            SUM(CASE WHEN ID='AMRT' THEN GL ELSE NULL END) AS amortisai,
            SUM(CASE WHEN ID='INTEREST' THEN GL ELSE NULL END) AS interest
        FROM ZFIT_CMSGL")->getRowArray();

        $ebitda_actual_handle_subtraction = $ebitda_actual['kolom_1'] - $ebitda_actual['kolom_2'] - $ebitda_actual['kolom_3'] - $ebitda_actual['kolom_4'];
        $ebitda_actual_handle_addition = $ebitda_actual_dep_amor_inte['depresiasi'] + $ebitda_actual_dep_amor_inte['amortisai'] + $ebitda_actual_dep_amor_inte['interest'] + $ebitda_actual['tax'];
        $data['ebitda_actual_fix'] = $ebitda_actual_handle_subtraction + $ebitda_actual_handle_addition;

        $data['ebitda_variance_fix'] = $data['ebitda_budget_fix'] - $data['ebitda_actual_fix'];

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

        // sfas

        echo view("pages/finance/profitability", $data);
    }
}
