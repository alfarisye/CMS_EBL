<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FiActBal extends Migration
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
                'COMP' => [
                    'type' => 'CHAR',
                    'constraint' => 4,
                ],
                'GL_ACCOUNT' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                ],
                'FISC' => [
                    'type' => 'INT',
                    'constraint' => 4,
                ],
                'FI' => [
                    'type' => 'CHAR',
                    'constraint' => 2,
                    'null' => true,
                ],
                'DEBIT_PER' => [
                    'type' => 'decimal',
                    'constraint' => '23,4',
                ],
                'CREDIT_PER' => [
                    'type' => 'decimal',
                    'constraint' => '23,4',
                ],
                'PER_SALES' => [
                    'type' => 'decimal',
                    'constraint' => '23,4',
                ],
                'BALANCE' => [
                    'type' => 'decimal',
                    'constraint' => '23,4',
                ],
            ]
        );
        $this->forge->addKey('id', true);
        $this->forge->createTable('FI_ACT_BAL', true);
    }

    public function down()
    {
        $this->forge->dropTable('FI_ACT_BAL', true);
    }
}
