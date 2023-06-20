<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Parameter extends Migration
{
    public function up()
    {
        $this->forge->addField([
                "id_Parameter" => [
                    'type' => 'INT',
                    'constraint' => '2',
                    "unsigned" => true,
                    "auto_increment" => true,
                ],
                "nama_parameter" => [
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                ],
                "status" => [
                    "type" => "VARCHAR",
                    'constraint' => "1",
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
        $this->forge->addKey('id_Parameter', true);
        $this->forge->createTable('T_Parameter');
    }

    public function down()
    {
        $this->forge->dropTable('T_Parameter');
    }
}
