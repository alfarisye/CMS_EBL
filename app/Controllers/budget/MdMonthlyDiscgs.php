<?php
namespace App\Controllers\budget;

use App\Controllers\BaseController;
use App\Models\budget\MdMonthlyDiscg;
use App\Models\GLogs;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class MdMonthlyDiscgs extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function md_monthly_discg()
    {
        $data['title'] = "md monthly discg";
        echo view('pages/budget/md_monthly_discg', $data);
    }

    public function get_md_monthly_discg(){
        $db = \Config\Database::connect();
        $query = $db->query("select * from md_monthly_discg where deleted_at='' OR deleted_at IS NULL");
        return $this->respond($query->getResult(), 200);
    }
    
    public function md_monthly_discg_insert(){
        $data=$this->request->getJSON();
        $MdMonthlyDiscg = new MdMonthlyDiscg();
        $MdMonthlyDiscg->save($data);
        $this->GLogs->after_insert('md_monthly_discg');
        return $this->respond($data, 200);
    }

    public function md_monthly_discg_update($id)
    {
        $data=$this->request->getJSON();
        $MdMonthlyDiscg = new MdMonthlyDiscg();
        $MdMonthlyDiscg->find($id);
        $this->GLogs->before_update($id,'md_monthly_discg','id_monthlybudget_discg');
        $MdMonthlyDiscg->update($id, $data);
        $this->GLogs->after_update($id,'md_monthly_discg','id_monthlybudget_discg');
        return $this->respond($MdMonthlyDiscg, 200);
    }

    public function md_monthly_discg_delete($id)
    {
        $this->GLogs->before_delete($id,'md_monthly_discg','id_monthlybudget_discg');
        $db = \Config\Database::connect();
        $date=date('Y-m-d H:i:s');
        $query = $db->query("delete from  md_monthly_discg  where id_monthlybudget_discg='$id'");
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }
}
