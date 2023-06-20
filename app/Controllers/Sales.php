<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QualityReport;
use App\Models\GLogs;
use App\Models\Sales\MasterActivity;
use App\Models\Sales\MasterLaytime;
use App\Models\Sales\ContractOrder;
use App\Models\Sales\Laycan;
use App\Models\Sales\SalesRC;
use App\Models\Sales\SalesLaytime;
use App\Models\Sales\SalesLaytimeItem;
use App\Models\Sales\SalesShipment;
use App\Models\TCostmining;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;
use App\Models\Contractors;
use App\Models\CostType;
use App\Models\TCoalIndex;

class Sales extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }


    public function display_report()
    {
        $data['title'] = "Display Report";
        echo view('pages/sales/display_report', $data);
    }

    public function master_activity()
    {
        $data['title'] = "Master Activity";
        echo view('pages/master/master_activity', $data);
    }

    public function invoice()
    {
        $data['title'] = "Invoice";
        echo view('pages/sales/invoice', $data);
    }

    public function salesinvoice()
    {
        $data['title'] = "Invoice";
        echo view('pages/sales/salesinvoice', $data);
    }

    public function demurageinvoice()
    {
        $data['title'] = "Invoice";
        echo view('pages/sales/demurage-invoice', $data);
    }

    public function royaltyinvoice()
    {
        $data['title'] = "Invoice";

        $db = db_connect();
        $begda = $_GET['datfrom'] ?? false;
        $endda = $_GET['datto'] ?? false;

        if ($begda <> false and $endda == false) {
            $endda = $begda;
        }

        if ($begda <> false and $endda <> false) {
            $sql_data = "select t1.id, (select SHIPMENT_ID from T_SAL_SHIPMENT as ts where ts.contract_no = t1.CONTRCT_NO limit 1)as SHIPMENT_ID,
                        t1.CONTRCT_NO, t1.XBLNR, t1.DOC_NO, t1.BELNR, t1.STBLG, t1.CPUDT, t1.BUDAT, t1.MESSAGE_SAP, t1.STATUS_SAP,
                        t1.USNAM from FI_ROYLT_INV as t1 where t1.status = '1' and t1.BUDAT BETWEEN '" . $begda . "' and '" . $endda . "' order by t1.CONTRCT_NO asc;";
        } else {
            $sql_data = "select t1.id, (select SHIPMENT_ID from T_SAL_SHIPMENT as ts where ts.contract_no = t1.CONTRCT_NO limit 1)as SHIPMENT_ID,
                        t1.CONTRCT_NO, t1.XBLNR, t1.DOC_NO, t1.BELNR, t1.STBLG, t1.CPUDT, t1.BUDAT, t1.MESSAGE_SAP, t1.STATUS_SAP,
                        t1.USNAM from FI_ROYLT_INV as t1 where t1.status = '1' order by t1.CONTRCT_NO asc;";
        }
        $v_data = $db->query($sql_data);
        $data['tdata'] = $v_data->getResultArray();

        $sql_opt_cont = "select contract_no from T_SAL_SHIPMENT group by contract_no order by contract_no asc";
        $v_opt_cont = $db->query($sql_opt_cont);
        $data['opt_cont'] = $v_opt_cont->getResultArray();

        $sql_opt_curr = "select FCURR from FI_CUR_EXC group by FCURR order by FCURR asc;";
        $v_opt_curr = $db->query($sql_opt_curr);
        $data['opt_curr'] = $v_opt_curr->getResultArray();

        $sql_opt_bank = "select SAKNR, concat(SAKNR,' (',TXT50,')') as TXT50 
                        from FI_MD_GL where BUKRS ='HH10' and FDLEV in ('ZC','ZB') order by SAKNR";
        $v_opt_bank = $db->query($sql_opt_bank);
        $data['opt_bank'] = $v_opt_bank->getResultArray();

        $sql_opt_wbs = "select PSPNR, OBJNR, POSID, POST1 from T_PRPS where PBUKR = 'HH10' order by POSID asc";
        $v_opt_wbs = $db->query($sql_opt_wbs);
        $data['opt_wbs'] = $v_opt_wbs->getResultArray();

        $sql_opt_cost = "select KOSTL, concat(KOSTL,' (',LTEXT,')') as LTEXT 
                        from FI_CSKS where KHINR like 'HH10%' order by KOSTL asc";
        $v_opt_cost = $db->query($sql_opt_cost);
        $data['opt_cost'] = $v_opt_cost->getResultArray();

        $sql_opt_ord = "select AUFNR, IF((KTEXT<>'' or KTEXT<>null),
                        concat(AUFNR,' (', KTEXT,')'),AUFNR) as KTEXT 
                        from FI_AUFK  where BUKRS = 'HH10' and AUART <> '$$' order by AUFNR asc";
        $v_opt_ord = $db->query($sql_opt_ord);
        $data['opt_ord'] = $v_opt_ord->getResultArray();

        echo view('pages/sales/royaltyinvoice', $data);
    }

    public function add_royaltyinvoice()
    {
        try {
            $db = db_connect();
            $contractno = $this->request->getPost('contractno');
            $invdate = $this->request->getPost('invdate');
            $posdate = $this->request->getPost('posdate');
            $reference = $this->request->getPost('reference');
            $royalty = $this->request->getPost('royalty');
            $bankaccount = $this->request->getPost('bankaccount');
            $currency = $this->request->getPost('currency');
            $exchangerate = $this->request->getPost('exchangerate');
            // $formFile = $this->request->getPost('formFile');
            $filepath = "";
            $file = $this->request->getFile('formFile');
            if ($file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    // Get file name and extension
                    $name = $file->getName();
                    $ext = $file->getClientExtension();
                    $filepath = WRITEPATH . 'uploads/' . $file->store();
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

            $formFile = $filepath;
            $text = $this->request->getPost('text');
            $wbselement = $this->request->getPost('wbselement');
            $costcenter = $this->request->getPost('costcenter');
            $order = $this->request->getPost('order');
            $DOC_NO = "";
            $usernm = session()->get('username');
            $datetime_now = Time::now()->format('Y-m-d H:i:s');

            $qstr_insert = "INSERT INTO `FI_ROYLT_INV` (`CONTRCT_NO`, `BLDAT`, `BUDAT`, `ECURR`, `KURSF`, `ATTCH`,
                         `XBLNR`, `CNB_Acc`, `ROYLT`, `PROJK`, `KOSTL`, `AUFNR`, `SGTXT`, `DOC_NO`, `BELNR`, `STBLG`, `USNAM`, `CPUDT`)
                          VALUES (:CONTRCT_NO:, :BLDAT:, :BUDAT:, :ECURR:, :KURSF:, :ATTCH:, :XBLNR:, :CNB_Acc:, 
                          :ROYLT:, :PROJK:, :KOSTL:, :AUFNR:, :SGTXT:, :DOC_NO:, :BELNR:, :STBLG:, :USNAM:, :CPUDT:);";
            $qinsert = $db->query($qstr_insert, [
                'CONTRCT_NO' => $contractno,
                'BLDAT' => $invdate,
                'BUDAT' => $posdate,
                'ECURR' => $currency,
                'KURSF' => $exchangerate,
                'ATTCH' => $formFile,
                'XBLNR' => $reference,
                'CNB_Acc' => $bankaccount,
                'ROYLT' => $royalty,
                'PROJK' => $wbselement,
                'KOSTL' => $costcenter,
                'AUFNR' => $order,
                'SGTXT' => $text,
                'DOC_NO' => $DOC_NO,
                'BELNR' => '',
                'STBLG' => '',
                'USNAM' => $usernm,
                'CPUDT' => $datetime_now,
            ]);
            if ($qinsert) {
                $message = "Royatky Invoice has been created";

                // Nomor Urut Dokumen Invoice CMS dengan format (Tahun/Bulan/Sequence : YYYY/MM/XXXXX)
                $sql_get_id = "select id, year(CPUDT) as years, month(CPUDT) as months from FI_ROYLT_INV where CPUDT = '" . $datetime_now . "' and USNAM = '" . $usernm . "';";
                $get_id = $db->query($sql_get_id)->getFirstRow();

                $id0 = "";
                $do = 5 - strlen($get_id->id);
                if ($do > 0) {
                    for ($i = 1; $i <= $do; $i++) {
                        $id0 = "0" . $id0;
                    }
                }
                $DOC_NO = $get_id->years . "/" . $get_id->months . "/" . $id0 . $get_id->id;

                $sql_upd_doc_no = "UPDATE `FI_ROYLT_INV` SET `DOC_NO`='" . $DOC_NO . "' WHERE  `id`=" . $get_id->id . ";";
                $upd_doc_no = $db->query($sql_upd_doc_no);
            } else {
                $message = "Create data cancelled";
            }
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/sales/royaltyinvoice/")->with('message', $message);
    }

    public function get_royaltyinvoice($id)
    {
        header('Content-Type: application/json');

        $db = db_connect();
        $sql_get = "select t0.*,
                (select t1.TXT50 from FI_MD_GL as t1 where t1.BUKRS='HH10' and t1.FDLEV in ('ZC','ZB') and t1.SAKNR=t0.CNB_Acc limit 1) as BANKTEXT,
                (select t2.LTEXT from FI_CSKS as t2 where t2.KHINR like 'HH10%' and t2.KOSTL = t0.KOSTL) as COSTTEXT,
                (select t3.KTEXT from FI_AUFK as t3 where t3.BUKRS='HH10' and t3.AUFNR = t0.AUFNR) as ORDTEXT
                from FI_ROYLT_INV as t0 where id=" . $id;
        $qget = $db->query($sql_get)->getRowArray();
        return $this->response->setJSON($qget);
    }

    public function del_royaltyinvoice($id)
    {
        try {
            $db = db_connect();
            $sql_del = "UPDATE `FI_ROYLT_INV` SET `status`='0' WHERE `id`=" . $id;
            $qdel = $db->query($sql_del);
            if ($qdel) {
                $message = "Data has been deleted";
            } else {
                $message = "No data deleted";
            }
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/sales/royaltyinvoice/")->with('message', $message);
    }

    public function upd_royaltyinvoice()
    {
        $db = db_connect();
        $id = $this->request->getPost('id');
        $contractno = $this->request->getPost('edcontractno');
        $invdate = $this->request->getPost('edinvdate');
        $posdate = $this->request->getPost('edposdate');
        $reference = $this->request->getPost('edreference');
        $royalty = $this->request->getPost('edroyalty');
        $bankaccount = $this->request->getPost('edbankaccount');
        $currency = $this->request->getPost('edcurrency');
        $exchangerate = $this->request->getPost('edexchangerate');
        // $formFile = $this->request->getPost('formFile');
        $sql_cek = "select * from FI_ROYLT_INV where id = '" . $id . "';";
        $x = $db->query($sql_cek)->getFirstRow();
        $filepath = "";
        $file = $this->request->getFile('edformFile');
        if ($file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $old = $x->ATTCH;
                if (file_exists('uploads/' . $old) && $old != "") { //jika file sudah ada
                    $texk = 'uploads/' . $old;
                    unlink('uploads/' . $old);
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
                // Response
                $data['success'] = 2;
                $data['message'] = 'File not uploaded.';
            }
        }

        $formFile = $filepath;
        $text = $this->request->getPost('edtext');
        $wbselement = $this->request->getPost('edwbselement');
        $costcenter = $this->request->getPost('edcostcenter');
        $order = $this->request->getPost('edorder');

        try {
            $db = db_connect();
            $qstr_upd = "UPDATE `FI_ROYLT_INV` SET `CONTRCT_NO`=:CONTRCT_NO:, `BLDAT`=:BLDAT:, `BUDAT`=:BUDAT:, `ECURR`=:ECURR:, 
                        `KURSF`=:KURSF:, `ATTCH`=:ATTCH:, `XBLNR`=:XBLNR:, `CNB_Acc`=:CNB_Acc:, `ROYLT`=:ROYLT:, `PROJK`=:PROJK:,
                        `KOSTL`=:KOSTL:, `AUFNR`=:AUFNR:, `SGTXT`=:SGTXT: WHERE `id`=:id:;";
            $qdel = $db->query($qstr_upd, [
                'id' => $id,
                'CONTRCT_NO' => $contractno,
                'BLDAT' => $invdate,
                'BUDAT' => $posdate,
                'ECURR' => $currency,
                'KURSF' => $exchangerate,
                'ATTCH' => $formFile,
                'XBLNR' => $reference,
                'CNB_Acc' => $bankaccount,
                'ROYLT' => $royalty,
                'PROJK' => $wbselement,
                'KOSTL' => $costcenter,
                'AUFNR' => $order,
                'SGTXT' => $text,
            ]);
            if ($qdel) {
                $message = "Royalty Invoice has been updated";
            } else {
                $message = "No data update";
            }
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/sales/royaltyinvoice/")->with('message', $message);
    }

    public function dwl_royaltyinvoice($id)
    {
        // d($id);
        $db = db_connect();
        $sql_cek = "select * from FI_ROYLT_INV where id = '" . $id . "';";
        $data = $db->query($sql_cek)->getFirstRow();

        return $this->response->download($data->ATTCH, null);
        echo view('pages/sales/royaltyinvoice', $data->ATTCH);
    }

    public function despatchinvoice()
    {
        $data['title'] = "Invoice";
        echo view('pages/sales/despatchinvoice', $data);
    }

    public function get_master_activity()
    {
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_MASTER_ACTIVITY where status!='X'");
        return $this->respond($query->getResult(), 200);
    }

    public function master_activity_insert()
    {
        $data = $this->request->getJSON();
        $db = \Config\Database::connect();
        $cek = $db->query("select * from T_SAL_MASTER_ACTIVITY where sequence='" . $data->sequence . "'");
        if (count($cek->getResult()) > 0) {
            return $this->respond(array("message" => "Sequence code already on database"), 404, "Sequence code already on database");
        } else {
            $MasterActivity = new MasterActivity();
            $MasterActivity->save($data);
            $this->GLogs->after_insert('T_SAL_MASTER_ACTIVITY');
            return $this->respond($data, 200);
        }
    }

    public function master_activity_update($id)
    {
        $data = $this->request->getJSON();
        $MasterActivity = new MasterActivity();
        $MasterActivity->find($id);
        $this->GLogs->before_update($id, 'T_SAL_MASTER_ACTIVITY');
        $MasterActivity->update($id, $data);
        $this->GLogs->after_update($id, 'T_SAL_MASTER_ACTIVITY');
        return $this->respond($MasterActivity, 200);
    }

    public function master_activity_delete($id)
    {
        $this->GLogs->before_delete($id, 'T_SAL_MASTER_ACTIVITY');
        $db = \Config\Database::connect();
        $query = $db->query("delete from T_SAL_MASTER_ACTIVITY where id='$id'");
        $data = array(array("data" => $id));
        return $this->respond($data, 200);
    }

    public function master_laytime()
    {
        $data['title'] = "Master Laytime";
        echo view('pages/master/master_laytime', $data);
    }

    public function get_master_laytime()
    {
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_MASTER_LAYTIME where status!='X'");
        return $this->respond($query->getResult(), 200);
    }

    public function master_laytime_insert()
    {
        $data = $this->request->getJSON();
        $db = \Config\Database::connect();
        $cek = $db->query("select * from T_SAL_MASTER_LAYTIME where code='" . $data->code . "'");
        if (count($cek->getResult()) > 0) {
            return $this->respond(array("message" => "code already on database"), 404, "code already on database");
        } else {
            $MasterLaytime = new MasterLaytime();
            $MasterLaytime->save($data);
            $this->GLogs->after_insert('T_SAL_MASTER_LAYTIME');
            return $this->respond($data, 200);
        }
    }

    public function master_laytime_update($id)
    {
        $data = $this->request->getJSON();
        $MasterLaytime = new MasterLaytime();
        $MasterLaytime->find($id);
        $this->GLogs->before_update($id, 'T_SAL_MASTER_LAYTIME');
        $MasterLaytime->update($id, $data);
        $this->GLogs->after_update($id, 'T_SAL_MASTER_LAYTIME');
        return $this->respond($MasterLaytime, 200);
    }

    public function master_laytime_delete($id)
    {
        $this->GLogs->before_delete($id, 'T_SAL_MASTER_LAYTIME');
        $db = \Config\Database::connect();
        $query = $db->query("delete from T_SAL_MASTER_LAYTIME where id='$id'");
        $data = array(array("data" => $id));
        return $this->respond($data, 200);
    }

    // =======================================
    public function product_material()
    {
        $data['title'] = "Master Activity";
        echo view('pages/sales/product_material', $data);
    }

    public function get_product_material()
    {
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_MDMATERIAL");
        return $this->respond($query->getResult(), 200);
    }

    // ============= CUSTOMER =========================
    public function get_customer()
    {
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_MDCUSTOMER where BUKRS='HH10'");
        return $this->respond($query->getResult(), 200);
    }

    //============== SALES ORDER =========================
    public function sales_order()
    {
        $data['title'] = "Sales Order";
        echo view('pages/sales/sales_order', $data);
    }

    public function sales_to_contract()
    {
        $data['title'] = "Sales To Contract";
        echo view('pages/sales/sales_to_contract', $data);
    }

    public function get_sales_order()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        @$blank = $_GET['blank'];
        @$status = $_GET['status'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            if ($blank) {
                $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status='1' AND date between '$dari_tanggal' AND '$sampai_tanggal'  ");
                // $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status!='2' AND date between '$dari_tanggal' AND '$sampai_tanggal' AND contract_no=''");
            } else {
                $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status!='2' AND date between '$dari_tanggal' AND '$sampai_tanggal' ");
            }
        } else {
            if ($status) {
                $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status='$status' ");
            } else {
                $query = $db->query("select * from T_SAL_CONTRACT_ORDER where status!='2' ");
            }
        }
        return $this->respond($query->getResult(), 200);
    }


    public function sales_order_insert()
    {
        $data = $this->request->getJSON();
        $db = \Config\Database::connect();
        $query = $db->query("SELECT id as max_id FROM T_SAL_CONTRACT_ORDER ORDER BY max_id DESC LIMIT 1");
        $res = $query->getResult();
        $ContractOrder = new ContractOrder();
        if (count($res) > 0) {
            $max_id = $res[0]->max_id;
            $new_id = "SO" . substr(date("Y"), 2, 2) . date("m") . str_pad(substr($max_id, -4) + 1, 4, "0", STR_PAD_LEFT);
            $data->id = $new_id;
        } else {
            $new_id = "SO" . substr(date("Y"), 2, 2) . date("m") . "0001";
            $data->id = $new_id;
        }
        $ContractOrder->save($data);
        return $this->respond($data, 200);
    }

    public function sales_order_update($id)
    {
        $data = $this->request->getJSON();
        $ContractOrder = new ContractOrder();
        $ContractOrder->find($id);
        // $this->GLogs->before_update($id,'T_SAL_CONTRACT_ORDER');
        $ContractOrder->update($id, $data);
        // $this->GLogs->after_update($id,'T_SAL_CONTRACT_ORDER');
        return $this->respond($ContractOrder, 200);
    }
    public function sales_order_update_mobile()
    {
        $id = $_GET['id'];
        // $data=$this->request->getJSON();
        // var_dump($data);
        // $ContractOrder = new ContractOrder();
        // $ContractOrder->find($id);
        // $ContractOrder->update($id, $data);
        return $this->respond($id, 200);
    }

    public function sales_order_delete($id)
    {
        $this->GLogs->before_delete($id, 'T_SAL_CONTRACT_ORDER');
        $db = \Config\Database::connect();
        $date = date("Y-m-d H:i:s");
        $query = $db->query("update T_SAL_CONTRACT_ORDER set deleted_at='$date' where id='$id'");
        // $query = $db->query("delete from T_SAL_CONTRACT_ORDER where id='$id'");
        $data = array(array("data" => $id));
        return $this->respond($data, 200);
    }
    //=======================================
    public function get_fi_cur_exc()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("select * from FI_CUR_EXC where GDATU between '$dari_tanggal' AND '$sampai_tanggal' order by GDATU desc");
        } else {
            $year = date("Y");
            $query = $db->query("select * from FI_CUR_EXC where GDATU LIKE '$year%'");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function get_t_sal_target()
    {
        @$month = $_GET['month'];
        @$year = $_GET['year'];
        $db = \Config\Database::connect();
        if ($month && $year) {
            $query = $db->query("select * from T_SAL_TARGET where month='$month' AND year='$year'");
        } else {
            $year = date("Y");
            $query = $db->query("select * from T_SAL_TARGET where year LIKE '$year%'");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function get_t_sal_shipment()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("select * from T_SAL_SHIPMENT where receipt_date between '$dari_tanggal' AND '$sampai_tanggal' OR discharging_date between '$dari_tanggal' AND '$sampai_tanggal' order by receipt_date desc");
        } else {
            $year = date("Y");
            $query = $db->query("select * from T_SAL_SHIPMENT where receipt_date LIKE '$year%' OR discharging_date LIKE '$year%'");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function get_t_sal_price()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("select AVG(final_price) as final_price2, SUM(amount) as amount2 from T_SAL_PRICE where curr='IDR' AND date_final between '$dari_tanggal' AND '$sampai_tanggal' order by date_final desc");
            $query2 = $db->query("select AVG(final_price) as final_price2, SUM(amount) as amount2 from T_SAL_PRICE where curr='USD' AND date_final between '$dari_tanggal' AND '$sampai_tanggal' order by date_final desc");
        } else {
            $year = date("Y");
            $query = $db->query("select AVG(final_price) as final_price2, SUM(amount) as amount2 from T_SAL_PRICE where curr='IDR' AND date_final LIKE '$year%'");
            $query2 = $db->query("select AVG(final_price) as final_price2, SUM(amount) as amount2 from T_SAL_PRICE where curr='USD' AND date_final LIKE '$year%'");
        }
        $data = ["IDR" => $query->getResult(), "USD" => $query2->getResult()];
        return $this->respond($data, 200);
    }

    public function get_T_SAL_CONTRACT_ORDER()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("select * from T_SAL_CONTRACT_ORDER where date between '$dari_tanggal' AND '$sampai_tanggal'  order by date desc");
        } else {
            $query = $db->query("select * from T_SAL_CONTRACT_ORDER ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function get_t_sal_approval_step()
    {
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_APPROVAL_STEP ");
        return $this->respond($query->getResult(), 200);
    }

    // ================================================
    public function contract_order_approval()
    {
        $data['title'] = "Contract Order Approval";
        echo view('pages/sales/contract_order_approval', $data);
    }

    public function get_contract_order_approval()
    {
    }

    // ================== SALES SHIPMENT ===================
    //============== SALES ORDER =========================
    public function sales_shipment()
    {
        $data['title'] = "Sales Order";
        echo view('pages/sales/sales_shipment', $data);
    }

    public function get_sales_shipment()
    {
        @$laycan = $_GET['laycan'];
        @$type = $_GET['type'];
        $db = \Config\Database::connect();
        if ($laycan) {
            $query = $db->query("select tb1.* , COALESCE(DATEDIFF(tb2.draft_date, tb2.issue_date),0)+COALESCE(DATEDIFF(tb2.issue_date, tb2.received_date),0) as total_days
            from T_SAL_SHIPMENT tb1 left join T_SAL_LAYCAN tb2 on tb2.shipment_no=tb1.shipment_id AND tb2.contract_no=tb1.contract_no where tb1.shipment_id!='' order by tb1.id desc");
        } else if ($type) {
            $query = $db->query("select * from T_SAL_SHIPMENT tb1 where tb1.shipment_id!='' AND tb1.shipment_id NOT IN (select shipment_no from T_SAL_LAYTIME) order by tb1.id desc");
        } else {
            $query = $db->query("select * from T_SAL_SHIPMENT where shipment_id!='' order by id desc");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function sales_shipment_insert()
    {

        $data = $this->request->getJSON();
        $SalesShipment = new SalesShipment();
        $db = \Config\Database::connect();
        // $this->GLogs->after_insert('T_SAL_SHIPMENT');
        $query = $db->query("SELECT id as max_id FROM T_SAL_SHIPMENT ORDER BY id DESC LIMIT 1");
        $res = $query->getResult();
        if (count($res) > 0) {
            $max_id = $res[0]->max_id;
            $new_id = "SH" . date("Y") . str_pad(substr($max_id, -4) + 1, 4, "0", STR_PAD_LEFT);
            $data->shipment_id = $new_id;
        } else {
            $new_id = "SH" . date("Y") . "0001";
            $data->shipment_id = $new_id;
        }
        try {
            $SalesShipment->save($data);
            $db = \Config\Database::connect();
            $query = $db->query("select * from T_SAL_SHIPMENT order by id desc limit 1");
            return $this->respond($query->getResult(), 200);
        } catch (\Exception $e) {
            $max_id = $res[0]->max_id;
            $new_id = "SH" . date("Y") . str_pad(substr($max_id, -4) + 2, 4, "0", STR_PAD_LEFT);
            $data->shipment_id = $new_id;
            $SalesShipment->save($data);
            $db = \Config\Database::connect();
            $query = $db->query("select * from T_SAL_SHIPMENT order by id desc limit 1");
            return $this->respond($query->getResult(), 200);
        }
        //  return $this->respond($data, 200);
    }


    public function sales_shipment_update($id)
    {
        $data = $this->request->getJSON();
        $SalesShipment = new SalesShipment();
        $SalesShipment->find($id);
        $this->GLogs->before_update($id, 'T_SAL_SHIPMENT');
        $SalesShipment->update($id, $data);
        $this->GLogs->after_update($id, 'T_SAL_SHIPMENT');
        return $this->respond($SalesShipment, 200);
    }

    public function sales_shipment_delete($id)
    {
        // $QualityReport = new QualityReport();
        // $QualityReport->find($id);
        $this->GLogs->before_delete($id, 'T_SAL_SHIPMENT');
        $db = \Config\Database::connect();
        $query = $db->query("delete from T_SAL_SHIPMENT where id='$id'");
        // $QualityReport->delete($id);
        $data = array(array("data" => $id));
        return $this->respond($data, 200);
    }
    //=======================================
    //============== SALES VESSEL LAYCAN =========================
    public function sales_laycan()
    {
        $data['title'] = "Vessel Update";
        echo view('pages/sales/sales_laycan', $data);
    }

    public function get_sales_laycan()
    {
        @$shipment_no = $_GET['shipment_no'];
        @$contract_no = $_GET['contract_no'];
        $db = \Config\Database::connect();
        if ($shipment_no && $contract_no) {
            $query = $db->query("select tb1.* from T_SAL_LAYCAN tb1 
            where tb1.shipment_no='$shipment_no' AND tb1.contract_no='$contract_no' 
            order by tb1.id desc");
        } else {
            $query = $db->query("select * from T_SAL_LAYCAN order by id desc");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function sales_laycan_insert()
    {

        $data = $this->request->getJSON();
        $Laycan = new Laycan();
        $Laycan->save($data);
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_LAYCAN order by id desc limit 1");
        $data = $query->getResult();
        // $this->GLogs->after_insert('T_SAL_LAYCAN');
        return $this->respond($data, 200);
    }

    public function sales_laycan_method()
    {
        $data = $this->request->getJSON();
        $shipment_no = $data->shipment_no;
        $contract_no = $data->contract_no;
        $activity = $data->item_activity;
        $status = $data->status;
        $draft_date = $data->draft_date ?? "";
        $issue_date = $data->issue_date ?? "";
        $received_date = $data->received_date ?? "";
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_LAYCAN where shipment_no='$shipment_no' AND contract_no='$contract_no' AND item_activity='$activity' order by id desc limit 1");
        $total = $query->getResult();
        $qry = "";
        if (count($total) > 0) {
            if ($draft_date != '') {
                $qry = "update T_SAL_LAYCAN set status='$status', draft_date='$draft_date' where shipment_no='$shipment_no' AND contract_no='$contract_no' AND item_activity='$activity' ";
                $query = $db->query($qry);
            }
            if ($issue_date != '') {
                $qry = "update T_SAL_LAYCAN set status='$status', issue_date='$issue_date' where shipment_no='$shipment_no' AND contract_no='$contract_no' AND item_activity='$activity' ";
                $query = $db->query($qry);
            }
            if ($received_date != '') {
                $qry = "update T_SAL_LAYCAN set status='$status', received_date='$received_date' where shipment_no='$shipment_no' AND contract_no='$contract_no' AND item_activity='$activity' ";
                $query = $db->query($qry);
            }
        } else {
            $qry = "insert into T_SAL_LAYCAN (shipment_no,contract_no,item_activity,status,draft_date,issue_date,received_date) VALUES ('$shipment_no','$contract_no','$activity','$status','$draft_date','$issue_date','$received_date')";
            $query = $db->query($qry);
        }
        // return $this->respond(array("status"=>'sukses'),200);
        echo $qry;
    }

    public function sales_laycan_update($id)
    {
        $data = $this->request->getJSON();
        $Laycan = new Laycan();
        $Laycan->find($id);
        $this->GLogs->before_update($id, 'T_SAL_LAYCAN');
        $Laycan->update($id, $data);
        $this->GLogs->after_update($id, 'T_SAL_LAYCAN');
        return $this->respond($Laycan, 200);
    }

    public function sales_laycan_delete($id)
    {
        // $QualityReport = new QualityReport();
        // $QualityReport->find($id);
        $this->GLogs->before_delete($id, 'T_SAL_LAYCAN');
        $db = \Config\Database::connect();
        $query = $db->query("delete from T_SAL_LAYCAN where id='$id'");
        // $QualityReport->delete($id);
        $data = array(array("data" => $id));
        return $this->respond($data, 200);
    }
    //=======================================

    //============== SALES RC =========================
    public function sales_rc()
    {
        $data['title'] = "Vessel Laycan";
        echo view('pages/sales/sales_rc', $data);
    }

    public function get_sales_rc()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("select * from T_SAL_RC where date between '$dari_tanggal' AND '$sampai_tanggal' order by id desc");
        } else {
            $query = $db->query("select * from T_SAL_RC ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function sales_rc_insert()
    {

        $data = $this->request->getJSON();
        $SalesRC = new SalesRC();
        $SalesRC->save($data);
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_RC order by id desc limit 1");
        $data = $query->getResult();
        // $this->GLogs->after_insert('T_SAL_RC');
        return $this->respond($data, 200);
    }

    public function sales_rc_update($id)
    {
        $data = $this->request->getJSON();
        $SalesRC = new SalesRC();
        $SalesRC->find($id);
        $this->GLogs->before_update($id, 'T_SAL_RC');
        $SalesRC->update($id, $data);
        $this->GLogs->after_update($id, 'T_SAL_RC');
        return $this->respond($SalesRC, 200);
    }

    public function sales_rc_delete($id)
    {
        // $QualityReport = new QualityReport();
        // $QualityReport->find($id);
        $this->GLogs->before_delete($id, 'T_SAL_RC');
        $db = \Config\Database::connect();
        $query = $db->query("delete from T_SAL_RC where id='$id'");
        // $QualityReport->delete($id);
        $data = array(array("data" => $id));
        return $this->respond($data, 200);
    }
    //=================  ======================
    public function sales_laytime()
    {
        $data['title'] = "Sales Laytime";
        echo view('pages/sales/sales_laytime', $data);
    }

    public function sales_laytime_pdf()
    {
        $data['title'] = "Sales Laytime PDF";
        echo view('pages/sales/sales_laytime_pdf', $data);
    }

    public function sales_shipment_pdf()
    {
        $data['title'] = "Sales Shipment PDF";
        $db = \Config\Database::connect();
        @$id = $_GET['id'];
        if ($id) {
            $query = $db->query("select * from T_SAL_SHIPMENT  tb1 left join T_SAL_CONTRACT_ORDER tb2 on tb2.id=tb1.contract_id where tb1.id='$id'");
            $data['shipment'] = $query->getResult();
            echo view('pages/sales/sales_shipment_pdf', $data);
        }
    }

    public function get_sales_laytime()
    {
        @$dari_tanggal = $_GET['dari_tanggal'];
        @$sampai_tanggal = $_GET['sampai_tanggal'];
        @$id = $_GET['id'];
        $db = \Config\Database::connect();
        if ($dari_tanggal && $sampai_tanggal) {
            $query = $db->query("select * from T_SAL_LAYTIME where vessel_arrived_date between '$dari_tanggal' AND '$sampai_tanggal' order by id desc");
        } else if ($id) {
            $query = $db->query("select * from T_SAL_LAYTIME where id='$id'");
        } else {
            $query = $db->query("select * from T_SAL_LAYTIME ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function sales_laytime_insert()
    {

        $data = $this->request->getJSON();
        $SalesLaytime = new SalesLaytime();
        $SalesLaytime->save($data);
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_LAYTIME order by id desc limit 1");
        $data = $query->getResult();
        return $this->respond($data, 200);
    }

    public function sales_laytime_update($id)
    {
        $data = $this->request->getJSON();
        $SalesLaytime = new SalesLaytime();
        $SalesLaytime->find($id);
        $this->GLogs->before_update($id, 'T_SAL_LAYTIME');
        $SalesLaytime->update($id, $data);
        $this->GLogs->after_update($id, 'T_SAL_LAYTIME');
        return $this->respond($SalesLaytime, 200);
    }

    public function sales_laytime_delete($id)
    {
        $this->GLogs->before_delete($id, 'T_SAL_LAYTIME');
        $db = \Config\Database::connect();
        $query = $db->query("delete from T_SAL_LAYTIME where id='$id'");
        $data = array(array("data" => $id));
        return $this->respond($data, 200);
    }
    //=======================================

    //  =============== SALES LAYTIME ITEM ===================
    public function get_sales_laytime_item()
    {
        @$laytime_id = $_GET['id'];
        $db = \Config\Database::connect();
        if ($laytime_id) {
            $query = $db->query("select * from T_SAL_LAYTIME_ITEM where laytime_id ='$laytime_id' order by id asc");
        } else {
            $query = $db->query("select * from T_SAL_LAYTIME_ITEM ");
        }
        return $this->respond($query->getResult(), 200);
    }

    public function sales_laytime_item_insert()
    {

        $data = $this->request->getJSON();
        $SalesLaytimeItem = new SalesLaytimeItem();
        $SalesLaytimeItem->save($data);
        $db = \Config\Database::connect();
        $query = $db->query("select * from T_SAL_LAYTIME_ITEM order by id desc limit 1");
        $data = $query->getResult();
        return $this->respond($data, 200);
    }

    public function sales_laytime_item_update($id)
    {
        $data = $this->request->getJSON();
        $SalesLaytimeItem = new SalesLaytimeItem();
        $SalesLaytimeItem->find($id);
        //  $this->GLogs->before_update($id,'T_SAL_LAYTIME_ITEM');
        $SalesLaytimeItem->update($id, $data);
        //  $this->GLogs->after_update($id,'T_SAL_LAYTIME_ITEM');
        return $this->respond($SalesLaytimeItem, 200);
    }

    public function sales_laytime_item_delete($id)
    {
        $this->GLogs->before_delete($id, 'T_SAL_LAYTIME_ITEM');
        $db = \Config\Database::connect();
        $query = $db->query("delete from T_SAL_LAYTIME_ITEM where id='$id'");
        $data = array(array("data" => $id));
        return $this->respond($data, 200);
    }
    //  ======================================

    public function generate_sales_order_pdf()
    {
        $no = $_GET['contract_no'];
        $id = $_GET['id'];
        $db = \Config\Database::connect();
        $path = WRITEPATH . "uploads/contract/contract_order_$no.pdf";
        $path = str_replace("\\", "\\\\", $path);
        $db->query("update T_SAL_CONTRACT_ORDER set pdf='$path' where id='$id'");
        // $mpdf = new \Mpdf\Mpdf();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf']);
        $html = view('pages/sales/sales_order_pdf.php', []);
        $mpdf->WriteHTML($html);
        $mpdf->Output($path, 'F'); // opens in browser
        echo "update T_SAL_CONTRACT_ORDER set pdf='$path' where id='$id'";
    }

    public function generate_sales_shipment_pdf()
    {
        $no = $_GET['contract_no'];
        $id = $_GET['id'];
        $db = \Config\Database::connect();
        $path = WRITEPATH . "uploads/contract/contract_order_$no.pdf";
        $path = str_replace("\\", "\\\\", $path);
        $db->query("update T_SAL_CONTRACT_ORDER set pdf='$path' where id='$id'");
        // $mpdf = new \Mpdf\Mpdf();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mpdf']);
        $html = view('pages/sales/sales_shipment_pdf.php', []);
        $mpdf->WriteHTML($html);
        $mpdf->Output($path, 'F'); // opens in browser
        echo "update T_SAL_CONTRACT_ORDER set pdf='$path' where id='$id'";
    }


    // sani coal index
    public function coal_index()
    {
        $data['title'] = "Coal Index";
        // echo view("pages/sales/coal_index", $data);
        $TCoalindex = new TCoalIndex();
        $builder = $TCoalindex->builder();
        $data['t_coalindex'] = $builder->select("*")
            ->where("deletion_status = '0'")
            ->get()->getResultArray();
        // dd($data['t_coalindex']);
        echo view('pages/sales/coal_index', $data);
    }

    public function coal_add()
    {
        $month = $this->request->getVar('month');
        $year = $this->request->getVar('yearIndex');
        $indexType = $this->request->getVar('typeIndex');
        $indexQty = $this->request->getVar('indexQty');
        $dateIndex = $this->request->getVar('dateIndex');

        $model = new TCoalIndex();
        $model->save([
            'month_index' => $month,
            'year_index' => $year,
            'index_type' => $indexType,
            'index_qty' => $indexQty,
            // 'date_index' => Time::now()->format('Y-m-d'),
            'date_index' => $dateIndex,
            'create_by' => session()->get('username'),
            'create_on' => Time::now()->format('Y-m-d H:i:s'),
            'deletion_status' => '0'
        ]);
        return redirect()->to("/sales/coal/")->with('message', 'A coal index has been created');
    }

    public function coal_edit($id_coalindex)
    {
        $data['title'] = "Edit - Coal Index";

        $model_coal = new TCoalIndex();
        $builder = $model_coal->builder();
        $data['month_index'] = $builder->select("*")
            ->where("id_coalindex", $id_coalindex)
            ->get()->getRowArray();

        $data['coal_index'] = $builder->select("distinct(index_type)")
            ->get()->getResultArray();
        // dd($data['t_coalindex']);
        echo view('pages/sales/edit_coal_index', $data);
    }

    public function coal_update()
    {
        $id_coalindex = $this->request->getVar('id_coalindex');
        $year = $this->request->getVar('yearIndex');
        $month_index = $this->request->getVar('month');
        $index_type = $this->request->getVar('index_type');
        $index_qty = $this->request->getVar('index_qty');
        $date_index = $this->request->getVar('dateIndex');

        // dd($id_coalindex, $month_index, $index_type, $index_qty);

        $cost_update = new TCoalIndex();

        $cost_update->save([
            'id_coalindex' => $id_coalindex,
            'month_index' => $month_index,
            'year_index' => $year,
            'index_type' => $index_type,
            'index_qty' => $index_qty,
            'date_index' => $date_index,
            'create_by' => session()->get('username'),
            'create_on' => Time::now()->format('Y-m-d H:i:s')
        ]);
        return redirect()->to("/sales/coal/")->with('message', 'A coal index has been updated');
    }

    public function coal_delete($id_coalindex)
    {
        $model_coal = new TCoalIndex();

        $model_coal->update(
            $id_coalindex,
            [
                'deletion_status' => '1',
                'change_by' => session()->get('username'),
                'change_on' => Time::now()->format('Y-m-d H:i:s')
            ]
        );
        return redirect()->to("/sales/coal/")->with('message', 'A coal index has been created');
    }


    // sani cost mining
    public function cost_index()
    {
        $data['title'] = "Add Cost Mining";

        $cost = new TCostmining();
        $builder = $cost->builder();
        $builder->select('t_costmining.*, md_contractors.contractor_name, md_costtype.cost_type')
            ->join('md_contractors', 't_costmining.id_contractor = md_contractors.id')
            ->join('md_costtype', 't_costmining.id_costtype = md_costtype.id_costtype')
            ->where("t_costmining.Deletion_status", "0");
        $data['cost'] = $builder->get()->getResultArray();
        // dd($data);

        // contractor
        $contractorSales = new Contractors();
        $builder = $contractorSales->builder();
        $builder->select('md_contractors.*');
        $data['contractor'] = $builder->get()->getResultArray();

        // cost type
        $costtype = new CostType();
        $builder = $costtype->builder();
        $builder->select('md_costtype.*');
        $data['costtype'] = $builder->get()->getResultArray();

        echo view("pages/sales/addcost", $data);
    }

    public function cost_add()
    {
        $year = $this->request->getVar('year');
        $month = $this->request->getVar('month');
        $contractor = $this->request->getVar('id_contractor');
        $cost_type = $this->request->getVar('id_costtype');
        $cost = $this->request->getVar('cost');

        $model = new TCostmining();
        $model->save([
            'year' => $year,
            'month' => $month,
            'id_contractor' => $contractor,
            'id_costtype' => $cost_type,
            'cost' => $cost,
            'create_by' => session()->get('username'),
            'create_on' => Time::now()->format('Y-m-d H:i:s')
        ]);
        return redirect()->to("/sales/costmining/")->with('message', 'A cost contractor has been created');
    }

    public function cost_edit($id_costmining)
    {
        $data['title'] = "Edit - Cost Mining";

        $cost = new TCostmining();
        $builder = $cost->builder();
        $builder->select('t_costmining.*, md_contractors.contractor_name, md_costtype.cost_type')
            ->join('md_contractors', 't_costmining.id_contractor = md_contractors.id')
            ->join('md_costtype', 't_costmining.id_costtype = md_costtype.id_costtype')
            ->where("id_costmining", $id_costmining);
        $data['cost'] = $builder->get()->getRowArray();

        // dd($data['cost']);

        // contractor
        $contractorSales = new Contractors();
        $builder = $contractorSales->builder();
        $builder->select('md_contractors.*');
        $data['contractor'] = $builder->get()->getResultArray();

        // cost type
        $costtype = new CostType();
        $builder = $costtype->builder();
        $builder->select('md_costtype.*');
        $data['costtype'] = $builder->get()->getResultArray();

        echo view('pages/sales/edit_cost_mining', $data);
    }

    public function cost_update()
    {
        $id_costmining = $this->request->getVar('id_costmining');
        $year = $this->request->getVar('year');
        $month = $this->request->getVar('month');
        $contractor = $this->request->getVar('id_contractor');
        $cost_type = $this->request->getVar('id_costtype');
        $cost = $this->request->getVar('cost');

        $cost_update = new TCostmining();

        $cost_update->save([
            'id_costmining' => $id_costmining,
            'year' => $year,
            'month' => $month,
            'id_contractor' => $contractor,
            'id_costtype' => $cost_type,
            'cost' => $cost,
            'create_by' => session()->get('username'),
            'create_on' => Time::now()->format('Y-m-d H:i:s')
        ]);
        return redirect()->to("/sales/costmining/")->with('message', 'A cost contractor has been updated');
    }

    public function cost_delete($id_costmining)
    {
        $cost_mining = new TCostmining();


        $cost_mining->update(
            $id_costmining,
            [
                'deletion_status' => '1',
                'change_by' => session()->get('username'),
                'change_on' => Time::now()->format('Y-m-d H:i:s')
            ]
        );

        return redirect()->to("/sales/costmining/")->with('message', 'A cost contractor has been deleted');
    }


    // // == #Tempcode Malik
}
