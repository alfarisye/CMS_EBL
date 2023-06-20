<?php

namespace App\Models\DMO;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class SalesDmo extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'T_SAL_DMO';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["qty1",'qty2',"qty3","qty4","qty5","qty6","qty7","qty8","qty9","qty10","percent1",'percent2',"percent3","percent4","percent5","percent6","percent7","percent8","percent9","percent10",'contract_id',"contract_no","customer_name","contract_qty","created_by","created_at","updated_by","updated_at","deleted_by","deleted_at"];

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
