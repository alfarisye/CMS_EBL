<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditTimesheet extends Migration
{
    public function up()
    {
        $fields = [
            'prd_ob_distance' => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            'prd_cg_distance' => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
        ];
        $this->forge->addColumn("timesheets", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('timesheets', 'prd_ob_distance');
        $this->forge->dropColumn('timesheets', 'prd_cg_distance');
    }
}
