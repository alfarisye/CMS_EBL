<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditMDContractor extends Migration
{
    public function up()
    {
        $fields = [
            'contractor_type' => [
                "type" => "enum",
                "constraint" => ["timesheet", "crush_coal", "hauling"],
            ],
        ];
        $this->forge->addColumn("md_contractors", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('md_contractors', 'contractor_type');
    }
}
