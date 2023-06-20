<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Timesheets;
use App\Models\TimesheetLogs;
use App\Models\Contractors;
use App\Models\OverburdenS;
use CodeIgniter\I18n\Time;
use Config\Database;
use CodeIgniter\API\ResponseTrait;

class Overburden extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $db = Database::connect();

        $data['title'] = "Production - Overburden";
         $contractor_id = session()->get("contractor_id");

        $overburnden = new Timesheets();
        $ok = array('1', '2', '3');
        if ($contractor_id == 0) {
            $builder = $overburnden->builder();
            $builder->select('timesheets.*, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mc.contractor_name');
            $builder->join('md_monthlybudget mm', 'mm.id_monthlybudget = timesheets.Id_monthlybudget');
            $builder->join('md_contractors mc', 'mc.id = timesheets.Id_contractor');
            $builder->where("deleted_at IS NULL");
            $builder->whereIn("mc.id", $ok);
            $builder->orderBy("timesheets.id");
            $data['overburnden'] = $builder->get()->getResultArray();
        } else {
            $builder = $overburnden->builder();
            $builder->select('timesheets.*, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mc.contractor_name');
            $builder->join('md_monthlybudget mm', 'mm.id_monthlybudget = timesheets.Id_monthlybudget');
            $builder->join('md_contractors mc', 'mc.id = timesheets.Id_contractor');
            $builder->where("deleted_at IS NULL");
            $builder->where('mc.id', session()->get('contractor_id'));
            $builder->orderBy("timesheets.id");
            $data['overburnden'] = $builder->get()->getResultArray();
        }
        
        
        //dd($data['overburnden']);
        $Contractors = new Contractors();
        $ok = array('1', '2', '3');
        if ($contractor_id == 0) {
            $data['contractors'] = $Contractors->where('contractor_type', 'timesheet')
                ->whereIn('id', $ok)->findAll();
        } else {
            $data['contractors'] = $Contractors->where('id', session()->get('contractor_id'))->findAll();
        }

        $data['today'] = Time::now()->format('Y-m-d');
        
       //- dd($data['contractors']);

        echo view('pages/Operation-overburden', $data);
    }
    


    private function generateId($date_production)
    {
        $Timesheets = new Timesheets();
        $builder = $Timesheets->builder();
        $builder->select('MAX(prd_code) as max_id');
        $builder->where('prd_date', $date_production);
        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "TM-" . $date_production . "-0001";
            return $new_id;
        } else {
            $new_id = "TM-" . $date_production . "-" . str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function add()
    {
        try {

            $date_production = $this->request->getVar('date_production');
            $id_contractor = $this->request->getVar('Id_contractor');
            $ob_qty = $this->request->getVar('ob_qty');
            $distanceob_qty = $this->request->getVar('distanceob_qty');
            $parsed_prd = Time::parse($date_production);
            $month = $parsed_prd->getMonth();
            $year = $parsed_prd->getYear();
            if ($parsed_prd->getDay() > 25 && $parsed_prd->getMonth() < 12 ) {
                $month++;
            } 
            $status = 'approved';
            // else if ($parsed_prd->getDay() > 25 && $parsed_prd->getMonth() == 12) {
            //     $month = 1;
            //     $year++;
            // }

           // dd($date_production);

            $db = \Config\Database::connect();
            $builder = $db->table('md_monthlybudget');
            $builder->select('md_monthlybudget.id_monthlybudget, md_annualbudget.id_annualbudget, ob_dailybudget_qt, cg_dailybudget_qt');
            $builder->join('md_annualbudget', 'md_annualbudget.id_annualbudget = md_monthlybudget.id_annualbudget');
            $builder->where("md_monthlybudget.month = $month AND md_monthlybudget.year = $year
                AND id_contractor = $id_contractor");
            $budget = $builder->get()->getRowArray();
            //dd($budget);

            $db = \Config\Database::connect();
            $builder = $db->table('md_monthly_disob');
            $builder->select('id_monthlybudget_disob');
            $builder->where("md_monthly_disob.month = $month AND md_monthly_disob.year = $year
            AND id_contractor = $id_contractor");
            $disob = $builder->get()->getRowArray();
            
            if (is_null($budget)) {
                throw new \Exception("Budget not found, please select another month");
            }

            $generate_id = $this->generateId($date_production);
            $created_at = Time::now();
            $overburnden = new Timesheets();
            $overburnden->save([
                'prd_date' => $date_production,
                'prd_code' => $generate_id,
                'id_contractor' => $id_contractor,
                'prd_ob_day_qty' => $ob_qty,
                'prd_ob_total' => $ob_qty,
                'created_at' => $created_at,
                'prd_ob_distance' => $distanceob_qty,
                'prd_cg_distance' => 0,
                'id_monthlybudget' => $budget['id_monthlybudget'] ?? null,
                'id_annualbudget' => $budget['id_annualbudget'] ?? null,
                'id_monthlybudget_disob' =>$disob['id_monthlybudget_disob'] ?? null,
                'prd_ob_night_qty' => 0,
                'prd_cg_day_qty' => 0,
                'prd_cg_night_qty' => 0,
                'prd_cg_total' => 0,
                'prd_sr' => 0,
                'prd_rain' => 0,
                'prd_slip' => 0,
                'prd_%' => 0,
                'noted' => 'kosong',
                'status' => $status,
                'created_at' => 0,
                "prd_rainfall" => 0,
                "prd_revision" => 4,
            ]);
            $message = "Overburden has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/contractor-ob")->with('message', $message);
    }

    public function delete($id_overburden)
    {
        $Timesheets = new Timesheets();
        $found = $Timesheets->find($id_overburden);
        if ($found['status'] == 'draft') {
            $Timesheets->delete($id_overburden);
            return redirect()->to("contractor-ob")->with('message', 'A timesheet has been deleted');
        } else {
            return redirect()->to("contractor-ob")->with('message', 'You cannot delete a timesheet that has been used, please contact your administrator');
        }
    }

    public function edit($code)
    {

        $overburnden = new Timesheets();
        $builder = $overburnden->builder();
        $builder->select('timesheets.*, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mc.contractor_name');
        $builder->join('md_monthlybudget mm', 'mm.id_monthlybudget = timesheets.Id_monthlybudget');
        $builder->join('md_contractors mc', 'mc.id = timesheets.Id_contractor');
        $builder->where('timesheets.prd_code', $code);
        $data['overburnden'] = $builder->get()->getRowArray();
        $data['title'] = "Operation - Overburden";

        $TimesheetLogs = new TimesheetLogs();
        $builder = $TimesheetLogs->builder();
        $builder->select("*");
        $builder->where("prd_code", $code);
        $builder->orderBy("created_at", "DESC");
        $data['timesheet_logs'] = $builder->get()->getResultArray();

        $contractor_id = session()->get("contractor_id");
        $Contractors = new Contractors();
        $ok = array('1', '2', '3');
        if ($contractor_id == 0) {
        $data['contractors'] = $Contractors->where('contractor_type', 'timesheet')
                                           ->whereIn('id', $ok)->findAll();
        }else{
            $data['contractors'] = $Contractors->where('id', session()->get('contractor_id'))->findAll();
        }

        //dd($data);

        $data['today'] = Time::now()->format('Y-m-d');

        //dd($data);

        
        echo view('pages/overburden-edit', $data);
    }

    public function update()

    {
        $db = Database::connect();
        $id = $this->request->getVar('id');
        $Timesheets = $db->query("SELECT * FROM timesheets
                                WHERE id = $id")->getRowArray();
        //dd($Timesheets);

            $date_production = $this->request->getVar('date_production');
            $id_contractor = $this->request->getVar('Id_contractor');
            $ob_qty = $this->request->getVar('ob_qty');
            $distanceob_qty = $this->request->getVar('distanceob_qty');
            $prd_code = $this->request->getVar('prdCode');
            $last_update = Time::now();
            $overburnden = new Timesheets();
            $overburnden->update($id, [
                'prd_date' => $date_production,
                'id_contractor' => $id_contractor,
                'prd_ob_total' => $ob_qty,
                'last_update' => $last_update,
                'prd_ob_distance' => $distanceob_qty,
                'prd_code' => $prd_code,
                'prd_ob_day_qty' => $Timesheets['prd_ob_day_qty'],
                'prd_ob_night_qty' => $Timesheets['prd_ob_night_qty'],
                'prd_cg_day_qty' =>  $Timesheets['prd_cg_day_qty'],
                'prd_cg_night_qty' => $Timesheets['prd_cg_night_qty'],
                'prd_cg_total' => $Timesheets['prd_cg_total'],
                'prd_sr' => $Timesheets['prd_sr'],
                'prd_rain' => $Timesheets['prd_rain'],
                'prd_slip' => $Timesheets['prd_slip'],
                'prd_%' => $Timesheets['prd_%'],
                'noted' => $Timesheets['noted'],
                'status' => $Timesheets['status'],
                'create_date' => $Timesheets['create_date'],
                'prd_rainfall' => $Timesheets['prd_rainfall'],
                'prd_revision' => $Timesheets['prd_revision'],
                'prd_cg_distance' => $Timesheets['prd_cg_distance'],
                
            ]);

        return redirect()->to("/contractor-ob")->with('message', 'A Overburden has been updated');
    }

    // #Tempcode Malik ==
    public function get()
    {
        $dari_tanggal = $_GET['dari_tanggal'];
        $sampai_tanggal = $_GET['sampai_tanggal'];
        $Timesheets = new Timesheets();
        $builder = $Timesheets->builder();
        $builder->select('timesheets.id,
        timesheets.prd_date,
        timesheets.prd_cg_total as cg_total,
        timesheets.prd_ob_total as ob_total,
        timesheets.status,
        ');
        $builder->where("timesheets.prd_date BETWEEN '$dari_tanggal' AND '$sampai_tanggal' AND deleted_at IS NULL");
        $data = $builder->get()->getResultArray();
        return $this->respond($data, 200);
    }
    // == #Tempcode Malik
}
