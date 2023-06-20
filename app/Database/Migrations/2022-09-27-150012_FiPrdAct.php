<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FiPrdAct extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'id' => [
                    "type" => "INT",
                    "constraint" => 3,
                    "unsigned" => true,
                    "auto_increment" => true,
                ],
                'ACTVY' => [
                    'type' => 'CHAR',
                    'constraint' => 30,
                ],
            ]
        );
        $this->forge->addKey('id', true);
        $this->forge->createTable('FI_PRD_ACT', true);
    }

    public function down()
    {
        $this->forge->dropTable('FI_PRD_ACT', true);
    }
}
