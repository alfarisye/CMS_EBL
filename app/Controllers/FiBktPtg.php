<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\Message;
use CodeIgniter\I18n\Time;

use function PHPUnit\Framework\isNull;

class FiBktPtg extends BaseController
{
    public function index()
    {
        $data['title'] = "CMS – List Bukti Potong PPh 22";

        $tgl1 = $_GET['date1'] ?? false;
        $tgl2 = $_GET['date2'] ?? false;

        if ($tgl1 == "") {
            $tgl1 = date("Y-m")."-01";
        }
        if ($tgl2 == "") {
            $tgl2 = date("Y-m-d");
        }

        $db = db_connect();
        $param = "and BUDAT between '".$tgl1."' and '".$tgl2."'";

        $qstr_bkt_ptg = "
        select tb0.*, tb3.BKP, tb3.NBKP, tb3.EDBY, tb3.EDON, tb3.EDAT, IFNULL(tb3.id,0) as id_bkt_ptg from (
            select concat(tb1.GJAHR, tb1.BELNR) as id_doc, tb1.*, tb2.KUNNR, 
            (select tbc.NAME1 from T_MDCUSTOMER tbc where tbc.KUNNR = tb2.KUNNR and tbc.BUKRS = tb1.BUKRS) as cst_nm from (
                select BUDAT, BUKRS, HKONT, GJAHR, BELNR, BKTXT, IF(SHKZG = 'H',DMBTR*-1,DMBTR) AS DMBTR 
                from FI_ACT_TRS where HKONT = '1100610053' and BLART = 'DR' and AUGBL = '' ".$param." ) tb1
            join FI_ACT_TRS tb2 on tb1.BELNR = tb2.BELNR and tb1.GJAHR = tb2.GJAHR and tb1.BUKRS = tb2.BUKRS
            where tb2.KUNNR <> '' group by BUDAT, BUKRS, HKONT, GJAHR, BELNR, BKTXT, KUNNR, DMBTR) tb0 
        left join FI_BKT_PTG tb3 on tb3.BUKRS = tb0.BUKRS and tb3.BELNR = tb0.BELNR and tb3.GJAHR = tb0.GJAHR 
        order by GJAHR desc, BELNR desc";
        // dd($qstr_bkt_ptg);
        $q_bkt_ptg = $db->query($qstr_bkt_ptg);
        $data['bkt_ptg'] = $q_bkt_ptg->getResultArray();
        $data['tanggal1'] = $tgl1;
        $data['tanggal2'] = $tgl2;

        echo view('pages/finance/pph22', $data);
    }

    public function get($id)
    {   
        header('Content-Type: application/json');
        
        $db = db_connect();
        
        $qstr_get = "select tb0.*, tb3.BKP, tb3.NBKP, tb3.id as id_bkt_ptg
                    from (select concat(tb1.GJAHR, tb1.BELNR) as id_doc,
                    tb1.*, tb2.KUNNR, (select tbc.NAME1 from T_MDCUSTOMER tbc where tbc.KUNNR = tb2.KUNNR 
                    and tbc.BUKRS = tb1.BUKRS) as cst_nm from
                    (select BUKRS, HKONT, GJAHR, BELNR, BKTXT from FI_ACT_TRS
                    where HKONT = '1100610053' and BLART = 'DR') tb1 join FI_ACT_TRS tb2 
                    on tb1.BELNR = tb2.BELNR and tb1.GJAHR = tb2.GJAHR and tb1.BUKRS = tb2.BUKRS
                    where tb2.KUNNR <> ''
                    group by BUKRS, HKONT, GJAHR, BELNR, BKTXT, KUNNR) tb0 left join FI_BKT_PTG tb3 
                    on tb3.BUKRS = tb0.BUKRS and tb3.BELNR = tb0.BELNR and tb3.GJAHR = tb0.GJAHR 
                    where tb0.id_doc = :iddoc:";
        $qget = $db->query($qstr_get,[
            'iddoc' => $id,
        ]);
        $qdata = $qget->getRowArray();

        return $this->response->setJSON($qdata);
    }

