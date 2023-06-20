<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tpo extends Migration
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
                'EBELN' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
            ],
                'AEDAT' => [
                    'type' => 'DATE',
            ],
                'LIFNR' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                    'null' => true,
            ],
                'FULL_RELEASE' => [
                    'type' => 'VARCHAR',
                    'constraint' => 1,
                    'null' => true,
            ],
                'LT_PO' => [
                    'type' => 'VARCHAR',
                    'constraint' => 5,
                    'null' => true,
            ],
                'FRGSX' => [
                    'type' => 'VARCHAR',
                    'constraint' => 2,
                    'null' => true,
            ],
                'RLDATE' => [
                    'type' => 'DATE',
                    'null' => true,
            ],
                'BEDAT' => [
                    'type' => 'DATE',
                    'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_po', true);
    }

    public function down()
    {
        $this->forge->dropTable('t_po', true);
    }
}
