<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BlBlok extends Migration
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
            "nama_blok" => [
                "type" => "VARCHAR",
                "constraint" => 100,
                "null" => true,
            ],
            "group" => [
                "type" => "CHAR",
                "constraint" => 100,
            ],
            "deskripsi" => [
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
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("bl_blok");
        // $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('bl_blok');
        //
    }
}
