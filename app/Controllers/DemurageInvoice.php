<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Sales\DemurageInvoices;
use App\Models\Sales\SalesShipment;
use App\Models\Sales\ProfitCenters;
use App\Models\Sales\Orders;
use App\Models\Sales\Payterms;
use App\Models\Sales\Vendors;
use App\Models\Sales\Currencys;
use App\Models\Sales\SalesLaytime;
use App\Models\Sales\Prps;
use CodeIgniter\HTTP\Message;
use CodeIgniter\I18n\Time;
use PhpParser\Node\Stmt\Echo_;
use CodeIgniter\API\ResponseTrait;

class DemurageInvoice extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $data['title'] = "Demurage Invoice";
        @$from_date = $_GET['from_date'];
        @$to_date = $_GET['to_date'];

        $MDMasterBuild = new DemurageInvoices();
        $buildermaster = $MDMasterBuild->builder();
        if ($from_date && $to_date) {
            $to_date = new \DateTime($to_date);
            $to_date->modify('+1 day');
            $to_date = $to_date->format('Y-m-d');
            $buildermaster->select("ID, SHIPMENT_ID, CONTRCT_NO, LIFNR,
                                        BELNR,STBLG, XBLNR, CREATED_BY, STATUS_SAP, MESSAGE_SAP,
                                        DATE(CREATED_AT) AS created_on")
                ->where("CREATED_AT BETWEEN '$from_date' AND '$to_date' AND DELETED_AT IS NULL");
        } else {
            $buildermaster->select("ID, SHIPMENT_ID, CONTRCT_NO, LIFNR,
                                        BELNR, STBLG, XBLNR,CREATED_BY, STATUS_SAP, MESSAGE_SAP,
                                        DATE(CREATED_AT) AS created_on")
                ->where("DELETED_AT IS NULL");
        }
        $data['MDMasterBuild'] = $buildermaster->get()->getResultArray();
        // d($data['MDMasterBuild']);
        $Vendor = new Vendors();
        $Vendor = $Vendor->builder();
        $Vendor->select("id, BUKRS, LIFNR, NAME1")
            ->where("BUKRS = 'HH10'");
        $data['Vendor'] = $Vendor->get()->getResultArray();


        $ShipBuilder = new SalesShipment();
        $SalesShipBuilder = $ShipBuilder->builder();
        $SalesShipBuilder->select("id, shipment_id, gr_qty, contract_no, created_at, created_by, updated_at, updated_by, deleted_at");
        $data['SalesShipBuilder'] = $SalesShipBuilder->get()->getResultArray();

        $Currency = new Currencys();
        $Currency = $Currency->builder();
        $Currency->select("DISTINCT(TCURR)")
            ->where("KURST = 'B' & 'M'");
        $data['Currency'] = $Currency->get()->getResultArray();

        $Payterms = new Payterms();
        $Payterms = $Payterms->builder();
        $Payterms->select("ZTERM, ZBDIT");
        $data['Payterms'] = $Payterms->get()->getResultArray();

        $SalesLay = new SalesLaytime();
        $SalesLay = $SalesLay->builder();
        $SalesLay->select("id, value_demmurage");
        $data['SalesLay'] = $SalesLay->get()->getResultArray();

        $PBUKR = new Prps();
        $PBUKR = $PBUKR->builder();
        $PBUKR->select("id, PBUKR, POSID, POST1")
            ->where("PBUKR = 'HH10'");
        $data['PBUKR'] = $PBUKR->get()->getResultArray();

        $ProfitCenter = new ProfitCenters();
        $ProfitCenter = $ProfitCenter->builder();
        $ProfitCenter->select("id, KHINR, PRCTR, LTEXT")
            ->where("KHINR LIKE 'HH10%'");
        $data['ProfitCenter'] = $ProfitCenter->get()->getResultArray();

        $Order = new Orders();
        $Order = $Order->builder();
        $Order->select("id, BUKRS, AUFNR, KTEXT")
            ->where("BUKRS = 'HH10'")
            ->where("KTEXT IS NOT NULL AND KTEXT != ''");
        $data['Order'] = $Order->get()->getResultArray();

        // $data['ProfilCenter']= "Hello World";
        // $data['Order']= "Hello World";
        echo view('pages/sales/demurage-invoice', $data);
    }
    public function get($x)
    {
        // header('Content-Type: application/json');
        // $SalesOrder = new SalesShipment();
        // $SalesOrder = $SalesOrder->find($x); //cuman bisa pakai ID untuk get data using fetch in ajax
        // return $this->response->setJSON($SalesOrder);
        // Jika pakai selain ID maka gunakan builder untuk get data using fetch in ajax
        $ShipBuilder = new SalesShipment();
        $SalesShipBuilder = $ShipBuilder->builder();
        $SalesShipBuilder->select("*")
            ->where("shipment_id = '$x'");
        $data['SalesShip'] = $SalesShipBuilder->get()->getResultArray();

        $SalesLay = new SalesLaytime();
        $SalesLay = $SalesLay->builder();
        $SalesLay->select("*")
            ->where("shipment_no = '$x'");
        $data['SalesLay'] = $SalesLay->get()->getResultArray();
        // $data['ProfilCenter']= "Hello World";
        // $data['Order']= "Hello World";
        return $this->response->setJSON($data);
    }
    public function getEdit($id)
    {
        header('Content-Type: application/json');
        $DemurageInvoices = new DemurageInvoices();
        $DemurageInvoices = $DemurageInvoices->find($id); //cuman bisa pakai ID untuk get data using fetch in ajax
        return $this->response->setJSON($DemurageInvoices);
    }
    public function download($id)
    {
        $berkas = new DemurageInvoices();
        $data = $berkas->find($id);
        $namefile = Time::now()->format('Y-m-d');
        return $this->response->download($data['ATTCH'], null)->setFileName('Report Invoice_' . $namefile . '.pdf');
        echo view('pages/sales/demurage-invoice', $data['ATTCH']);
    }
    private function generateCode($Doc_No)
    {
        $ModulSales = new DemurageInvoices();
        $builder = $ModulSales->builder();
        $builder->select('MAX(DOC_NO) as max_id');
        $max_id = $builder->get()->getRowArray();
        $time = Time::now()->format('Y/m/');
        if ($max_id['max_id'] == null) {
            $new_id = $time . "$Doc_No" . "00001";
            return $new_id;
        } else {
            $new_id = $time . "$Doc_No" .  str_pad(substr($max_id['max_id'], -5) + 1, 5, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function add()
    {
        $data['title'] = "Demurage Invoice";
        $shipmentID = $this->request->getVar('shipmentID');
        $contractN = $this->request->getVar('contractN');
        $vendor = $this->request->getVar('vendor');
        $invoice_date = $this->request->getVar('invoice_date');
        $posting_date = $this->request->getVar('posting_date');
        $baseline_date = $this->request->getVar('baseline_date');
        $payterm = $this->request->getVar('payterm');
        //$att = $this->request->getVar('att');
        $validation = \Config\Services::validation();
        $att = $validation->setRules([
            'att' => 'uploaded[file]|max_size[file,5000]|ext_in[pdf],'
        ]);
        if ($att = $this->request->getFile('att')) {
            if ($att->isValid() && !$att->hasMoved()) {
                // Get att name and extension
                $name = $att->getName();
                $ext = $att->getClientExtension();
                $filepath = WRITEPATH . 'uploads/' . $att->store();
                // Response
                $data['success'] = 1;
                $data['message'] = 'Uploaded Successfully!';
                $data['filepath'] = $filepath;
                $data['extension'] = $ext;
            } else {
                // Response
                $data['success'] = 2;
                $data['message'] = 'File not uploaded.';
            }
        }
        $currency = $this->request->getVar('currency');
        $reference = $this->request->getVar('reference');
        $exchange_rate = $this->request->getVar('exchange_rate');
        $demurage = $this->request->getVar('demurage');
        $wbs_element = $this->request->getVar('WBS_element');
        $profit_center = $this->request->getVar('profit_center');
        $order = $this->request->getVar('order');
        $text = $this->request->getVar('text');
        $Doc_No = $this->request->getVar('Doc_No');
        $code = $this->generateCode($Doc_No);
        $data = [
            'DOC_NO'      => $code,
            'SHIPMENT_ID' => $shipmentID,
            'CONTRCT_NO'  => $contractN,
            'LIFNR'       => $vendor,
            'BLDAT'       => $invoice_date,
            'BUDAT'       => $posting_date,
            'ZFBDT'       => $baseline_date,
            'ZTERM'       => $payterm,
            'ATTCH'       => $filepath ?? '',
            'XBLNR'       => $reference,
            'KURSF'       => $exchange_rate ?? '',
            'DEMURAGE'    => $demurage ?? '',
            'PROJK'       => $wbs_element,
            'PRCTR'       => $profit_center,
            'AUFNR'       => $order,
            'ECURR'       => $currency,
            'SGTXT'       => $text,
            'USNAM'       => session()->get('username'),
            'CPUDT'       => Time::now()->format('Y-m-d H:i:s'),
            'CREATED_BY'  => session()->get('username'),
            'CREATED_AT'  => Time::now()->format('Y-m-d H:i:s'),
        ];
        //d($data);
        $insert_Data = array_filter($data, function ($var) {
            return $var != null;
        });
        $ModulSales = new DemurageInvoices();
        $ModulSales->save($insert_Data);
        $message = "Add data has been a success";

        return redirect()->to('demurage-invoice')->with('message', $message);
    }



    public function update()
    {
        $data['title'] = "Edit Demurage Invoice";
        $shipmentID = $this->request->getVar('shipmentIDup');
        $contractN = $this->request->getVar('contractNup');
        $vendor = $this->request->getVar('vendorup');
        $invoice_date = $this->request->getVar('invoice_dateup');
        $posting_date = $this->request->getVar('posting_dateup');
        $baseline_date = $this->request->getVar('baseline_dateup');
        $payterm = $this->request->getVar('paytermup');
        $id = $this->request->getVar('ID');
        //$att = $this->request->getVar('att');
        // $validation = \Config\Services::validation();
        // $att = $validation->setRules([
        //     'att' => 'uploaded[file]|max_size[file,30720]|ext_in[pdf],'
        // ]);
        $xx = new DemurageInvoices();
        $x = $xx->find($id);
        if ($file = $this->request->getFile('attup')) {
            if ($file->isValid() && !$file->hasMoved()) {
                $old = $x['ATTCH'];
                if (file_exists('uploads/' . $old)) { //jika file sudah ada
                    if ($old) {
                        unlink('uploads/' . $old);
                    }
                }
                $name = $file->getName();
                $ext = $file->getClientExtension();
                $filepath = WRITEPATH . 'uploads/' . $file->store();
                // Response
                $data['success'] = 1;
                $data['message'] = 'Uploaded Successfully!';
                $data['filepath'] = $filepath;
                $data['extension'] = $ext;
            } else {
                $data['success'] = 2;
                $data['message'] = 'File not uploaded.';
            }
        }
        $currencyup = $this->request->getVar('currencyup');
        $referenceup = $this->request->getVar('referenceup');
        $exchange_rateup = $this->request->getVar('exchange_rateup');
        $demurage = $this->request->getVar('demurageup');
        $wbs_element = $this->request->getVar('WBS_elementup');
        $profit_center = $this->request->getVar('profit_centerup');
        $order = $this->request->getVar('orderup');
        $text = $this->request->getVar('textup');
        $Doc_No = $this->request->getVar('Doc_Noup');
        $code = $this->generateCode($Doc_No);

        $data = [
            'DOC_NO'      => $code,
            'SHIPMENT_ID' => $shipmentID,
            'CONTRCT_NO'  => $contractN,
            'LIFNR'       => $vendor,
            'BLDAT'       => $invoice_date,
            'BUDAT'       => $posting_date,
            'ZFBDT'       => $baseline_date,
            'ZTERM'       => $payterm,
            'ATTCH'       => $filepath ?? '',
            'XBLNR'       => $referenceup,
            'KURSF'       => $exchange_rateup ?? '',
            'ECURR'       => $currencyup,
            'DEMURAGE'    => $demurage ?? '',
            'PROJK'       => $wbs_element,
            'PRCTR'       => $profit_center,
            'AUFNR'       => $order,
            'SGTXT'       => $text,
            'UPDATED_BY'  => session()->get('username'),
            'UPDATED_AT'  => Time::now()->format('Y-m-d H:i:s'),
        ];
        d($data);

        $insert_data = array_filter($data, function ($var) {
            return $var != null;
        });
        $DemurageUp = new DemurageInvoices();
        $DemurageUp->update($id, $insert_data);
        return redirect()->back()->with('message', 'A Demurage Invoice has been updated');
    }


    public function delete($id)
    {
        try {
            $MDMaster     = new DemurageInvoices();
            $DeleteMaster = $MDMaster->find($id);
            $DeleteMaster['DELETED_AT'] = Time::now()->format('Y-m-d H:i:s');
            $DeleteMaster['DELETED_BY'] = session()->get('username');
            var_dump($DeleteMaster);
            $MDMaster->save($DeleteMaster);

            $message = "Demurage Invoice has been Deleted";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to('demurage-invoice')->with('message', $message);
    }
}
