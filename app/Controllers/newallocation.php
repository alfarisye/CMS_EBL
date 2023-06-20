<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\K3lhReport;
use App\Models\Type;
use App\Models\Category;
use CodeIgniter\I18n\Time;
use App\Models\DocReminder as DocReminderModel;

class newallocation extends BaseController
{
    public function index()
    {
        $data['title'] = "Report - K3LH";

        $tgl_mulai = $_GET['tgl_awal'] ??false;
        $tgl_akhir = $_GET['tgl_akhir']??false;

        $K3lhReport = new K3lhReport();
        $builder = $K3lhReport->builder();
        $builder->select('t_k3lh.*, t_type.id_type, t_type.type as type_text, t_category.*')
                ->join('t_type', 't_type.id_type = t_k3lh.Type')
                ->join('t_category', 't_category.Id_category = ty_category');
            if($tgl_mulai && $tgl_akhir){
                $builder->where("t_k3lh.date BETWEEN '$tgl_mulai' AND '$tgl_akhir'");
                $data['K3lhReport']=$builder->get()->getResultArray();
            }else{
                $data['K3lhReport']=$builder->get()->getResultArray();
                
            }
            

        $Category = new Category();
        $builder = $Category->builder();
        $builder->select('t_category.*, t_type.type as type_text')
                ->join('t_type', 't_type.id_type = t_category.id_type');
        $data['category']=$builder->get()->getResultArray();
            //dd($data);

        $Type = new Type();
        $data['type'] = $Type->findAll();   

        $data['today'] = Time::now()->format('Y-m-d');

        echo view('pages/new-allocation', $data);
    }

    public function action($action)
    {
        $Category = new Category();

        $categorydata = $Category->where('id_type', $action)->findAll();

        //dd($categorydata);
        echo json_encode($categorydata);
    }    

   
    public function add()
    {
        try {
            $Type = $this->request->getVar('Type');
            $category = $this->request->getVar('category');
            
            $Category = new Category();
            $Category->save([
                'id_type' => $Type,
                'category' => $category,                
            ]);

           

            $message = "Category has been created";
        } catch(\Throwable $th) {
            $message = $th->getMessage();
        }
            
        return redirect()->to("/newallocation")->with('message', $message);
    }

   

    public function delete($id)
    {
        $Category = new Category();
        $Category->where('Id_category',$id);             
        $Category->delete();
        return redirect()->to("/newallocation")->with('message', 'category has been deleted');
        
    }

    public function edit($id)
    {
        $data['title'] = "Report - K3LH";
        
        
        
        $Type = new Type();
        $data['type'] = $Type->findAll();
        
        $Category = new Category();
        $builder = $Category->builder();
        $builder->select('t_category.*, t_type.type as type_text')
                ->join('t_type', 't_type.id_type = t_category.id_type')
                ->where('Id_category', $id);
        $data['category']=$builder->get()->getResultArray();
        
        
        echo view('pages/edit-category', $data);
    }

    public function update()
    {
        $Id_category = $this->request->getVar('Id_category');
        $Type = $this->request->getVar('Type');
        $category_name = $this->request->getVar('ty_category');

        $Category = new Category();
        $Category->update($Id_category, [
                'id_type' => $Type,
                'category' => $category_name                
            ]);
            
        return redirect()->to("/newallocation")->with('message', 'Category has been updated');
    }
}
