<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class Timesheets extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'timesheets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["prd_date", "prd_cg_day_qty", "prd_cg_night_qty", "prd_cg_total", 
        "prd_ob_day_qty", "prd_ob_night_qty", "prd_ob_total", "prd_sr", "prd_rain", "prd_slip", "prd_%",
        "prd_rainfall", "noted", "prd_revision", "status", "id_contractor", "id_annualbudget", "id_monthlybudget",
        "prd_code", "prd_ob_distance", "prd_cg_distance"
    ];

    // Dates
    protected $useTimestamps = true;
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
    protected $afterInsert    = ["add_to_timesheet_log"];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = ["edit_timesheet_log"];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = ["delete_timesheet_log"];

    public function add_to_timesheet_log(array $datas) {
        $action = "ADD";
        $TimesheetLogs = new \App\Models\TimesheetLogs();
        $data = $datas['data'];
        $data_to_be_saved = [
            "prd_date" => $data["prd_date"],
            "prd_code" => $data["prd_code"] ,
            "prd_cg_day_qty" => $data["prd_cg_day_qty"],
            "prd_cg_night_qty" => $data["prd_cg_night_qty"],
            "prd_cg_total" => $data["prd_cg_total"],
            "prd_ob_day_qty" => $data["prd_ob_day_qty"],
            "prd_ob_night_qty" => $data["prd_ob_night_qty"],
            "prd_ob_total" => $data["prd_ob_total"],
            "prd_sr" => $data["prd_sr"],
            "prd_rain" => $data["prd_rain"],
            "prd_slip" => $data["prd_slip"],
            "prd_%" => $data["prd_%"],
            "prd_rainfall" => $data["prd_rainfall"],
            "noted" => $data["noted"],
            "changes" => $data["prd_revision"],
            "status" => $data["status"],
            "id_timesheet" => $datas["id"] ,
            "prd_ob_distance" => $data["prd_ob_distance"],
            "prd_cg_distance" => $data["prd_cg_distance"],
            "created_at" => Time::now(),
            "actor" => session()->get('username'),
            "action" => $action
        ];
        $TimesheetLogs->insert($data_to_be_saved);
    }

    public function edit_timesheet_log(array $datas) {
        $action = "EDIT";
        $TimesheetLogs = new \App\Models\TimesheetLogs();
        $data = $datas['data'];
        $data_to_be_saved = [
            "prd_date" => $data["prd_date"],
            "prd_code" => $data["prd_code"] ?? 0,
            "prd_cg_day_qty" => $data["prd_cg_day_qty"],
            "prd_cg_night_qty" => $data["prd_cg_night_qty"],
            "prd_cg_total" => $data["prd_cg_total"],
            "prd_ob_day_qty" => $data["prd_ob_day_qty"],
            "prd_ob_night_qty" => $data["prd_ob_night_qty"],
            "prd_ob_total" => $data["prd_ob_total"],
            "prd_sr" => $data["prd_sr"],
            "prd_rain" => $data["prd_rain"],
            "prd_slip" => $data["prd_slip"],
            "prd_%" => $data["prd_%"],
            "prd_rainfall" => $data["prd_rainfall"],
            "noted" => $data["noted"],
            "changes" => $data["prd_revision"],
            "status" => $data["status"],
            "id_timesheet" => $datas["id"],
            "prd_ob_distance" => $data["prd_ob_distance"],
            "prd_cg_distance" => $data["prd_cg_distance"] ?? 0,
            "created_at" => Time::now(),
            "actor" => session()->get('username'),
            "action" => $action
        ];
        $TimesheetLogs->insert($data_to_be_saved);
    }

    public function delete_timesheet_log(array $datas) {
        $action = "DELETE";
        $TimesheetLogs = new \App\Models\TimesheetLogs();
        $data_to_be_saved = [
            "id_timesheet" => $datas["id"], 
            "created_at" => Time::now(),
            "actor" => session()->get('username'),
            "action" => $action
        ];
        $TimesheetLogs->insert($data_to_be_saved);
    }
}
