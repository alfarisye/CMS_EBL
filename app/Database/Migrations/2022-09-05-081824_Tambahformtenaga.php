<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Tambahformtenaga extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id_tambahtenaga" => [
                "type" => "INT",
                "constraint" => 6,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "id_tenagakerja" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true,
            ],
            "id_form" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true,
            ],
            "value" => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
        ]);
    $this->forge->addKey('id_tambahtenaga', true);
    $this->forge->addForeignKey("id_tenagakerja", "T_tenagakerja", "id_tenagakerja" );
    $this->forge->addForeignKey("id_form", "T_Form", "id_form");
    $this->forge->createTable('t_tambahtenaga');
}

public function down()
{
    $this->forge->dropTable('t_tambahtenaga');
}
}