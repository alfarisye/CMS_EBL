<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BlBlokForm extends Migration
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
            "blok_id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "bl_type_id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "nama_form" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "status" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
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
        $this->forge->addForeignKey("blok_id", "bl_blok", "id");
        $this->forge->addForeignKey("bl_type_id", "bl_type", "id");
        $this->forge->createTable("bl_blok_form");
    }

    public function down()
    {
        $this->forge->dropTable('bl_blok_form');
        //
    }
}
