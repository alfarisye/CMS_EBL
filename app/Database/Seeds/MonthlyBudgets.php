<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class MonthlyBudgets extends Seeder
{
    public function run()
    {
        $data = [
            "year" => Time::now()->format('Y'),
            "month" => Time::now()->format('m'),
            "project" => "Tambang 1",
            "cg_monthlybudget_qt" => "100000",
            "ob_monthlybudget_qt" => "100000",
            "cg_dailybudget_qt" => "1000",
            "ob_dailybudget_qt" => "1000",
            "create_date" => Time::now(),
            "revision" => 0,
            "status" => "active",
            "id_annualbudget" => 1
        ];
        $this->db->table('md_monthlybudget')->insert($data);
    }
}
