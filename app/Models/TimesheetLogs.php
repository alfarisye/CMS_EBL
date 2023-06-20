<?php

namespace App\Models;

use CodeIgniter\Model;

class TimesheetLogs extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'timesheet_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ["prd_cg_day_qty", "prd_cg_night_qty", "prd_cg_total", 
        "prd_ob_day_qty", "prd_ob_night_qty", "prd_ob_total", "prd_sr", "prd_rain", "prd_slip", "prd_%",
        "prd_rainfall", "noted", "prd_revision", "status", "id_timesheet", "created_at", "actor", "prd_code",
        "action", "changes", "prd_cg_distance", "prd_ob_distance"
    ];

    // Dates
    protected $useTimestamps = false;
    // protected $dateFormat    = 'datetime';
    // protected $createdField  = 'created_at';
    // protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

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
