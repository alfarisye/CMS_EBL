<?php
namespace App\Controllers\budget;

use App\Controllers\BaseController;
use App\Models\budget\MdAnnualdiscg;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class MdAnnualdiscgs extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function md_annualdiscg()
    {
        $data['title'] = "md annualdiscg";
        echo view('pages/budget/md_annualdiscg', $data);
    }

    public function get_md_annualdiscg(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from md_annualdiscg where deleted_at='' OR deleted_at IS NULL");
        return $this->respond($query->getResult(), 200);
    }
    
    public function md_annualdiscg_insert(){
        $data=$this->request->getJSON();
        $MdAnnualdiscg = new MdAnnualdiscg();
        $MdAnnualdiscg->save($data);
        $this->GLogs->after_insert('md_annualdiscg');
        return $this->respond($data, 200);
    }

    public function md_annualdiscg_update($id)
    {
        $data=$this->request->getJSON();
        $MdAnnualdiscg = new MdAnnualdiscg();
        $MdAnnualdiscg->find($id);
        $this->GLogs->before_update($id,'md_annualdiscg','id_annualdiscg');
        $MdAnnualdiscg->update($id, $data);
        $this->GLogs->after_update($id,'md_annualdiscg','id_annualdiscg');
        return $this->respond($MdAnnualdiscg, 200);
    }

    public function md_annualdiscg_delete($id)
    {
        $this->GLogs->before_delete($id,'md_annualdiscg','id_annualdiscg');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("delete from  md_annualdiscg  where id_annualdiscg='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
