<?php
namespace App\Controllers\budget;

use App\Controllers\BaseController;
use App\Models\budget\MdMonthlybudgetCc;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class MdMonthlybudgetCcs extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function md_monthlybudget_cc()
    {
        $data['title'] = "md monthlybudget cc";
        echo view('pages/budget/md_monthlybudget_cc', $data);
    }

    public function get_md_monthlybudget_cc(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from md_monthlybudget_cc where deleted_at='' OR deleted_at IS NULL");
        return $this->respond($query->getResult(), 200);
    }

    public function get_md_contractors(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from md_contractors ");
        return $this->respond($query->getResult(), 200);
    }
    
    public function md_monthlybudget_cc_insert(){
        $data=$this->request->getJSON();
        $MdMonthlybudgetCc = new MdMonthlybudgetCc();
        $MdMonthlybudgetCc->save($data);
        $this->GLogs->after_insert('md_monthlybudget_cc');
        return $this->respond($data, 200);
    }

    public function md_monthlybudget_cc_update($id)
    {
        $data=$this->request->getJSON();
        $MdMonthlybudgetCc = new MdMonthlybudgetCc();
        $MdMonthlybudgetCc->find($id);
        $this->GLogs->before_update($id,'md_monthlybudget_cc','id_monthlybudgetcc');
        $MdMonthlybudgetCc->update($id, $data);
        $this->GLogs->after_update($id,'md_monthlybudget_cc','id_monthlybudgetcc');
        return $this->respond($MdMonthlybudgetCc, 200);
    }

    public function md_monthlybudget_cc_delete($id)
    {
        $this->GLogs->before_delete($id,'md_monthlybudget_cc','id_monthlybudgetcc');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("delete from  md_monthlybudget_cc  where id_monthlybudgetcc='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
