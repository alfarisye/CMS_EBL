<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CSRActivity extends Migration
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
                "doc_no" => [
                    'type' => 'VARCHAR',
                    'constraint' => '9',
                ],
                'date' => [
                    "type" => "DATE",
                    'null' => true,
                ],
                "allocation" => [
                    'type' => 'VARCHAR',
                    'constraint' => '150',                    
                ],
                "location" => [
                    'type' => 'VARCHAR',
                    'constraint' => '50',                    
                ],
                "activity" => [
                    'type' => 'VARCHAR',
                    'constraint' => '250',                   
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
                "actual_cost" =>[
                    'type' => 'INT',
                    'constraint' => '12'
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
                "Deletion_status" => [
                    'type' => 'VARCHAR',
                    'constraint' => '1',
                    'default'    => 0
                ],
            ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_csractivity');
    }

    public function down()
    {
        $this->forge->dropTable('t_csractivity');
    }
}
