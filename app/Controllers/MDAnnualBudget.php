<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MDAnnualBudgets;
use CodeIgniter\HTTP\Message;
use CodeIgniter\I18n\Time;

class MDAnnualBudget extends BaseController
{
    public function index()
    {
        $data['title'] = "Master Data Annual Budget";

        $MDAnnualBudgets = new MDAnnualBudgets();
        $data['AnnualBudgets'] = $MDAnnualBudgets->findAll();
        echo view('pages/md-annualbudget', $data);
    }

    public function add()
    {
        try {
            $project = $this->request->getVar('project');
            $years = $this->request->getVar('years');
            $cg_budget = $this->request->getVar('cg_budget');
            $ob_budget = $this->request->getVar('ob_budget');
            $revision = $this->request->getVar('revision');
            $status = $this->request->getVar('status');

            $MDAnnualBudgets = new MDAnnualBudgets();
            $MDAnnualBudgets->save([
                'year' => $years,
                'project' => $project,
                'cg_annualbudget_qt' => $cg_budget,
                'ob_annualbudget_qt' => $ob_budget,
                'revision' =>  $revision,
                'status' =>  $status,
                'create_date' => Time::now()->format('Y-m-d H:i:s'),
            ]);


            $message = "Annual Budget has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/master-data/annualbudget")->with('message', $message);
    }
    
    public function get($id)
    {
        header('Content-Type: application/json');
        $MDAnnualBudgets = new MDAnnualBudgets();
        $MDAnnualBudgets = $MDAnnualBudgets->find($id);
        return $this->response->setJSON($MDAnnualBudgets);
    }

    public function update()
    {
        try {
            $MDAnnualBudgets = new MDAnnualBudgets();
            $UpdAnnualBudget = $MDAnnualBudgets->find($this->request->getPost('id'));
            $UpdAnnualBudget['year'] = $this->request->getPost('edyears');
            $UpdAnnualBudget['project'] = $this->request->getPost('edproject');
            $UpdAnnualBudget['cg_annualbudget_qt'] = $this->request->getPost('edcg_budget');
            $UpdAnnualBudget['ob_annualbudget_qt'] = $this->request->getPost('edob_budget');
            $UpdAnnualBudget['revision'] = $this->request->getPost('edrevision');
            $UpdAnnualBudget['status'] = $this->request->getPost('edstatus');
            $UpdAnnualBudget['last_update'] = Time::now()->format('Y-m-d H:i:s');

            $MDAnnualBudgets->save($UpdAnnualBudget);

            $message = "Annual Budget has been update";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/master-data/annualbudget/")->with('message', $message);
    }
}
