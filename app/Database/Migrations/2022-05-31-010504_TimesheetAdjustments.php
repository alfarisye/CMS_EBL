<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TimesheetAdjustments extends Migration
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
            "status" => [
                "type" => "enum",
                "constraint" => ["draft", "verify", "posted"],
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "updated_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "deleted_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "created_by" => [
                "type" => "varchar",
                "constraint" => 25,
            ],
            "updated_by" => [
                "type" => "varchar",
                "constraint" => 25,
            ],
            "code" => [
                "type" => "varchar",
                "constraint" => 25,
                "unique" => true,
            ],
            "notes" => [
                "type" => "text",
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('timesheet_adjustments');
    }

    public function down()
    {
        $this->forge->dropTable('timesheet_adjustments');
    }
}
