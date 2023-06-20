<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditManJamkerja extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn("T_jamkerja", [
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
        $this->forge->modifyColumn("T_jamkerja", [
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
