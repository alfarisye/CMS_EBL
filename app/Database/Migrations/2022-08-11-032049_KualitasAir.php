<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class KualitasAir extends Migration
{
    public function up()
    {
        $this->forge->addField([
                "id_KualitasAir" => [
                    "type" => "INT",
                    "constraint" => 6,
                    "unsigned" => true,
                    "auto_increment" => true,
                ],
                "id_BML" => [
                    'type' => 'INT',
                    'constraint' => '2',
                    "unsigned" => true,
                    "null" => true,
                ],
                "id_Parameter" => [
                    'type' => 'INT',
                    'constraint' => '2',
                    "unsigned" => true,
                    "null" => true,
                ],
                "location" => [
                    "type" => "VARCHAR",
                    'constraint' => "50",
                ],
                'date' => [
                    "type" => "DATE",
                    'null' => true,
                ],
                "debit_air" => [
                    'type' => 'Float',
                    'constraint' => '10',                    
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
        $this->forge->addKey('id_KualitasAir', true);
        $this->forge->addForeignKey("id_BML", "T_BML", "id_BML" );
        $this->forge->addForeignKey("id_Parameter", "T_Parameter", "id_Parameter" );
        $this->forge->createTable('T_KualitasAir');
    }

    public function down()
    {
        $this->forge->dropTable('T_KualitasAir');
    }
}
