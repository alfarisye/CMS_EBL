<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Type;
use App\Models\Category;
use App\Models\budgetcsr;
use App\Models\Activitycsr;
use App\Models\NewAllocation;
use CodeIgniter\I18n\Time;

class CSRAct extends BaseController
{
    public function index()
    {
        $data['title'] = "CSR - Activity";

        $tgl_mulai = $_GET['tgl_awal'] ?? false;
        $tgl_akhir = $_GET['tgl_akhir'] ?? false;
       

        $Activitycsr = new Activitycsr();
        $builder = $Activitycsr->builder();
        $builder->select("t_csractivity.*, new_allocation.allocation")
                ->join('new_allocation', 't_csractivity.allocation = new_allocation.id_allo')
                ->where("t_csractivity.deletion_status", "0");
        if ($tgl_mulai && $tgl_akhir) {
            $builder->where("t_csractivity.date BETWEEN '$tgl_mulai' AND '$tgl_akhir'");
            $data['Activitycsr'] = $builder->get()->getResultArray();
        } else {
            $data['Activitycsr'] = $builder->get()->getResultArray();
        }
        //dd($data);

        $Type = new Type();
        $data['type'] = $Type->findAll();

        $new_allo = new NewAllocation();
        $builder = $new_allo->builder();
        $builder->select("new_allocation.*")
                ->where("new_allocation.Deletion_status", "0");
        $data['new_allo'] = $builder->get()->getResultArray();


        $data['today'] = Time::now()->format('Y-m-d');
     
        $data['year'] = range(date('Y'), date('Y') - 5);

        echo view('pages/CSR-Activity', $data);
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
        $Activitycsr = new Activitycsr();
        $builder = $Activitycsr->builder();
        $builder->select('MAX(doc_no) as max_id');

        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "CSR-A"  . "0001";
            return $new_id;
        } else {
            $new_id = "CSR-A" . str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function add()
    {
        $validationRule = [
            'userfile' => [
                'label' => 'File Excel',
                'rules' => 'uploaded[userfile]'
                    . '|mime_in[userfile,application/vnd.ms-excel,application/zip,application/x-7z-compressed,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.rar]'
                    . '|max_size[userfile,5028]'
            ],
        ];
        if (!$this->validate($validationRule)) {
            // dd($this->validator);
            return redirect()->to("/CSRAct")->with('message', implode(";", $this->validator->getErrors()));
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
            $date = $this->request->getVar('date');
            $allocation = $this->request->getVar('allocation');
            $formtyp_act = $this->request->getVar('formtyp_act') ?? null;
            $location = $this->request->getVar('location');
            $activity = $this->request->getVar('activity');
            $cost = $this->request->getVar('actual_cost');
            $Remark = $this->request->getVar('Remark');


            $generate_id = $this->generateId();


            $Activitycsr = new Activitycsr();
            $Activitycsr->save([
                'doc_no' => $generate_id,
                'date' => $date,
                'allocation' => $allocation,
                'formtyp_act' => $formtyp_act,
                'location' => $location,
                'activity' => $activity,
                'actual_cost' => $cost,
                'Remark' => $Remark,
                'upload_file_path' => $filepath,
                'upload_file_name' => $original_filename,
                'upload_file_type' => $original_ext,
                'create_by' => session()->get('username'),
                'create_on' => Time::now()->format('Y-m-d H:i:s')
            ]);



            $message = "CSR Activity has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/CSRAct")->with('message', $message);
    }

    public function download($id)
    {
        try {
            $Activitycsr = new Activitycsr();
            $Activitycsr = $Activitycsr->where('id', $id)->first();
            $filepath = $Activitycsr['upload_file_path'];
            $filename = $Activitycsr['upload_file_name'];
            return $this->response->download($filepath, null)->setFileName($filename);
        } catch (\Throwable $th) {
            return redirect()->to("/CSRAct")->with('message', $th->getMessage());
        }
    }



    public function dashboard()
    {
        // $data['actual_pendidikan']
        // $data['actual_kesehatan']
        // $data['actual_kemandirian']
        // $data['actual_sosbud']
        // $data['actual_pembangunan']
        /**
         *  1 => "Pendidikan",
         *  2 => "Kesehatan",
         *  3 => "Kemandirian Ekonomi",
         *  4 => "Sosial Budaya",
         *  5 => "Pembangunan Fisik",
         */
        //$act_ppn = $_GET['$act_ppn'] ;
       $act_ppn = $_GET['parameter']?? false; 

         //variabel untuk activity allocation
        $pendidikan_act = new Activitycsr();
        $builder = $pendidikan_act->builder();
        $pendidikan_act = $builder->select("SUM(actual_cost) AS total")
                                  ->where("t_csractivity.allocation", 1)
                                  ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
                                  ->where("t_csractivity.Deletion_status", 0)->get()->getRowArray();
        //  dd($data);                                           
        $kesehatan_act = new Activitycsr();
        $builder = $kesehatan_act->builder();
        $kesehatan_act = $builder->select("SUM(actual_cost) AS total")
                                 ->where("t_csractivity.allocation", 2)
                                 ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
                                 ->where("t_csractivity.Deletion_status", 0)->get()->getRowArray();          
        $ekonomi_act = new Activitycsr();
        $builder = $ekonomi_act->builder();
        $ekonomi_act = $builder->select("SUM(actual_cost) AS total")
                               ->where("t_csractivity.allocation", 3)
                               ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
                               ->where("t_csractivity.Deletion_status", 0)->get()->getRowArray();
        $sosbud_act = new Activitycsr();
        $builder = $sosbud_act->builder();
        $sosbud_act = $builder->select("SUM(actual_cost) AS total")
                              ->where("t_csractivity.allocation", 4)
                              ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
                              ->where("t_csractivity.Deletion_status", 0)->get()->getRowArray();
        $pembangunan_act = new Activitycsr();
        $builder = $pembangunan_act->builder();
        $pembangunan_act = $builder->select("SUM(actual_cost) AS total")
                                  ->where("t_csractivity.allocation", 5)
                                  ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
                                  ->where("t_csractivity.Deletion_status", 0)->get()->getRowArray();

        //variabel untuk budget allocation
        $pendidikan_bdg = new budgetcsr();
        $builder = $pendidikan_bdg->builder();
        $pendidikan_bdg = $builder->select("SUM(budget_amount) AS budget")
                                          ->where("t_csrbudget.allocation", 1)
                                          ->where("t_csrbudget.formtyp_bdg LIKE '%$act_ppn%'")
                                          ->where("t_csrbudget.Deletion_status", 0)->get()->getRowArray();          
        $kesehatan_bdg = new budgetcsr();
        $builder = $kesehatan_bdg->builder();
        $kesehatan_bdg = $builder->select("SUM(budget_amount) AS budget")
                                 ->where("t_csrbudget.allocation", 2)
                                 ->where("t_csrbudget.formtyp_bdg LIKE '%$act_ppn%'")
                                 ->where("t_csrbudget.Deletion_status", 0)->get()->getRowArray();
        $ekonomi_bdg = new budgetcsr();
        $builder = $ekonomi_bdg->builder();
        $ekonomi_bdg = $builder->select("SUM(budget_amount) AS budget")
                               ->where("t_csrbudget.allocation", 3)
                               ->where("t_csrbudget.formtyp_bdg LIKE '%$act_ppn%'")
                               ->where("t_csrbudget.Deletion_status", 0)->get()->getRowArray();
        $sosbud_bdg = new budgetcsr();
        $builder = $sosbud_bdg->builder();
        $sosbud_bdg = $builder->select("SUM(budget_amount) AS budget")
                              ->where("t_csrbudget.allocation", 4)
                              ->where("t_csrbudget.formtyp_bdg LIKE '%$act_ppn%'")
                              ->where("t_csrbudget.Deletion_status", 0)->get()->getRowArray();
        $pembangunan_bdg = new budgetcsr();
        $builder = $pembangunan_bdg->builder();
        $pembangunan_bdg = $builder->select("SUM(budget_amount) AS budget")
                                   ->where("t_csrbudget.allocation", 5)
                                   ->where("t_csrbudget.formtyp_bdg LIKE '%$act_ppn%'")
                                   ->where("t_csrbudget.Deletion_status", 0)->get()->getRowArray();                                                            

        $Activitycsr = new Activitycsr();
        $builder = $Activitycsr->builder();
        $data['actual_pendidikan'] = $pendidikan_act + $pendidikan_bdg;
        $data['actual_kesehatan'] = $kesehatan_act + $kesehatan_bdg;
        $data['actual_kemandirian'] = $ekonomi_act + $ekonomi_bdg;
        $data['actual_sosbud'] = $sosbud_act + $sosbud_bdg;
        $data['actual_pembangunan'] = $pembangunan_act + $pembangunan_bdg;
        $data['actual_activity'] = $Activitycsr->where("Deletion_status", 0)
                                                ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")->countAllResults();
                                              
        $data['total_location'] = $Activitycsr->select("location")
                                              ->where("Deletion_status", 0)
                                              ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
                                              ->groupBy('location')->countAllResults();
        $data['total_cost'] = $builder->select("SUM(actual_cost) as total")
                                      ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
                                      ->where("Deletion_status", 0)->get()->getRowArray();

        $data['title'] = "CSR Monitoring";

        $data['actual_data'] = json_encode($builder->select("SUM(actual_cost) as total, 
            CASE 
                WHEN allocation = 1 THEN 'Pendidikan' 
                WHEN allocation = 2 THEN 'Kesehatan' 
                WHEN allocation = 3 THEN 'Kemandirian Ekonomi' 
                WHEN allocation = 4 THEN 'Sosial Budaya' 
                WHEN allocation = 5 THEN 'Pembangunan Fisik' 
            END AS allocation")
            ->where("Deletion_status", 0)
            ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
            ->where("YEAR(date) = YEAR(NOW())")
            ->groupBy("allocation")
            ->get()->getResultArray());
        $data['cost_location'] = json_encode($builder->select("SUM(actual_cost) as total, 
            location")
            ->where("Deletion_status", 0)
            ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
            ->where("YEAR(date) = YEAR(NOW())")
            ->groupBy("location")
            ->get()->getResultArray());

        $CSRBudget = new budgetcsr();
        $builder = $CSRBudget->builder();
        $data['plan_data'] = json_encode($builder->select("SUM(budget_amount) as total, 
            CASE 
                WHEN allocation = 1 THEN 'Pendidikan' 
                WHEN allocation = 2 THEN 'Kesehatan' 
                WHEN allocation = 3 THEN 'Kemandirian Ekonomi' 
                WHEN allocation = 4 THEN 'Sosial Budaya' 
                WHEN allocation = 5 THEN 'Pembangunan Fisik' 
            END AS allocation")
            ->where("Deletion_status", 0)
            ->where("t_csrbudget.formtyp_bdg LIKE '%$act_ppn%'")
            ->where("period_year = YEAR(NOW())")
            ->groupBy("allocation")
            ->get()->getResultArray());

        $data['year'] = range(date('Y'), date('Y') - 4);

        echo view('pages/CSR-dashboard', $data);
    }

    public function getJson($year, $month)
    {

        $act_ppn = $_GET['parameter']?? false; 
        $Activitycsr = new Activitycsr();
        $builder = $Activitycsr->builder();
        $data['actual_data'] = ($builder->select("SUM(actual_cost) as total, 
            CASE 
                WHEN allocation = 1 THEN 'Pendidikan' 
                WHEN allocation = 2 THEN 'Kesehatan' 
                WHEN allocation = 3 THEN 'Kemandirian Ekonomi' 
                WHEN allocation = 4 THEN 'Sosial Budaya' 
                WHEN allocation = 5 THEN 'Pembangunan Fisik' 
            END AS allocation")
            ->where("Deletion_status", 0)
            ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
            ->where("YEAR(date) = $year")
            ->where("MONTH(date) = $month")
            ->groupBy("allocation")
            ->get()->getResultArray());
        $data['cost_location'] = ($builder->select("SUM(actual_cost) as total, 
            location")
            ->where("Deletion_status", 0)
            ->where("t_csractivity.formtyp_act LIKE '%$act_ppn%'")
            ->where("YEAR(date) = $year")
            ->where("MONTH(date) = $month")
            ->groupBy("location")
            ->get()->getResultArray());

        $CSRBudget = new budgetcsr();
        $builder = $CSRBudget->builder();
        $data['plan_data'] = ($builder->select("SUM(budget_amount) as total, 
            CASE 
                WHEN allocation = 1 THEN 'Pendidikan' 
                WHEN allocation = 2 THEN 'Kesehatan' 
                WHEN allocation = 3 THEN 'Kemandirian Ekonomi' 
                WHEN allocation = 4 THEN 'Sosial Budaya' 
                WHEN allocation = 5 THEN 'Pembangunan Fisik' 
            END AS allocation")
            ->where("Deletion_status", 0)
            ->where("t_csrbudget.formtyp_bdg LIKE '%$act_ppn%'")
            ->where("period_year = $year")
            ->where("period_month = $month")
            ->groupBy("allocation")
            ->get()->getResultArray());

        return $this->response->setJSON($data);
    }


    public function delete($id)
    {
        $Activitycsr = new Activitycsr();
        //dd($id);             
        $Activitycsr->update(
            $id,
            [
                'deletion_status' => '1',
                'change_by' => session()->get('username'),
                'change_on' => Time::now()->format('Y-m-d H:i:s')
            ]
        );
        return redirect()->to("/CSRAct")->with('message', 'CSR Activity has been deleted');
    }

    public function edit($id)
    {
        $data['title'] = "Edit - CSR Activity";


        $Activitycsr = new Activitycsr();
        $builder = $Activitycsr->builder();
        $builder->select("t_csractivity.*, new_allocation.allocation")
                ->join('new_allocation', 't_csractivity.allocation = new_allocation.id_allo');
        $builder->where("id", $id);
        $data['Activitycsr'] = $builder->get()->getRowArray();

        $new_allo = new NewAllocation();
        $builder = $new_allo->builder();
        $builder->select("new_allocation.*");
        $data['new_allo'] = $builder->get()->getResultArray();

        echo view('pages/CSR-Activity-edit', $data);
    }


    public function update()
    {
        $doc_file = $this->request->getFile('userfile');
        $validationRule = [
            'userfile' => [
                'label' => 'File Excel',
                'rules' => 'mime_in[userfile,application/vnd.ms-excel,application/zip,application/x-7z-compressed,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.rar]'
                    . '|max_size[userfile,5028]'

            ],
        ];
        if (!$this->validate($validationRule)) {
            return redirect()->back()->with('message', implode(";", $this->validator->getErrors()));
        }

        $date = $this->request->getVar('date');
        $allocation = $this->request->getVar('allocation');
        $formtyp_act = $this->request->getVar('formtyp_act') ?? null;
        $location = $this->request->getVar('location');
        $activity = $this->request->getVar('activity');
        $cost = $this->request->getVar('actual_cost');
        $Remark = $this->request->getVar('Remark');
        $id = $this->request->getVar('id');

        if ($doc_file->isValid()) {
            $original_filename = $doc_file->getName();
            $original_ext = $doc_file->getExtension();
            if (!$doc_file->hasMoved()) {
                $filepath = WRITEPATH . 'uploads/' . $doc_file->store();
            } else {
                $filepath = "";
            }
            try {
                $Activitycsr = new Activitycsr();
                $Activitycsr->update($id, [
                    'date' => $date,
                    'allocation' => $allocation,
                    'location' => $location,
                    'activity' => $activity,
                    'actual_cost' => $cost,
                    'Remark' => $Remark,
                    'upload_file_path' => $filepath,
                    'upload_file_name' => $original_filename,
                    'upload_file_type' => $original_ext,
                    'updated_by' => session()->get('username'),
                    'updated_on' => Time::now()->format('Y-m-d H:i:s'),
                ]);
                $message = "CSR Activity successfully updated";
            } catch (\Throwable $th) {
                $message = $th->getMessage();
            }
            return redirect()->to("/CSRAct")->with('message', $message);
        }
        try {
            $Activitycsr = new Activitycsr();
            $Activitycsr->update($id, [
                'date' => $date,
                'allocation' => $allocation,
                'formtyp_act' => $formtyp_act,
                'location' => $location,
                'activity' => $activity,
                'actual_cost' => $cost,
                'Remark' => $Remark,
                'updated_by' => session()->get('username'),
                'updated_on' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            $message = "Document Reminder successfully updated";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/CSRAct")->with('message', $message);
    }

    // public function typeform($typ)
    // {
    //     if ($typ == '$act_ppn'){
    //         $sql_where = "%$act_ppn%";
    //     } else if ($typ == '$act_ppn'){
    //         $sql_where = "%$act_ppn%";
    //     } else {
    //         $query = "SELECT * FROM t_csrbudget";
    //         return $query;  
    //     }
        
    //     $query = "SELECT * FROM t_csrbudget WHERE formtyp_bdg LIKE $sql_where";
    //     return $query;

    //     $query = "SELECT * FROM t_csractivity WHERE formtyp_act LIKE $sql_where";
    //     return $query;
    // }
}
