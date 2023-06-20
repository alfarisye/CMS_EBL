<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Notification extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 10,
                "auto_increment" => true,
            ],
            "user_id_from" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            "user_id_to" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            "user_id_cc" => [
                "type" => "TEXT",
                "null" => true,
            ],
            "subject" => [
                "type" => "CHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "attach" => [
                "type" => "TEXT",
                "null" => true,
            ],
            "type" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            "status" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            "message" => [
                "type" => "TEXT",
                "null" => true,
            ],
            "detail" => [
                "type" => "TEXT",
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
            "deleted_by" => [
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
        $this->forge->createTable("Notification");
    }

    public function down()
    {
        $this->forge->dropTable('Notification', true);
        //
    }
}
