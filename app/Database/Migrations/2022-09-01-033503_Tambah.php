<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tambah extends Migration
{
    public function up()
    {
        $fields = [
            "id_gabung" => [
                "type" => "CHAR",
                'constraint' => '20',
            ],
        ];
        $this->forge->addColumn("T_KualitasAir", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('T_KualitasAir', 'id_gabung');
    }
}
