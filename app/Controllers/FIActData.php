<?php

namespace App\Controllers;
use App\Models\FiPrdDats;
use App\Models\FiPrdActs;
use CodeIgniter\HTTP\Message;
use CodeIgniter\I18n\Time;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

use function PHPUnit\Framework\isNull;

class FIActData extends BaseController
{
    public function index()
    {
        $data['title'] = "CMS – Update Production Data";

        $id_actv = $_GET['idactv'] ?? false;

        $FiPrdActs = new FiPrdActs();
        $data['t_actitvy'] = $FiPrdActs->orderBy('ACTVY')->findAll();

        $db = db_connect();
        $qstr_vendor = "SELECT LIFNR, BUKRS, NAME1 FROM t_vendor WHERE BUKRS = 'HH10' ORDER BY NAME1 ASC";
        $q_vendor = $db->query($qstr_vendor);
        $data['vendor'] = $q_vendor->getResultArray();

        $qstr_data = "select FI_PRD_DAT.*,
        (select t_vendor.NAME1 from t_vendor where t_vendor.LIFNR = FI_PRD_DAT.LIFNR limit 1) as vendor_name,
        FI_PRD_ACT.ACTVY as act_desc
        from FI_PRD_DAT join FI_PRD_ACT on FI_PRD_DAT.ACTVY = FI_PRD_ACT.id 
        where FI_PRD_DAT.ACTVY LIKE :id_actv:
          and FI_PRD_DAT.status = '1'
        order by CRTDA desc, EDTAT desc";
        
        if ($id_actv == '' or is_null($id_actv)) {
            $q_data = $db->query($qstr_data, [
                'id_actv'  => '%%',
            ]);
        }else{
            $q_data = $db->query($qstr_data, [
                'id_actv'  => $id_actv,
            ]);
        }
        
        $data['PrdDat'] = $q_data->getResultArray();

        echo view('pages/finance/updateproductiondata', $data);
    }

    public function add()
    {
        try {
            $activity_id = $this->request->getVar('activity');
            $vendor_id = $this->request->getVar('vendor');
            $quantity = $this->request->getVar('quantity');
            $tarif = $this->request->getVar('tarif');

            $FiPrdDats = new FiPrdDats();
            $FiPrdDats->save([
                'ACTVY' => $activity_id,
                'LIFNR' => $vendor_id,
                'QTY' => $quantity,
                'TRF' => $tarif,
                'CRTDB' => session()->get('username'),
                'CRTDA' => Time::now()->format('Y-m-d H:i:s'),
            ]);

            $message = "Production Data has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/finance/updateproductiondata/")->with('message', $message);
    }

    public function get($id)
    {
        header('Content-Type: application/json');
        $FiPrdDats = new FiPrdDats();
        $FiPrdDats = $FiPrdDats->find($id);
        return $this->response->setJSON($FiPrdDats);
    }

    public function update()
    {
        try {
            // fields :
            // ACTVY, CRTDA, CRTDB, EDTAT, EDTBY, LIFNR, QTY, TRF, id,

            $FiPrdDats = new FiPrdDats();
            $UpFiPrdDat = $FiPrdDats->find($this->request->getPost('id'));
            if ($UpFiPrdDat) {
                $UpFiPrdDat['ACTVY'] = $this->request->getPost('edactivity');
                $UpFiPrdDat['LIFNR'] = $this->request->getPost('edvendor');
                $UpFiPrdDat['QTY'] = $this->request->getPost('edquantity');
                $UpFiPrdDat['TRF'] = $this->request->getPost('edtarif');
                $UpFiPrdDat['EDTBY'] = session()->get('username');
                $UpFiPrdDat['EDTAT'] = Time::now()->format('Y-m-d H:i:s');

                $FiPrdDats->save($UpFiPrdDat);

                $message = "Production Data has been update";
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/finance/updateproductiondata/")->with('message', $message);
    }
    
    public function delete($id)
    {

        try {
            // fields :
            // ACTVY, CRTDA, CRTDB, EDTAT, EDTBY, LIFNR, QTY, TRF, id,

            $FiPrdDats = new FiPrdDats();
            $UpFiPrdDat = $FiPrdDats->find($id);
            if ($UpFiPrdDat) {
                $UpFiPrdDat['EDTBY'] = session()->get('username');
                $UpFiPrdDat['EDTAT'] = Time::now()->format('Y-m-d H:i:s');
                $UpFiPrdDat['status'] = false;

                $FiPrdDats->save($UpFiPrdDat);

                $message = "Production Data has been deleted";
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        // $FiPrdDats = new FiPrdDats();
        // $FiPrdDats->update($id,['status' => false]);
        // $message = "Production Data has been deleted";
        return redirect()->to("/finance/updateproductiondata/")->with('message', $message);
    }
}
