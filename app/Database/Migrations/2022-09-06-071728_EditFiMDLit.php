<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditFiMDLit extends Migration
{
    public function up()
    {
        $fields = [
            'LANGT' => [
                'type' => 'CHAR',
                'constraint' => 50,
            ],
        ];
        $this->forge->addColumn("FI_MD_LIT", $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('FI_MD_LIT', 'LANGT');
    }
}
