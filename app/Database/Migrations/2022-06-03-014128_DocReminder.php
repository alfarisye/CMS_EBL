<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DocReminder extends Migration
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
            "code" => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "unique" => true,
            ],
            "doc_no" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
            "group_email_id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "email_type" => [
                "type" => "enum('to', 'cc')",
            ],
            "doc_desc" => [
                "type" => "VARCHAR",
                "constraint" => 150,
            ],
            "due_date" => [
                "type" => "DATE",
            ],
            "remind_on" => [
                "type" => "DATE",
            ],
            "email_status" => [
                "type" => "enum('delivered', 'undelivered')",
                'default' => 'undelivered',
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
            "created_by" => [
                "type" => "varchar",
                "constraint" => 50,
            ],
            "created_on" => [
                "type" => "DATETIME",
            ],
            "updated_by" => [
                "type" => "varchar",
                "constraint" => 50,
            ],
            "updated_on" => [
                "type" => "DATETIME",
            ],
            "deletion_status" => [
                "type" => "enum('0','1')",
                "default" => "0",
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('group_email_id', 'group_email', 'group_id', 'NULL', 'NULL');
        $this->forge->createTable('doc_reminder');
    }

    public function down()
    {
        $this->forge->dropTable('doc_reminder');
    }
}
