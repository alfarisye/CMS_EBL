<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MDAnnualBudget extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_annualbudget' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'year' => [
                'type' => 'INT',
                'constraint' => 4,
            ],
            'project' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'cg_annualbudget_qt' => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            'ob_annualbudget_qt' => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            'create_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_update' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'revision' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            // 'status' => [
            //     'type' => 'INT',
            //     'constraint' => 1,
            //     'default' => 1,
            // ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],
        ]);
        $this->forge->addKey('id_annualbudget', true);
        $this->forge->addUniqueKey([
            'year',
            'project',
        ]);
        $this->forge->createTable('md_annualbudget');
    }

    public function down()
    {
        $this->forge->dropTable('md_annualbudget');
    }
}
