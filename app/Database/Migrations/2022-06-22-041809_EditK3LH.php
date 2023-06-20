<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditK3LH extends Migration
{
    public function up()
    {
        $fields = [
            "Deletion_status" => [
                'type' => 'VARCHAR',
                'constraint' => '1',
                'default'    => 0
            ],
        ];
        $this->forge->modifyColumn("t_k3lh", $fields);
}

    public function down()
    {
        $fields = [
            "Deletion_status" => [
                'type' => 'VARCHAR',
                'constraint' => '1',
            ],
        ];
        $this->forge->modifyColumn("t_k3lh", $fields);
        
    }
}
            
