<?php

namespace App\Models\Inventory;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class ExplosiveMaterial extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'im_stock_explsvmaterial';
    protected $primaryKey       = 'id_explsvmaterial';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["id_explsvmaterial",'product_explsvmaterial','qty',"amount_explsvmaterial","location","expired","periode","post_date",'created_by','updated_by','deleted_by'];

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
