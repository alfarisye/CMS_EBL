<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Sales\DespatchInvoices;
use App\Models\Sales\SalesShipment;
use App\Models\Sales\TSalPrice;
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

class DespatchInvoice extends BaseController
{
    public function index()
    {
            $data['title'] = "Despatch Invoice";
            $MDMasterBuild = new DespatchInvoices();
            $buildermaster = $MDMasterBuild->builder();
            
            $dari = $_GET['daritanggal'] ?? false;
            $sampai = $_GET['sampaitanggal'] ?? false;

            if (($dari  == '' or is_null($dari)) and ($sampai  == '' or is_null($sampai))) {
                $buildermaster->select("ID, SHIPMENT_ID, CONTRCT_NO, KUNNR, BLDAT, BUDAT, ZFBDT, ZTERM, ECURR, KURSF, ATTCH, XBLNR, DSPTCH, PROJK, PRCTR, AUFNR, SGTXT, DOC_NO, BELNR, STBLG, USNAM, CPUDT, DELBY, DELON,STATUS_SAP,MESSAGE_SAP")->where("DELBY IS NULL");
            }
            else{
                $buildermaster->select("ID, SHIPMENT_ID, CONTRCT_NO, KUNNR, BLDAT, BUDAT, ZFBDT, ZTERM, ECURR, KURSF, ATTCH, XBLNR, DSPTCH, PROJK, PRCTR, AUFNR, SGTXT, DOC_NO, BELNR, STBLG, USNAM, CPUDT, DELBY, DELON,STATUS_SAP,MESSAGE_SAP")->where("DELBY IS NULL AND BUDAT BETWEEN $dari and $sampai");
            }

            $data['MDMasterBuild']=$buildermaster->get()->getResultArray();
    
            $ShipBuilder = new SalesShipment();
            $SalesShipBuilder = $ShipBuilder->builder();
            $SalesShipBuilder->select("id, shipment_id, gr_qty, contract_no, created_at, created_by, updated_at, updated_by, deleted_at");
            $data['SalesShipBuilder']=$SalesShipBuilder->get()->getResultArray();
            //dd($data['SalesShipBuilder']);

            $Currency = new Currencys();
            $Currency = $Currency->builder();
            $Currency->select ("DISTINCT(TCURR)")
                    ->where("KURST = 'B' & 'M'");
            $data['Currency']=$Currency->get()->getResultArray();

            $Payterms = new Payterms();
            $Payterms = $Payterms->builder();
            $Payterms->select("ZTERM, ZBDIT");
            $data['Payterms']=$Payterms->get()->getResultArray();

            // $SalesLay = new SalesLaytime();
            // $SalesLay = $SalesLay->builder();
            // $SalesLay->select("id, value_demmurage");
            // $data['SalesLay']=$SalesLay->get()->getResultArray();

            $PBUKR = new Prps();
            $PBUKR = $PBUKR->builder();
            $PBUKR->select("id, PBUKR, POSID, POST1")
                 ->where("PBUKR = 'HH10'");

            $data['PBUKR']=$PBUKR->get()->getResultArray();

            $ProfitCenter = new ProfitCenters();
            $ProfitCenter = $ProfitCenter->builder();
            $ProfitCenter->select("id, KHINR, PRCTR, LTEXT")
                 ->where("KHINR LIKE 'HH10%'");
            $data['ProfitCenter']=$ProfitCenter->get()->getResultArray();

            $Order = new Orders();
            $Order = $Order->builder();
            $Order->select("id, BUKRS, AUFNR, KTEXT")
                 ->where("BUKRS = 'HH10'");
            $data['Order']=$Order->get()->getResultArray();

            echo view('pages/sales/despatchinvoice', $data);
    }

