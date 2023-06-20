<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Editk3lhdes extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn("t_k3lh", [
            "Description" => [
                "type" => "VARCHAR",
                "constraint" => 255,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn("t_k3lh", [
            "Description" => [
                'type' => 'VARCHAR',
                'constraint' => '150',
            ],
        ]);
    }
}