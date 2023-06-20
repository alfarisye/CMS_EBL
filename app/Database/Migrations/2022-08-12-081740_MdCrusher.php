<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MdCrusher extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "crusher_code" => [
                'type' => 'VARCHAR',
                'constraint' => 5,
            ],
            "crusher_description" => [
                'type' => 'VARCHAR',
                'constraint' => 30,
            ],
            'company_code' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
            ],
            'UPDT' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'USR' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'stat' => [
                'type' => 'INT',
                'constraint' => 1,
                'default' => 1,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('md_crusher', true);
    }

    public function down()
    {
        $this->forge->dropTable('md_crusher', true);
    }
}
