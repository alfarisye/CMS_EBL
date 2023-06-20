<?php

namespace App\Models\Sales;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class ContractOrder extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'T_SAL_CONTRACT_ORDER';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["id","contract_no",'date',"category","type","customer_code","customer_name","address","product","product_name","quantity","uom","contract_price","currency","delivery_condition","price_condition","quality_parameter","parameter","top","status",'created_by','created_at','updated_at','updated_by','wbs_element','approved_by1','approved_date1','approved_by2','approved_date2','approved_by3','approved_date3','approved_by4','approved_date4','approved_by5','approved_date5','approved_by6','approved_date6','contract_date','pdf','pdf2','valid_until'];

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
