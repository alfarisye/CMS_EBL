<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FIINRKP extends Migration
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
                'BDGID' => [
                    'type' => 'CHAR',
                    'constraint' => 14,
                    "null" => true,
                ],
                'GJAHR' => [
                    'type' => 'INT',
                    'constraint' => 4,
                    "null" => true,
                ],
                "MONAT" => [
                    "type" => "CHAR",
                    'constraint' => 2,
                    "null" => true,
                ],
                "SHPMN" => [
                    "type" => "CHAR",
                    'constraint' => 4,
                    "null" => true,
                ],
                "PRC" => [
                    "type" => "DECIMAL",
                    'constraint' => 18,
                    "null" => true,
                ],
                "QTY" => [
                    "type" => "DECIMAL",
                    'constraint' => 13,
                    "null" => true,
                ],
                "STATS" => [
                    "type" => "CHAR",
                    'constraint' => 2,
                    "null" => true,
                ],
                "CRTDB" => [
                    "type" => "CHAR",
                    'constraint' => 20,
                    "null" => true,
                ],
                "CRTDA" => [
                    "type" => "DATETIME",
                    "null" => true,
                ],
                "EDTBY" => [
                    "type" => "CHAR",
                    'constraint' => 20,
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
        $this->forge->createTable('FI_IN_RKP', true);
    }

    public function down()
    {
        $this->forge->dropTable('FI_IN_RKP', true);
    }
}
