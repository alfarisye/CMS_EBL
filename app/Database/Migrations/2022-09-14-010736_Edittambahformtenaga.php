<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Edittambahformtenaga extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn("t_tambahtenaga", [
            "value" => [
                "type" => "FLOAT",
                "constraint" => 10,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn("t_tambahtenaga", [
            "value" => [
                'type' => 'VARCHAR',
                "constraint" => 10,
            ],
        ]);
    }
}
