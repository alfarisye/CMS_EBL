<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TSALPRICE extends Migration
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
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "contract_no" => [
                "type" => "CHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "contract_price" => [
                "type" => "decimal",
                'constraint' => '15,2',
            ],
            "date_final" => [
                "type" => "DATE",
                "null" => true,
            ],
            "final_price" => [
                "type" => "decimal",
                'constraint' => '15,2',
            ],
            "amount" => [
                "type" => "decimal",
                'constraint' => '15,2',
            ],
            "curr" => [
                "type" => "CHAR",
                'constraint' => 255,
                "null" => true,
            ],
        ]);
        $this->forge->addKey("id", true);
        // $this->forge->addForeignKey("shipment_id", "T_SAL_SHIPMENT", "id");
        $this->forge->createTable("T_SAL_PRICE");
    }

    public function down()
    {
        $this->forge->dropTable('T_SAL_PRICE');
        //
    }
}
