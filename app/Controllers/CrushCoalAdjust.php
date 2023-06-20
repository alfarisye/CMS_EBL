<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Contractors;
use App\Models\MDCrusher;
use App\Models\CrushCoalAdjust as ModelsCrushCoalAdjust;
use CodeIgniter\I18n\Time;

class CrushCoalAdjust extends BaseController
{

    private function generateCode($date)
    {
        $new_date = new Time($date);
        $month = $new_date->getMonth();
        $year = $new_date->getYear();
        $ModelsCrushCoal = new ModelsCrushCoalAdjust();
        $builder = $ModelsCrushCoal->builder();
        $builder->select('MAX(code) as max_id');
        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "CR-ADJ-$month-$year-" . "0001";
            return $new_id;
        } else {
            $new_id = "CR-ADJ-$month-$year-" .  str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function index()
    {
        $data['title'] = "Crush Coal Adjustment";
        $CrushCoal = new ModelsCrushCoalAdjust();
        $Contractor = new Contractors();
        $Crusher = new MDCrusher();
        $data['adjustments'] = $CrushCoal->select('*')
            ->where('deletion_status', 0)
            ->findAll();

        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/crush-coal-adjust/table', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }
    
    public function add()
    {
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $cc_adjustment = $this->request->getVar('cc_adjustment');
        $notes = $this->request->getVar('notes');
        $code = $this->generateCode($start_date);
        $data = [
            'code' => $code,
            'post_date_start' => $start_date,
            'post_date_end' => $end_date,
            'cc_adjustment' => $cc_adjustment,
            'notes' => $notes,
            'created_by' => session()->get('username'),
            "created_on" => Time::now(),
        ];
        $insert_data = array_filter($data, function($var){
            return $var != null;
        });
        $CrushCoal = new ModelsCrushCoalAdjust();
        $CrushCoal->save($insert_data);

        return redirect()->to("/crush-coal/adjust")->with('message', "Adjustment berhasil ditambahkan");
    }

    public function delete($id)
    {
        $ModelsCrushCoal = new ModelsCrushCoalAdjust();
        // $GroupEmail->where("group_id", $id)->delete();
        $ModelsCrushCoal->update($id, [
            'deletion_status' => true,
            'changed_by' => session()->get('username'),
            'changed_on' => Time::now()
        ]);

        return redirect()->to("/crush-coal/adjust")->with('message', "Data berhasil dihapus");
    }

    public function edit($id)
    {
        $data['title'] = "Production - Timesheet";
        $CrushCoal = new ModelsCrushCoalAdjust();
        
        $data['coals'] = $CrushCoal->where('id', $id)->first();

        echo view('pages/crush-coal-adjust/approval', $data);
    }

    public function update()
    {
        $start_date = $this->request->getVar('start_date');
        $end_date = $this->request->getVar('end_date');
        $notes = $this->request->getVar('notes');
        $cc_adjustment = $this->request->getVar('cc_adjustment');
        $stats = $this->request->getVar('status');
        $id = $this->request->getVar('id');
        $posted_date = null;
        if($stats == 'posted') {
            $posted_date =  Time::now();
        }
        $data = [
            'post_date_start' => $start_date,
            'post_date_end' => $end_date,
            'cc_adjustment' => $cc_adjustment,
            'doc_date' => $posted_date,
            'notes' => $notes,
            'status' => $stats,
            'changed_on' => Time::now(),
            'changed_by' => session()->get('username')
        ];
        $insert_data = array_filter($data, function($var){
            return $var != null;
        });
        $CrushCoal = new ModelsCrushCoalAdjust();
        $CrushCoal->update($id, $insert_data);
        return redirect()->back()->with('message', 'A data has been updated');
    }
}
