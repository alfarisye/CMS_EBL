<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class tambahform extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id_tambahform" => [
                "type" => "INT",
                "constraint" => 6,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "id_JamKerja" => [
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
                'type' => 'Float',
                'constraint' => '10',
            ],
        ]);
    $this->forge->addKey('id_tambahform', true);
    $this->forge->addForeignKey("id_JamKerja", "T_jamkerja", "id_JamKerja" );
    $this->forge->addForeignKey("id_form", "T_Form", "id_form");
    $this->forge->createTable('t_tambahform');
}

public function down()
{
    $this->forge->dropTable('t_tambahform');
}
}