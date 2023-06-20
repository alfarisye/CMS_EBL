<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CrushCoal extends Migration
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
            "production_code" => [
                "type" => "VARCHAR",
                'constraint' => '50',
                "unique" => true,
            ],
            "production_date" => [
                'type' => 'DATE',
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
            ],
            'status' => [
                "type" => "enum",
                "constraint" => ["draft", "submitted", "verified", "approved"],
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
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey("id_crusher", "md_crusher", "id");
        $this->forge->addForeignKey("id_contractor", "md_contractors", "id");
        $this->forge->createTable('t_crushcoal', true);
    }

    public function down()
    {
        $this->forge->dropTable('t_crushcoal', true);
    }
}
