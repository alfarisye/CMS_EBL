<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Editperiod extends Migration
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
                'period_month' => [
                    "type" => "INT",
                    "constraint" => 2,
                    'after' => 'period',
                    'null' => true,
                ],
                'period_year' => [
                    "type" => "INT",
                    "constraint" => 4,
                    'null' => true,
                ],
                "allocation" => [
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    
                ],
                "budget_amount" => [
                    'type' => 'INT',
                    'constraint' => '12',
                    
                ],
                "create_by" => [
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                ],
                "create_on" => [
                    "type" => "DATE",
                    "null" => true,
                ],
                "change_by" => [
                    "type" => "VARCHAR",
                    "constraint" => 50,
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
        $this->forge->createTable('t_csrbudget');
    }

    public function down()
    {
        $this->forge->dropTable('t_csrbudget');
    }
}
                
            
