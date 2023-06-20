<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditTimesheetAdjustmentLogs extends Migration
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
        $this->forge->addColumn("timesheet_adjustment_logs", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('timesheet_adjustment_logs', 'adj_ob_distance');
        $this->forge->dropColumn('timesheet_adjustment_logs', 'adj_cg_distance');
    }
}
