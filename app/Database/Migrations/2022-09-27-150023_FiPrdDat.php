<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FiPrdDat extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'id' => [
                    "type" => "INT",
                    "constraint" => 11,
                    "unsigned" => true,
                    "auto_increment" => true,
                ],
                'ACTVY' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    "null" => true,
                ],
                'LIFNR' => [
                    'type' => 'CHAR',
                    'constraint' => 25,
                    "null" => true,
                ],
                'QTY' => [
                    'type' => 'DECIMAL',
                    'constraint' => 15,
                    "null" => true,
                ],
                "TRF" => [
                    "type" => "DECIMAL",
                    'constraint' => 20,
                    "null" => true,
                ],
                "CRTDB" => [
                    "type" => "CHAR",
                    'constraint' => 25,
                    "null" => true,
                ],
                "CRTDA" => [
                    "type" => "DATETIME",
                    "null" => true,
                ],
                "EDTBY" => [
                    "type" => "CHAR",
                    'constraint' => 25,
                    "null" => true,
                ],
                "EDTAT" => [
                    "type" => "DATETIME",
                    "null" => true,
                ],
                "status" => [
                    "type" => "INT",
                    "default" => "1",
                ]
            ]
        );
        $this->forge->addKey('id', true);
        $this->forge->createTable('FI_PRD_DAT', true);
    }

    public function down()
    {
        $this->forge->dropTable('FI_PRD_DAT', true);
    }
}
