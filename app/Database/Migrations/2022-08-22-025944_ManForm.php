<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ManForm extends Migration
{
    public function up()
    {
        $this->forge->addField([
                "id_form" => [
                    'type' => 'INT',
                    'constraint' => '2',
                    "unsigned" => true,
                    "auto_increment" => true,
                ],
                "nama_form" => [
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                ],
                "status" => [
                    "type" => "INT",
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
        $this->forge->addKey('id_form', true);
        $this->forge->createTable('T_Form');
    }

    public function down()
    {
        $this->forge->dropTable('T_Form');
    }
}