    public function Buyer($x,$mode){
        $db = db_connect();
        $result='';$result2='';
        //logic buyer
        $q_buyer = $db->query("SELECT t1.CUSTOMER_CODE AS CUSCD , t1.customer_name AS CUSNM
                                FROM T_SAL_CONTRACT_ORDER t1 , T_SAL_SHIPMENT t2
                                WHERE  t1.contract_no = t2.contract_no
                                AND t2.shipment_id = '".$x."'
                                LIMIT 1 ;");
        $row = $q_buyer->getNumRows();
        if($row>0){
            $row = $q_buyer->getRow(0);
            $result=$row->CUSCD;
            $result2=$row->CUSNM;
        }
        
        if($mode==1){
            return $result;
        }
        else{
            return $result2;
        }
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
        $data['SalesShip']=$SalesShipBuilder->get()->getResultArray();

        $data['Buyer']=$this->Buyer($x,1);
        $data['BuyerDesc']=$this->Buyer($x,2);

        //dd($data['SalesShip']);

        $SalesLay = new SalesLaytime();
        $SalesLay = $SalesLay->builder();
        $SalesLay->select("*")
                 ->where("shipment_no = '$x'");
        $data['SalesLay']=$SalesLay->get()->getResultArray();
        return $this->response->setJSON($data);
    }

    public function getEdit($id)
    {
        header('Content-Type: application/json');
        $SalesOrder = new DespatchInvoices();
        $SalesOrder = $SalesOrder->find($id); 
        return $this->response->setJSON($SalesOrder);
    }

    public function download($id)
    {  
        // d($id);
        $berkas = new DespatchInvoices();
        $data = $berkas->find($id);

        return $this->response->download($data['ATTCH'], null);
        echo view('pages/sales/demurage-invoice', $data['ATTCH']);
    }


    private function generateCode($Doc_No)
    {
        $ModulSales = new DespatchInvoices();
        $builder = $ModulSales->builder();
        $builder->select('MAX(DOC_NO) as max_id');
        $max_id = $builder->get()->getRowArray();
        $time = Time::now()->format('Y/m/');
        if ($max_id['max_id'] == null) {
            $new_id = $time."$Doc_No" . "00001";
            return $new_id;
        } else {
            $new_id = $time."$Doc_No" .  str_pad(substr($max_id['max_id'], -5) + 1, 5, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }
    
    public function add()
    {       $data['title'] = "Despatch Invoice";
            $shipmentID = $this->request->getVar('shipmentID');
            $contractN = $this->request->getVar('contractN');
            $buyer = $this->request->getVar('Buyer');
            $invoice_date = $this->request->getVar('invoice_date');
            $posting_date = $this->request->getVar('posting_date');
            $baseline_date = $this->request->getVar('baseline_date');
            $payterm = $this->request->getVar('payterm');
            $filepath = '';
            if($file = $this->request->getFile('att')) {
                if ($file->isValid() && ! $file->hasMoved()) {
                   // Get file name and extension
                   $name = $file->getName();
                   $ext = $file->getClientExtension();
                   $filepath = WRITEPATH . 'uploads/' . $file->store();
                   // Response
                   $data['success'] = 1;
                   $data['message'] = 'Uploaded Successfully!';
                   $data['filepath'] = $filepath;
                   $data['extension'] = $ext;
    
                }else{
                   // Response
                   $data['success'] = 2;
                   $data['message'] = 'File not uploaded.'; 
                }
            }
            $currency = $this->request->getVar('currency');
            $reference = $this->request->getVar('reference');
            $exchange_rate = $this->request->getVar('exchange_rate');
            $despatch= $this->request->getVar('despatch');
            $wbs_element = $this->request->getVar('WBS_element');
            $profit_center = $this->request->getVar('profit_center');
            $order = $this->request->getVar('order');
            $text = $this->request->getVar('text');
            $Doc_No = $this->request->getVar('Doc_No');
            $code = $this->generateCode($Doc_No);
            $data = [
                'DOC_NO' => $code,
                'SHIPMENT_ID' => $shipmentID,
                'CONTRCT_NO' => $contractN,
                'KUNNR' => $buyer,
                'BLDAT' => $invoice_date,
                'BUDAT' => $posting_date,
                'ZFBDT' => $baseline_date,
                'ZTERM' => $payterm,
                'ATTCH' => $filepath,
                'XBLNR' => $reference,
                'KURSF' => $exchange_rate,
                'DSPTCH' => $despatch,
                'PROJK' => $wbs_element,
                'PRCTR' => $profit_center,
                'AUFNR' => $order,
                'ECURR' => $currency,
                'SGTXT' => $text,
                'USNAM' => session()->get('username'),
                'CPUDT' => Time::now()->format('Y-m-d H:i:s'),
                // 'DELBY' => session()->get('username'),
                // 'DELON' => Time::now()->format('Y-m-d H:i:s'),
                
            ];
            d($data);
            $insert_Data = array_filter($data, function($var){
                return $var != null;
            });
            $ModulSales = new DespatchInvoices();
            $ModulSales->save($insert_Data);
           $message = "Add data has been a success";

           return redirect()->to('despatchinvoice')->with('message', $message);
    }
    
   

    public function update()
    {   
        $data['title'] = "Edit Despatch Invoice";
        $filepath='';
        $shipmentID = $this->request->getVar('shipmentIDup');
        $contractN = $this->request->getVar('contractNup');
        $buyer = $this->request->getVar('Buyerup');
        $invoice_date = $this->request->getVar('invoice_dateup');
        $posting_date = $this->request->getVar('posting_dateup');
        $baseline_date = $this->request->getVar('baseline_dateup');
        $payterm = $this->request->getVar('paytermup');
        $id = $this->request->getVar('ID');
        $att = $this->request->getVar('attup');
        $validation = \Config\Services::validation();
        $att = $validation->setRules([
            'att' => 'uploaded[file]|max_size[file,30720]|ext_in[pdf],'
        ]);
        $xx = new DespatchInvoices();
        $x = $xx->find($id);
        if($file = $this->request->getFile('attup')) {
            if ($file->isValid() && ! $file->hasMoved()) {
                $old = $x['ATTCH'];
                //dd($old);
                if(file_exists('uploads/'.$old)){ //jika file sudah ada
                    unlink('uploads/'.$old);
                }
               $name = $file->getName();
               $ext = $file->getClientExtension();
               $filepath = WRITEPATH . 'uploads/' . $file->store();
               // Response
               $data['success'] = 1;
               $data['message'] = 'Uploaded Successfully!';
               $data['filepath'] = $filepath;
               $data['extension'] = $ext;
            }else{
               // Response
               $data['success'] = 2;
               $data['message'] = 'File not uploaded.'; 
            }
        }

        $referenceup = $this->request->getVar('referenceup');
        $exchange_rateup = $this->request->getVar('exchange_rateup');
        $despatch = $this->request->getVar('despatchup');
        $wbs_element = $this->request->getVar('WBS_elementup');
        $profit_center = $this->request->getVar('profit_centerup');
        $order = $this->request->getVar('orderup');
        $text = $this->request->getVar('textup');
        $Doc_No = $this->request->getVar('Doc_Noup');
        $code = $this->generateCode($Doc_No);
   
        $data = [
            'DOC_NO' => $code,
            'SHIPMENT_ID' => $shipmentID,
            'CONTRCT_NO' => $contractN,
            'KUNNR' => $buyer,
            'BLDAT' => $invoice_date,
            'BUDAT' => $posting_date,
            'ZFBDT' => $baseline_date,
            'ZTERM' => $payterm,
            'ATTCH' => $filepath,
            'XBLNR' => $referenceup,
            'KURSF' => $exchange_rateup,
            'DSPTCH' => $despatch,
            'PROJK' => $wbs_element,
            'PRCTR' => $profit_center,
            'AUFNR' => $order,
            'SGTXT' => $text,
            'EDTBY' => session()->get('username'),
            'EDTON' => Time::now()->format('Y-m-d H:i:s'),
        ];
        d($data);
  
        $insert_data = array_filter($data, function($var){
            return $var != null;
        });
        $DespatchUp = new DespatchInvoices();
        $DespatchUp->update($id, $insert_data);
       return redirect()->back()->with('message', 'A Despatch Invoice has been updated');
    }
    

    public function delete($id)
    {
        try {
            $MDMaster = new DespatchInvoices();
            $DeleteMaster = $MDMaster->find($id);
            $DeleteMaster['DELON'] = Time::now()->format('Y-m-d H:i:s');
            $DeleteMaster['DELBY'] = session()->get('username');
            var_dump($DeleteMaster);
            $MDMaster->save($DeleteMaster);

            $message = "Demurage Invoice has been Deleted";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
     return redirect()->to('despatchinvoice')->with('message', $message);
    }

}