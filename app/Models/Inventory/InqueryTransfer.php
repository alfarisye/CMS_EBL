<?php

namespace App\Models\Inventory;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class InqueryTransfer extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'inquiry_transfer';
    protected $primaryKey       = 'Ticket_Code';
    protected $useAutoIncrement = false;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ["DO",'Ticket_Code',"Code_Number","Weigh_In","Weigh_Out","Deduction_Weigh","Net_Weigh","Plate_Number","Driver_Id","Driver_Name","Transfer_Type","Product_Code","Product_Name","Transporter_Id","Transporter_Description","Supplier_Id","Supplier_Name","Destination","Remarks","User_Id","Shift","Posting_Date","Posting_Time","Depart_Date","Depart_Time","Block_Code","Block_Description","Destination_Code","Destination_Description","USR","UPDT","STAT","Storage",'Weigh','Seal','Crusher_Code','Crusher_Description','Transfer_Code','Shift','Flag','Jetty','Sync','created_by','updated_by'];

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
