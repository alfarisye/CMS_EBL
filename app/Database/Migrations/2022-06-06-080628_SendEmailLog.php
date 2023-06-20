<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SendEmailLog extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'email_address' => [
                'type' => 'varchar',
                'constraint' => 255,
            ],
            'subject' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'doc_reminder_id' => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "status" => [
                "type" => "enum('success','error')",
            ],
            "error_message" => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('doc_reminder_id', 'doc_reminder', 'id');
        $this->forge->createTable('send_email_log');
    }

    public function down()
    {
        $this->forge->dropTable('send_email_log');
    }
}
