<?php
namespace App\Controllers\budget;

use App\Controllers\BaseController;
use App\Models\budget\MdAnnualhp;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class MdAnnualhps extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function md_annualhp()
    {
        $data['title'] = "md annualhp";
        echo view('pages/budget/md_annualhp', $data);
    }

    public function get_md_annualhp(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from md_annualhp where deleted_at='' OR deleted_at IS NULL");
        return $this->respond($query->getResult(), 200);
    }
    
    public function md_annualhp_insert(){
        $data=$this->request->getJSON();
        $MdAnnualhp = new MdAnnualhp();
        $MdAnnualhp->save($data);
        $this->GLogs->after_insert('md_annualhp');
        return $this->respond($data, 200);
    }

    public function md_annualhp_update($id)
    {
        $data=$this->request->getJSON();
        $MdAnnualhp = new MdAnnualhp();
        $MdAnnualhp->find($id);
        $this->GLogs->before_update($id,'md_annualhp','id_annualhp');
        $MdAnnualhp->update($id, $data);
        $this->GLogs->after_update($id,'md_annualhp','id_annualhp');
        return $this->respond($MdAnnualhp, 200);
    }

    public function md_annualhp_delete($id)
    {
        $this->GLogs->before_delete($id,'md_annualhp','id_annualhp');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("delete from  md_annualhp  where id_annualhp='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
