<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditDocReminder extends Migration
{
    public function up()
    {
        $fields = [
            'group_email_cc' => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                'after' => 'group_email_id',
                'null' => true,
            ],
            // 'CONSTRAINT group_email_cc FOREIGN KEY(group_email_cc) REFERENCES group_email(group_id)'
        ];
        $this->forge->addColumn("doc_reminder", $fields);
        $this->forge->modifyColumn("doc_reminder", [
            'email_type' => [
                "type" => "enum('to', 'cc')",
                "null" => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropForeignKey('doc_reminder', 'group_email_cc');
        $this->forge->dropColumn('doc_reminder', 'group_email_cc');
        $this->forge->modifyColumn("doc_reminder", [
            'email_type' => [
                "type" => "enum('to', 'cc')",
            ],
        ]);
    }
}
