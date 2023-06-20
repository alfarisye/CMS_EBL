<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NewAllocation;
use CodeIgniter\I18n\Time;
use App\Models\KualitasAir;
use App\Models\BML;

class Newbml extends BaseController
{
    public function index()
    {
        $data['title'] = "Master Data - Tambah Bml";
        
        $air_bml = new BML();
        $builder = $air_bml->builder();
        $builder->select('T_BML.*');
        $data['air_bml']=$builder->get()->getResultArray();
        //dd($data);


        $air_bml = new BML();
        $data['air_bml'] = $air_bml
            ->where("Deletion_status", "0")->findAll();
        
        //dd($data);
        echo view('pages/new-bml', $data);
    }

    public function add()
    {
        try {
            
            $nm_bgt = $this->request->getVar('nama_budget');
            $budget = $this->request->getVar('budget'); 
            $budget_max = $this->request->getVar('budget_max');
    
            //$created_at = Time::now();
            $air_bml = new BML();
            $air_bml->save([
                
                'nama_budget' => $nm_bgt,
                'budget' => $budget,
                'budget_max' => $budget_max,
                'create_by' => session()->get('username'),
                'create_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);

           

            $message = "BML has been created";
        } catch(\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/tambah-bml")->with('message', $message);
    }
    public function delete($id_BML)
    {
        $air_bml = new BML();
        //dd($id);             
        $air_bml->update($id_BML,
        [
            'Deletion_status' => '1',
            'change_by' => session()->get('username'),
            'change_on'=> Time::now()->format('Y-m-d H:i:s')
        ]);   
        return redirect()->to("/tambah-bml")->with('message', 'BML has been deleted');
        
    }

    public function edit($id_BML)
    {
        $data['title'] = "Edit - CSR Budget";
        
        
        $air_bml = new BML();
        $builder = $air_bml->builder();
        $builder->select("T_BML.*");
        $builder->where("id_BML", $id_BML);
        $data['air_bml'] = $builder->get()->getRowArray();
        //dd($data);


        
        $data['year'] = range(date('Y'), date('Y') - 5);
           

        echo view('pages/edit-bml', $data);
    }

    public function update()
    {
            $nm_bgt = $this->request->getVar('nama_budget');
            $budget = $this->request->getVar('budget'); 
            $budget_max = $this->request->getVar('budget_max');
            $id_BML = $this->request->getVar('id_BML');
            //$created_at = Time::now();
            $air_bml = new BML();
            $air_bml->update($id_BML,[
                'nama_budget' => $nm_bgt,
                'budget' => $budget,
                'budget_max' => $budget_max,
                'create_by' => session()->get('username'),
                'create_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);

            
        return redirect()->to("/tambah-bml")->with('message', 'BML has been updated');
    }
}