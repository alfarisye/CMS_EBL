<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GroupEmails extends Migration
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
            "group_id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
            ],
            "email" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('group_id', 'group_email', 'group_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('group_emails');
    }

    public function down()
    {
        $this->forge->dropTable('group_emails');
    }
}
