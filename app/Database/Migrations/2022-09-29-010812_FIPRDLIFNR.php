<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FIPRDLIFNR extends Migration
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
                'LIFNR' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                    "null" => true,
                ],
                'VNDRD' => [
                    'type' => 'CHAR',
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
        $this->forge->createTable('FI_PRD_LIFNR', true);
    }

    public function down()
    {
        $this->forge->dropTable('FI_PRD_LIFNR', true);
    }
}
