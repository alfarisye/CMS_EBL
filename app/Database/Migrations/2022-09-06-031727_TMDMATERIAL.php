<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TMDMATERIAL extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 10,
                "auto_increment" => true,
            ],
            "MTART" => [
                "type" => "CHAR",
                "constraint" => 5,
            ],
            "MATNR" => [
                "type" => "CHAR",
                "constraint" => 18,
                "null" => true,
            ],
            "MAKTX" => [
                "type" => "CHAR",
                "constraint" => 30,
                "null" => true,
            ],
            "MEINS" => [
                "type" => "CHAR",
                "constraint" => 5,
                "null" => true,
            ],
            "MATKL" => [
                "type" => "CHAR",
                "constraint" => 9,
                "null" => true,
            ],
            "LVORM" => [
                "type" => "CHAR",
                "constraint" => 1,
                "null" => true,
            ],
            "WERKS" => [
                "type" => "CHAR",
                "constraint" => 4,
                "null" => true,
            ],
        ]);
        $this->forge->addKey("id", true);
        // $this->forge->addForeignKey("shipment_id", "T_SAL_SHIPMENT", "id");
        $this->forge->createTable("T_MDMATERIAL");
    }

    public function down()
    {
        $this->forge->dropTable('T_MDMATERIAL');
    }
}
