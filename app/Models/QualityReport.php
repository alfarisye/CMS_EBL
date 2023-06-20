<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;
use App\Models\Glogs;

class QualityReport extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'quality_report';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['Project_location','Sample_type','Lab_sample_id','Customer_sample_id','tanggal_mulai','tanggal_akhir','status','From_meter','To_meter','Thick_meter','Seam','Weight_of_Recieved','Total_moisture','Moisture_in_sample','Ash_content','Volatil_matter','Fixed_carbon','Total_sulphu','Gross_calorifi_adb','Gross_calorifi_ar','Gross_calorifi_daf','Gross_calorifi_dab','RD','HGI','EQM','Sulphur','Carbon','Hydrogen','Nitrogen','Oxygen','SiO2','Al2O3','TiO2','Fe2O3','CaO','MgO','K2O','Na2O','SO3','P2O5','Mn3O4','Deformation_reducing','Spherical_reducing','Hemishare_reducing','Flow_reducing','Deformation_oxidicing','Spherical_oxidicing','Hemishare_oxidicing','Flow_oxidicing','Sudiom','Potasium','As','Hg','Se',"created_at","updated_at","deleted_at"];

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
