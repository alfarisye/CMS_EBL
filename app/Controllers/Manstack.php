<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\K3lhReport;
use App\Models\Type;
use App\Models\Category;
use App\Models\ManStockholder;
use App\Models\budgetcsr;
use CodeIgniter\I18n\Time;
use App\Models\DocReminder as DocReminderModel;

class ManStack extends BaseController
{
    public function index()
    {
        $data['title'] = "Master Data - Form Kerja";
        
        $man_stack = new ManStockholder();
        $builder = $man_stack->builder();
        $builder->select('T_stockholder.*');
        $data['man_stack']=$builder->get()->getResultArray();


        $man_stack = new ManStockholder();
        $data['man_stack'] = $man_stack
            ->where("Deletion_status", "0")->findAll();
        
        //dd($data);
        echo view('pages/man-stackholder', $data);
    }

    public function add()
    {
        try {
            
            $mn_stack = $this->request->getVar('stockholder');
    
            //$created_at = Time::now();
            $man_stack = new ManStockholder();
            $man_stack->save([
                
                'stockholder' => $mn_stack,
                'create_by' => session()->get('username'),
                'create_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);

            $message = "Stackholder has been created";
        } catch(\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/tambah-manstack")->with('message', $message);
    }


    public function delete($id_stockholder)
    {
        $man_stack = new ManStockholder();
        //dd($id);             
        $man_stack->update($id_stockholder,
        [
            'Deletion_status' => '1',
            'change_by' => session()->get('username'),
            'change_on'=> Time::now()->format('Y-m-d H:i:s')
        ]);   
        return redirect()->to("/tambah-manstack")->with('message', 'stackholder has been deleted');
        
    }

    public function edit($id_stockholder)
    {
        $data['title'] = "Edit - Stackholder";
        
        
        $man_stack = new ManStockholder();
        $builder = $man_stack->builder();
        $builder->select("T_stockholder.*");
        $builder->where("id_stockholder", $id_stockholder);
        $data['man_stack'] = $builder->get()->getRowArray();
        //dd($data);


        
        $data['year'] = range(date('Y'), date('Y') - 5);
           

        echo view('pages/edit-stackholder', $data);
    }


    public function update()
    {
            $mn_stack = $this->request->getVar('stockholder');
            $id_stockholder = $this->request->getVar('id_stockholder');
            //$created_at = Time::now();
            $man_stack = new ManStockholder();
            $man_stack->update($id_stockholder,[
                'stockholder' => $mn_stack,
                'create_by' => session()->get('username'),
                'create_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);

            
        return redirect()->to("/tambah-manstack")->with('message', 'BML has been updated');
    }
}
