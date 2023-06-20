<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\K3lhReport;
use App\Models\Type;
use App\Models\Category;
use App\Models\budgetcsr;
use CodeIgniter\I18n\Time;
use App\Models\NewAllocation;
use App\Models\DocReminder as DocReminderModel;

class CSRBudget extends BaseController
{
    public function index()
    {
        $data['title'] = "CSR - Budget";

        $bulan = $_GET['bulan'] ??false;
        $tahun = $_GET['tahun']??false;

        $budgetcsr = new budgetcsr();
        $builder = $budgetcsr->builder();
        $builder->select("t_csrbudget.*, new_allocation.allocation")
                ->join('new_allocation', 't_csrbudget.allocation = new_allocation.id_allo')
                ->where("t_csrbudget.deletion_status", "0");
            if($bulan && $tahun){
                // dd($bulan, $tahun);
                $builder->where("period_month", $bulan);
                $builder->where("period_year", $tahun);
                $data['budgetcsr']=$builder->get()->getResultArray();
            }else{
                $data['budgetcsr']=$builder->get()->getResultArray();
            }
                


        

        $Type = new Type();
        $data['type'] = $Type->findAll(); 

        $data['today'] = Time::now()->format('Y-m-d');

        $new_allo = new NewAllocation();
        $builder = $new_allo->builder();
        $builder->select("new_allocation.*")
                ->where("new_allocation.Deletion_status", "0");
        $data['new_allo'] = $builder->get()->getResultArray();

        
        $budgetcsr = new budgetcsr();
           
        $data['year'] = range(date('Y'), date('Y') - 5);
        //dd($data);



        echo view('pages/CSR-Budget', $data);
    }

    public function action($action)
    {
        $Category = new Category();

        $categorydata = $Category->where('id_type', $action)->findAll();

        //dd($categorydata);
        echo json_encode($categorydata);
    }    

