<?php
namespace App\Controllers\budget;

use App\Controllers\BaseController;
use App\Models\budget\MdAnnualcrushcoal;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class MdAnnualcrushcoals extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function md_annualcrushcoal()
    {
        $data['title'] = "md annualcrushcoal";
        echo view('pages/budget/md_annualcrushcoal', $data);
    }

    public function get_md_annualcrushcoal(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from md_annualcrushcoal where deleted_at='' OR deleted_at IS NULL");
        return $this->respond($query->getResult(), 200);
    }
    
    public function md_annualcrushcoal_insert(){
        $data=$this->request->getJSON();
        $MdAnnualcrushcoal = new MdAnnualcrushcoal();
        $MdAnnualcrushcoal->save($data);
        $this->GLogs->after_insert('md_annualcrushcoal');
        return $this->respond($data, 200);
    }

    public function md_annualcrushcoal_update($id)
    {
        $data=$this->request->getJSON();
        $MdAnnualcrushcoal = new MdAnnualcrushcoal();
        $MdAnnualcrushcoal->find($id);
        $MdAnnualcrushcoal->update($id, $data);
        return $this->respond($MdAnnualcrushcoal, 200);
    }

    public function md_annualcrushcoal_delete($id)
    {
        $this->GLogs->before_delete($id,'md_annualcrushcoal','id_annualcrushcoal');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("delete from md_annualcrushcoal where id_annualcrushcoal='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
