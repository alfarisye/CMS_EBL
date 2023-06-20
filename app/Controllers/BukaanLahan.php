<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BlBlok;
use App\Models\BlBlokForm;
use App\Models\BlProduksi;
use App\Models\BlGeojson;
use App\Models\BlType;
use App\Models\GLogs;
use App\Models\Timesheets;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class BukaanLahan extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function test(){
        $data=[
            [
                csrf_token() => csrf_hash()
            ]
        ];
        return $this->respond($data, 200);
    }

    public function test2(){
        $data['t_parameter'] = [
            [
                "id_parameter"=>"1",
                "nama_parameter"=>"Form 1",
                "batasan"=>"100",
                "status"=>"true",
            ],
            [
                "id_parameter"=>"2",
                "nama_parameter"=>"Form 2",
                "batasan"=>"80",
                "status"=>"true",
            ],
            [
                "id_parameter"=>"3",
                "nama_parameter"=>"Form 3",
                "batasan"=>"90",
                "status"=>"true",
            ],
        ];
        $data['title'] = "test";
        echo view('pages/bukaan-lahan/test', $data);
    }

    public function bl_master()
    {
        $data['title'] = "Master Field";
        echo view('pages/bukaan-lahan/master', $data);
    }

    public function logs(){
        $data['title'] = "Master Field";
        echo view('pages/g_logs', $data);
    }

    public function get_logs(){
        $table=$_GET['table'];
        $db = \Config\Database::connect();
        $query = $db->query("select * from g_logs where `table`='$table'");
        return $this->respond($query->getResult(), 200);
    }

    public function bl_master_data()
    {
        $dari_tanggal=$_GET['dari_tanggal'];
        $sampai_tanggal=$_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        $query = $db->query("
        SELECT tb1.*,tb2.id as id_geojson,tb2.geojson,tb3.nama_type,tb4.nama_blok, tb4.deskripsi as deskripsi_blok,tb4.group as blok_group ,tb5.nama_form
        FROM bl_produksi tb1 
        left join bl_geojson tb2 on tb2.periode=tb1.periode AND tb2.bl_type_id=tb1.bl_type_id 
        left join bl_type tb3 on tb3.id=tb1.bl_type_id 
        left join bl_blok tb4 on tb4.id=tb1.blok_id 
        left join bl_blok_form tb5 on tb5.blok_id=tb1.blok_id AND tb5.bl_type_id=tb1.bl_type_id
        where (tb1.periode BETWEEN '$dari_tanggal' AND '$sampai_tanggal') AND tb1.deleted_at IS NULL  AND (tb5.blok_id=tb1.blok_id AND tb5.bl_type_id=tb1.bl_type_id);");
        return $this->respond($query->getResult(), 200);
    }

    public function bl_blok()
    {
        $data['title'] = "Bukaan Lahan Blok";
        echo view('pages/bukaan-lahan/blok', $data);
    }


    public function bl_blok_get()
    {
        $BlBlok = new BlBlok();
        $builder = $BlBlok->builder();
        $builder->select('bl_blok.*');
        $builder->where("deleted_at IS NULL order by created_at DESC");
        $data=$builder->get()->getResultArray();
        return $this->respond($data, 200);
    }

    public function bl_blok_add()
    {
        $BlBlok = new BlBlok();
        $data=$this->request->getJSON();
        $BlBlok->save($data);
        $this->GLogs->after_insert('bl_blok');
        return $this->respond($data, 200);
    }
    public function bl_blok_update($id)
    {
        $BlBlok = new BlBlok();
        $BlBlok->find($id);
        $data=$this->request->getJSON();
        $this->GLogs->before_update($id,'bl_blok');
        $BlBlok->update($id, $data);
        $this->GLogs->after_update($id,'bl_blok');
        return $this->respond($BlBlok, 200);
    }
    public function bl_blok_delete($id)
    {
        $BlBlok = new BlBlok();
        $BlBlok->find($id);
        $this->GLogs->before_delete($id,'bl_blok');
        $BlBlok->delete($id);
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }

    // BUKAAN LAHAN TYPE
    public function bl_type()
    {
        $data['title'] = "Bukaan Lahan Type";
        echo view('pages/bukaan-lahan/type', $data);
    }
    public function bl_type_get()
    {
        $BlType = new BlType();
        $builder = $BlType->builder();
        $builder->select('bl_type.*');
        $builder->where("deleted_at IS NULL order by created_at DESC");
        $data=$builder->get()->getResultArray();
        return $this->respond($data, 200);
    }

    public function bl_type_add()
    {
        $BlType = new BlType();
        $data=$this->request->getJSON();
        $BlType->save($data);
        $this->GLogs->after_insert('bl_type');
        return $this->respond($data, 200);
    }
    public function bl_type_update($id)
    {
        $BlType = new BlType();
        $BlType->find($id);
        $data=$this->request->getJSON();
        $this->GLogs->before_update($id,'bl_type');
        $BlType->update($id, $data);
        $this->GLogs->after_update($id,'bl_type');
        return $this->respond($BlType, 200);
    }
    public function bl_type_delete($id)
    {
        $BlType = new BlType();
        $BlType->find($id);
        $this->GLogs->before_delete($id,'bl_type');
        $BlType->delete($id);
        $data=array(array("data"=>$id));
        return $this->respond($data, 200);
    }

     // BUKAAN LAHAN Produksi
     public function bl_produksi()
     {
         $data['title'] = "Bukaan Lahan Produksi";
         echo view('pages/bukaan-lahan/produksi', $data);
     }
     public function bl_total_produksi()
     {
         $data['title'] = "Bukaan Lahan Total Produksi";
         echo view('pages/bukaan-lahan/total', $data);
     }
     public function bl_produksi_get()
     {
        $dari_tanggal=$_GET['dari_tanggal'];
        $sampai_tanggal=$_GET['sampai_tanggal'];
        $bl_type_id=$_GET['bl_type_id'];
        $BlProduksi = new BlProduksi();
        $builder = $BlProduksi->builder();
        $builder->select('bl_produksi.*');
        $builder->where("(periode BETWEEN '$dari_tanggal' AND '$sampai_tanggal') AND bl_type_id='$bl_type_id' AND deleted_at IS NULL ");
        $data=$builder->get()->getResultArray();
        return $this->respond($data, 200);
     }

     public function bl_produksi_total()
     {
        $dari_tanggal=$_GET['dari_tanggal'];
        $sampai_tanggal=$_GET['sampai_tanggal'];
        $BlProduksi = new BlProduksi();
        $builder = $BlProduksi->builder();
        $builder->select('bl_produksi.*,tb2.nama_type');
        $builder->join('bl_type tb2', 'tb2.id= bl_produksi.bl_type_id');
        $builder->join('bl_blok_form tb3', 'tb3.blok_id= bl_produksi.blok_id AND tb3.bl_type_id=bl_produksi.bl_type_id');
        $builder->where("(bl_produksi.periode BETWEEN '$dari_tanggal' AND '$sampai_tanggal') AND bl_produksi.deleted_at IS NULL AND (tb3.blok_id=bl_produksi.blok_id AND tb3.bl_type_id=bl_produksi.bl_type_id)");
        $data=$builder->get()->getResultArray();
        return $this->respond($data, 200);
     }
 
     public function bl_produksi_add()
     {
        $BlProduksi = new BlProduksi();
        $builder = $BlProduksi->builder();
        $data=$this->request->getJSON();
        $builder->where('type_blok', $data->type_blok)->where('periode', $data->periode);
        $hasil=$builder->get()->getResultArray();
        $res='insert';
        if(count($hasil)>0){
            $this->GLogs->before_delete($data->type_blok,'bl_produksi','type_blok');
            $builder->set('bl_type_id', $data->bl_type_id, false);
            $builder->set('blok_id', $data->blok_id, false);
            $builder->set('data_produksi', $data->data_produksi, false);
            $builder->set('periode', $data->periode);
            $builder->where('type_blok', $data->type_blok);
            $builder->update();
            $this->GLogs->after_update($data->type_blok,'bl_produksi','type_blok');
            $res="update";
        //    return $this->respond('situ', 200);
        }else{
            $BlProduksi->save($data);
            $this->GLogs->after_insert('bl_produksi');
            $res="insert";
        //    return $this->respond($data, 200);
    }
        return $this->respond($res, 200);
     }
     
     public function bl_produksi_update($id)
     {
        $BlProduksi = new BlProduksi();
        $BlProduksi->find($id);
        $data=$this->request->getJSON();
        $this->GLogs->before_update($id,'bl_produksi');
        $BlProduksi->update($id, $data);
        $this->GLogs->after_update($id,'bl_produksi');
        return $this->respond($BlProduksi, 200);
     }
     public function bl_produksi_delete($id)
     {
         $BlProduksi = new BlProduksi();
         $builder = $BlProduksi->builder();
         $builder->where('id', $id);
         $this->GLogs->before_delete($id,'bl_produksi');
         $builder->delete();
        $data=array(array("data"=>$id));
         return $this->respond($data, 200);
     }

     // BUKAAN LAHAN Form
     public function bl_form()
     {
         $data['title'] = "Bukaan Lahan Form";
         echo view('pages/bukaan-lahan/form', $data);
     }
     public function bl_form_get()
     {
         $BlBlokForm = new BlBlokForm();
         $builder = $BlBlokForm->builder();
         $builder->select('bl_blok_form.*');
         $builder->where("deleted_at IS NULL order by created_at DESC");
         $data=$builder->get()->getResultArray();
         return $this->respond($data, 200);
     }

     public function bl_form_join()
     {
        $BlBlokForm = new BlBlokForm();
        $builder = $BlBlokForm->builder();
        $builder->select('bl_blok_form.*,tb2.nama_blok,tb2.deskripsi as deskripsi_blok,tb3.nama_type,tb3.deskripsi as deskripsi_type');
        $builder->join('bl_blok tb2', 'tb2.id = bl_blok_form.blok_id');
        $builder->join('bl_type tb3', 'tb3.id = bl_blok_form.bl_type_id');
        $builder->where("bl_blok_form.deleted_at IS NULL");
        $data=$builder->get()->getResultArray();
        return $this->respond($data, 200);
     }
 
     public function bl_form_add()
     {
         $BlBlokForm = new BlBlokForm();
         $data=$this->request->getJSON();
         $BlBlokForm->save($data);
        $this->GLogs->after_insert('bl_blok_form');
        return $this->respond($data, 200);
     }

     public function bl_form_update($id)
     {
        $BlBlokForm = new BlBlokForm();
        $BlBlokForm->find($id);
        $builder = $BlBlokForm->builder();
        $data=$this->request->getJSON();
        $id=$data->id;
        $blok_id=$data->blok_id;
        $bl_type_id=$data->bl_type_id;
        $builder->where("bl_blok_form.id='$id' AND bl_blok_form.blok_id='$blok_id' AND bl_blok_form.bl_type_id='$bl_type_id'");
        $hasil=$builder->get()->getResultArray();
        if(count($hasil)>0){
            $this->GLogs->before_update($id,'bl_blok_form');
            $BlBlokForm->update($id, $data);
            $this->GLogs->after_update($id,'bl_blok_form');
            return $this->respond($BlBlokForm, 200);
        }else{
            $builder->where("bl_blok_form.blok_id='$blok_id' AND bl_blok_form.bl_type_id='$bl_type_id'");
            $hasil=$builder->get()->getResultArray();
            if(count($hasil)>0){
                return $this->respond($hasil, 500);
            }else{
                $this->GLogs->before_update($id,'bl_blok_form');
                $BlBlokForm->update($id, $data);
                $this->GLogs->after_update($id,'bl_blok_form');
                return $this->respond($BlBlokForm, 200);
            }
        }
     }
     public function bl_form_delete($id)
     {
        $BlBlokForm = new BlBlokForm();
        $builder = $BlBlokForm->builder();
        $builder->where('id', $id);
        $this->GLogs->before_delete($id,'bl_blok_form');
        $builder->delete();
        $data=array(array("data"=>$id));
         return $this->respond($data, 200);
     }


    //  BlGeojson
    public function bl_geojson()
    {
        $bl_type_id=$_GET['bl_type_id'];
        $periode=$_GET['periode'];
       $BlGeojson = new BlGeojson();
       $builder = $BlGeojson->builder();
       $builder->where("bl_geojson.bl_type_id='$bl_type_id' AND bl_geojson.periode='$periode'");
       $hasil=$builder->get()->getResultArray();
        return $this->respond($hasil, 200);
    }
    public function bl_geojson_upsert()
    {
       $BlGeojson = new BlGeojson();
       $builder = $BlGeojson->builder();
       $data=$this->request->getJSON();
       $bl_type_id=$data->bl_type_id;
       $periode=$data->periode;
       $builder->where("bl_geojson.bl_type_id='$bl_type_id' AND bl_geojson.periode='$periode'");
       $hasil=$builder->get()->getResultArray();
       if(count($hasil)>0){
            $id=$hasil[0]['id'];
            $this->GLogs->before_update($id,'bl_geojson');
            $BlGeojson->update($id, $data);
            $this->GLogs->after_update($id,'bl_geojson');
            return $this->respond($data, 200);
       }else{
            $BlGeojson->save($data);
            $this->GLogs->after_insert('bl_geojson');
            return $this->respond($data, 200);
       }
    }

    public function bl_geojson_delete(){
        $data=$this->request->getJSON();
        $bl_type_id=$data->bl_type_id;
        $periode=$data->periode;
        $BlGeojson = new BlGeojson();
        $builder = $BlGeojson->builder();
        $builder->where("bl_geojson.bl_type_id='$bl_type_id' AND bl_geojson.periode='$periode'");
        $builder->delete();
    }

    public function upload()
    {
        $data = array();
      // Read new token and assign to $data['token']
    //   $data['csrf_test_name'] = csrf_hash();

      ## Validation
      $validation = \Config\Services::validation();

      $input = $validation->setRules([
        //  'file' => 'uploaded[file]|max_size[file,30720]|ext_in[file,jpeg,jpg,docx,pdf,geojson,GEOJSON,PNG],'
         'file' => 'uploaded[file]|max_size[file,30720],'
      ]);

      if ($validation->withRequest($this->request)->run() == FALSE){

         $data['success'] = 0;
         $data['error'] = $validation->getError('file');// Error response

      }else{
      
         if($file = $this->request->getFile('file')) {
            if ($file->isValid() && ! $file->hasMoved()) {
               // Get file name and extension
               $name = $file->getName();
               $ext = $file->getClientExtension();
               $filepath = WRITEPATH . 'uploads/' . $file->store();
               // Get random file name
            //    $newName = $file->getRandomName();

               // Store file in public/uploads/ folder
            //    $file->move('../public/uploads', $name);

               // File path to display preview
            //    $filepath = "/uploads/".$name;

               // Response
               $data['success'] = 1;
               $data['message'] = 'Uploaded Successfully!';
               $data['filepath'] = $filepath;
               $data['extension'] = $ext;

            }else{
               // Response
               $data['success'] = 2;
               $data['message'] = 'File not uploaded.'; 
            }
         }else{
            // Response
            $data['success'] = 2;
            $data['message'] = 'File not uploaded.';
         }
      }
      return $this->response->setJSON($data);

    }

    public function download_geojson()
    {
        $data=$this->request->getJSON();
        $path=$data->path;
        return $this->response->download($path, null)->setFileName('test.geojson');
    }

    public function download_pdf()
    {
        $data=$this->request->getJSON();
        $path=$data->path;
        return $this->response->download($path, null)->setFileName('report.pdf');
    }
    
    // // == #Tempcode Malik
}
