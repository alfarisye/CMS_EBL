<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ManJamKerja extends Migration
{
    public function up()
    {
        $this->forge->addField([
                "id_JamKerja" => [
                    'type' => 'INT',
                    'constraint' => '6',
                    "unsigned" => true,
                    "auto_increment" => true,
                ],
                "id_form" => [
                    'type' => 'INT',
                    'constraint' => '2',
                    "unsigned" => true,
                    "null" => true,
                ],
                "id_stockholder" => [
                    'type' => 'INT',
                    'constraint' => '2',
                    "unsigned" => true,
                    "null" => true,
                ],
                "month_jamkerja" => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                "year_jamkerja" => [
                    'type' => 'DATE',
                    'null' => true,
                ],
                "data_jamkerja" => [
                    'type' => 'INT',
                    'constraint' => '6',
                    'null' => true,
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
        $this->forge->addKey('id_JamKerja', true);
        $this->forge->addForeignKey("id_form", "T_Form", "id_form" );
        $this->forge->addForeignKey("id_stockholder", "T_stockholder", "id_stockholder" );
        $this->forge->createTable('T_jamkerja');
    }

    public function down()
    {
        $this->forge->dropTable('T_jamkerja');
    }
}
