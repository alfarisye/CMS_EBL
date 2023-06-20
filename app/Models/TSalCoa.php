<?php
namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class TSalCoa extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'T_SAL_COA';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["Contract1","Contract2","Contract3","Contract4","Contract5","Contract6","Contract7","Contract8","Contract9","Contract10","preliminary1","preliminary2","preliminary3","preliminary4","preliminary5","preliminary6","preliminary7","preliminary8","preliminary9","preliminary10","final1","final2","final3","final4","final5","final6","final7","final8","final9","final10","selisih1","selisih2","selisih3","selisih4","selisih5","selisih6","selisih7","selisih8","selisih9","selisih10","created_by","updated_by","date_report",'shipment_id',"contract_no","vessel","quantity","source_cargo","destination","date_sampling","customer_name","loading_port","standard"];

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
