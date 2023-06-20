<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Timesheet extends Migration
{
    public function up()
    {
        // $this->db->disableForeignKeyChecks();
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "prd_date" => [
                "type" => "DATE",
                "null" => true,
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
                'type' => 'decimal',
                'constraint' => '15,2',
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
            "prd_revision" => [
                "type" => "INT",
                "constraint" => 2,
            ],
            "status" => [
                "type" => "enum",
                "constraint" => ["draft", "submitted", "verified", "approved", "rejected", "posted"],
            ],
            "create_date" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "last_update" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "id_contractor" => [
                "type" => "INT",
                "constraint" => 11,
                "null" => true,
                "unsigned" => true,
            ],
            "id_annualbudget" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "id_monthlybudget" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "deleted_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "prd_code" => [
                "type" => "VARCHAR",
                "constraint" => 20,
                "unique" => true,
            ],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("id_contractor", "md_contractors", "id");
        $this->forge->addForeignKey("id_annualbudget", "md_annualbudget", "id_annualbudget");
        $this->forge->addForeignKey("id_monthlybudget", "md_monthlybudget", "id_monthlybudget");
        $this->forge->createTable("timesheets");
        // $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('timesheets');
    }
}
