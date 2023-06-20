<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EditCsr extends Migration
{
    public function up()
    {
        $fieldsbudget = [
            "formtyp_bdg" => [
                "type" => "enum",
                "constraint" => ["BUDGET EKSTERNAL", "BUDGET INTERNAL"],
            ],
        ];

        $fieldsact = [
            "formtyp_act" => [
                "type" => "enum",
                "constraint" => ["ACTIVITY EKSTERNAL", "ACTIVITY INTERNAL", "BOTH"],
            ],
        ];

        $this->forge->addColumn("t_csrbudget", $fieldsbudget);
        $this->forge->addColumn("t_csractivity", $fieldsact);
        
    }

    public function down()
    {
        $this->forge->dropColumn('t_csrbudget', 'formtyp_bdg');
        $this->forge->dropColumn('t_csractivity', 'formtyp_act');
    }
}
