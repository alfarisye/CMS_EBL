<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class TimesheetAdjustments extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'timesheet_adjustments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["start_date", "end_date", "cg_adjustment", 
        "ob_adjustment", "status", "created_at", "updated_at", "deleted_at", "created_by", "updated_by",
        "code", "notes", "adj_ob_distance", "adj_cg_distance", "id_contractor"
    ];

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
    protected $afterInsert    = ["add_log"];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = ["add_log"];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = ["log_delete"];

    public function add_log(array $datas) {
        $data = $datas['data'];
        $TimesheetAdjustmentLogs = new \App\Models\TimesheetAdjustmentLogs();
        $TimesheetAdjustmentLogs->insert([
            "created_at" => Time::now(),
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'cg_adjustment' => $data['cg_adjustment'],
            'ob_adjustment' => $data['ob_adjustment'],
            'adj_ob_distance' => $data['adj_ob_distance'],
            'adj_cg_distance' => $data['adj_cg_distance'],
            'notes' => $data['notes'],
            'code' => $data['code'],
            'actor' => session()->get('username'),
            'id_timesheet_adjustment' => $datas['id'],
            'status' => $data['status'],
            'id_contractor' => $data['id_contractor'],
        ]);
    }

    public function log_delete(array $datas) {
        $action = "DELETE";
        $TimesheetAdjustmentLogs = new \App\Models\TimesheetAdjustmentLogs();
        $data_to_be_saved = [
            "id_timesheet_adjustment" => $datas["id"], 
            "created_at" => Time::now(),
            "actor" => session()->get('username'),
        ];
        $TimesheetAdjustmentLogs->insert($data_to_be_saved);
    }


}
