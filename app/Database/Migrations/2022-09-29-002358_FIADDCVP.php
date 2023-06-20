<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FIADDCVP extends Migration
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
                'CTGRY' => [
                    'type' => 'CHAR',
                    'constraint' => 20,
                    "null" => true,
                ],
                'RKMRK' => [
                    'type' => 'CHAR',
                    'constraint' => 2,
                    "null" => true,
                ],
                'GJAHR' => [
                    'type' => 'INT',
                    'constraint' => 4,
                    "null" => true,
                ],
                "DMBTR" => [
                    "type" => "DECIMAL",
                    'constraint' => '18,2',
                    "null" => true,
                ],
                "COMMT" => [
                    "type" => "CHAR",
                    'constraint' => 50,
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
        $this->forge->createTable('FI_ADD_CVP', true);
    }

    public function down()
    {
        $this->forge->dropTable('FI_ADD_CVP', true);
    }
}
