<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class GroupEmail extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "group_id" => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            "group_name" => [
                "type" => "VARCHAR",
                "constraint" => 50,
            ],
        ]);
        $this->forge->addKey('group_id', true);
        $this->forge->createTable('group_email');
    }

    public function down()
    {
        $this->forge->dropTable('group_email');
    }
}
