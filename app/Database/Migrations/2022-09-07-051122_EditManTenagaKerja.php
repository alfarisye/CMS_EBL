<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditManTenagaKerja extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn("T_tenagakerja", [
            "month_jamkerja" => [
                "type" => "INT",
                "constraint" => 2,
                'null' => true,
            ],
            "year_jamkerja" => [
                "type" => "INT",
                "constraint" => 2,
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn("T_tenagakerja", [
            "month_jamkerja" => [
                'type' => 'DATE',
                'null' => true,
            ],
            "year_jamkerja" => [
                'type' => 'DATE',
                'null' => true,
            ],
        ]);
    }
}
