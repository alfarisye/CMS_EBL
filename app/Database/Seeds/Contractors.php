<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class Contractors extends Seeder
{
    public function run()
    {
        $data = [
            'contractor_name' => 'PT. Bintang Jaya',
            'status' => 1,
            'start_date' => '2020-01-01',
            'end_date' => '2020-12-31',
            'created_at' => Time::now(),
        ];
        $this->db->table('md_contractors')->insert($data);
    }
}
