<?php

namespace App\Models\Sales;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class SalesShipment extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'T_SAL_SHIPMENT';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["shipment_id","contract_no",'contract_id','category',"type","bl_date","ETA_date",'file_laycan','TBBG','laycan_date_end',"vessel","gi_qty","receipt_date","gr_qty","uom","deletion","status","type_supply",'laycan_date','bl_qty','discharging_date','file','customer_name','discharging_qty',"created_by","updated_by"];

    // Dates
    protected $useTimestamps = true;
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
    protected $beforeUpdate = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
