<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TPlant extends Migration
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
                'WERKS' => [
                    'type' => 'VARCHAR',
                    'constraint' => 4,
                ],
                'NAME1' => [
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                    'null' => true,
                ],
                'BUKRS' => [
                    'type' => 'VARCHAR',
                    'constraint' => 4,
                ],
                'BUTXT' => [
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('t_plant', true);
    }

    public function down()
    {
        $this->forge->dropTable('t_plant', true);
    }
}
