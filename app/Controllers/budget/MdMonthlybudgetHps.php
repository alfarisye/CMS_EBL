<?php
namespace App\Controllers\budget;

use App\Controllers\BaseController;
use App\Models\budget\MdMonthlybudgetHp;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class MdMonthlybudgetHps extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function md_monthlybudget_hp()
    {
        $data['title'] = "md monthlybudget hp";
        echo view('pages/budget/md_monthlybudget_hp', $data);
    }

    public function get_md_monthlybudget_hp(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from md_monthlybudget_hp where deleted_at='' OR deleted_at IS NULL");
        return $this->respond($query->getResult(), 200);
    }
    
    public function md_monthlybudget_hp_insert(){
        $data=$this->request->getJSON();
        $MdMonthlybudgetHp = new MdMonthlybudgetHp();
        $MdMonthlybudgetHp->save($data);
        $this->GLogs->after_insert('md_monthlybudget_hp');
        return $this->respond($data, 200);
    }

    public function md_monthlybudget_hp_update($id)
    {
        $data=$this->request->getJSON();
        $MdMonthlybudgetHp = new MdMonthlybudgetHp();
        $MdMonthlybudgetHp->find($id);
        $this->GLogs->before_update($id,'md_monthlybudget_hp','id_monthlybudgethp');
        $MdMonthlybudgetHp->update($id, $data);
        $this->GLogs->after_update($id,'md_monthlybudget_hp','id_monthlybudgethp');
        return $this->respond($MdMonthlybudgetHp, 200);
    }

    public function md_monthlybudget_hp_delete($id)
    {
        $this->GLogs->before_delete($id,'md_monthlybudget_hp','id_monthlybudgethp');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("delete from md_monthlybudget_hp where id_monthlybudgethp='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
