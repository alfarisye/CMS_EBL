<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TPRPS extends Migration
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
                'POSID' => [
                    'type' => 'CHAR',
                    'constraint' => 24,
                ],
                'POST1' => [
                    'type' => 'CHAR',
                    'constraint' => 40,
                ],
                'OBJNR' => [
                    'type' => 'CHAR',
                    'constraint' => 22,
                ],
                "ERDAT" => [
                    "type" => "DATE",
                    "null" => true,
                ]
            ]
        );
        $this->forge->addKey('id', true);
        $this->forge->createTable('T_PRPS', true);
    }

    public function down()
    {
        $this->forge->dropTable('T_PRPS', true);
    }
}
