<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BML extends Migration
{
    public function up()
    {
        $this->forge->addField([
                "id_BML" => [
                    'type' => 'INT',
                    'constraint' => '2',
                    "unsigned" => true,
                    "auto_increment" => true,
                ],
                "nama_budget" => [
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                ],
                "budget" => [
                    "type" => "INT",
                    'constraint' => "5",
                ],
                "create_by" => [
                    "type" => "VARCHAR",
                    "constraint" => 20,
                ],
                "create_on" => [
                    "type" => "DATE",
                    "null" => true,
                ],
                "change_by" => [
                    "type" => "VARCHAR",
                    "constraint" => 20,
                ],
                "change_on" => [
                    "type" => "DATE",
                    "null" => true,
                ],
                "Deletion_status" => [
                    'type' => 'VARCHAR',
                    'constraint' => '1',
                    'default'    => 0
                ],
            ]);
        $this->forge->addKey('id_BML', true);
        $this->forge->createTable('T_BML');
    }

    public function down()
    {
        $this->forge->dropTable('T_BML');
    }
}
