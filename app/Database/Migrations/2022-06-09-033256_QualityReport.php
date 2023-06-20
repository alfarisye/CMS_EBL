<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class QualityReport extends Migration
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
            "sample_id" => [
                "type" => "CHAR",
                "constraint" => 50,
                'unique'     => true
            ],
            "tgl_mulai" => [
                "type" => "DATE",
                "null" => true,
            ],
            "tgl_akhir" => [
                "type" => "DATE",
                "null" => true,
            ],
            "kategori" => [
                "type" => "CHAR",
                "constraint" => 30,
            ],
            "status_progress" => [
                "type" => "VARCHAR",
                "constraint" => 100,
            ],
            "TM_arb" => [
                "type" => "decimal",
                "constraint" => '5,2',
                'default'    => 0.00,
            ],
            "M_adb" => [
                "type" => "decimal",
                "constraint" => '5,2',
                'default'    => 0.00,
            ],
            "Ash_adb" => [
                "type" => "decimal",
                "constraint" => '5,2',
                'default'    => 0.00,
            ],
            "VM_adb" => [
                "type" => "decimal",
                "constraint" => '5,2',
                'default'    => 0.00,
            ],
            "FC_adb" => [
                "type" => "decimal",
                "constraint" => '5,2',
                'default'    => 0.00,
            ],
            "TS_adb" => [
                "type" => "decimal",
                "constraint" => '5,2',
                'default'    => 0.00,
            ],
            "CV_adb" => [
                "type" => "int",
                "constraint" => 4,
                'default'    => 0,
            ],
            "CV_arb" => [
                "type" => "int",
                "constraint" => 4,
                'default'    => 0,
            ],
            "CV_daf" => [
                "type" => "int",
                "constraint" => 4,
                'default'    => 0,
            ],
            "CV_db" => [
                "type" => "int",
                "constraint" => 4,
                'default'    => 0,
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
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("quality_report");
        // $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('quality_report');
        //
    }
}
