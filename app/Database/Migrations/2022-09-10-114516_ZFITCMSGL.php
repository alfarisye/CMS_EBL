<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ZFITCMSGL extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'id_zfit' => [
                    "type" => "INT",
                    "constraint" => 11,
                    "unsigned" => true,
                    "auto_increment" => true,
                ],
                'COMP' => [
                    'type' => 'CHAR',
                    'constraint' => 4,
                ],
                'GL' => [
                    'type' => 'CHAR',
                    'constraint' => 10,
                ],
                'ID' => [
                    'type' => 'CHAR',
                    'constraint' => 25,
                ],
            ]
        );
        $this->forge->addKey('id_zfit', true);
        $this->forge->createTable('ZFIT_CMSGL', true);
    }

    public function down()
    {
        $this->forge->dropTable('ZFIT_CMSGL', true);
    }
}
