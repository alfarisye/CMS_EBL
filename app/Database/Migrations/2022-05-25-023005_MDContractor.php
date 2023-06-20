<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MDContractor extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'contractor_name' => [
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                ],
                'start_date' => [
                    'type' => 'DATETIME',
                ],
                'end_date' => [
                    'type' => 'DATETIME',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'status' => [
                    'type' => 'INT',
                    'constraint' => 1,
                    'default' => 1,
                ],
            ]
        );
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('contractor_name');
        $this->forge->createTable('md_contractors');
    }

    public function down()
    {
        $this->forge->dropTable('md_contractors');
    }
}
