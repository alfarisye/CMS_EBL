<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Parakualitas extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id_parakualitas" => [
                "type" => "INT",
                "constraint" => 6,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "id_KualitasAir" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true,
            ],
            "id_Parameter" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true,
            ],
            "value" => [
                'type' => 'Float',
                'constraint' => '10',
            ],
        ]);
    $this->forge->addKey('id_parakualitas', true);
    $this->forge->addForeignKey("id_KualitasAir", "T_KualitasAir", "id_KualitasAir" );
    $this->forge->addForeignKey("id_Parameter", "T_Parameter", "id_Parameter");
    $this->forge->createTable('t_parakualitas');
}

public function down()
{
    $this->forge->dropTable('t_parakualitas');
}
}