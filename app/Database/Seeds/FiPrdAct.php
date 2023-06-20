<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class FiPrdAct extends Seeder
{
    public function run()
    {
        $data = [
            ["id" => 1, "ACTVY" => "OB"],
            ["id" => 2, "ACTVY" => "CG"],
            ["id" => 3, "ACTVY" => "Haul to ROM"],
            ["id" => 4, "ACTVY" => "Crusher"],
            ["id" => 5, "ACTVY" => "Hauling"],
            ["id" => 5, "ACTVY" => "Port, Tag Assist, Unloading"],
            ["id" => 5, "ACTVY" => "Chemical"],
            ["id" => 5, "ACTVY" => "Syahbandar & Free Agent"],
            ["id" => 5, "ACTVY" => "Surveyor"],
            ["id" => 5, "ACTVY" => "Amortization"],
            ["id" => 5, "ACTVY" => "Others"]
        ];
        foreach($data as $d) {
            $this->db->table('FI_PRD_ACT')->insert($d);
        }
    }
}
