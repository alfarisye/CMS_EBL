<?php

namespace App\Models\Sales;

use CodeIgniter\Model;

class DemurageInvoices extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'FI_DEMURAGE_INV';
    protected $primaryKey       = 'ID';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['ID','SHIPMENT_ID','CONTRCT_NO','LIFNR',
                                    'BLDAT','BUDAT','ZFBDT',
                                    'ZTERM','ECURR','KURSF',
                                    'ATTCH','XBLNR',
                                    'DEMURAGE','PROJK','PRCTR',
                                    'AUFNR','SGTXT','DOC_NO',
                                    'BELNR','STBLG','USNAM',
                                    'CPUDT','CREATED_AT','CREATED_BY',
                                    'UPDATED_AT','UPDATED_BY',
                                    'DELETED_AT','DELETED_BY','STATUS_SAP','MESSAGE_SAP'];

    // Dates
    protected $useTimestamps = false;
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
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
