<?php
namespace App\Controllers\budget;

use App\Controllers\BaseController;
use App\Models\budget\MdMonthlyDisob;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class MdMonthlyDisobs extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function md_monthly_disob()
    {
        $data['title'] = "md monthly disob";
        echo view('pages/budget/md_monthly_disob', $data);
    }

    public function get_md_monthly_disob(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from md_monthly_disob where deleted_at='' OR deleted_at IS NULL");
        return $this->respond($query->getResult(), 200);
    }
    
    public function md_monthly_disob_insert(){
        $data=$this->request->getJSON();
        $MdMonthlyDisob = new MdMonthlyDisob();
        $MdMonthlyDisob->save($data);
        $this->GLogs->after_insert('md_monthly_disob');
        return $this->respond($data, 200);
    }

    public function md_monthly_disob_update($id)
    {
        $data=$this->request->getJSON();
        $MdMonthlyDisob = new MdMonthlyDisob();
        $MdMonthlyDisob->find($id);
        $this->GLogs->before_update($id,'md_monthly_disob','id_monthlybudget_disob');
        $MdMonthlyDisob->update($id, $data);
        $this->GLogs->after_update($id,'md_monthly_disob','id_monthlybudget_disob');
        return $this->respond($MdMonthlyDisob, 200);
    }

    public function md_monthly_disob_delete($id)
    {
        $this->GLogs->before_delete($id,'md_monthly_disob','id_monthlybudget_disob');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("delete from  md_monthly_disob  where id_monthlybudget_disob='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
