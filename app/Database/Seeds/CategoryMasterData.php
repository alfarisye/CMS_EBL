<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategoryMasterData extends Seeder
{
    public function run()
    {
        /**
                Pada Category apabila user memilih Type Lost Time Injury maka pilihan yang 
                muncul adalah Ringan, Berat, Mati (Fatality)
                Apabila user memilih Potential Lost 
                Time atau Non Potential Lost Time maka pilihan yang muncul adalah Near Miss, 
                First Aid, Medical Treatment, Fire Case dan Property Damage. 
         */
        $data = [
            // Lost Time Injury
            [
                "Id_category" => 1,
                "id_type" => 1,
                "category" => "Ringan"
            ],
            [
                "Id_category" => 2,
                "id_type" => 1,
                "category" => "Berat"
            ],
            [
                "Id_category" => 3,
                "id_type" => 1,
                "category" => "Mati (Fatality)"
            ],
            // Potential Lost Time
            [
                "Id_category" => 4,
                "id_type" => 2,
                "category" => "Near Miss"
            ],
            [
                "Id_category" => 5,
                "id_type" => 2,
                "category" => "First Aid"
            ],
            [
                "Id_category" => 6,
                "id_type" => 2,
                "category" => "Medical Treatment"
            ],
            [
                "Id_category" => 7,
                "id_type" => 2,
                "category" => "Fire Case"
            ],
            [
                "Id_category" => 8,
                "id_type" => 2,
                "category" => "Property Damage"
            ],
            // Non Potential Lost Time
            [
                "Id_category" => 9,
                "id_type" => 3,
                "category" => "Near Miss"
            ],
            [
                "Id_category" => 10,
                "id_type" => 3,
                "category" => "First Aid"
            ],
            [
                "Id_category" => 11,
                "id_type" => 3,
                "category" => "Medical Treatment"
            ],
            [
                "Id_category" => 12,
                "id_type" => 3,
                "category" => "Fire Case"
            ],
            [
                "Id_category" => 13,
                "id_type" => 3,
                "category" => "Property Damage"
            ],
        ];
        foreach($data as $d) {
            $this->db->table('t_category')->insert($d);
        }
    }
}
