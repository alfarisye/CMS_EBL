<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Category extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "Id_category" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "id_type" => [
                'type' => 'INT',
                'constraint' => '2',
                "unsigned" => true
            ],
            "category" => [
                'type' => 'VARCHAR',
                'constraint'=> '50'
            ],
            ]);
            $this->forge->addKey("Id_category", true);
            $this->forge->addForeignKey("id_type", "t_type", "id_type");
            $this->forge->createTable("t_category");  
        }
    
        public function down()
        {
            $this->forge->dropTable("t_category");
        }
    }
