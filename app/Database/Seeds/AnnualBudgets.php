<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class AnnualBudgets extends Seeder
{
    public function run()
    {
        $data = [
            "year" => 2022,
            "project" => "Tambang 1",
            "cg_annualbudget_qt" => "1000000",
            "ob_annualbudget_qt" => "1000000",
            "create_date" => Time::now(),
            "revision" => 0,
            "status" => "active"
        ];
        $this->db->table('md_annualbudget')->insert($data);
    }
}
