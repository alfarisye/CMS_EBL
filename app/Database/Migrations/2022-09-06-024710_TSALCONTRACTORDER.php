<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TSALCONTRACTORDER extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "CHAR",
                "constraint" => 7,
            ],
            "contract_no" => [
                "type" => "CHAR",
                "constraint" => 30,
                "null" => true,
            ],
            "date" => [
                "type" => "DATE",
                "null" => true,
            ],
            "category" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "type" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "customer_code" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "customer_name" => [
                "type" => "CHAR",
                "constraint" => 40,
                "null" => true,
            ],
            "address" => [
                "type" => "CHAR",
                "constraint" => 255,
                "null" => true,
            ],
            "address" => [
                "type" => "CHAR",
                "constraint" => 120,
                "null" => true,
            ],
            "product" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "product_name" => [
                "type" => "CHAR",
                "constraint" => 40,
                "null" => true,
            ],
            "quantity" => [
                "type" => "decimal",
                'constraint' => '15,2',
            ],
            "uom" => [
                "type" => "CHAR",
                "constraint" => 3,
                "null" => true,
            ],
            "contract_price" => [
                "type" => "decimal",
                'constraint' => '15,2',
            ],
            "currency" => [
                "type" => "CHAR",
                "constraint" => 50,
                "null" => true,
            ],
            "delivery_condition" => [
                "type" => "CHAR",
                "constraint" => 130,
                "null" => true,
            ],
            "price_condition" => [
                "type" => "CHAR",
                "constraint" => 150,
                "null" => true,
            ],
            "quality_parameter" => [
                "type" => "CHAR",
                "constraint" => 150,
                "null" => true,
            ],
            "parameter" => [
                "type" => "CHAR",
                "constraint" => 150,
                "null" => true,
            ],
            // 
            
            "top" => [
                "type" => "CHAR",
                "constraint" => 150,
                "null" => true,
            ],
            
            "status" => [
                "type" => "CHAR",
                "constraint" => 2,
                "null" => true,
            ],
            
            "created_by" => [
                "type" => "CHAR",
                "constraint" => 255,
                "null" => true,
            ],
            
            "created_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],

            "updated_by" => [
                "type" => "CHAR",
                "constraint" => 255,
                "null" => true,
            ],
            
            "updated_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            "deleted_at" => [
                "type" => "DATETIME",
                "null" => true,
            ],
            
            "wbs_element" => [
                "type" => "CHAR",
                "constraint" => 40,
                "null" => true,
            ],
            
            "approved_by1" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "approved_date1" => [
                "type" => "DATE",
                "null" => true,
            ],
            "approved_by2" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "approved_date2" => [
                "type" => "DATE",
                "null" => true,
            ],
            "approved_by3" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "approved_date3" => [
                "type" => "DATE",
                "null" => true,
            ],
            "approved_by4" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "approved_date4" => [
                "type" => "DATE",
                "null" => true,
            ],
            "approved_by5" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "approved_date5" => [
                "type" => "DATE",
                "null" => true,
            ],
            "approved_by6" => [
                "type" => "CHAR",
                "constraint" => 20,
                "null" => true,
            ],
            "approved_date6" => [
                "type" => "DATE",
                "null" => true,
            ],
            "contract_date" => [
                "type" => "DATE",
                "null" => true,
            ],
        ]);
        $this->forge->addKey("id", true);
        $this->forge->createTable("T_SAL_CONTRACT_ORDER");
    }

    public function down()
    {
        $this->forge->dropTable('T_SAL_CONTRACT_ORDER');
        //
    }
}
