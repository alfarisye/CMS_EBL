<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Timesheets;
use App\Models\TimesheetLogs;
use App\Models\Contractors;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;

class Timesheet extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $data['title'] = "Production - Timesheet";

        $Timesheets = new Timesheets();
        $builder = $Timesheets->builder();
        $builder->select('timesheets.*, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mc.contractor_name');
        $builder->join('md_monthlybudget mm', 'mm.id_monthlybudget = timesheets.id_monthlybudget');
        $builder->join('md_contractors mc', 'mc.id = timesheets.id_contractor');
        $builder->where("deleted_at IS NULL");
        $data['timesheets'] = $builder->get()->getResultArray();

        $Contractors = new Contractors();
        $data['contractors'] = $Contractors->where('contractor_type', 'timesheet')->findAll();

        $data['today'] = Time::now()->format('Y-m-d');
        //dd($data);

        echo view('pages/timesheet-table', $data);
    }
    


    private function generateId($prd_date)
    {
        $Timesheets = new Timesheets();
        $builder = $Timesheets->builder();
        $builder->select('MAX(prd_code) as max_id');
        $builder->where('prd_date', $prd_date);
        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "TM-" . $prd_date . "-0001";
            return $new_id;
        } else {
            $new_id = "TM-" . $prd_date . "-" . str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function add()
    {
        try {
            $prd_date = $this->request->getVar('prd_date');
            $id_contractor = $this->request->getVar('id_contractor');
            $prd_ob_day_qty = $this->request->getVar('prd_ob_day_qty');
            $prd_ob_night_qty = $this->request->getVar('prd_ob_night_qty');
            $prd_cg_day_qty = $this->request->getVar('prd_cg_day_qty');
            $prd_cg_night_qty = $this->request->getVar('prd_cg_night_qty');
            $prd_ob_total = $prd_ob_day_qty + $prd_ob_night_qty;
            $prd_cg_total = $prd_cg_day_qty + $prd_cg_night_qty;
            $prd_sr = round(handleDivision($prd_ob_total, $prd_cg_total), 2);
            $prd_rain = $this->request->getVar('prd_rain');
            $prd_slip = $this->request->getVar('prd_slip');
            $prd_ob_distance = $this->request->getVar('prd_ob_distance');
            $prd_cg_distance = $this->request->getVar('prd_cg_distance');
            $prd_percentage = $this->request->getVar('prd_%');
            $noted = $this->request->getVar('noted');
            $prd_rainfall = $this->request->getVar('prd_rainfall');
            $status = 'draft';

            $parsed_prd = Time::parse($prd_date);
            $month = $parsed_prd->getMonth();
            $year = $parsed_prd->getYear();
            if ($parsed_prd->getDay() > 25 && $parsed_prd->getMonth() < 12 ) {
                $month++;
            } 
            // else if ($parsed_prd->getDay() > 25 && $parsed_prd->getMonth() == 12) {
            //     $month = 1;
            //     $year++;
            // }

            $db = \Config\Database::connect();
            $builder = $db->table('md_monthlybudget');
            $builder->select('md_monthlybudget.id_monthlybudget, md_annualbudget.id_annualbudget, ob_dailybudget_qt, cg_dailybudget_qt');
            $builder->join('md_annualbudget', 'md_annualbudget.id_annualbudget = md_monthlybudget.id_annualbudget');
            $builder->where("md_monthlybudget.month = $month AND md_monthlybudget.year = $year
                AND id_contractor = $id_contractor");
            $budget = $builder->get()->getRowArray();

            if (is_null($budget)) {
                throw new \Exception("Budget not found, please select another month");
            }

            $generate_id = $this->generateId($prd_date);

            $created_at = Time::now();
            $Timesheets = new Timesheets();
            $Timesheets->save([
                'prd_date' => $prd_date,
                'prd_code' => $generate_id,
                'id_contractor' => $id_contractor,
                'prd_ob_day_qty' => $prd_ob_day_qty,
                'prd_ob_night_qty' => $prd_ob_night_qty,
                'prd_cg_day_qty' => $prd_cg_day_qty,
                'prd_cg_night_qty' => $prd_cg_night_qty,
                'prd_ob_total' => $prd_ob_total,
                'prd_cg_total' => $prd_cg_total,
                'prd_ob_distance' => $prd_ob_distance,
                'prd_cg_distance' => $prd_cg_distance,
                'prd_sr' => $prd_sr,
                'prd_rain' => $prd_rain,
                'prd_slip' => $prd_slip,
                'prd_%' => $prd_percentage,
                'noted' => $noted,
                'status' => $status,
                'created_at' => $created_at,
                'id_monthlybudget' => $budget['id_monthlybudget'] ?? null,
                'id_annualbudget' => $budget['id_annualbudget'] ?? null,
                "prd_rainfall" => $prd_rainfall,
                "prd_revision" => 0,
            ]);
            $message = "A timesheet has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("operation/timesheet")->with('message', $message);
    }

    public function delete($id)
    {
        $Timesheets = new Timesheets();
        $found = $Timesheets->find($id);
        if ($found['status'] == 'draft') {
            $Timesheets->delete($id);
            return redirect()->to("operation/timesheet")->with('message', 'A timesheet has been deleted');
        } else {
            return redirect()->to("operation/timesheet")->with('message', 'You cannot delete a timesheet that has been used, please contact your administrator');
        }
    }

    public function edit($code)
    {
        $data['title'] = "Production - Timesheet";
        $Contractors = new Contractors();
        $data['contractors'] = $Contractors->where('contractor_type', 'timesheet')->findAll();
        $Timesheets = new Timesheets();
        $data['timesheets'] = $Timesheets->where('prd_code', $code)->first();

        $TimesheetLogs = new TimesheetLogs();
        $builder = $TimesheetLogs->builder();
        $builder->select("*");
        $builder->where("prd_code", $code);
        $builder->orderBy("created_at", "DESC");
        $data['timesheet_logs'] = $builder->get()->getResultArray();

        switch ($data['timesheets']['status'] ?? null) {
            case 'draft':
                break;
            case  'submitted' || 'approved' || 'verified':
                $TimesheetLogs = new TimesheetLogs();
                $builder = $TimesheetLogs->builder();
                $builder->select("*");
                $builder->where("prd_code", $code);
                $builder->where("status", "draft");
                $builder->orderBy("created_at", "DESC");
                $data['timesheet_draft'] = $builder->get()->getRowArray();
                break;
            default:
                redirect()->to("operation/timesheet");
                break;
        }
        echo view('pages/timesheet-approval', $data);
    }

    public function update()
    {
        $prd_date = $this->request->getVar('prdDate');
        $prd_code = $this->request->getVar('prdCode');
        $id_contractor = $this->request->getVar('contractors');
        $prd_ob_day_qty = $this->request->getVar('obPrdDay');
        $prd_ob_night_qty = $this->request->getVar('obPrdNight');
        $prd_cg_day_qty = $this->request->getVar('cgPrdDay');
        $prd_cg_night_qty = $this->request->getVar('cgPrdNight');
        $prd_ob_total = $prd_ob_day_qty + $prd_ob_night_qty;
        $prd_cg_total = $prd_cg_day_qty + $prd_cg_night_qty;
        $prd_sr = round(handleDivision($prd_ob_total, $prd_cg_total), 2);
        $prd_rain = $this->request->getVar('prdRain');
        $prd_slip = $this->request->getVar('prdSlip');
        $prd_percentage = $this->request->getVar('prdPercent');
        $noted = $this->request->getVar('prdRemark');
        $prd_rainfall = $this->request->getVar('prdRainfall');
        $stats = $this->request->getVar('status');
        $id = $this->request->getVar('id');
        $prd_revision = (int) $this->request->getVar('prdRevision');

        $prd_ob_distance = $this->request->getVar('prd_ob_distance');
        $prd_cg_distance = $this->request->getVar('prd_cg_distance');
        
        $prd_revision++;
        $Timesheets = new Timesheets();
        $Timesheets->update($id, [
            'prd_date' => $prd_date,
            'id_contractor' => $id_contractor,
            'prd_ob_day_qty' => $prd_ob_day_qty,
            'prd_ob_night_qty' => $prd_ob_night_qty,
            'prd_cg_day_qty' => $prd_cg_day_qty,
            'prd_cg_night_qty' => $prd_cg_night_qty,
            'prd_ob_total' => $prd_ob_total,
            'prd_cg_total' => $prd_cg_total,
            'prd_ob_distance' => $prd_ob_distance,
            'prd_cg_distance' => $prd_cg_distance,
            'prd_sr' => $prd_sr,
            'prd_rain' => $prd_rain,
            'prd_slip' => $prd_slip,
            'prd_%' => $prd_percentage,
            'noted' => $noted,
            'status' => $stats,
            'prd_rainfall' => $prd_rainfall,
            "prd_revision" => $prd_revision,
            "prd_code" => $prd_code,
        ]);
        return redirect()->back()->with('message', 'A timesheet has been updated');
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
