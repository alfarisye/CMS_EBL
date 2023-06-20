<?php

namespace App\Models;

use CodeIgniter\Model;

class FiSalesInv extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'FI_SALES_INV';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    // protected $allowedFields    = ["SHIPMENT_ID","CONTRACT_NO","KUNNR","SHIPMENT_TYPE","BLDAT","BUDAT","UKURS","TCURR","ZFBDT","ZTERM","XBLNR","ATTCH","FNL_PRICE","FNL_QTY","FNL_AMNT","SAL_DISC","PPN","PPN_AMNT","PPH_22","PROJK","PRCTR","AUFNR","SGTXT","DOC_NO","BELNR","STBLG","USNAM","CPUDT","created_by","updated_by","deleted_by"];
    protected $allowedFields    = ["ATTCH","AUFNR","BELNR","BLDAT","BUDAT","CONTRACT_NO","CPUDT","created_by","deleted_by","DOC_NO","FNL_AMNT","FNL_PRICE","FNL_QTY","FNL_QTY1","FNL_QTY10","FNL_QTY2","FNL_QTY3","FNL_QTY4","FNL_QTY5","FNL_QTY6","FNL_QTY7","FNL_QTY8","FNL_QTY9","KUNNR","MESSAGE_SAP","PPH_22","PPN","PPN_AMNT","PRCTR","PROJK","SAL_DISC","SGTXT","SHIPMENT_ID","SHIPMENT_ID1","SHIPMENT_ID10","SHIPMENT_ID2","SHIPMENT_ID3","SHIPMENT_ID4","SHIPMENT_ID5","SHIPMENT_ID6","SHIPMENT_ID7","SHIPMENT_ID8","SHIPMENT_ID9","SHIPMENT_TYPE","STATUS_SAP","STBLG","STJAH","TCURR","UKURS","updated_by","USNAM","XBLNR","ZFBDT","ZTERM"];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}