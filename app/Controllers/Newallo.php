<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NewAllocation;
use CodeIgniter\I18n\Time;

class Newallo extends BaseController
{
    public function index()
    {
        $data['title'] = "CSR - NewAllocation";
        
        $new_allo = new NewAllocation();
        $builder = $new_allo->builder();
        $builder->select('new_allocation.*')
                ->where("new_allocation.Deletion_status", "0");
        $data['new_allo']=$builder->get()->getResultArray();
        
        
        //dd($data);
        echo view('pages/new-allocation', $data);
    }
    public function add()
    {
        $validationRule = [
            'userfile' => [
                'label' => 'Icon',
                'rules' => 'uploaded[userfile]'
                    . '|mime_in[userfile,image/png,image/jpeg,image/gif]'
                    . '|max_size[userfile,50280]'
            ],
        ];
        if (!$this->validate($validationRule)) {
            // dd($this->validator);
            return redirect()->to("/new-csr-allocation")->with('message', implode(";", $this->validator->getErrors()));
        }
        $doc_file = $this->request->getFile('userfile');
        $original_filename = $doc_file->getName();
        $original_ext = $doc_file->getExtension();
        if (!$doc_file->hasMoved()) {
            $filepath = WRITEPATH . 'uploads/' . $doc_file->store();
        } else {
            $filepath = "";
        }
       


        try {
            $allocation = $this->request->getVar('allocation');

            $new_allo = new NewAllocation();
            $new_allo->save([
                'allocation' => $allocation,
                'upload_file_path' => $filepath,
                'upload_file_name' => $original_filename,
                'upload_file_type' => $original_ext,
                'create_by' => session()->get('username'),
                'create_on' => Time::now()->format('Y-m-d H:i:s')
            ]);



            $message = "Accident report has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/new-csr-allocation")->with('message', $message);
    }

    public function download($id_allo)
    {
        try {
            $new_allo = new NewAllocation();
            $new_allo = $new_allo->where('id_allo', $id_allo)->first();
            $filepath = $new_allo['upload_file_path'];
            $filename = $new_allo['upload_file_name'];
            return $this->response->download($filepath, null)->setFileName($filename);
        } catch (\Throwable $th) {
            return redirect()->to("/new-csr-allocation")->with('message', $th->getMessage());
        }
    }

    public function delete($id_allo)
    {
        $new_allo = new NewAllocation();
        $new_allo->update($id_allo,
        [
            'Deletion_status' => '1',
            'change_by' => session()->get('username'),
            'change_on'=> Time::now()->format('Y-m-d H:i:s')
        ]);       
        
        return redirect()->to("/new-csr-allocation")->with('message', 'Allocation has been deleted');
    }

    public function edit($id_allo)
    {
        $data['title'] = "Edit - New Allocation CSR";


        $new_allo = new NewAllocation();
        $builder = $new_allo->builder();
        $builder->select("new_allocation.*");
        $builder->where("id_allo", $id_allo);
        $data['new_allo'] = $builder->get()->getRowArray();

        echo view('pages/edit-alloca', $data);
    }

    public function update()
    {
        $doc_file = $this->request->getFile('userfile');
        $validationRule = [
            'userfile' => [
                'label' => 'File Icon',
                'rules' => 'mime_in[userfile,image/png,image/jpeg,image/gif]'
                    . '|max_size[userfile,5028]'

            ],
        ];
        if (!$this->validate($validationRule)) {
            return redirect()->back()->with('message', implode(";", $this->validator->getErrors()));
        }

        $allocation = $this->request->getVar('allocation');
        $id_allo = $this->request->getVar('id_allo');

        if ($doc_file->isValid()) {
            $original_filename = $doc_file->getName();
            $original_ext = $doc_file->getExtension();
            if (!$doc_file->hasMoved()) {
                $filepath = WRITEPATH . 'uploads/' . $doc_file->store();
            } else {
                $filepath = "";
            }
            try {
                $new_allo = new NewAllocation();
                $new_allo->update($id_allo, [
                    
                    'allocation' => $allocation,
                    'upload_file_path' => $filepath,
                    'upload_file_name' => $original_filename,
                    'upload_file_type' => $original_ext,
                    'updated_by' => session()->get('username'),
                    'updated_on' => Time::now()->format('Y-m-d H:i:s'),
                ]);
                $message = "Allocation successfully updated";
            } catch (\Throwable $th) {
                $message = $th->getMessage();
            }
            return redirect()->to("/new-csr-allocation")->with('message', $message);
        }
        try {
            $new_allo = new NewAllocation();
            $new_allo->update($id_allo, [
                'allocation' => $allocation,
                'updated_by' => session()->get('username'),
                'updated_on' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            $message = "Allocation successfully updated";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/new-csr-allocation")->with('message', $message);
    }
}
