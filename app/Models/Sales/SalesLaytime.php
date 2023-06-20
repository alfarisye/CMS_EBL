<?php

namespace App\Models\Sales;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class SalesLaytime extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'T_SAL_LAYTIME';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['type','shipment_no','contract_no','agreed_laycan','vessel_laytime','vessel_name','vessel_arrived_date','vessel_arrived_time','nor_tendered_date','nor_tendered_time','nor_retendered_date','nor_retendered_time','remarks','loading_commence_date','loading_commence_time','loading_completed_date','loading_completed_time','cargo_qty','cargo_uom','loading_rate_qty','loading_rate_oum','discharging_port','demmurage','dispatch','curr','laytime_allow_hour','laytime_allow_days','days_demmurage','value_demmurage','days_dispatch','status','value_dispatch','file','created_by','updated_by'];

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
