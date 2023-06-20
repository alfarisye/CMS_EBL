<?php

namespace App\Models\Sales;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class SalesLaytimeItem extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'T_SAL_LAYTIME_ITEM';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['item','laytime_id','wheater','date','day','from','to','code','description','rate','laytime_to_count','laytime_not_count','laytime_used_hour','laytime_used_days','balance_days','created_by','created_at','updated_by','updated_at','deleted_at','deleted_by'];

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
