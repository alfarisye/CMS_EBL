<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrushCoalAdjust extends Migration
{
    public function up()
    {
        $this->forge->addField(
            [
                'id' => [
                    "type" => "INT",
                    "constraint" => 11,
                    "unsigned" => true,
                    "auto_increment" => true,
                ],
                "code" => [
                    "type" => "VARCHAR",
                    'constraint' => '50',
                    "unique" => true,
                ],
                'doc_date' => [
                    'type' => 'date',
                    'null' => true,
                ],
                'post_date_start' => [
                    'type' => 'date'
                ],
                'post_date_end' => [
                    'type' => 'date'
                ],
                'cc_adjustment' => [
                    'type' => 'decimal',
                    'constraint' => '15,2',
                ],
                'notes' => [
                    'type' => 'varchar',
                    'constraint' => '255'
                ],
                'status' => [
                    "type" => "enum",
                    "constraint" => ["draft", "posted"],
                    'default' => 'draft'
                ],
                'deletion_status' => [
                    'type' => 'INT',
                    'constraint' => 1,
                    'default' => 0,
                ],
                'created_by' => [
                    'type' => 'VARCHAR',
                    'constraint' => 25,
                ],
                'created_on' => [
                    'type' => 'DATETIME',
                ],
                'changed_by' => [
                    'type' => 'VARCHAR',
                    'constraint' => 25
                ],
                'changed_on' => [
                    'type' => 'DATETIME',
                ],
                'MBLNR' => [
                    'type' => 'VARCHAR',
                    'null' => true,
                    'constraint' => 255,
                ],
                'MJAHR' => [
                    'type' => 'VARCHAR',
                    'null' => true,
                    'constraint' => 255,
                ],
                'ZEILE' => [
                    'type' => 'VARCHAR',
                    'null' => true,
                    'constraint' => 255,
                ],
            ]
        );
        $this->forge->addKey('id', true);
        $this->forge->createTable('t_adjcrushcoal', true);
    }

    public function down()
    {
        $this->forge->dropTable('t_adjcrushcoal');
    }
}
