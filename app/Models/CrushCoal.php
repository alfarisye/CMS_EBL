<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\I18n\Time;


class CrushCoal extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 't_crushcoal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "id", "production_code", "production_date", "id_contractor", 
        "rc_qty", "id_crusher", "cc_qty", "status", "deletion_status",
        "created_by", "created_on", "changed_by", "changed_on", "id_monthlybudgetcc"
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
        $CrushCoalLog = new \App\Models\CrushCoalLog();
        $data = $datas['data'];
        $data_to_be_saved = [
            "crushcoal_id" => $datas["id"],
            "production_date" => $data["production_date"],
            "id_contractor" => $data["id_contractor"],
            "rc_qty" => $data["rc_qty"],
            "id_crusher" => $data["id_crusher"],
            "cc_qty" => $data["cc_qty"],
            "status" => $data["status"] ?? 'draft',
            "deletion_status" => $data["deletion_status"] ?? 0,
            "created_by" => $data["created_by"],
            "created_on" => $data["created_on"],
            "changed_by" => $data["changed_by"] ?? null,
            "changed_on" => $data["changed_on"] ?? null,
            "MBLNR" => $data["MBLNR"] ?? null,
            "MJAHR" => $data["MJAHR"] ?? null,
            "ZEILE" => $data["ZEILE"] ?? null,
            "action" => $action
        ];
        $CrushCoalLog->insert($data_to_be_saved);
    }

    public function edit(array $datas) {
        $action = "EDIT";
        $CrushCoalLog = new \App\Models\CrushCoalLog();
        $data = $datas['data'];
        $data_to_be_saved = [
            "crushcoal_id" => $datas["id"] ?? null,
            "production_date" => $data["production_date"] ?? null,
            "id_contractor" => $data["id_contractor"] ?? null,
            "rc_qty" => $data["rc_qty"] ?? null,
            "id_crusher" => $data["id_crusher"] ?? null,
            "cc_qty" => $data["cc_qty"] ?? null,
            "status" => $data["status"] ?? null,
            "deletion_status" => $data["deletion_status"] ?? null,
            "created_by" => $data["created_by"] ?? null,
            "created_on" => $data["created_on"] ?? null,
            "changed_by" => $data["changed_by"] ?? null,
            "changed_on" => $data["changed_on"] ?? null,
            "MBLNR" => $data["MBLNR"] ?? null,
            "MJAHR" => $data["MJAHR"] ?? null,
            "ZEILE" => $data["ZEILE"] ?? null,
            "action" => $action
        ];
        $CrushCoalLog->insert($data_to_be_saved);
    }
}
