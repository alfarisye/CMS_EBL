<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FiSalesInv extends Migration
{
    public function up()
    {
        $this->forge->addField([
            "id" => [
                "type" => "INT",
                "constraint" => 10,
                "auto_increment" => true,
            ],
            "ATTCH" => [
                "type" => "VARCHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "AUFNR" => [
                "type" => "CHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "BELNR" => [
                "type" => "CHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "BLDAT" => [
                "type" => "DATE",
                
                "null" => true,
            ],
            "BUDAT" => [
                "type" => "DATE",
                
                "null" => true,
            ],
            "CONTRACT_NO" => [
                "type" => "CHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "CPUDT" => [
                "type" => "DATE",
                
                "null" => true,
            ],
            "created_by" => [
                "type" => "VARCHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "deleted_by" => [
                "type" => "VARCHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "DOC_NO" => [
                "type" => "CHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "FNL_AMNT" => [
                "type" => "DECIMAL",
                "contraint" =>"12,2",
                "null" => true,
            ],
            "FNL_PRICE" => [
                "type" => "DECIMAL",
                "contraint" =>"12,2",
                "null" => true,
            ],
            "FNL_QTY" => [
                "type" => "DECIMAL",
                "contraint" =>"12,2",
                "null" => true,
            ],
            "FNL_QTY1" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "FNL_QTY10" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "FNL_QTY2" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "FNL_QTY3" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "FNL_QTY4" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "FNL_QTY5" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "FNL_QTY6" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "FNL_QTY7" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "FNL_QTY8" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "FNL_QTY9" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "KUNNR" => [
                "type" => "CHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "MESSAGE_SAP" => [
                "type" => "CHAR",
                "contraint" =>"200",
                "null" => true,
            ],
            "PPH_22" => [
                "type" => "DECIMAL",
                "contraint" =>"16",
                "null" => true,
            ],
            "PPN" => [
                "type" => "VARCHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "PPN_AMNT" => [
                "type" => "INT",
                "contraint" =>"10",
                "null" => true,
            ],
            "PRCTR" => [
                "type" => "CHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "PROJK" => [
                "type" => "CHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "SAL_DISC" => [
                "type" => "INT",
                "contraint" =>"10",
                "null" => true,
            ],
            "SGTXT" => [
                "type" => "CHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "SHIPMENT_ID" => [
                "type" => "CHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID1" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID10" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID2" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID3" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID4" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID5" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID6" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID7" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID8" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_ID9" => [
                "type" => "VARCHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "SHIPMENT_TYPE" => [
                "type" => "CHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "STATUS_SAP" => [
                "type" => "CHAR",
                "contraint" =>"1",
                "null" => true,
            ],
            "STBLG" => [
                "type" => "CHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "STJAH" => [
                "type" => "INT",
                "contraint" =>"10",
                "null" => true,
            ],
            "TCURR" => [
                "type" => "CHAR",
                "contraint" =>"50",
                "null" => true,
            ],
            "UKURS" => [
                "type" => "DECIMAL",
                "contraint" =>"12,2",
                "null" => true,
            ],
            "updated_by" => [
                "type" => "VARCHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "USNAM" => [
                "type" => "CHAR",
                "contraint" =>"255",
                "null" => true,
            ],
            "XBLNR" => [
                "type" => "CHAR",
                "contraint" =>"20",
                "null" => true,
            ],
            "ZFBDT" => [
                "type" => "DATE",
                
                "null" => true,
            ],
            "ZTERM" => [
                "type" => "CHAR",
                "contraint" =>"4",
                "null" => true,
            ],
           
        ]);
        $this->forge->addKey("id", true);
        
        $this->forge->createTable("FI_SALES_INV");
    }

    public function down()
    {
        $this->forge->dropTable('FI_SALES_INV');
    }
}

