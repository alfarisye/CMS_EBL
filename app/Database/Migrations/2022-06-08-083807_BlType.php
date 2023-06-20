<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BlType extends Migration
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
            "nama_type" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "deskripsi" => [
                "type" => "TEXT",
            ],
            "geojson" => [
                "type" => "VARCHAR",
                "constraint" => 255,
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
        $this->forge->createTable("bl_type");
    }

    public function down()
    {
        $this->forge->dropTable('bl_type');
        //
    }
}
