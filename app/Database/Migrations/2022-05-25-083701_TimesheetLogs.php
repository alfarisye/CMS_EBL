<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TimesheetLogs extends Migration
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
            "prd_cg_day_qty" => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            "prd_cg_night_qty" => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            "prd_cg_total" => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            "prd_ob_day_qty" => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            "prd_ob_night_qty" => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            "prd_ob_total" => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            "prd_sr" => [
                "type" => "INT",
                "constraint" => 11,
            ],
            "prd_rain" => [
                "type" => "INT",
                "constraint" => 11,
            ],
            "prd_slip" => [
                "type" => "INT",
                "constraint" => 11,
            ],
            "prd_%" => [
                'type' => 'decimal',
                'constraint' => '5,2',
            ],
            "prd_rainfall" => [
                "type" => "INT",
                "constraint" => 11,
            ],
            "noted" => [
                "type" => "TEXT",
                "null" => true,
            ],
            "changes" => [
                "type" => "INT",
                "constraint" => 2,
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "status" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "id_timesheet" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "actor" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "prd_code" => [
                "type" => "VARCHAR",
                "constraint" => 20,
            ],
            "action" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("id_timesheet", "timesheets", "id");
        $this->forge->createTable("timesheet_logs");
    }

    public function down()
    {
        $this->forge->dropTable("timesheet_logs");
    }
}
