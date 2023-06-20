<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tpoitem extends Migration
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
                'EBELN' => [
                        'type' => 'VARCHAR',
                        'constraint' => 10,
                ],
                'EBELP' => [
                        'type' => 'VARCHAR',
                        'constraint' => 5,
                ],
                'TXZ01' => [
                        'type' => 'VARCHAR',
                        'constraint' => 40,
                        'null' => true,
                ],
                'BUKRS' => [
                        'type' => 'VARCHAR',
                        'constraint' => 4,
                ],
                'LOEKZ' => [
                        'type' => 'VARCHAR',
                        'constraint' => 1,
                        'null' => true,
                ],
                'WERKS' => [
                        'type' => 'VARCHAR',
                        'constraint' => 4,
                        'comment' => 'Plant',
                        'null' => true,
                ],
                'MATNR' => [
                        'type' => 'VARCHAR',
                        'constraint' => 18,
                        'null' => true,
                ],
                'MENGE' => [
                        'type' => 'DECIMAL',
                        'constraint' => '18,2',
                        'null' => true,
                ],
                'MEINS' => [
                        'type' => 'VARCHAR',
                        'constraint' => 3,
                        'null' => true,
                ],
                'ELIKZ' => [
                        'type' => 'VARCHAR',
                        'constraint' => 1,
                        'null' => true,
                ],
                'WEPOS' => [
                        'type' => 'VARCHAR',
                        'constraint' => 1,
                        'null' => true,
                ],
                'ETA' => [
                        'type' => 'DATE',
                        'null' => true,
                ],
                'BANFN' => [
                        'type' => 'VARCHAR',
                        'constraint' => 10,
                        'null' => true,
                ],
                'BNFPO' => [
                        'type' => 'VARCHAR',
                        'constraint' => 5,
                        'null' => true,
                ],
                'NETWR' => [
                        'type' => 'DECIMAL',
                        'constraint' => '18,2',
                        'null' => true,
                ],
                'TBINV' => [
                        'type' => 'DECIMAL',
                        'constraint' => '18,2',
                        'null' => true,
                ],
                'KNTTP' => [
                        'type' => 'VARCHAR',
                        'constraint' => 1,
                        'null' => true,
                ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_po_item', true);
    }

    public function down()
    {
        $this->forge->dropTable('t_po_item', true);
    }
}
