<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NewAllocation;
use CodeIgniter\I18n\Time;
use App\Models\KualitasAir;
use App\Models\BML;
use App\Models\Parameter;

class Newpara extends BaseController
{
    public function index()
    {
        $data['title'] = "Master Data - Tambah Bml";
        
        $air_para = new Parameter();
        $builder = $air_para->builder();
        $builder->select('T_Parameter.*');
        $data['air_para']=$builder->get()->getResultArray();


        $air_para = new Parameter();
        $data['air_para'] = $air_para
            ->where("Deletion_status", "0")->findAll();
        
        //dd($data);
        echo view('pages/new-parameter', $data);
    }

    public function add()
    {
        try {
            
            $nm_para = $this->request->getVar('nama_parameter');
            $status = $this->request->getVar('status'); 
    
            //$created_at = Time::now();
            $air_para = new Parameter();
            $air_para->save([
                
                'nama_parameter' => $nm_para,
                'status' => $status,
                'create_by' => session()->get('username'),
                'create_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);

           

            $message = "Parameter has been created";
        } catch(\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/tambah-parameter")->with('message', $message);
    }
    public function delete($id_Parameter)
    {
        $air_para = new Parameter();
        //dd($id);             
        $air_para->update($id_Parameter,
        [
            'Deletion_status' => '1',
            'change_by' => session()->get('username'),
            'change_on'=> Time::now()->format('Y-m-d H:i:s')
        ]);   
        return redirect()->to("/tambah-parameter")->with('message', 'Parameter di Hapus');
        
    }

    public function edit($id_Parameter)
    {
        $data['title'] = "Edit - CSR Budget";
        
        
        $air_para = new Parameter();
        $builder = $air_para->builder();
        $builder->select("T_Parameter.*");
        $builder->where("id_Parameter", $id_Parameter);
        $data['air_para'] = $builder->get()->getRowArray();
        //dd($data);


        
        $data['year'] = range(date('Y'), date('Y') - 5);
           

        echo view('pages/edit-parameter', $data);
    }

    public function update()
    {
        $nm_para = $this->request->getVar('nama_parameter');
        $status = $this->request->getVar('status'); 
        $id_Parameter = $this->request->getVar('id_Parameter');
        $air_para = new Parameter();
        $air_para->update($id_Parameter, [
                'nama_parameter' => $nm_para,
                'status' => $status,
                'change_by' => session()->get('username'),
                'change_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);
            
        return redirect()->to("/tambah-parameter")->with('message', 'Parameter has been updated');
    }
}