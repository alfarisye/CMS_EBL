<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TSALMASTERACTIVITY extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 10,
                "auto_increment" => true,
            ],
            "sequence" => [
                "type" => "INT",
                "constraint" => 3,
                "null" => true,
            ],
            "activity" => [
                "type" => "CHAR",
                "constraint" => 30,
                "null" => true,
            ],
            
            "status" => [
                "type" => "CHAR",
                "constraint" => 2,
                "null" => true,
            ],
            
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
        $this->forge->createTable("T_SAL_MASTER_ACTIVITY");
    }

    public function down()
    {
        $this->forge->dropTable('T_SAL_MASTER_ACTIVITY');
    }
}
