<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FiMdGl extends Migration
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
                'SAKNR' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                ],
                'FDLEV' => [
                    'type' => 'CHAR',
                    'constraint' => 2,
                ],
                'FSTAG' => [
                    'type' => 'CHAR',
                    'constraint' => 4,
                    'null' => true,
                ],
                'MITKZ' => [
                    'type' => 'CHAR',
                    'constraint' => 1,
                    'null' => true,
                ],
                'WAERS' => [
                    'type' => 'CHAR',
                    'constraint' => 5,
                    'null' => true,
                ],
                'XINTB' => [
                    'type' => 'CHAR',
                    'constraint' => 3,
                    'null' => true,
                ],
                'XOPVW' => [
                    'type' => 'CHAR',
                    'constraint' => 1,
                    'null' => true,
                ],
                'XBILK' => [
                    'type' => 'CHAR',
                    'constraint' => 1,
                    'null' => true,
                ],
                'TXT50' => [
                    'type' => 'CHAR',
                    'constraint' => 50,
                    'null' => true,
                ],
            ]
        );
        $this->forge->addKey('id', true);
        $this->forge->createTable('FI_MD_GL', true);
    }

    public function down()
    {
        $this->forge->dropTable('FI_MD_GL', true);
    }
}
