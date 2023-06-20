<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Glogs extends Migration
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
            "table" => [
                "type" => "VARCHAR",
                "constraint" => 100,
                "null" => true,
            ],
            "action" => [
                "type" => "CHAR",
                "constraint" => 100,
            ],
            "data_before" => [
                "type" => "TEXT",
            ],
            "data_after" => [
                "type" => "TEXT",
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
                "type" => "CHAR",
                "constraint" => 100,
                "null" => true,
            ],
            "updated_by" => [
                "type" => "CHAR",
                "constraint" => 100,
                "null" => true,
            ],
            "deleted_by" => [
                "type" => "CHAR",
                "constraint" => 100,
                "null" => true,
            ],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("g_logs");
        // $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('g_logs');
        //
    }
}
