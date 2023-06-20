<?php
namespace App\Models\budget;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class MdMonthlybudgetHp extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'md_monthlybudget_hp';
    protected $primaryKey       = 'id_monthlybudgethp';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["id_monthlybudgethp","id_annualhp","year","month","project","hp_mounthlybudget_qty","_dailybudget_qty","create_date","last_update","revision","status","id_contractor"];

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
