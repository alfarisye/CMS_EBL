<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;


class CrushCoalAdjust extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 't_adjcrushcoal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "id", "doc_date", 'post_date_start', 'post_date_end', 'cc_adjustment',
        'notes', 'status', 'deletion_status', 'created_by', 'created_on', 'changed_by',
        'changed_on', 'MBLNR', 'MJAHR', 'ZEILE', 'code'
    ];

    // Dates
    protected $useTimestamps = false;
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
    protected $afterInsert    = ["add"];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = ["edit"];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function add(array $datas) {
        $action = "ADD";
        $CrushCoalLog = new \App\Models\CrushCoalAdjustLog();
        $data = $datas['data'];
        $data_to_be_saved = [
            "id_adjustment" => $datas["id"],
            'post_date_start' => $data['post_date_start'],
            'post_date_end' => $data['post_date_end'],
            'cc_adjustment' => $data['cc_adjustment'],
            'doc_date' => $data['doc_date'] ?? null,
            'notes' => $data['notes'],
            'status' => $data['status'] ?? 'draft',
            'changed_on' => Time::now(),
            'changed_by' => session()->get('username'),
            "action" => $action
        ];
        $CrushCoalLog->insert($data_to_be_saved);
    }

    public function edit(array $datas) {
        $action = "EDIT";
        $CrushCoalLog = new \App\Models\CrushCoalAdjustLog();
        $data = $datas['data'];
        $data_to_be_saved = [
            "id_adjustment" => $datas["id"] ?? null,
            'post_date_start' => $data['post_date_start'] ?? null,
            'post_date_end' => $data['post_date_end'] ?? null,
            'cc_adjustment' => $data['cc_adjustment'] ?? null,
            'doc_date' => $data['doc_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? null,
            'changed_on' => Time::now(),
            'changed_by' => session()->get('username'),
            "action" => $action
        ];
        $insert_data = array_filter($data_to_be_saved, function($var){
            return $var != null;
        });
        $CrushCoalLog->insert($insert_data);
    }
}
