<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\CostType;
use CodeIgniter\I18n\Time;

class CostTypes extends BaseController
{
    public function index(){
        $data['title'] = "Cost Type";
        $CostType = new CostType();
        $data['costs'] = $CostType->findAll();

        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/manage-costtype', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function add()
    {
        $namecost = $this->request->getVar('namecost');
        $coststatus = $this->request->getVar('coststatus');

        $CostType = new CostType();
        $CostType->save([
            'cost_type' => $namecost,
            'status' => $coststatus,
            'created_at' => Time::now(),
        ]);
        return redirect()->to("/master-data/costtype/")->with('message', 'A cost type has been created');
    }

    public function delete($id_costtype)
    {
        try {
            $CostType = new CostType();
            $CostType->delete($id_costtype);
            return redirect()->to("/master-data/costtype/")->with('message', 'A cost type has been deleted');
        }
        catch (\Exception $e) {
            return redirect()->to("/master-data/costtype/")->with('message', 'You cannot delete this cost type, check if the cost type is used in other table');
        }
    }

    public function get($id_costtype)
    {
        header('Content-Type: application/json');
        $CostType = new CostType();
        $CostType = $CostType->find($id_costtype);
        return $this->response->setJSON($CostType);
    }

    public function update()
    {
        $CostType = new CostType();
        $CostTypeUpdate = $CostType->find($this->request->getPost('id_costtype'));
        $CostTypeUpdate['cost_type'] = $this->request->getPost('namecost');
        $CostTypeUpdate['status'] = $this->request->getPost('coststatus');

        $CostType->save($CostTypeUpdate);
        return redirect()->to("/master-data/costtype/")->with('message', "Cost type berhasil diupdate");
    }
}