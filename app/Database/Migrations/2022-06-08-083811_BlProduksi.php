<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BlProduksi extends Migration
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
            "type_blok" => [
                "type" => "CHAR",
                'constraint' => '20',
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
            "data_produksi" => [
                "type" => "decimal",
                'constraint' => '15,2',
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
            "deskipsi_data" => [
                "type" => "TEXT",
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
            "created_by" => [
                "type" => "INT",
                "constraint" => 11,
                "null" => true,
            ],
            "updated_by" => [
                "type" => "INT",
                "constraint" => 11,
                "null" => true,
            ],
            "deleted_by" => [
                "type" => "INT",
                "constraint" => 11,
                "null" => true,
            ],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("blok_id", "bl_blok", "id");
        $this->forge->addForeignKey("bl_type_id", "bl_type", "id");
        $this->forge->createTable("bl_produksi");
    }

    public function down()
    {
        $this->forge->dropTable('bl_produksi');
        //
    }
}
