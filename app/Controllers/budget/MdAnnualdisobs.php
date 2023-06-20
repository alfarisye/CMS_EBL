<?php
namespace App\Controllers\budget;

use App\Controllers\BaseController;
use App\Models\budget\MdAnnualdisob;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class MdAnnualdisobs extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function md_annualdisob()
    {
        $data['title'] = "md annualdisob";
        echo view('pages/budget/md_annualdisob', $data);
    }

    public function get_md_annualdisob(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from md_annualdisob where deleted_at='' OR deleted_at IS NULL");
        return $this->respond($query->getResult(), 200);
    }
    
    public function md_annualdisob_insert(){
        $data=$this->request->getJSON();
        $MdAnnualdisob = new MdAnnualdisob();
        $MdAnnualdisob->save($data);
        $this->GLogs->after_insert('md_annualdisob');
        return $this->respond($data, 200);
    }

    public function md_annualdisob_update($id)
    {
        $data=$this->request->getJSON();
        $MdAnnualdisob = new MdAnnualdisob();
        $MdAnnualdisob->find($id);
        $this->GLogs->before_update($id,'md_annualdisob','id_annualdisob');
        $MdAnnualdisob->update($id, $data);
        $this->GLogs->after_update($id,'md_annualdisob','id_annualdisob');
        return $this->respond($MdAnnualdisob, 200);
    }

    public function md_annualdisob_delete($id)
    {
        $this->GLogs->before_delete($id,'md_annualdisob','id_annualdisob');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("delete from  md_annualdisob  where id_annualdisob='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
