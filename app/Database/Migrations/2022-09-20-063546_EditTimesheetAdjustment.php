<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditTimesheetAdjustment extends Migration
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
        $this->forge->addColumn("timesheet_adjustments", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('timesheet_adjustments', 'id_contractor');
    }
}
