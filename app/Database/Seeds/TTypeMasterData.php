<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TTypeMasterData extends Seeder
{
    public function run()
    {
        $data = [
            /**
                Pada Category apabila user memilih Type Lost Time Injury maka pilihan yang 
                muncul adalah Ringan, Berat, Mati (Fatality)
                Apabila user memilih Potential Lost 
                Time atau Non Potential Lost Time maka pilihan yang muncul adalah Near Miss, 
                First Aid, Medical Treatment, Fire Case dan Property Damage. 
             */
            [
                "id_type" => 1,
                "type" => "Lost Time Injury",
            ],
            [
                "id_type" => 2,
                "type" => "Potential Lost Time",
            ],
            [
                "id_type" => 3,
                "type" => "Non Potential Lost Time Injury",
            ],
            
        ];
        foreach($data as $d) {
            $this->db->table('t_type')->insert($d);
        }
    }
}
