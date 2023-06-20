<?php

namespace App\Models;

use CodeIgniter\Model;

class DocReminder extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'doc_reminder';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ["doc_no", "group_email_id", "email_type", "doc_desc", "due_date", "remind_on",
        "email_status", "upload_file_path", "upload_file_name", "upload_file_type", 
        "created_by", "created_on", "updated_by", "updated_on", "deletion_status", 'code',
        'group_email_cc'
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
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
