<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Contractors;
use App\Models\TimesheetAdjustments;
use App\Models\TimesheetAdjustmentLogs;
use CodeIgniter\I18n\Time;
use PhpParser\Node\Stmt\TryCatch;
use CodeIgniter\API\ResponseTrait;

class TimesheetAdjustment extends BaseController
{
    use ResponseTrait;
    private function generateId($start_date)
    {
        $TimesheetAdjustments = new TimesheetAdjustments();
        $start_month = Time::parse($start_date)->format('m');
        $builder = $TimesheetAdjustments->builder();
        $builder->select('MAX(code) as max_id');
        $builder->where('MONTH(start_date) =', $start_month);
        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "TM-ADJ-" . $start_month . "-0001";
            return $new_id;
        } else {
            $new_id = "TM-ADJ-" . $start_month . "-" . str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function index()
    {
        $Contractors = new Contractors();
        $TimesheetAdjustments = new TimesheetAdjustments();
        $data = [
            'title' => 'Timesheet Adjustment',
            "timesheet_adjustments" => $TimesheetAdjustments->select("timesheet_adjustments.*, mc.contractor_name")->join('md_contractors mc', 'mc.id = timesheet_adjustments.id_contractor')->findAll()
        ];
        $Contractors = new Contractors();
        $data['contractors'] = $Contractors->where('contractor_type', 'timesheet')->findAll();
        echo view('pages/timesheet-adjustment', $data);
    }

    public function add()
    {
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $cg_adjustment = $this->request->getVar('cg_adjustment');
        $ob_adjustment = $this->request->getVar('ob_adjustment');
        $adj_cg_distance = $this->request->getVar('adj_cg_distance');
        $adj_ob_distance = $this->request->getVar('adj_ob_distance');
        $notes = $this->request->getVar('notes');
        $id_contractor = $this->request->getVar('id_contractor');

        try {
            $id = $this->generateId($start_date);
            $TimesheetAdjustments = new TimesheetAdjustments();
            $TimesheetAdjustments->save([
                'start_date' => $start_date,
                'end_date' => $end_date,
                'cg_adjustment' => $cg_adjustment,
                'ob_adjustment' => $ob_adjustment,
                'adj_cg_distance' => $adj_cg_distance,
                'adj_ob_distance' => $adj_ob_distance,
                'notes' => $notes,
                'code' => $id,
                "created_by" => session()->get('username'),
                "status" => "draft",
                'id_contractor' => $id_contractor,
            ]);
            $message = "Timesheet Adjustment has been added.";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("operation/timesheet/adjust")->with('message', $message);
    }

    public function delete($id)
    {
        $TimesheetAdjustments = new TimesheetAdjustments();
        $found = $TimesheetAdjustments->find($id);
        if ($found['status'] == 'draft') {
            $TimesheetAdjustments->delete($id);
            return redirect()->to("operation/timesheet/adjust")->with('message', 'Timesheet Adjustment has been deleted.');
        } else {
            return redirect()->to("operation/timesheet/adjust")->with('message', 'You cannot delete an adjustment that has been used, please contact your administrator');
        }
    }

    public function edit($id)
    {
        $TimesheetAdjustments = new TimesheetAdjustments();
        $TimesheetAdjustmentLogs = new TimesheetAdjustmentLogs();
        $data = [
            'title' => 'Timesheet Adjustment',
            'timesheet_adjustment' => $TimesheetAdjustments->where('code', $id)->first(),
            'logs' => $TimesheetAdjustmentLogs->where('code', $id)->orderBy("created_at", "DESC")->findAll(),
        ];
        $Contractors = new Contractors();
        $data['contractors'] = $Contractors->where('contractor_type', 'timesheet')->findAll();
        echo view('pages/adjustment-approval', $data);
    }

    public function update()
    {
        $id = $this->request->getVar('id');
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $cg_adjustment = $this->request->getVar('cg_adjustment');
        $ob_adjustment = $this->request->getVar('ob_adjustment');
        $notes = $this->request->getVar('notes');
        $status = $this->request->getVar('status');
        $code = $this->request->getVar('code');
        $adj_cg_distance = $this->request->getVar('adj_cg_distance');
        $adj_ob_distance = $this->request->getVar('adj_ob_distance');
        $id_contractor = $this->request->getVar('id_contractor');

        $TimesheetAdjustments = new TimesheetAdjustments();
        $TimesheetAdjustments->update($id, [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'cg_adjustment' => $cg_adjustment,
            'ob_adjustment' => $ob_adjustment,
            'adj_cg_distance' => $adj_cg_distance,
            'adj_ob_distance' => $adj_ob_distance,
            'notes' => $notes,
            'status' => $status,
            "updated_by" => session()->get('username'),
            "code" => $code,
            'id_contractor' => $id_contractor,
        ]);
        return redirect()->back()->with('message', 'An adjustment has been updated');
    }

    // #Tempcode Malik ==
    public function get()
    {
        $dari_tanggal = $_GET['dari_tanggal'];
        $sampai_tanggal = $_GET['sampai_tanggal'];
        $TimesheetAdjustments = new TimesheetAdjustments();
        $builder = $TimesheetAdjustments->builder();
        $builder->select('timesheet_adjustments.id,
            timesheet_adjustments.start_date as prd_date,
            timesheet_adjustments.cg_adjustment as cg_total,
            timesheet_adjustments.ob_adjustment as ob_total,
            timesheet_adjustments.status,
        ');
        $builder->where("(timesheet_adjustments.start_date BETWEEN '$dari_tanggal' AND '$sampai_tanggal' OR  timesheet_adjustments.end_date BETWEEN '$dari_tanggal' AND '$sampai_tanggal') AND deleted_at IS NULL");
        $data = $builder->get()->getResultArray();
        return $this->respond($data, 200);
    }
    // == #Tempcode Malik
}
