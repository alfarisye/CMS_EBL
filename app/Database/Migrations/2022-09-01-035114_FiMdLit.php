<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FiMdLit extends Migration
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
                'SPRAS' => [
                    'type' => 'CHAR',
                    'constraint' => 1,
                ],
                'KURZT' => [
                    'type' => 'CHAR',
                    'constraint' => 4,
                ],
            ]
        );
        $this->forge->addKey('id', true);
        $this->forge->createTable('FI_MD_LIT', true);
    }

    public function down()
    {
        $this->forge->dropTable('FI_MD_LIT', true);
    }
}
