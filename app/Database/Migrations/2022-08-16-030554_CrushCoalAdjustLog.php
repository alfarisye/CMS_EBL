<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrushCoalAdjustLog extends Migration
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
                'id_adjustment' => [
                    "type" => "INT",
                    "constraint" => 11,
                    "unsigned" => true,
                ],
                "code" => [
                    "type" => "VARCHAR",
                    'constraint' => '50',
                    'null' => true,
                ],
                'doc_date' => [
                    'type' => 'date',
                    'null' => true,
                ],
                'post_date_start' => [
                    'type' => 'date',
                    'null' => true,
                ],
                'post_date_end' => [
                    'type' => 'date',
                    'null' => true,
                ],
                'cc_adjustment' => [
                    'type' => 'decimal',
                    'constraint' => '15,2',
                    'null' => true,
                ],
                'notes' => [
                    'type' => 'varchar',
                    'constraint' => '255',
                    'null' => true,
                ],
                'status' => [
                    "type" => "enum",
                    "constraint" => ["draft", "posted"],
                    'default' => 'draft',
                    'null' => true,
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
        $this->forge->createTable('log_t_adjcrushcoal', true);
    }

    public function down()
    {
        $this->forge->dropTable('log_t_adjcrushcoal');
    }
}
