<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Type;
use App\Models\Category;
use App\Models\Plant;
use CodeIgniter\I18n\Time;

class Tracking extends BaseController
{
    public function index()
    {
        $data['title'] = "PR - Tracking";

        
        $plant_cd = $_GET['plnt'] ?? false;
        $pr_number = $_GET['pr_no'] ?? false;

        $db = db_connect();
        $sql_pr = "select WERKS, BANFN, BNFPO, BADAT, MATNR, TXZ01, MENGE, MEINS, AFNAM, FULL_RELEASE, EBELN,
        (IF(FULL_RELEASE='X','Full Release','In Progress')) as FULL_REL,
        (IF(BNFPO MOD 10 = 0,(BNFPO/10),BNFPO))as ITEM
        from t_pr
        where BANFN = :no_pr:
        and WERKS = :plant:";
        $q_pr = $db->query($sql_pr, [
            'no_pr'     => $pr_number,
            'plant'     => $plant_cd,
        ]);
        $data['v_pr'] = $q_pr->getResultArray();

        $sql_po = "select EBELP, TXZ01, EBELN, MENGE, MEINS, ETA, BANFN,
        (select BEDAT from t_po where t_po.EBELN = t_po_item.EBELN) as PODATE,
        (select LIFNR from t_po where t_po.EBELN = t_po_item.EBELN) as KDVENDOR,
        (select NAME1 from t_vendor where t_vendor.LIFNR = (select KDVENDOR) limit 1) as VENDOR,
        (select ORT01 from t_vendor where t_vendor.LIFNR = (select KDVENDOR) limit 1) as CITY,
        (select FULL_RELEASE from t_po where t_po.EBELN = t_po_item.EBELN) as FULL_RELEASE,
        (IF((select FULL_RELEASE)='X','Full Release','In Progress')) as FULL_REL,
        (IF(EBELP MOD 10 = 0,(EBELP/10),EBELP))as ITEM
        from t_po_item
        where (LOEKZ = '' or LOEKZ = null)
          and BANFN <> ''
          and BANFN = :no_pr:
          and WERKS = :plant:";
        
        $q_po = $db->query($sql_po, [
            'no_pr'     => $pr_number,
            'plant'     => $plant_cd,
        ]);

        $data['v_po'] = $q_po->getResultArray();

        $sql_gr = "select t_gr.MJAHR, t_gr.MBLNR, t_gr.ZEILE, t_gr.EBELN, t_gr.EBELP,
        t_gr.MENGE, t_gr.MEINS, t_gr.BUDAT, t_gr.WEMPF, t_gr.LT_POGR, t_gr.STATUS,
        (select TXZ01 from t_pr where t_pr.BANFN = t_po_item.BANFN and t_pr.BNFPO = t_po_item.BNFPO LIMIT 1)as TXZ01,
        (IF(t_gr.EBELP MOD 10 = 0,(t_gr.EBELP/10),t_gr.EBELP))as ITEM,
        (IF(t_gr.ZEILE MOD 10 = 0,(t_gr.ZEILE/10),t_gr.ZEILE))as ZEILE2
        from t_gr, t_po_item
        where t_gr.EBELN = t_po_item.EBELN
          and (t_gr.EBELP + 0) = (t_po_item.EBELP + 0)
          and (t_po_item.LOEKZ = '' or t_po_item.LOEKZ = null)
          and (t_gr.DELT = '' or t_gr.DELT = null)
          and t_po_item.BANFN <> ''
          and t_po_item.BANFN = :no_pr:
          and t_po_item.WERKS = :plant:";

        $q_gr = $db->query($sql_gr, [
            'no_pr'     => $pr_number,
            'plant'     => $plant_cd,
        ]);
        $data['v_gr'] = $q_gr->getResultArray();
        
        $PLant = new PLant();
        $builder = $PLant->builder();
        $data['Plant'] = $builder->select('*')->get()->getResultArray();
        
        $sql_cek_pr = "select BANFN, WERKS, BADAT,
        (select NAME1 from t_plant where t_plant.WERKS = t_pr.WERKS) as PLANT
        from t_pr 
        where (LOEKZ = '' or LOEKZ = null)
        group by BANFN, WERKS, BADAT
        order by BADAT desc, WERKS, BANFN";

        $data['cek_pr'] = $db->query($sql_cek_pr)->getResultArray();

        $data['today'] = Time::now()->format('Y-m-d');

        echo view('pages/PR-Tracking', $data);
    }
}
