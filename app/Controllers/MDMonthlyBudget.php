<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use App\Models\MDMonthlyBudgets;

class MDMonthlyBudget extends BaseController
{
    public function index()
    {
        $data['title'] = "Master Data Monthly Budget";

        $db = db_connect();
        $qdata = "select *, (select contractor_name from md_contractors 
                where md_contractors.id = md_monthlybudget.id_contractor limit 1) 
                as nm_contractor from md_monthlybudget";
        $q_gr = $db->query($qdata);
        $data['MonthlyBudgets'] = $q_gr->getResultArray();

        $qstr_contractor = "select id, contractor_name from md_contractors order by contractor_name";
        $q_contractor = $db->query($qstr_contractor);
        $data['contractor'] = $q_contractor->getResultArray();

        $qstr_annbdgt = "select id_annualbudget, project, `year`,
                            CONCAT(id_annualbudget,' - ',project,' - ',`year`) as desc_annualbdgt from md_annualbudget
                            order by `year` desc, project asc";
        $q_annbdgt = $db->query($qstr_annbdgt);
        $data['vannbdgt'] = $q_annbdgt->getResultArray();

        echo view('pages/md-monthlybudget', $data);
    }

    public function add()
    {
        try {
            $project = $this->request->getVar('project');
            $years = $this->request->getVar('years');
            $month = $this->request->getVar('month');
            $contractor_id = $this->request->getVar('contractor');
            $cg_mbudget = $this->request->getVar('cg_mbudget');
            $cg_dbudget = $this->request->getVar('cg_dbudget');
            $ob_mbudget = $this->request->getVar('ob_mbudget');
            $ob_dbudget = $this->request->getVar('ob_dbudget');
            $revision = $this->request->getVar('revision');
            $status = $this->request->getVar('status');
            $id_ann_bdgt = $this->request->getVar('id_ann_bdgt');

            $MDMonthlyBudgets = new MDMonthlyBudgets();
            $MDMonthlyBudgets->save([
                'id_contractor' => $contractor_id,
                'year' => $years,
                'month' => $month,
                'project' => $project,
                'month' => $month,
                'cg_monthlybudget_qt' => $cg_mbudget,
                'ob_monthlybudget_qt' => $ob_mbudget,
                'cg_dailybudget_qt' => $cg_dbudget,
                'ob_dailybudget_qt' => $ob_dbudget,
                'revision' =>  $revision,
                'status' =>  $status,
                'id_annualbudget' =>  $id_ann_bdgt,
                'create_date' => Time::now()->format('Y-m-d H:i:s'),
            ]);

            $message = "Monthly Budget has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/master-data/monthlybudget")->with('message', $message);
    }

    public function get($id)
    {
        header('Content-Type: application/json');
        $MDMonthlyBudgets = new MDMonthlyBudgets();
        $MDMonthlyBudgets = $MDMonthlyBudgets->find($id);
        return $this->response->setJSON($MDMonthlyBudgets);
    }

    public function update()
    {
        try {
            // fields :
            // 'id_contractor','year','month','project','cg_monthlybudget_qt',
            // 'ob_monthlybudget_qt','cg_dailybudget_qt','ob_dailybudget_qt',
            // 'create_date','last_update','revision','status','id_annualbudget'

            $MDMonthlyBudgets = new MDMonthlyBudgets();
            $UpdMonthlyBudget = $MDMonthlyBudgets->find($this->request->getPost('id'));
            $UpdMonthlyBudget['year'] = $this->request->getPost('edyears');
            $UpdMonthlyBudget['project'] = $this->request->getPost('edproject');
            $UpdMonthlyBudget['id_contractor'] = $this->request->getPost('edcontractor');
            $UpdMonthlyBudget['month'] = $this->request->getPost('edmonth');
            $UpdMonthlyBudget['cg_monthlybudget_qt'] = $this->request->getPost('edcg_mbudget');
            $UpdMonthlyBudget['ob_monthlybudget_qt'] = $this->request->getPost('edob_mbudget');
            $UpdMonthlyBudget['cg_dailybudget_qt'] = $this->request->getPost('edcg_dbudget');
            $UpdMonthlyBudget['ob_dailybudget_qt'] = $this->request->getPost('edob_dbudget');
            $UpdMonthlyBudget['revision'] = $this->request->getPost('edrevision');
            $UpdMonthlyBudget['status'] = $this->request->getPost('edstatus');
            $UpdMonthlyBudget['id_annualbudget'] = $this->request->getPost('edid_ann_bdgt');
            $UpdMonthlyBudget['last_update'] = Time::now()->format('Y-m-d H:i:s');

            $MDMonthlyBudgets->save($UpdMonthlyBudget);

            $message = "Monthly Budget has been update";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/master-data/monthlybudget/")->with('message', $message);
    }
}
