<?php

namespace App\Models;

use CodeIgniter\Model;

class MDMonthlyBudgets extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'md_monthlybudget';
    protected $primaryKey       = 'id_monthlybudget';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_contractor','year','month','project','cg_monthlybudget_qt','ob_monthlybudget_qt',
                                'cg_dailybudget_qt','ob_dailybudget_qt','create_date','last_update','revision',
                                'status','id_annualbudget'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'create_date';
    protected $updatedField  = 'last_update';
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
