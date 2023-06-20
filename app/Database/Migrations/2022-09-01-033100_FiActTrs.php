<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FiActTrs extends Migration
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
                'BUKRS' => [
                    'type' => 'CHAR',
                    'constraint' => 4,
                ],
                'HKONT' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                ],
                'GJAHR' => [
                    'type' => 'INT',
                    'constraint' => 4,
                ],
                'MONAT' => [
                    'type' => 'CHAR',
                    'constraint' => 2,
                    'null' => true,
                ],
                'BLART' => [
                    'type' => 'CHAR',
                    'constraint' => 2,
                    'null' => true,
                ],
                'BELNR' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                    'null' => true,
                ],
                'BKTXT' => [
                    'type' => 'CHAR',
                    'constraint' => 25,
                    'null' => true,
                ],
                'XBLNR' => [
                    'type' => 'CHAR',
                    'constraint' => 16,
                    'null' => true,
                ],
                'BUZEI' => [
                    'type' => 'INT',
                    'constraint' => 3,
                ],
                'AUGDT' => [
                    'type' => 'date',
                ],
                'AUGBL' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                ],
                'SHKZG' => [
                    'type' => 'CHAR',
                    'constraint' => 1,
                ],
                'MWSKZ' => [
                    'type' => 'CHAR',
                    'constraint' => 2,
                ],
                'DMBTR' => [
                    'type' => 'decimal',
                    'constraint' => '23,4',
                ],
                'WRBTR' => [
                    'type' => 'decimal',
                    'constraint' => '23,4',
                ],
                'ZOUNR' => [
                    'type' => 'CHAR',
                    'constraint' => 18,
                ],
                'SGTXT' => [
                    'type' => 'CHAR',
                    'constraint' => 50,
                ],
                'KOSTL' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                ],
                'PRCTR' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                ],
                'AUFNR' => [
                    'type' => 'CHAR',
                    'constraint' => 12,
                ],
                'KUNNR' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                ],
                'LIFNR' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                ],
                'ZFBDT' => [
                    'type' => 'date',
                ],
                'ZTERM' => [
                    'type' => 'CHAR',
                    'constraint' => 4,
                ],
            ]
        );
        $this->forge->addKey('id', true);
        $this->forge->createTable('FI_ACT_TRS', true);
    }

    public function down()
    {
        $this->forge->dropTable('FI_ACT_TRS', true);
    }
}
