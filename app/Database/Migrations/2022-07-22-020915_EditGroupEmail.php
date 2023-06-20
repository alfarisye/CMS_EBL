<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditGroupEmail extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type' => 'INT',
                'constraint' => 1,
                'default' => 1,
            ],
        ];
        $this->forge->addColumn("group_email", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('group_email', 'status');
    }
}
