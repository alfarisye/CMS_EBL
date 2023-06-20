<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TSALSHIPMENT extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 10,
                "auto_increment" => true,
            ],
            "shipment_id" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            "contract_no" => [
                "type" => "CHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "category" => [
                "type" => "CHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "type" => [
                "type" => "CHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "bl_date" => [
                "type" => "DATE",
                "null" => true,
            ],
            "vessel" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            
            "gi_qty" => [
                "type" => "DECIMAL",
                "constraint" => '15,2',
                "null" => true,
            ],
            
            "receipt_date" => [
                "type" => "DATE",
                "null" => true,
            ],
            "gr_qty" => [
                "type" => "DECIMAL",
                "constraint" => '15,2',
                "null" => true,
            ],
            "uom" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            "deletion" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            "status" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            "type_supply" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            // 
            "created_by" => [
                "type" => "CHAR",
                "constraint" => 30,
                "null" => true,
            ],
            "created_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "updated_by" => [
                "type" => "CHAR",
                "constraint" => 30,
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
        // $this->forge->addForeignKey("shipment_id", "T_SAL_SHIPMENT", "id");
        $this->forge->createTable("T_SAL_SHIPMENT");
    }

    public function down()
    {
        //
    }
}
