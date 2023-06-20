<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TDistanceinput as ModelDistance;
use App\Models\Contractors;
use App\Models\Timesheets;
use App\Models\TimesheetLogs;
use CodeIgniter\I18n\Time;
use Config\Database;
use CodeIgniter\API\ResponseTrait;

class Distanceinput extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        $data['title'] = "Input Distance";

        $Distance = new Timesheets();
        $builder = $Distance->builder();
        $builder->select('timesheets.*, mc.contractor_name');
        $builder->join('md_contractors mc', "timesheets.id_contractor = mc.id");
        // $builder->join('md_monthly_discg mmcg', "timesheets.id_monthlybudget = mmcg.Id_monthlybudget_discg");
        $builder->where('timesheets.deleted_at IS NULL');
        $builder->orderBy("timesheets.id");
        $data['DataDistance'] = $builder->get()->getResultArray();


        $contractor_id = session()->get('contractor_id');
        $Contractors = new Contractors();
        $ids = array('1', '2', '3');
        if ($contractor_id == 0) {
            $data['contractors'] = $Contractors->where('contractor_type', 'timesheet')
                ->whereIn('id', $ids)->findAll();
        } else {
            $data['contractors'] = $Contractors->where('id', session()->get('contractor_id'))->findAll();
        }

        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/operation/VDistance', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
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

            $date_production = $this->request->getVar('prd_date');
            $id_contractor = $this->request->getVar('id_contractor');
            $distancecg_qty = $this->request->getVar('prd_cg_distance');
            $zerovalue = $this->request->getVar('zerovalue');
            $noted = $this->request->getVar('noted');
            // $create_on = Time::now();
            $create_by = session()->get('username');
            $parsed_prd = Time::parse($date_production);
            $month = $parsed_prd->getMonth();
            $year = $parsed_prd->getYear();
            $generate_id = $this->generateId($date_production);
            $status = 'approved';

            if ($parsed_prd->getDay() > 25 && $parsed_prd->getMonth() < 12) {
                $month++;
            }

            $db = \Config\Database::connect();
            $builder = $db->table('md_monthlybudget');
            $builder->select('md_monthlybudget.id_monthlybudget, md_annualbudget.id_annualbudget, ob_dailybudget_qt, cg_dailybudget_qt');
            $builder->join('md_annualbudget', 'md_annualbudget.id_annualbudget = md_monthlybudget.id_annualbudget');
            $builder->where("md_monthlybudget.month = $month AND md_monthlybudget.year = $year AND id_contractor = $id_contractor");
            $budget = $builder->get()->getRowArray();

            $db = Database::connect();
            $builder = $db->table('md_monthly_discg');
            $builder->select('id_monthlybudget_discg AS id_discg');
            $builder->where("md_monthly_discg.month = $month AND md_monthly_discg.year = $year AND md_monthly_discg.id_contractor = $id_contractor");
            $id_distance = $builder->get()->getRowArray();


            if (is_null($id_distance)) {
                throw new \Exception("ID not found, please select another month");
            }

            $data = [
                'prd_date' => $date_production,
                'prd_code' => $generate_id,
                'id_contractor' => $id_contractor,
                'prd_cg_distance' => $distancecg_qty,
                'prd_ob_distance' => $zerovalue,
                'prd_cg_day_qty' => $zerovalue,
                'prd_cg_night_qty' => $zerovalue,
                'prd_cg_total' => $zerovalue,
                'prd_ob_day_qty' => $zerovalue,
                'prd_ob_night_qty' => $zerovalue,
                'prd_ob_total' => $zerovalue,
                'id_monthlybudget' => $budget['id_monthlybudget'] ?? null,
                'id_annualbudget' => $budget['id_annualbudget'] ?? null,
                'Id_monthlybudget_discg' => $id_distance['id_discg'] ?? null,
                'prd_sr' => $zerovalue,
                'prd_rain' => $zerovalue,
                'prd_slip' => $zerovalue,
                'prd_%' => $zerovalue,
                'noted' => $noted,
                'status' => $status,
                'created_at' => $create_by,
                'prd_rainfall' => $zerovalue,
                'prd_revision' => 4,

            ];

            $insert_data = array_filter($data, function ($var) {
                return $var != null;
            });

            $ModelDistance = new Timesheets();
            $ModelDistance->save($insert_data);
            $message = "Data Added Successfully!";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("contractor-distance")->with('message', $message);
    }

    public function delete($id_distance)
    {
        // $ModelDistance = new Timesheets();
        // $ModelDistance->update($id_distance, [
        //     'deleted_at' => '1',
        //     'change_by' => session()->get('username'),
        //     'last_update' => Time::now()
        // ]);
        // $message = "Deleted successfully!";
        // return redirect()->to("contractor-distance")->with('message', $message);
        $Timesheets = new Timesheets();
        $found = $Timesheets->find($id_distance);
        if ($found['status'] == 'draft') {
            $Timesheets->delete($id_distance);
            return redirect()->to("contractor-distance")->with('message', 'A timesheet has been deleted');
        } else {
            return redirect()->to("contractor-distance")->with('message', 'You cannot delete a timesheet that has been used, please contact your administrator');
        }
    }

    public function edit($code)
    {
        $data['title'] = "Edit Distance";
        $contractor_id = session()->get('contractor_id');

        $Contractors = new Contractors();
        $ids = array('1', '2', '3');
        if ($contractor_id == 0) {
            $data['contractors'] = $Contractors->where('contractor_type', 'timesheet')
                ->whereIn('id', $ids)->findAll();
        } else {
            $data['contractors'] = $Contractors->where('id', session()->get('contractor_id'))->findAll();
        }

        // $Distance = new ModelDistance();
        // $data['DataDistance'] = $Distance->where('id_distance', $id_distance)->first();
        $distance = new Timesheets();
        $builder = $distance->builder();
        $builder->select('timesheets.*, mm.cg_dailybudget_qt, mm.ob_dailybudget_qt, mc.contractor_name');
        $builder->join('md_monthlybudget mm', 'mm.id_monthlybudget = timesheets.Id_monthlybudget');
        $builder->join('md_contractors mc', 'mc.id = timesheets.Id_contractor');
        $builder->where('timesheets.prd_code', $code);
        $data['distance'] = $builder->get()->getRowArray();
        $data['title'] = "Operation - Distance";

        $TimesheetLogs = new TimesheetLogs();
        $builder = $TimesheetLogs->builder();
        $builder->select("*");
        $builder->where("prd_code", $code);
        $builder->orderBy("created_at", "DESC");
        $data['timesheet_logs'] = $builder->get()->getResultArray();

        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/operation/VDistance_edit', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function update()
    {
        $db = Database::connect();
        $id = $this->request->getVar('id');
        $Timesheets = $db->query("SELECT * FROM timesheets
                                WHERE id = $id")->getRowArray();
                                
        $prd_code = $this->request->getVar('prd_code');
        $date_production = $this->request->getVar('prd_date');
        $id_contractor = $this->request->getVar('id_contractor');
        $distancecg_qty = $this->request->getVar('distancecg_qty');
        $remarks = $this->request->getVar('remarks');
        $change_by = session()->get('username');
        $change_on = Time::now();

        $data = [
            'prd_date' => $date_production,
            'id_contractor' => $id_contractor,
            'prd_ob_total' => $Timesheets['prd_ob_total'],
            'last_update' => $change_on,
            'prd_ob_distance' => $Timesheets['prd_ob_distance'],
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
            'noted' => $remarks,
            'status' => $Timesheets['status'],
            'create_date' => $Timesheets['create_date'],
            'prd_rainfall' => $Timesheets['prd_rainfall'],
            'prd_revision' => $Timesheets['prd_revision'],
            'prd_cg_distance' => $distancecg_qty,
        ];

        $insert_data = array_filter($data, function ($var) {
            return $var != null;
        });

        $ModelDistance = new Timesheets();
        $ModelDistance->update($id, $insert_data);
        $message = "Data Updated Successfully!";
        return redirect()->to("contractor-distance")->with('message', $message);
    }
}