    public function update()
    {   
        try {
            $db = db_connect();
            $id_doc = $this->request->getPost('id');
            $tgl1 = $this->request->getPost('edtgl1');
            $tgl2 = $this->request->getPost('edtgl2');

            if ($tgl1 == "") {
                $tgl1 = date("Y-m")."-01";
            }
            if ($tgl2 == "") {
                $tgl2 = date("Y-m-d");
            }

            $BUKRS = $this->request->getPost('edbukrs');
            $id_bkt_ptg = $this->request->getPost('id_bkt_ptg');
            $BELNR = $this->request->getPost('eddocno');
            $GJAHR = $this->request->getPost('edyear');
            $KUNNR = $this->request->getPost('edkunnr');
            // $BKP = $this->request->getPost('edbkt');
            $NBKP = $this->request->getPost('ednobkt');
            if ($NBKP) {
                $BKP = "1";
            }else{
                $BKP = "0";
            }

            if ($id_bkt_ptg == '' or is_null($id_bkt_ptg)) {
                $qstr_get_trs = "select id, BUKRS, BELNR, GJAHR, HKONT, DMBTR, SGTXT from FI_ACT_TRS 
                                where BUKRS = :bukrs:
                                and BELNR = :belnr: 
                                and GJAHR = :gjahr:
                                and HKONT = '1100610053'
                                and BLART = 'DR'";
                $qtrs = $db->query($qstr_get_trs, [
                    'bukrs' => $BUKRS,
                    'belnr' => $BELNR,
                    'gjahr' => $GJAHR,
                ])->getRowArray();

                if (is_array($qtrs)) {
                    if ($qtrs['id'] != '') {
                        $qstr_ins_bkt_ptg = "INSERT INTO `FI_BKT_PTG` (`BUKRS`, `BELNR`, `GJAHR`, `KUNNR`, 
                        `HKONT`, `DMBTR`, `SGTXT`, `BKP`, `NBKP`, `EDBY`, `EDON`, `EDAT`) VALUES 
                        (:BUKRS:, :BELNR:, :GJAHR:, :KUNNR:, :HKONT:, :DMBTR:, :SGTXT:, :BKP:, :NBKP:, :EDBY:, :EDON:, :EDAT:);";
                        $qins_bkt_ptg = $db->query($qstr_ins_bkt_ptg,[
                            'BUKRS' => $qtrs['BUKRS'],
                            'BELNR' => $qtrs['BELNR'],
                            'GJAHR' => $qtrs['GJAHR'],
                            'KUNNR' => $KUNNR,
                            'HKONT' => $qtrs['HKONT'],
                            'DMBTR' => $qtrs['DMBTR'],
                            'SGTXT' => $qtrs['SGTXT'],
                            'BKP' => $BKP,
                            'NBKP' => $NBKP,
                            'EDBY' => session()->get('username'),
                            'EDON' => Time::now()->format('Y-m-d'),
                            'EDAT' => Time::now()->format('H:i:s'),
                        ]);
                        // return $qins_bkt_ptg;
                        $message = "Bukti Potong PPh22 has been Created";
                    }else{ $message = "No document found.."; }
                }else{ $message = "No document found."; }
            }else{
                $qstr_upd_bkt_ptg = "UPDATE `FI_BKT_PTG` SET `BKP`=:BKP:, `NBKP`=:NBKP:, 
                            `EDBY`=:EDBY:, `EDON`=:EDON:, `EDAT`=:EDAT: WHERE  `id`=:id:;";  
                $qupd_bkt_ptg = $db->query($qstr_upd_bkt_ptg,[
                    'BKP' => $BKP,
                    'NBKP' => $NBKP,
                    'EDBY' => session()->get('username'),
                    'EDON' => Time::now()->format('Y-m-d'),
                    'EDAT' => Time::now()->format('H:i:s'),
                    'id' => $id_bkt_ptg,
                ]);
                if ($qupd_bkt_ptg) {
                    $message = "Bukti Potong PPh22 has been Update";
                }                
            }


        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/finance/pph22/?date1=".$tgl1."&date2=".$tgl2)->with('message', $message);
    }

    public function delete($id)
    {
        $tgl1 = $_GET['date1'] ?? false;
        $tgl2 = $_GET['date2'] ?? false;

        if ($tgl1 == "") {
            $tgl1 = date("Y-m")."-01";
        }
        if ($tgl2 == "") {
            $tgl2 = date("Y-m-d");
        }

        try {
            $db = db_connect();
            $qstr_del_bkt_ptg = "DELETE FROM `FI_BKT_PTG` WHERE `id`=:id:;";  
            $qdel_bkt_ptg = $db->query($qstr_del_bkt_ptg,[
                'id' => $id,
            ]);
            if ($qdel_bkt_ptg) {
                $message = "Bukti Potong PPh22 has been deleted";
            }else{
                $message = "No data deleted";
            }
            
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/finance/pph22/?date1=".$tgl1."&date2=".$tgl2)->with('message', $message);
    }
}
