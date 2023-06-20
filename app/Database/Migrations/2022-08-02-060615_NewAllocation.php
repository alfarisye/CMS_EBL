<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class NewAllocation extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id_allo" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "allocation" => [
                'type' => 'VARCHAR',
                'constraint'=> '50'
            ],
            'upload_file_path' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => true,
            ],
            'upload_file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'upload_file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            "create_by" => [
                "type" => "VARCHAR",
                "constraint" => 30,
            ],
            "create_on" => [
                "type" => "DATE",
                "null" => true,
            ],
            "change_by" => [
                "type" => "VARCHAR",
                "constraint" => 30,
            ],
            "change_on" => [
                "type" => "DATE",
                "null" => true,
            ],
            ]);
            $this->forge->addKey("id_allo", true);
            $this->forge->createTable("new_allocation");  
    }

    public function down()
    {
        $this->forge->dropTable("new_allocation");
    }
}
