<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditTimesheetAdjustment extends Migration
{
    public function up()
    {
        $fields = [
            'adj_ob_distance' => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            'adj_cg_distance' => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
        ];
        $this->forge->addColumn("timesheet_adjustments", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('timesheet_adjustments', 'adj_ob_distance');
        $this->forge->dropColumn('timesheet_adjustments', 'adj_cg_distance');
    }
}
