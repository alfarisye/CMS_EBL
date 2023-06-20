<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrushCoalLog extends Migration
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
            "crushcoal_id" => [
                "type" => "VARCHAR",
                'constraint' => '50',
                'null' => true,
            ],
            "production_date" => [
                'type' => 'DATE',
                'null' => true,
            ],
            "id_contractor" => [
                "type" => "INT",
                "constraint" => 11,
                "null" => true,
                "unsigned" => true,
            ],
            "rc_qty" => [
                'type' => 'decimal',
                'constraint' => '15,2',
                'null' => true,
            ],
            "id_crusher" => [
                "type" => "INT",
                "constraint" => 11,
                "null" => true,
                "unsigned" => true,
            ],
            "cc_qty" => [
                'type' => 'decimal',
                'constraint' => '15,2',
                'null' => true,
            ],
            'status' => [
                "type" => "enum",
                "constraint" => ["draft", "submitted", "verified", "approved"],
                'default' => 'draft',
                'null' => true,
            ],
            'deletion_status' => [
                'type' => 'INT',
                'constraint' => 1,
                'default' => 0,
                'null' => true,
            ],
            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => true,
            ],
            'created_on' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'changed_by' => [
                'type' => 'VARCHAR',
                'constraint' => 25,
                'null' => true,
            ],
            'changed_on' => [
                'type' => 'DATETIME',
                'null' => true,
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
            "action" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('log_t_crushcoal', true);
    }

    public function down()
    {
        $this->forge->dropTable('log_t_crushcoal', true);
    }
}
