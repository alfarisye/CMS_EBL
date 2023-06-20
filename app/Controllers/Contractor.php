<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Contractors;
use CodeIgniter\I18n\Time;

class Contractor extends BaseController
{
    public function index()
    {
        $data['title'] = "Manage Contractor";
        $Contractors = new Contractors();
        $data['contractors'] = $Contractors->findAll();
        $data['today'] = Time::now()->format('Y-m-d');

        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/manage-contractor', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function add()
    {
        $name = $this->request->getVar('name');
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $status = $this->request->getVar('status') == 'on' ? 1 : 0;
        $contractor_type = $this->request->getVar('contractor_type');

        $model = new Contractors();
        $model->save([
            'contractor_name' => $name,
            'status' => $status,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'contractor_type' => $contractor_type,
            'created_at' => Time::now(),
        ]);
        return redirect()->to("/master-data/contractor/")->with('message', 'A contractor has been created');
    }

    public function delete($id)
    {
        try {
            $model = new Contractors();
            $model->delete($id);
            return redirect()->to("/master-data/contractor/")->with('message', 'A contractor has been deleted');
        }
        catch (\Exception $e) {
            return redirect()->to("/master-data/contractor/")->with('message', 'You cannot delete this contractor, check if the contractor is used in other table');
        }
    }

    public function get($id)
    {
        header('Content-Type: application/json');
        $contractor = new Contractors();
        $contractor = $contractor->find($id);
        return $this->response->setJSON($contractor);
    }

    public function update()
    {
        $role = new Contractors();
        $contractorDate = $role->find($this->request->getPost('id'));
        $contractorDate['contractor_name'] = $this->request->getPost('name');
        $contractorDate['start_date'] = $this->request->getPost('start_date');
        $contractorDate['end_date'] = $this->request->getPost('end_date');
        $contractorDate['status'] = $this->request->getPost('status') ? 1 : 0;
        $contractorDate['contractor_type'] = $this->request->getPost('contractor_type');

        $role->save($contractorDate);
        return redirect()->to("/master-data/contractor/")->with('message', "Contractor berhasil diupdate");
    }
}
