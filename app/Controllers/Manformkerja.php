<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\K3lhReport;
use App\Models\Type;
use App\Models\Category;
use App\Models\ManForm;
use App\Models\budgetcsr;
use CodeIgniter\I18n\Time;
use App\Models\DocReminder as DocReminderModel;

class Manformkerja extends BaseController
{
    public function index()
    {
        $data['title'] = "Master Data - Form Kerja";
        
        $man_form = new ManForm();
        $builder = $man_form->builder();
        $builder->select('T_Form.*');
        $data['man_form']=$builder->get()->getResultArray();


        $man_form = new ManForm();
        $data['man_form'] = $man_form
            ->where("Deletion_status", "0")->findAll();
        
        //dd($data);
        echo view('pages/man-form', $data);
    }

    public function add()
    {
        try {
            
            $nm_form = $this->request->getVar('nama_form');
            $status = $this->request->getVar('status'); 
    
            //$created_at = Time::now();
            $man_form = new ManForm();
            $man_form->save([
                
                'nama_form' => $nm_form,
                'status' => $status,
                'create_by' => session()->get('username'),
                'create_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);

           

            $message = "Accident report has been created";
        } catch(\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/tambah-manform")->with('message', $message);
    }

    public function delete($id_form)
    {
        $man_form = new ManForm();
        //dd($id);             
        $man_form->update($id_form,
        [
            'Deletion_status' => '1',
            'change_by' => session()->get('username'),
            'change_on'=> Time::now()->format('Y-m-d H:i:s')
        ]);   
        return redirect()->to("/tambah-manform")->with('message', 'Form has been deleted');
        
    }

    public function edit($id_form)
    {
        $data['title'] = "Edit - CSR Budget";
        
        
        $man_form = new ManForm();
        $builder = $man_form->builder();
        $builder->select("T_Form.*");
        $builder->where("id_form", $id_form);
        $data['man_form'] = $builder->get()->getRowArray();
        //dd($data);


        
        $data['year'] = range(date('Y'), date('Y') - 5);
           

        echo view('pages/edit-form', $data);
    }


    public function update()
    {
            $nm_form = $this->request->getVar('nama_form');
            $status = $this->request->getVar('status'); 
            $id_form = $this->request->getVar('id_form');
            //$created_at = Time::now();
            $man_form = new ManForm();
            $man_form->update($id_form,[
                'nama_form' => $nm_form,
                'status' => $status,
                'create_by' => session()->get('username'),
                'create_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);

            
        return redirect()->to("/tambah-manform")->with('message', 'BML has been updated');
    }
}
