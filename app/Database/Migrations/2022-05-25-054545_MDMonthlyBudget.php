<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MDMonthlyBudget extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_monthlybudget' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'year' => [
                'type' => 'INT',
                'constraint' => 4,
            ],
            'month' => [
                'type' => 'INT',
                'constraint' => 2,
            ],
            'project' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'cg_monthlybudget_qt' => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            'ob_monthlybudget_qt' => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            'cg_dailybudget_qt' => [
                'type' => 'decimal',
                'constraint' => '15,2',
            ],
            'ob_dailybudget_qt' => [
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
            "id_annualbudget" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "null" => true,
            ],
        ]);
        $this->forge->addKey('id_monthlybudget', true);
        $this->forge->addUniqueKey([
            'year',
            'month',
            'project',
        ]);
        $this->forge->addForeignKey('id_annualbudget', 'md_annualbudget', 'id_annualbudget');
        $this->forge->createTable('md_monthlybudget');
    }

    public function down()
    {
        $this->forge->dropTable('md_monthlybudget', true);
    }
}
