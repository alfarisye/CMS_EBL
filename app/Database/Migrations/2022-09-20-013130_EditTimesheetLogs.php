<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditTimesheetLogs extends Migration
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
        $this->forge->addColumn("timesheet_logs", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('timesheet_logs', 'prd_ob_distance');
        $this->forge->dropColumn('timesheet_logs', 'prd_cg_distance');
    }
}
