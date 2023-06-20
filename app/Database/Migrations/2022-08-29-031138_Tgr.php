<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tgr extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            'MJAHR' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
            ],
            'MBLNR' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'ZEILE' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
            ],
            'BUDAT' => [
                'type' => 'DATE',
            ],
            'EBELN' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
            ],
            'EBELP' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'WERKS' => [
                'type' => 'VARCHAR',
                'constraint' => 4,
                'null' => true,
            ],
            'BWART' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => true,
            ],
            'MENGE' => [
                'type' => 'DECIMAL',
                'constraint' => '13,2',
                'null' => true,
            ],
            'MENGE2' => [
                'type' => 'DECIMAL',
                'constraint' => '13,2',
                'null' => true,
            ],
            'ERFMG' => [
                'type' => 'DECIMAL',
                'constraint' => '13,2',
                'null' => true,
            ],
            'MEINS' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'null' => true,
            ],
            'WEMPF' => [
                'type' => 'VARCHAR',
                'constraint' => 26,
                'null' => true,
            ],
            'DELT' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'null' => true,
            ],
            'LT_POGR' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'null' => true,
            ],
            'STATUS' => [
                'type' => 'VARCHAR',
                'constraint' => 26,
                'null' => true,
            ],
        ],);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_gr', true);
    }

    public function down()
    {
        $this->forge->dropTable('t_gr', true);
    }
}
