<?php

namespace App\Models;

use CodeIgniter\Model;

class Activitycsr extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 't_csractivity';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ["doc_no", "date", "allocation", "location", "activity", 
                                    "upload_file_path", "upload_file_name", "upload_file_type",
                                    "actual_cost", "create_by", "create_on", 
                                    "change_by", "change_on", "deletion_status", "formtyp_act","Remark"];
    
    
    
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
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
