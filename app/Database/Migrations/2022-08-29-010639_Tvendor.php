<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tvendor extends Migration
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
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                ],
                'BUKRS' => [
                    'type' => 'VARCHAR',
                    'constraint' => 4,
                ],
                'NAME1' => [
                    'type' => 'VARCHAR',
                    'constraint' => 35,
                    'null' => true,
                ],
                'ORT01' => [
                    'type' => 'VARCHAR',
                    'constraint' => 35,
                    'null' => true,
                ],
                'AKTIF' => [
                    'type' => 'VARCHAR',
                    'constraint' => 1,
                    'null' => true,
                ],
            ],
        );
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_vendor', true);
    }

    public function down()
    {
        $this->forge->dropTable('t_vendor', true);
    }
}
