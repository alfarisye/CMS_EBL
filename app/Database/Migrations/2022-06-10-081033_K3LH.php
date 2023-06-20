<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class K3LH extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "acc_no" => [
                'type' => 'VARCHAR',
                'constraint' => '9',
            ],
            "date" => [
                'type' => 'DATE',
                "null" => true,
            ],
            "Type" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true,
            ],
            "ty_category" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true,
            ],
            "Description" => [
                'type' => 'VARCHAR',
                'constraint' => '150',
            ],
            "Create_by" => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            "Create_on" => [
                "type" => "DATE",
                "null" => true,
            ],
            "Change_by" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "Change_on" => [
                "type" => "DATE",
                "null" => true,
            ],
            "Deletion_status" => [
                'type' => 'VARCHAR',
                'constraint' => '1',
            ],
            "action" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->addForeignKey("ty_category", "t_category", "Id_category" );
        $this->forge->addForeignKey("Type", "t_type", "id_type");
        $this->forge->createTable("t_k3lh");  
    }

    public function down()
    {
        $this->forge->dropTable("t_k3lh");
    }
}
