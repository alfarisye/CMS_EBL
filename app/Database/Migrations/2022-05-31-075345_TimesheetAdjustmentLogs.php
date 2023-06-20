<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TimesheetAdjustmentsLog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "start_date" => [
                "type" => "DATE",
            ],
            "end_date" => [
                "type" => "DATE",
            ],
            "cg_adjustment" => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            "ob_adjustment" => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            "notes" => [
                "type" => "TEXT",
            ],
            "code" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "created_at" => [
                "type" => "DATETIME",
            ],
            "actor" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "id_timesheet_adjustment" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "status" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey("id_timesheet_adjustment", "timesheet_adjustments", "id");
        $this->forge->createTable('timesheet_adjustment_logs');
    }

    public function down()
    {
        $this->forge->dropTable('timesheet_adjustment_logs');
    }
}
