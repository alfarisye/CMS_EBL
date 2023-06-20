<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tpr extends Migration
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
                'BANFN' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                ],
                'BNFPO' => [
                    'type' => 'VARCHAR',
                    'constraint' => 5,
                ],
                'WERKS' => [
                    'type' => 'VARCHAR',
                    'constraint' => 4,
                ],
                'BADAT' => [
                    'type' => 'DATE',
                ],
                'MATNR' => [
                    'type' => 'VARCHAR',
                    'constraint' => 18,
                    'null' => true,
                ],
                'TXZ01' => [
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                    'null' => true,
                ],
                'MENGE' => [
                    'type' => 'DECIMAL',
                    'constraint' => '16,2',
                    'null' => true,
                ],
                'MEINS' => [
                    'type' => 'VARCHAR',
                    'constraint' => 3,
                    'null' => true,
                ],
                'AFNAM' => [
                    'type' => 'VARCHAR',
                    'constraint' => 12,
                    'null' => true,
                ],
                'LOEKZ' => [
                    'type' => 'VARCHAR',
                    'constraint' => 1,
                    'null' => true,
                ],
                'FULL_RELEASE' => [
                    'type' => 'VARCHAR',
                    'constraint' => 1,
                    'null' => true,
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
                'LT_PR' => [
                    'type' => 'VARCHAR',
                    'constraint' => 5,
                    'null' => true,
                ],
                'LT_PRPO' => [
                    'type' => 'VARCHAR',
                    'constraint' => 5,
                    'null' => true,
                ],
                'FRGST' => [
                    'type' => 'VARCHAR',
                    'constraint' => 2,
                    'null' => true,
                ],
                'EBAKZ' => [
                    'type' => 'VARCHAR',
                    'constraint' => 1,
                    'null' => true,
                ],
                'BLCKD' => [
                    'type' => 'VARCHAR',
                    'constraint' => 1,
                    'null' => true,
                ],
                'PREIS' => [
                    'type' => 'DECIMAL',
                    'constraint' => '16,2',
                    'null' => true,
                ],
                'RLWRT' => [
                    'type' => 'DECIMAL',
                    'constraint' => '16,2',
                    'null' => true,
                ],
                'KNTTP' => [
                    'type' => 'VARCHAR',
                    'constraint' => 1,
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('t_pr', true);
    }

    public function down()
    {
        $this->forge->dropTable('t_pr', true);
    }
}
