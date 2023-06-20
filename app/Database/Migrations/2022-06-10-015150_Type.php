<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Type extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id_type" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "type" => [
                'type' => 'VARCHAR',
                'constraint'=> '50'
            ],
            ]);
            $this->forge->addKey("id_type", true);
            $this->forge->createTable("t_type");  
    }
    public function down()
    {
        $this->forge->dropTable("t_type");
    }
}
