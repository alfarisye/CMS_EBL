<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QualityReport;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class QualityReports extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }


    public function quality_report()
    {
        $data['title'] = "Quality Report";
        echo view('pages/quality/main', $data);
    }

    public function quality_report_upload()
    {
        $data['title'] = "Quality Report";
        echo view('pages/quality/upload', $data);
    }
    
    
    
    public function quality_report_push_data(){
        $data=$this->upload_excel();
        // return $this->respond($data, 200);
        array_shift($data);
        $QualityReport = new QualityReport();
        $arr=[];
        foreach ($data as $val) {
            $object = (object)[];
            // $tgl_mulai=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($val[2]);
            // $tgl_akhir=\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($val[3]);
            $tgl_mulai=date('Y-m-d',strtotime($val[4]));
            $tgl_akhir=date('Y-m-d',strtotime($val[5]));
            $obj["Project_location"]=$val[0];
            $obj["Sample_type"]=$val[1];
            $obj["Lab_sample_id"]=$val[2];
            $obj["Customer_sample_id"]=$val[3];
            $obj["tanggal_mulai"]=$tgl_mulai;
            $obj["tanggal_akhir"]=$tgl_akhir;
            $obj["status"]=$val[6];
            $obj["From_meter"]=$val[7];
            $obj['To_meter']=$val[8]?$val[8]:'0';
            $obj['Thick_meter']=$val[9]?$val[9]:'0';
            $obj['Seam']=$val[10]?$val[10]:'0';
            $obj['Weight_of_Recieved']=$val[11]?$val[11]:'0';
            $obj['Total_moisture']=$val[12]?$val[12]:'0';
            $obj['Moisture_in_sample']=$val[13]?$val[13]:'0';
            $obj['Ash_content']=$val[14]?$val[14]:'0';
            $obj['Volatil_matter']=$val[15]?$val[15]:'0';
            $obj['Fixed_carbon']=$val[16]?$val[16]:'0';
            $obj['Total_sulphu']=$val[17]?$val[17]:'0';
            $obj['Gross_calorifi_adb']=$val[18]?$val[18]:'0';
            $obj['Gross_calorifi_ar']=$val[19]?$val[19]:'0';
            $obj['Gross_calorifi_daf']=$val[20]?$val[20]:'0';
            $obj['Gross_calorifi_dab']=$val[21]?$val[21]:'0';
            $obj['RD']=$val[22]?$val[22]:'0';
            $obj['HGI']=$val[23]?$val[23]:'0';
            $obj['EQM']=$val[24]?$val[24]:'0';
            $obj['Sulphur']=$val[25]?$val[25]:'0';
            $obj['Carbon']=$val[26]?$val[26]:'0';
            $obj['Hydrogen']=$val[27]?$val[27]:'0';
            $obj['Nitrogen']=$val[28]?$val[28]:'0';
            $obj['Oxygen']=$val[29]?$val[29]:'0';
            $obj['SiO2']=$val[30]?$val[30]:'0';
            $obj['Al2O3']=$val[31]?$val[31]:'0';
            $obj['TiO2']=$val[32]?$val[32]:'0';
            $obj['Fe2O3']=$val[33]?$val[33]:'0';
            $obj['CaO']=$val[34]?$val[34]:'0';
            $obj['MgO']=$val[35]?$val[35]:'0';
            $obj['K2O']=$val[36]?$val[36]:'0';
            $obj['Na2O']=$val[37]?$val[37]:'0';
            $obj['SO3']=$val[38]?$val[38]:'0';
            $obj['P2O5']=$val[39]?$val[39]:'0';
            $obj['Mn3O4']=$val[40]?$val[40]:'0';
            $obj['Deformation_reducing']=$val[41]?$val[41]:'0';
            $obj['Spherical_reducing']=$val[42]?$val[42]:'0';
            $obj['Hemishare_reducing']=$val[43]?$val[43]:'0';
            $obj['Flow_reducing']=$val[44]?$val[44]:'0';
            $obj['Deformation_oxidicing']=$val[45]?$val[45]:'0';
            $obj['Spherical_oxidicing']=$val[46]?$val[46]:'0';
            $obj['Hemishare_oxidicing']=$val[47]?$val[47]:'0';
            $obj['Flow_oxidicing']=$val[48]?$val[48]:'0';
            $obj['Sudiom']=$val[49]?$val[49]:'0';
            $obj['Potasium']=$val[50]?$val[50]:'0';
            $obj['As']=$val[51]?$val[51]:'0';
            $obj['Hg']=$val[52]?$val[52]:'0';
            $obj['Se']=$val[53]?$val[53]:'0';
            
            // var_dump($obj);
            try {
                $QualityReport->save($obj);
                // $this->GLogs->after_insert('quality_report');
                $obj['message'] ="Sample ID '".$obj['Lab_sample_id']."' Berhasil Insert";
                $obj['status_upload']=true;
            } catch (\Exception $e) {
                $obj['message'] ="NO : ".$val[0]." ".$e->getMessage();
                $obj['status_upload']=false;
            }
            array_push($arr,$obj);
        }
        return $this->respond($arr, 200);
    }

    public function quality_report_get()
    {
        @$tgl_mulai = $_GET['tgl_awal'];
        @$tgl_akhir = $_GET['tgl_akhir'];
        @$sort = $_GET['sort'];
        $QualityReport = new QualityReport();
        $builder = $QualityReport->builder();
        if($sort){
            $builder->select('quality_report.*');
            $builder->where("deleted_at IS NULL order by created_at $sort limit 2");
        }else{
            $builder->select('quality_report.*');
            $builder->where("(tanggal_mulai BETWEEN '$tgl_mulai' AND '$tgl_akhir' AND tanggal_akhir BETWEEN '$tgl_mulai' AND '$tgl_akhir' ) AND deleted_at IS NULL order by tanggal_mulai DESC");
        }
        $data=$builder->get()->getResultArray();
        return $this->respond($data, 200);
    }

    public function quality_report_add()
    {
        $QualityReport = new QualityReport();
        $data=$this->request->getJSON();
        $QualityReport->save($data);
        $this->GLogs->after_insert('quality_report');
        return $this->respond($data, 200);
    }
    public function quality_report_update($id)
    {
        $QualityReport = new QualityReport();
        $QualityReport->find($id);
        $data=$this->request->getJSON();
        $this->GLogs->before_update($id,'quality_report');
        $QualityReport->update($id, $data);
        $this->GLogs->after_update($id,'quality_report');
        return $this->respond($QualityReport, 200);
    }
    public function quality_report_delete($id)
    {
        // $QualityReport = new QualityReport();
        // $QualityReport->find($id);
        $this->GLogs->before_delete($id,'quality_report');
        $db = \Config\Database::connect();
        $query = $db->query("delete from quality_report where id='$id'");
        // $QualityReport->delete($id);
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }

    public function upload_excel(){
        $data = array();
        $validation = \Config\Services::validation();
        $input = $validation->setRules([
          //  'file' => 'uploaded[file]|max_size[file,30720]|ext_in[file,jpeg,jpg,docx,pdf,geojson,GEOJSON,PNG],'
           'file' => 'uploaded[file]|max_size[file,30720],'
        ]);
        // $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $testAgainstFormats = [
            \PhpOffice\PhpSpreadsheet\IOFactory::READER_XLS,
            \PhpOffice\PhpSpreadsheet\IOFactory::READER_XLSX,
            \PhpOffice\PhpSpreadsheet\IOFactory::READER_HTML,
        ];
        // Tell the reader to only read the data. Ignore formatting etc.
        // $reader->setReadDataOnly(true);
  
        if ($validation->withRequest($this->request)->run() == FALSE){
           $data['success'] = 0;
           $data['error'] = $validation->getError('file');// Error response
        }else{
           if($file = $this->request->getFile('file')) {
              if ($file->isValid() && ! $file->hasMoved()) {
                 $name = $file->getName();
                 $ext = $file->getClientExtension();
                 $filepath = WRITEPATH . 'uploads/' . $file->store();
          
                 $data['success'] = 1;
                 $data['message'] = 'Uploaded Successfully!';
                 $data['filepath'] = $filepath;
                 $data['extension'] = $ext;
                //  $spreadsheet = $reader->load($filepath);
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filepath, 0, $testAgainstFormats);
  
                 $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());
                 $dataexcel = $sheet->toArray();
                 return $dataexcel;
                //  $keys = array_shift($dataexcel);
                //  $result = array_map(function($values) use ($keys) {
                //     return array_combine($keys, $values); }, $dataexcel);
                //  return $result;
  
              }else{
                 return false; 
              }
           }else{
             return false;
           }
        }
        return false;
      }

    
    // // == #Tempcode Malik
}
