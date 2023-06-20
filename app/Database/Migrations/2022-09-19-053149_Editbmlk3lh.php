<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Editbmlk3lh extends Migration
{
    public function up()
    {
        $fields = [
            "budget_max" => [
                "type" => "INT",
                'constraint' => "5",
            ],
        ];
        $this->forge->addColumn("T_BML", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('T_BML','budget_max');
    }
}
