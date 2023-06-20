<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TSALTARGET extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 10,
                "auto_increment" => true,
            ],
            "month" => [
                "type" => "INT",
                "constraint" => 10,
                "null" => true,
            ],
            "year" => [
                "type" => "INT",
                "constraint" => 10,
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
            "quantity" => [
                "type" => "decimal",
                "constraint" => '15,2',
                "null" => true,
            ],
        ]);
        $this->forge->addKey("id", true);
        // $this->forge->addForeignKey("shipment_id", "T_SAL_SHIPMENT", "id");
        $this->forge->createTable("T_SAL_TARGET");
    }

    public function down()
    {
        $this->forge->dropTable('T_SAL_TARGET');
        //
    }
}
