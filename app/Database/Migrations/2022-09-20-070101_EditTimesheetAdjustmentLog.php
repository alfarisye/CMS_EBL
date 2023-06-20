<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditTimesheetAdjustmentLog extends Migration
{
    public function up()
    {
        $fields = [
            "id_contractor" => [
                "type" => "INT",
                "constraint" => 11,
                "null" => true,
                "unsigned" => true,
            ],
        ];
        $this->forge->addColumn("timesheet_adjustment_logs", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('timesheet_adjustment_logs', 'id_contractor');
    }
}
