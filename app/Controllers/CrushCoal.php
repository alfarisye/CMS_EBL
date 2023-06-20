<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Contractors;
use App\Models\MDCrusher;
use App\Models\CrushCoal as ModelsCrushCoal;
use CodeIgniter\I18n\Time;
use Config\Database;

class CrushCoal extends BaseController
{

    private function generateCode($production_date)
    {
        $ModelsCrushCoal = new ModelsCrushCoal();
        $builder = $ModelsCrushCoal->builder();
        $builder->select('MAX(production_code) as max_id');
        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "CR-$production_date-" . "0001";
            return $new_id;
        } else {
            $new_id = "CR-$production_date-" .  str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function index()
    {
        $data['title'] = "Crush Coal";
        $CrushCoal = new ModelsCrushCoal();
        $Contractor = new Contractors();
        $Crusher = new MDCrusher();
        $data['crush_coals'] = $CrushCoal->select('t_crushcoal.*, c.contractor_name, cr.crusher_description')
            ->join('md_contractors c', "id_contractor = c.id")
            ->join('md_crusher cr', "id_crusher = cr.id")
            ->where('deletion_status', 0)
            ->findAll();
        $data['contractors'] = $Contractor->where('contractor_type', 'crush_coal')->findAll();
        $data['crushers'] = $Crusher->findAll();

        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/crush-coal/table', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function add()
    {
        $production_date = $this->request->getVar('production_date');
        $id_contractor = $this->request->getVar('id_contractor');
        $rc_qty = $this->request->getVar('rc_qty');
        $id_crusher = $this->request->getVar('id_crusher');
        $cc_qty = $this->request->getVar('cc_qty');
        $code = $this->generateCode($production_date);

        $parsed_prd = Time::parse($production_date);
        $month = $parsed_prd->getMonth();
        $year = $parsed_prd->getYear();
        if ($parsed_prd->getDay() > 25 && $parsed_prd->getMonth() < 12 ) {
            $month++;
        }

        $db = Database::connect();
        $md_cc = $db->query("SELECT id_monthlybudgetcc AS id FROM md_monthlybudget_cc mmc WHERE mmc.year = $year 
            AND mmc.month = $month AND id_contractor = $id_contractor")->getRowArray();

        $data = [
            'production_code' => $code,
            'production_date' => $production_date,
            'id_contractor' => $id_contractor,
            'rc_qty' => $rc_qty,
            'id_crusher' => $id_crusher,
            'cc_qty' => $cc_qty,
            'created_by' => session()->get('username'),
            "created_on" => Time::now(),
            'id_monthlybudgetcc' => $md_cc['id'],
        ];
        $insert_data = array_filter($data, function($var){
            return $var != null;
        });
        $CrushCoal = new ModelsCrushCoal();
        $CrushCoal->save($insert_data);

        return redirect()->to("operation/crush-coal")->with('message', "Crush Coal berhasil ditambahkan");
    }

    public function delete($id)
    {
        $ModelsCrushCoal = new ModelsCrushCoal();
        // $GroupEmail->where("group_id", $id)->delete();
        $ModelsCrushCoal->update($id, [
            'deletion_status' => true,
            'changed_by' => session()->get('username'),
            'changed_on' => Time::now()
        ]);

        return redirect()->to("operation/crush-coal")->with('message', "Data berhasil dihapus");
    }

    public function edit($id)
    {
        $data['title'] = "Production - Timesheet";
        $CrushCoal = new ModelsCrushCoal();
        $Contractor = new Contractors();
        $Crusher = new MDCrusher();
        $data['contractors'] = $Contractor->where('contractor_type', 'crush_coal')->findAll();
        $data['crushers'] = $Crusher->findAll();
        
        $data['coals'] = $CrushCoal->where('id', $id)->first();

        echo view('pages/crush-coal/approval', $data);
    }

    public function update()
    {
        $prd_date = $this->request->getVar('prdDate');
        $id_contractor = $this->request->getVar('contractors');
        $id_crusher = $this->request->getVar('crushers');
        $rc_qty = $this->request->getVar('rcQty');
        $cc_qty = $this->request->getVar('ccQty');
        $stats = $this->request->getVar('status');
        $id = $this->request->getVar('id');
        $data = [
            'prd_date' => $prd_date,
            'id_contractor' => $id_contractor,
            'production_date' => $prd_date,
            'id_contractor' => $id_contractor,
            'rc_qty' => $rc_qty,
            'id_crusher' => $id_crusher,
            'cc_qty' => $cc_qty,
            'status' => $stats,
            'changed_on' => Time::now(),
            'changed_by' => session()->get('username')
        ];
        $insert_data = array_filter($data, function($var){
            return $var != null;
        });
        $CrushCoal = new ModelsCrushCoal();
        $CrushCoal->update($id, $insert_data);
        return redirect()->back()->with('message', 'A data has been updated');
    }
}
