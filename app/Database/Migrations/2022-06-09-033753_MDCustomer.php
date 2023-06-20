<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MDCustomer extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                "type" => "INT",
                "constraint" => 11,
                "unsigned" => true,
                "auto_increment" => true,
            ],
            'MANDT' => [
                'type' => 'VARCHAR',
                "constraint" => 3,
            ],
            'KUNNR' => [
                'type' => 'VARCHAR',
                "constraint" => 10,
            ],
            'LAND1' => [
                'type' => 'VARCHAR',
                "constraint" => 3,
            ],
            'NAME1' => [
                'type' => 'VARCHAR',
                "constraint" => 35,
            ],
            'NAME2' => [
                'type' => 'VARCHAR',
                "constraint" => 35,
            ],
            'ORT01' => [
                'type' => 'VARCHAR',
                "constraint" => 35,
            ],
            'PSTLZ' => [
                'type' => 'VARCHAR',
                "constraint" => 10,
            ],
            'REGIO' => [
                'type' => 'VARCHAR',
                "constraint" => 3,
            ],
            'SORTL' => [
                'type' => 'VARCHAR',
                "constraint" => 10,
            ],
            'STRAS' => [
                'type' => 'VARCHAR',
                "constraint" => 35,
            ],
            'TELF1' => [
                'type' => 'VARCHAR',
                "constraint" => 16,
            ],
            'TELFX' => [
                'type' => 'VARCHAR',
                "constraint" => 31,
            ],
            'ADRNR' => [
                'type' => 'VARCHAR',
                "constraint" => 10,
            ],
            'ANRED' => [
                'type' => 'VARCHAR',
                "constraint" => 15,
            ],
            'KTOKD' => [
                'type' => 'VARCHAR',
                "constraint" => 4,
            ],
            'ORT02' => [
                'type' => 'VARCHAR',
                "constraint" => 35,
            ],
            'STCEG' => [
                'type' => 'VARCHAR',
                "constraint" => 20,
            ],
            'BUKRS' => [
                'type' => 'VARCHAR',
                "constraint" => 4,
            ],
            'ZUAWA' => [
                'type' => 'VARCHAR',
                "constraint" => 3,
            ],
            'AKONT' => [
                'type' => 'VARCHAR',
                "constraint" => 10,
            ],
            'ZTERM' => [
                'type' => 'VARCHAR',
                "constraint" => 4,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('T_MDCUSTOMER');
    }

    public function down()
    {
        $this->forge->dropTable('T_MDCUSTOMER');
    }
}