    private function generateId()
    {
        $budgetcsr = new budgetcsr();
        $builder = $budgetcsr->builder();
        $builder->select('MAX(doc_no) as max_id');
        
        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "CSR-B"  . "0001";
            return $new_id;
        } else {
            $new_id = "CSR-B" . str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function add()
    {
        try {
            $bulan = (int) $this->request->getVar('bulan');
            $tahun = (int) $this->request->getVar('tahun');
            $allocation = $this->request->getVar('allocation');
            $formtyp_bdg = $this->request->getVar('formtyp_bdg');
            $budget_amount = $this->request->getVar('budget_amount');
            //dd($tahun, $bulan);
    
            $generate_id = $this->generateId();
    
            //$created_at = Time::now();
            $budgetcsr = new budgetcsr();
            $budgetcsr->save([
                'doc_no' => $generate_id,
                'period_month' => $bulan,
                'period_year' => $tahun,
                'allocation' => $allocation,
                'formtyp_bdg' => $formtyp_bdg,
                'budget_amount' => $budget_amount,
                'create_by' => session()->get('username'),
                'create_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);

           

            $message = "CSR Budget has been created";
        } catch(\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/CSRBudget")->with('message', $message);
    }

    public function monitoring()
    {
        $K3lhReport = new K3lhReport();
        $builder = $K3lhReport->builder();
        $data['K3lhReport'] = $builder->select('t_k3lh.*, t_type.id_type, t_type.type as type_text, t_category.*')
                                      ->join('t_type', 't_type.id_type = t_k3lh.Type')
                                      ->join('t_category', 't_category.Id_category = ty_category')->get()->getResultArray();
        $Type = new Type();
        $data['type'] = $Type->findAll();
        
        $Category = new Category();
        $data['category'] = $Category->findAll();
        // dd($data);

        $data['title'] = "SHE Accident Monitoring";
        $DocReminderModel = new DocReminderModel();
        $builder = $DocReminderModel->builder();
        $undelivered = $builder
            ->select('MONTHNAME(remind_on) AS month, COUNT(1) AS val')
            ->where('deletion_status', '0')
            ->where('email_status', 'undelivered')
            ->where('YEAR(remind_on)', date('Y'))
            ->groupBy('MONTHNAME(remind_on)');
        $data['undelivered'] = json_encode($undelivered->get()->getResultArray());
        $delivered = $builder
            ->select('MONTHNAME(remind_on) AS month, COUNT(1) AS val')
            ->where('deletion_status', '0')
            ->where('email_status', 'delivered')
            ->where('YEAR(remind_on)', date('Y'))
            ->groupBy('MONTHNAME(remind_on)');
        $data['delivered'] = json_encode($delivered->get()->getResultArray());

        $total = $builder
            ->select('MONTHNAME(remind_on) AS month, COUNT(1) AS val')
            ->where('deletion_status', '0')
            ->where('YEAR(remind_on)', date('Y'))
            ->groupBy('MONTHNAME(remind_on)');
        $data['total'] = json_encode($total->get()->getResultArray());

        $data['total_count'] = $builder->where('deletion_status', '0')->countAllResults();
        $data['undelivered_count'] = $builder->where('deletion_status', '0')->where('email_status', 'undelivered')->countAllResults();
        $data['delivered_count'] = $builder->where('deletion_status', '0')->where('email_status', 'delivered')->countAllResults();

        $data['year'] = range(date('Y'), date('Y') - 4);

        echo view('pages/she-accident-monitoring', $data);
    }


    public function delete($id)
    {
        $budgetcsr = new budgetcsr();
        //dd($id);             
        $budgetcsr->update($id,
        [
            'deletion_status' => '1',
            'change_by' => session()->get('username'),
            'change_on'=> Time::now()->format('Y-m-d H:i:s')
        ]);   
        return redirect()->to("/CSRBudget")->with('message', 'CSR Budget has been deleted');
        
    }

    public function edit($id)
    {
        $data['title'] = "Edit - CSR Budget";
        
        
        $budgetcsr = new budgetcsr();
        $builder = $budgetcsr->builder();
        $builder->select("t_csrbudget.*, new_allocation.allocation")
                ->join("new_allocation", "t_csrbudget.allocation = new_allocation.id_allo");
        $builder->where("id", $id);
        $data['budgetcsr'] = $builder->get()->getRowArray();
        //dd($data);


        $new_allo = new NewAllocation();
        $builder = $new_allo->builder();
        $builder->select("new_allocation.*");
        $data['new_allo'] = $builder->get()->getResultArray();

        
        $data['year'] = range(date('Y'), date('Y') - 5);
           

        echo view('pages/CSR-Budget-edit', $data);
    }


    public function update()
    {
        $bulan = (int) $this->request->getVar('bulan');
        $tahun = (int) $this->request->getVar('tahun');
        $allocation = $this->request->getVar('allocation');  
        $formtyp_bdg = $this->request->getVar('formtyp_bdg');         
        $budget_amount = $this->request->getVar('budget_amount');
        $id = $this->request->getVar('id');
        $budgetcsr = new budgetcsr();
        $budgetcsr->update($id, [
                'period_month' => $bulan,
                'period_year' => $tahun,
                'allocation' => $allocation,
                'formtyp_bdg' => $formtyp_bdg,
                'budget_amount' => $budget_amount,
                'change_by' => session()->get('username'),
                'change_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);
            
        return redirect()->to("/CSRBudget")->with('message', 'CSR Budget has been updated');
    }

    public function typeform($typ)
    {
        if ($typ == 'EKSTERNAL'){
            $sql_where = "%EKSTERNAL%";
        } else if ($typ == 'INTERNAL'){
            $sql_where = "%INTERNAL%";
        } else {
            $query = "SELECT * FROM t_csrbudget";
            return $query;  
        }
        
        $query = "SELECT * FROM t_csrbudget WHERE formtyp_bdg LIKE $sql_where";
        return $query;

        $query = "SELECT * FROM t_csractivity WHERE formtyp_act LIKE $sql_where";
        return $query;
    }
}
