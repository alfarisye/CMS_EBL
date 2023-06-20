<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BlGeojson extends Migration
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
            "bl_type_id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "blok_id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "periode" => [
                "type" => "DATE",
                "null" => true,
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
            ]
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("bl_type_id", "bl_type", "id");
        $this->forge->createTable("bl_geojson");
    }

    public function down()
    {
        $this->forge->dropTable('bl_geojson');
        //
    }
}
