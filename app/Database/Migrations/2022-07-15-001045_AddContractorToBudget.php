<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContractorToBudget extends Migration
{
    public function up()
    {
        $fields = [
            'id_contractor' => [
                'type' => 'INT',
                'constraint' => 11,
                'after' => 'id_monthlybudget',
                "unsigned" => true,
            ],
        ];
        $this->forge->addColumn("md_monthlybudget", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('md_monthlybudget', 'id_contractor');
    }
}
