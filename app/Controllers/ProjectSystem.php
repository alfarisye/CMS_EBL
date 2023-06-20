<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\I18n\Time;
use App\Models\FiInBudget;
use Shuchkin\SimpleXLSX;

class ProjectSystem extends BaseController
{
    public function index()//Done 100%
    {
        $data['title'] = "CMS – Project System Budget";
        $db = db_connect();

        $qyear = "SELECT YEAR as year FROM PS_BUDGET GROUP BY YEAR";
        $q_year = $db->query($qyear);
        $data['year'] = $q_year->getResultArray();

        $filter = $_GET['b2c97ae425dd751b0e48a3acae79cf4a'] ?? false;
        $year = "2022";
        $type="";
        if(substr($filter,0,1) == '1'){
            $type="and SUBSTR(wbs,9,1) ='-'";//capex
        }
        else{
            $type="and SUBSTR(wbs,7,1) = '-'";//opex
        }

        if(substr($filter,1,2) ?? false and substr($filter,1,2)!="xx"){
            $year="20".substr($filter,1,2);
        }
        else{
            $year = "2022";
        }
        
        $qstr_PS_BudgetMTD = $this->budgetsql("= ".$year,0,$type);
        //dd($qstr_PS_BudgetMTD);
        $q_PS_BudgetMTD = $db->query($qstr_PS_BudgetMTD);
        $data['mtd'] = $q_PS_BudgetMTD->getResultArray();
        //dd($data['mtd']);
        //===============================================================================end chart 1
        // if(substr($filter,3,1) == '1'){
        //     $type="and SUBSTR(wbs,9,1) ='-'";//capex
        // }
        // else{
        //     $type="and SUBSTR(wbs,7,1) = '-'";//opex
        // }

        // if(substr($filter,4,2) ?? false and substr($filter,4,2)!="xx"){
        //     $year="20".substr($filter,4,2);
        // }
        // else{
        //     $year = "2022";
        // }
        $qstr_PS_BudgetYTD = $this->budgetsql("= ".$year,0,$type);
        $q_PS_BudgetYTD = $db->query($qstr_PS_BudgetYTD);
        $data['ytd'] = $q_PS_BudgetYTD->getResultArray();
        //dd($data['ytd']);

        //===============================================================================end chart 2
        // if(substr($filter,6,1) == '1'){
        //     $type="and SUBSTR(wbs,9,1) ='-'";//capex
        // }
        // else{
        //     $type="and SUBSTR(wbs,7,1) = '-'";//opex
        // }

        // if(substr($filter,7,2) ?? false and substr($filter,7,2)!="xx"){
        //     $year="20".substr($filter,7,2);
        // }
        // else{
        //     $year = "2022";
        // }

        $qstr_PS_CMMTD = $this->budgetsql("= ".$year,1,$type);
        // dd($this->budgetsql('= YEAR(CURDATE())',1,$type));
        $q_PS_CMMTD = $db->query($qstr_PS_CMMTD);
        $data['cmmtd'] = $q_PS_CMMTD->getResultArray();
        //dd($data['mtd']);

        //===============================================================================end chart 3
        // if(substr($filter,9,1) == '1'){
        //     $type="and SUBSTR(wbs,9,1) ='-'";//capex
        // }
        // else{
        //     $type="and SUBSTR(wbs,7,1) = '-'";//opex
        // }

        // if(substr($filter,10,2) ?? false and substr($filter,10,2)!="xx"){
        //     $year="20".substr($filter,10,2);
        // }
        // else{
        //     $year = "2022";
        // }
        $qstr_PS_CMYTD = $this->budgetsql("= ".$year,1,$type);
        //dd($qstr_PS_CMYTD);
        $q_PS_CMYTD = $db->query($qstr_PS_CMYTD);
        $data['cmytd'] = $q_PS_CMYTD->getResultArray();
        //dd($data['cmytd']);
        
        //===============================================================================end chart 4
        // dd(
        //     substr($filter,0,1),
        //     substr($filter,1,2),

        //     substr($filter,3,1),
        //     substr($filter,4,2),

        //     substr($filter,6,1),
        //     substr($filter,7,2),

        //     substr($filter,9,1),
        //     substr($filter,10,2),

        //     substr($filter,12,2),
        //     substr($filter,14,2),
        // );
        if(substr($filter,13,2) ?? false and substr($filter,12,2)!="xx"){
            $year="20".substr($filter,12,2);
        }
        else{
            $year = "2022";
        }
        $qa = $db->query($this->table1("'AB3.11-01.%'",$year));
        //dd($this->table1("'AB3.11-01.%'"));
        $row = $qa->getRow(0);
        $jana = $row->jan_act;
        $feba = $row->feb_act;
        $mara = $row->mar_act;
        $apra = $row->apr_act;
        $maya = $row->mei_act;
        $juna = $row->jun_act;
        $jula = $row->jul_act;
        $auga = $row->aug_act;
        $sepa = $row->sep_act;
        $octa = $row->oct_act;
        $nova = $row->nov_act;
        $deca = $row->dec_act;
        //dd($jana);

        $qb = $db->query("Select sum(net_weigh/1000000) as net_weight, month(posting_date)
                            From inquiry_receive
                            WHERE YEAR(posting_date) = $year
                            group by month(posting_date)
                            ORDER BY month(posting_date) ");

        $janb = $qb->getRow(0)->net_weight;
        $febb = $qb->getRow(1)->net_weight;
        $marb = $qb->getRow(2)->net_weight;
        $aprb = $qb->getRow(3)->net_weight;
        $mayb = $qb->getRow(4)->net_weight;
        $junb = $qb->getRow(5)->net_weight;
        $julb = $qb->getRow(6)->net_weight;
        $augb = $qb->getRow(7)->net_weight;
        $sepb = $qb->getRow(8)->net_weight;
        $octb = $qb->getRow(9)->net_weight;
        $novb = $qb->getRow(10)->net_weight;
        $decb = $qb->getRow(11)->net_weight;

        $janc = $jana/$janb;
        $febc = $feba/$febb;
        $marc = $mara/$marb;
        $aprc = $apra/$aprb;
        $mayc = $maya/$mayb;
        $junc = $juna/$junb;
        $julc = $jula/$julb;
        $augc = $auga/$augb;
        $sepc = $sepa/$sepb;
        $octc = $octa/$octb;
        $novc = $nova/$novb;
        $decc = $deca/$decb;
        
        $qd = $db->query($this->table1("'AB3.11-02.%'",$year));
        //dd($this->table1("'AB3.11-02.%'"));
        $row = $qd->getRow(0);
        $jand = $row->jan_act;
        //dd($jand);
        $febd = $row->feb_act;
        $mard = $row->mar_act;
        $aprd = $row->apr_act;
        $mayd = $row->mei_act;
        $jund = $row->jun_act;
        $juld = $row->jul_act;
        $augd = $row->aug_act;
        $sepd = $row->sep_act;
        $octd = $row->oct_act;
        $novd = $row->nov_act;
        $decd = $row->dec_act;
        
        // dd("Select sum(net_weigh/1000000) as net_weight, month(posting_date)
        // From inquiry_transfer
        // WHERE YEAR(posting_date) = $year
        // group by month(posting_date)
        // ORDER BY month(posting_date) ");

        $qe = $db->query("Select sum(net_weigh/1000000) as net_weight, month(posting_date)
        From inquiry_transfer
        WHERE YEAR(posting_date) = $year
        group by month(posting_date)
        ORDER BY month(posting_date) ");

        $jane = $qe->getRow(0)->net_weight;
        $febe = $qe->getRow(1)->net_weight;
        $mare = $qe->getRow(2)->net_weight;
        $apre = $qe->getRow(3)->net_weight;
        $maye = $qe->getRow(4)->net_weight;
        $june = $qe->getRow(5)->net_weight;
        $jule = $qe->getRow(6)->net_weight;
        $auge = $qe->getRow(7)->net_weight;
        $sepe = $qe->getRow(8)->net_weight;
        $octe = $qe->getRow(9)->net_weight;
        $nove = $qe->getRow(10)->net_weight;
        $dece = $qe->getRow(11)->net_weight;

        $janf = $jand/$jane;
        $febf = $febd/$febe;
        $marf = $mard/$mare;
        $aprf = $aprd/$apre;
        $mayf = $mayd/$maye;
        $junf = $jund/$june;
        $julf = $juld/$jule;
        $augf = $augd/$auge;
        $sepf = $sepd/$sepe;
        $octf = $octd/$octe;
        $novf = $novd/$nove;
        $decf = $decd/$dece;


        if(substr($filter,16,2)=="01"){
            $arraytable1 = array (
                array('January',$jana,$janb,$janc,$jand,$jane,$janf),
            );
            $arraytotaltable1 = array (
                array('Total',$jana,$janb,$janc,$jand,$jane,$janf),
            );
        }elseif(substr($filter,16,2)=="02"){
            $arraytable1 = array (
                array('February',$feba,$febb,$febc,$febd,$febe,$febf),
            );
            $arraytotaltable1 = array (
                array('Total',$feba,$febb,$febc,$febd,$febe,$febf),
            );
        }elseif(substr($filter,16,2)=="03"){
            $arraytable1 = array (
                array('March',$mara,$marb,$marc,$mard,$mare,$marf),
            );
            $arraytotaltable1 = array (
                array('Total',$juna,$junb,$junc,$jund,$june,$junf),
            );
        }elseif(substr($filter,16,2)=="04"){
            $arraytable1 = array (
                array('April',$apra,$aprb,$aprc,$aprd,$apre,$aprf),
            );
            $arraytotaltable1 = array (
                array('Total',$apra,$aprb,$aprc,$aprd,$apre,$aprf),
            );
        }elseif(substr($filter,16,2)=="05"){
            $arraytable1 = array (
                array('May',$maya,$mayb,$mayc,$mayd,$maye,$mayf),
            );
            $arraytotaltable1 = array (
                array('Total',$maya,$mayb,$mayc,$mayd,$maye,$mayf),
            );
        }elseif(substr($filter,16,2)=="06"){
            $arraytable1 = array (
                array('June',$juna,$junb,$junc,$jund,$june,$junf),
            );
            $arraytotaltable1 = array (
                array('Total',$juna,$junb,$junc,$jund,$june,$junf),
            );
        }elseif(substr($filter,16,2)=="07"){
            $arraytable1 = array (
                array('July',$jula,$julb,$julc,$juld,$jule,$julf),
            );
            $arraytotaltable1 = array (
                array('Total',$jula,$julb,$julc,$juld,$jule,$julf),
            );
        }elseif(substr($filter,16,2)=="08"){
            $arraytable1 = array (
                array('Augustus',$auga,$augb,$augc,$augd,$auge,$augf),
            );
            $arraytotaltable1 = array (
                array('Total',$auga,$augb,$augc,$augd,$auge,$augf),
            );
        }elseif(substr($filter,16,2)=="09"){
            $arraytable1 = array (
                array('September',$sepa,$sepb,$sepc,$sepd,$sepe,$sepf),
            );
            $arraytotaltable1 = array (
                array('Total',$sepa,$sepb,$sepc,$sepd,$sepe,$sepf),
            );
        }elseif(substr($filter,16,2)=="10"){
            $arraytable1 = array (
                array('October',$octa,$octb,$octc,$octd,$octe,$octf),
            );
            $arraytotaltable1 = array (
                array('Total',$octa,$octb,$octc,$octd,$octe,$octf),
            );
        }elseif(substr($filter,16,2)=="11"){
            $arraytable1 = array (
                array('November',$nova,$novb,$novc,$novd,$nove,$novf),
            );
            $arraytotaltable1 = array (
                array('Total',$nova,$novb,$novc,$novd,$nove,$novf),
            );
        }elseif(substr($filter,16,2)=="12"){
            $arraytable1 = array (
                array('December',$deca,$decb,$decc,$decd,$dece,$decf),
            );
            $arraytotaltable1 = array (
                array('Total',$deca,$decb,$decc,$decd,$dece,$decf),
            );
        }
        else{
            $arraytable1 = array (
                array('January',$jana,$janb,$janc,$jand,$jane,$janf),
                array('February',$feba,$febb,$febc,$febd,$febe,$febf),
                array('March',$mara,$marb,$marc,$mard,$mare,$marf),
                array('April',$apra,$aprb,$aprc,$aprd,$apre,$aprf),
                array('May',$maya,$mayb,$mayc,$mayd,$maye,$mayf),
                array('June',$juna,$junb,$junc,$jund,$june,$junf),
                array('July',$jula,$julb,$julc,$juld,$jule,$julf),
                array('Augustus',$auga,$augb,$augc,$augd,$auge,$augf),
                array('September',$sepa,$sepb,$sepc,$sepd,$sepe,$sepf),
                array('October',$octa,$octb,$octc,$octd,$octe,$octf),
                array('November',$nova,$novb,$novc,$novd,$nove,$novf),
                array('December',$deca,$decb,$decc,$decd,$dece,$decf),
            );
            $arraytotaltable1 = array (
                array('Total',
                    ($jana+$feba+$mara+$apra+$maya+$juna+$jula+$auga+$sepa+$octa+$nova+$deca),
                    ($janb+$febb+$marb+$aprb+$mayb+$junb+$julb+$augb+$sepb+$octb+$novb+$decb),
                    ($janc+$febc+$marc+$aprc+$mayc+$junc+$julc+$augc+$sepc+$octc+$novc+$decc),
                    ($jand+$febd+$mard+$aprd+$mayd+$jund+$juld+$augd+$sepd+$octd+$novd+$decd),
                    ($jane+$febe+$mare+$apre+$maye+$june+$jule+$auge+$sepe+$octe+$nove+$dece),
                    ($janf+$febf+$marf+$aprf+$mayf+$junf+$julf+$augf+$sepf+$octf+$novf+$decf),
                ));
        }
        
        //dd($arraytable1);
        
        $data['table1'] = $arraytable1;
        $data['totaltable1'] = $arraytotaltable1;

        $qa = $db->query($this->table1("'AB3.11-01.01.01.%'",$year));
        $row = $qa->getRow(0);
        $jana = $row->jan_act;
        $feba = $row->feb_act;
        $mara = $row->mar_act;
        $apra = $row->apr_act;
        $maya = $row->mei_act;
        $juna = $row->jun_act;
        $jula = $row->jul_act;
        $auga = $row->aug_act;
        $sepa = $row->sep_act;
        $octa = $row->oct_act;
        $nova = $row->nov_act;
        $deca = $row->dec_act;
        //dd($row);

        $qb = $db->query("Select sum(net_weigh/1000000) as net_weight, month(posting_date)
                            From inquiry_receive
                            Where Supplier_id = 'GMT'
                            and YEAR(posting_date) = $year
                            GROUP BY month(posting_date)
                            ORDER BY month(posting_date)");

        $janb = $qb->getRow(0)->net_weight;
        $febb = $qb->getRow(1)->net_weight;
        $marb = $qb->getRow(2)->net_weight;
        $aprb = $qb->getRow(3)->net_weight;
        $mayb = $qb->getRow(4)->net_weight;
        $junb = $qb->getRow(5)->net_weight;
        $julb = $qb->getRow(6)->net_weight;
        $augb = $qb->getRow(7)->net_weight;
        $sepb = $qb->getRow(8)->net_weight;
        $octb = $qb->getRow(9)->net_weight;
        $novb = $qb->getRow(10)->net_weight;
        $decb = $qb->getRow(11)->net_weight;

        $janc = $jana/$janb;
        $febc = $feba/$febb;
        $marc = $mara/$marb;
        $aprc = $apra/$aprb;
        $mayc = $maya/$mayb;
        $junc = $juna/$junb;
        $julc = $jula/$julb;
        $augc = $auga/$augb;
        $sepc = $sepa/$sepb;
        $octc = $octa/$octb;
        $novc = $nova/$novb;
        $decc = $deca/$decb;
        
        $qd = $db->query($this->table1("'AB3.11-01.01.02.%'",$year));
        $row = $qd->getRow(0);
        $jand = $row->jan_act;
        $febd = $row->feb_act;
        $mard = $row->mar_act;
        $aprd = $row->apr_act;
        $mayd = $row->mei_act;
        $jund = $row->jun_act;
        $juld = $row->jul_act;
        $augd = $row->aug_act;
        $sepd = $row->sep_act;
        $octd = $row->oct_act;
        $novd = $row->nov_act;
        $decd = $row->dec_act;

        $qe = $db->query("Select sum(net_weigh/1000000) as net_weight, month(posting_date)
                            From inquiry_receive
                            Where Supplier_id = 'CK'
                            and YEAR(posting_date) = $year
                            GROUP BY month(posting_date)
                            ORDER BY month(posting_date)");

        $jane = $qe->getRow(0)->net_weight;
        $febe = $qe->getRow(1)->net_weight;
        $mare = $qe->getRow(2)->net_weight;
        $apre = $qe->getRow(3)->net_weight;
        $maye = $qe->getRow(4)->net_weight;
        $june = $qe->getRow(5)->net_weight;
        $jule = $qe->getRow(6)->net_weight;
        $auge = $qe->getRow(7)->net_weight;
        $sepe = $qe->getRow(8)->net_weight;
        $octe = $qe->getRow(9)->net_weight;
        $nove = $qe->getRow(10)->net_weight;
        $dece = $qe->getRow(11)->net_weight;

        $janf = $jand/$jane;
        $febf = $febd/$febe;
        $marf = $mard/$mare;
        $aprf = $aprd/$apre;
        $mayf = $mayd/$maye;
        $junf = $jund/$june;
        $julf = $juld/$jule;
        $augf = $augd/$auge;
        $sepf = $sepd/$sepe;
        $octf = $octd/$octe;
        $novf = $novd/$nove;
        $decf = $decd/$dece;

        $qg = $db->query($this->table1("'AB3.11-01.01.03.%'",$year));
        $row = $qg->getRow(0);
        $jang = $row->jan_act;
        $febg = $row->feb_act;
        $marg = $row->mar_act;
        $aprg = $row->apr_act;
        $mayg = $row->mei_act;
        $jung = $row->jun_act;
        $julg = $row->jul_act;
        $augg = $row->aug_act;
        $sepg = $row->sep_act;
        $octg = $row->oct_act;
        $novg = $row->nov_act;
        $decg = $row->dec_act;

        $qh = $db->query("select COALESCE( (Select sum(net_weigh/1000000) as net_weight
        From inquiry_receive
        Where Supplier_id = 'HRS'
        and YEAR(posting_date) = $year
        GROUP BY month(posting_date)
        ORDER BY month(posting_date)
                 ),0) AS net_weight");

        $janh = $qh->getRow(0)->net_weight;
        $febh = $qh->getRow(1)->net_weight;
        $marh = $qh->getRow(2)->net_weight;
        $aprh = $qh->getRow(3)->net_weight;
        $mayh = $qh->getRow(4)->net_weight;
        $junh = $qh->getRow(5)->net_weight;
        $julh = $qh->getRow(6)->net_weight;
        $augh = $qh->getRow(7)->net_weight;
        $seph = $qh->getRow(8)->net_weight;
        $octh = $qh->getRow(9)->net_weight;
        $novh = $qh->getRow(10)->net_weight;
        $dech = $qh->getRow(11)->net_weight;

        if ($janh == 0 and $febh == 0 and $marh == 0 and $aprh == 0 and $mayh == 0 and $junh == 0 and $julh == 0 and $augh == 0 and $seph == 0 and $octh == 0 and $novh == 0 and $dech == 0){
            $janh = 1;
            $febh = 1;
            $marh = 1;
            $aprh = 1;
            $mayh = 1;
            $junh = 1;
            $julh = 1;
            $augh = 1;
            $seph = 1;
            $octh = 1;
            $novh = 1;
            $dech = 1;
        }

        $jani = $jang/$janh;
        $febi = $febg/$febh;
        $mari = $marg/$marh;
        $apri = $aprg/$aprh;
        $mayi = $mayg/$mayh;
        $juni = $jung/$junh;
        $juli = $julg/$julh;
        $augi = $augg/$augh;
        $sepi = $sepg/$seph;
        $octi = $octg/$octh;
        $novi = $novg/$novh;
        $deci = $decg/$dech;

        if ($janh == 1 and $febh == 1 and $marh == 1 and $aprh == 1 and $mayh == 1 and $junh == 1 and $julh == 1 and $augh == 1 and $seph == 1 and $octh == 1 and $novh == 1 and $dech == 1){
            $janh = 0;
            $febh = 0;
            $marh = 0;
            $aprh = 0;
            $mayh = 0;
            $junh = 0;
            $julh = 0;
            $augh = 0;
            $seph = 0;
            $octh = 0;
            $novh = 0;
            $dech = 0;
        }

        $arraytable2 = array (

            array("Actual Jan",$jana,$jand,$jang,$jana+$jand+$jang),
            array("QTY Jan",$janb,$jane,$janh,$janb+$jane+$janh),
            array("Kalkulasi Jan",$janc,$janf,$jani,$janc+$janf+$jani),

            array("Actual Feb",$feba, $febd,$febg, $feba+$febd+$febg),
            array("QTY Feb",$febb,$febe,$febh, $febb+$febe+$febh),
            array("Kalkulasi Feb",$febc,$febf,$febi, $febc+$febf+$febi),

            array("Actual Mar",$mara, $mard,$marg,$mara+$mard+$marg),
            array("QTY Mar",$marb,$mare,$marh, $marb+$mare+$marh),
            array("Kalkulasi Mar",$marc,$marf,$mari, $marc+$marf+$mari),

            array("Actual Apr",$apra, $aprd,$aprg,$apra+$aprd+$aprg),
            array("QTY Apr",$aprb,$apre,$aprh, $aprb+$apre+$aprh),
            array("Kalkulasi Apr",$aprc,$aprf,$apri, $aprc+$aprf+$apri),

            array("Actual May",$maya, $mayd,$mayg,$maya+$mayd+$mayg),
            array("QTY May",$mayb,$maye,$mayh, $mayb+$maye+$mayh),
            array("Kalkulasi May",$mayc,$mayf,$mayi, $mayc+$mayf+$mayi),

            array("Actual Jun",$juna, $jund,$jung,$juna+$jund+$jung),
            array("QTY Jun",$junb,$june,$junh, $junb+$june+$junh),
            array("Kalkulasi Jun",$junc,$junf,$juni, $junc+$junf+$juni),

            array("Actual Jul",$jula, $juld,$julg,$jula+$juld+$julg),
            array("QTY Jul",$julb,$jule,$julh, $julb+$jule+$julh),
            array("Kalkulasi Jul",$julc,$julf,$juli, $julc+$julf+$juli),

            array("Actual Aug",$auga, $augd,$augg,$auga+$augd+$augg),
            array("QTY Aug",$augb,$auge,$augh, $augb+$auge+$augh),
            array("Kalkulasi Aug",$augc,$augf,$augi, $augc+$augf+$augi),

            array("Actual Sep",$sepa, $sepd,$sepg,$sepa+$sepd+$sepg),
            array("QTY Sep",$sepb,$sepe,$seph, $sepb+$sepe+$seph),
            array("Kalkulasi Sep",$sepc,$sepf,$sepi, $sepc+$sepf+$sepi),

            array("Actual Oct",$octa, $octd,$octg,$octa+$octd+$octg),
            array("QTY Oct",$octb,$octe,$octh, $octb+$octe+$octh),
            array("Kalkulasi Oct",$octc,$octf,$octi, $octc+$octf+$octi),

            array("Actual Nov",$nova, $novd,$novg,$nova+$novd+$novg),
            array("QTY Nov",$novb,$nove,$novh, $novb+$nove+$novh),
            array("Kalkulasi Nov",$novc,$novf,$novi, $novc+$novf+$novi),

            array("Actual Dec",$deca, $decd,$decg,$deca+$decd+$decg),
            array("QTY Dec",$decb,$dece,$dech, $decb+$dece+$dech),
            array("Kalkulasi Dec",$decc,$decf,$deci, $decc+$decf+$deci),
        );
        
        $npop=0; $nshift=0;
        if(substr($filter,16,2)=="01"){
            $npop=33;
            $febc=0;$marc=0;$aprc=0;$mayc=0;$junc=0;$julc=0;$augc=0;$sepc=0;$octc=0;$novc=0;$decc=0;
            $febf=0;$marf=0;$aprf=0;$mayf=0;$junf=0;$julf=0;$augf=0;$sepf=0;$octf=0;$novf=0;$decf=0;
            $febi=0;$mari=0;$apri=0;$mayi=0;$juni=0;$juli=0;$augi=0;$sepi=0;$octi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="02"){
            $npop=30;$nshift=3;
            $janc=0;$marc=0;$aprc=0;$mayc=0;$junc=0;$julc=0;$augc=0;$sepc=0;$octc=0;$novc=0;$decc=0;
            $janf=0;$marf=0;$aprf=0;$mayf=0;$junf=0;$julf=0;$augf=0;$sepf=0;$octf=0;$novf=0;$decf=0;
            $jani=0;$mari=0;$apri=0;$mayi=0;$juni=0;$juli=0;$augi=0;$sepi=0;$octi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="03"){
            $npop=27;$nshift=6;
            $janc=0;$febc=0;$aprc=0;$mayc=0;$junc=0;$julc=0;$augc=0;$sepc=0;$octc=0;$novc=0;$decc=0;
            $janf=0;$febf=0;$aprf=0;$mayf=0;$junf=0;$julf=0;$augf=0;$sepf=0;$octf=0;$novf=0;$decf=0;
            $jani=0;$febi=0;$apri=0;$mayi=0;$juni=0;$juli=0;$augi=0;$sepi=0;$octi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="04"){
            $npop=24;$nshift=9;
            $janc=0;$febc=0;$marc=0;$mayc=0;$junc=0;$julc=0;$augc=0;$sepc=0;$octc=0;$novc=0;$decc=0;
            $janf=0;$febf=0;$marf=0;$mayf=0;$junf=0;$julf=0;$augf=0;$sepf=0;$octf=0;$novf=0;$decf=0;
            $jani=0;$febi=0;$mari=0;$mayi=0;$juni=0;$juli=0;$augi=0;$sepi=0;$octi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="05"){
            $npop=21;$nshift=12;
            $janc=0;$febc=0;$marc=0;$aprc=0;$junc=0;$julc=0;$augc=0;$sepc=0;$octc=0;$novc=0;$decc=0;
            $janf=0;$febf=0;$marf=0;$aprf=0;$junf=0;$julf=0;$augf=0;$sepf=0;$octf=0;$novf=0;$decf=0;
            $jani=0;$febi=0;$mari=0;$apri=0;$juni=0;$juli=0;$augi=0;$sepi=0;$octi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="06"){
            $npop=18;$nshift=15;
            $janc=0;$febc=0;$marc=0;$aprc=0;$mayc=0;$julc=0;$augc=0;$sepc=0;$octc=0;$novc=0;$decc=0;
            $janf=0;$febf=0;$marf=0;$aprf=0;$mayf=0;$julf=0;$augf=0;$sepf=0;$octf=0;$novf=0;$decf=0;
            $jani=0;$febi=0;$mari=0;$apri=0;$mayi=0;$juli=0;$augi=0;$sepi=0;$octi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="07"){
            $npop=15;$nshift=18;
            $janc=0;$febc=0;$marc=0;$aprc=0;$mayc=0;$junc=0;$augc=0;$sepc=0;$octc=0;$novc=0;$decc=0;
            $janf=0;$febf=0;$marf=0;$aprf=0;$mayf=0;$junf=0;$augf=0;$sepf=0;$octf=0;$novf=0;$decf=0;
            $jani=0;$febi=0;$mari=0;$apri=0;$mayi=0;$juni=0;$augi=0;$sepi=0;$octi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="08"){
            $npop=12;$nshift=21;
            $janc=0;$febc=0;$marc=0;$aprc=0;$mayc=0;$junc=0;$julc=0;$sepc=0;$octc=0;$novc=0;$decc=0;
            $janf=0;$febf=0;$marf=0;$aprf=0;$mayf=0;$junf=0;$julf=0;$sepf=0;$octf=0;$novf=0;$decf=0;
            $jani=0;$febi=0;$mari=0;$apri=0;$mayi=0;$juni=0;$juli=0;$sepi=0;$octi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="09"){
            $npop=9;$nshift=24;
            $janc=0;$febc=0;$marc=0;$aprc=0;$mayc=0;$junc=0;$julc=0;$augc=0;$octc=0;$novc=0;$decc=0;
            $janf=0;$febf=0;$marf=0;$aprf=0;$mayf=0;$junf=0;$julf=0;$augf=0;$octf=0;$novf=0;$decf=0;
            $jani=0;$febi=0;$mari=0;$apri=0;$mayi=0;$juni=0;$juli=0;$augi=0;$octi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="10"){
            $npop=6;$nshift=27;
            $janc=0;$febc=0;$marc=0;$aprc=0;$mayc=0;$junc=0;$julc=0;$augc=0;$sepc=0;$novc=0;$decc=0;
            $janf=0;$febf=0;$marf=0;$aprf=0;$mayf=0;$junf=0;$julf=0;$augf=0;$sepf=0;$novf=0;$decf=0;
            $jani=0;$febi=0;$mari=0;$apri=0;$mayi=0;$juni=0;$juli=0;$augi=0;$sepi=0;$novi=0;$deci=0;
        }elseif(substr($filter,16,2)=="11"){
            $npop=3;$nshift=30;
            $janc=0;$febc=0;$marc=0;$aprc=0;$mayc=0;$junc=0;$julc=0;$augc=0;$sepc=0;$octc=0;$decc=0;
            $janf=0;$febf=0;$marf=0;$aprf=0;$mayf=0;$junf=0;$julf=0;$augf=0;$sepf=0;$octf=0;$decf=0;
            $jani=0;$febi=0;$mari=0;$apri=0;$mayi=0;$juni=0;$juli=0;$augi=0;$sepi=0;$octi=0;$deci=0;
        }elseif(substr($filter,16,2)=="12"){
            $npop=0;$nshift=33;
            $janc=0;$febc=0;$marc=0;$aprc=0;$mayc=0;$junc=0;$julc=0;$augc=0;$sepc=0;$octc=0;$novc=0;
            $janf=0;$febf=0;$marf=0;$aprf=0;$mayf=0;$junf=0;$julf=0;$augf=0;$sepf=0;$octf=0;$novf=0;
            $jani=0;$febi=0;$mari=0;$apri=0;$mayi=0;$juni=0;$juli=0;$augi=0;$sepi=0;$octi=0;$novi=0;
        }
        
        for($i=0;$i<$npop;$i++){
            array_pop($arraytable2);  
        }
        for($i=0;$i<$nshift;$i++){
            array_shift($arraytable2);  
        }        

        $arraytotaltable2 = array (
            array(
                "Total Kalkulasi",
                ($janc+$febc+$marc+$aprc+$mayc+$junc+$julc+$augc+$sepc+$octc+$novc+$decc),
                ($janf+$febf+$marf+$aprf+$mayf+$junf+$julf+$augf+$sepf+$octf+$novf+$decf),
                ($jani+$febi+$mari+$apri+$mayi+$juni+$juli+$augi+$sepi+$octi+$novi+$deci),
                ($janc+$febc+$marc+$aprc+$mayc+$junc+$julc+$augc+$sepc+$octc+$novc+$decc)+
                ($janf+$febf+$marf+$aprf+$mayf+$junf+$julf+$augf+$sepf+$octf+$novf+$decf)+
                ($jani+$febi+$mari+$apri+$mayi+$juni+$juli+$augi+$sepi+$octi+$novi+$deci)
            ));

        //dd(substr($filter,16,2),$arraytable2,$arraytotaltable2);
        //dd($janc+$febc+$marc+$aprc+$mayc+$junc+$julc+$augc+$sepc+$octc+$novc+$decc);

        $data['table2'] = $arraytable2;
        $data['totaltable2'] = $arraytotaltable2;
        echo view('pages/projectSystemBudget', $data);
    }

    public function budgetsql($condition,$mode, $type){
        if($mode == 1){
            $jan = $this->costPerMT('01');
            $feb = $this->costPerMT('02');
            $mar = $this->costPerMT('03');
            $apr = $this->costPerMT('04');
            $mei = $this->costPerMT('05');
            $jun = $this->costPerMT('06');
            $jul = $this->costPerMT('07');
            $aug = $this->costPerMT('08');
            $sep = $this->costPerMT('09');
            $oct = $this->costPerMT('10');
            $nov = $this->costPerMT('11');
            $dec = $this->costPerMT('12');
            $ttl = "/(SELECT SUM(net_weigh/1000000) FROM inquiry_receive WHERE YEAR(posting_date) $condition)";
        }
        else{
            $jan = '';
            $feb = '';
            $mar = '';
            $apr = '';
            $mei = '';
            $jun = '';
            $jul = '';
            $aug = '';
            $sep = '';
            $oct = '';
            $nov = '';
            $dec = '';
            $ttl = '';
        }
        
        $query = 
                "SELECT
                year,
                /*--------------------*/
                SUM(jan_act/1000000)$jan AS jan_act,
                SUM(jan_bgd/1000000) AS jan_bgd,
                SUM(jan_cmm/1000000) AS jan_cmm,
                /*--------------------*/
                SUM(feb_act/1000000)$feb AS feb_act,
                SUM(feb_bgd/1000000) AS feb_bgd,
                SUM(feb_cmm/1000000) AS feb_cmm,
                /*--------------------*/
                SUM(mar_act/1000000)$mar AS mar_act,
                SUM(mar_bgd/1000000) AS mar_bgd,
                SUM(mar_cmm/1000000) AS mar_cmm,
                /*--------------------*/
                SUM(apr_act/1000000)$apr AS apr_act,
                SUM(apr_bgd/1000000) AS apr_bgd,
                SUM(apr_cmm/1000000) AS apr_cmm,
                /*--------------------*/
                SUM(mei_act/1000000)$mei AS mei_act,
                SUM(mei_bgd/1000000) AS mei_bgd,
                SUM(mei_cmm/1000000) AS mei_cmm,
                /*--------------------*/
                SUM(jun_act/1000000)$jun AS jun_act,
                SUM(jun_bgd/1000000) AS jun_bgd,
                SUM(jun_cmm/1000000) AS jun_cmm,
                /*--------------------*/
                SUM(jul_act/1000000)$jul AS jul_act,
                SUM(jul_bgd/1000000) AS jul_bgd,
                SUM(jul_cmm/1000000) AS jul_cmm,
                /*--------------------*/
                SUM(aug_act/1000000)$aug AS aug_act,
                SUM(aug_bgd/1000000) AS aug_bgd,
                SUM(aug_cmm/1000000) AS aug_cmm,
                /*--------------------*/
                SUM(sep_act/1000000)$sep AS sep_act,
                SUM(sep_bgd/1000000) AS sep_bgd,
                SUM(sep_cmm/1000000) AS sep_cmm,
                /*--------------------*/
                SUM(oct_act/1000000)$oct AS oct_act,
                SUM(oct_bgd/1000000) AS oct_bgd,
                SUM(oct_cmm/1000000) AS oct_cmm,
                /*--------------------*/
                SUM(nov_act/1000000)$nov AS nov_act,
                SUM(nov_bgd/1000000) AS nov_bgd,
                SUM(nov_cmm/1000000) AS nov_cmm,
                /*--------------------*/
                SUM(dec_act/1000000)$dec AS dec_act,
                SUM(dec_bgd/1000000) AS dec_bgd,
                SUM(dec_cmm/1000000) AS dec_cmm,
                /*--------------------*/
                SUM(ttl_act/1000000)$ttl AS ttl_act,
                SUM(ttl_bgd/1000000) AS ttl_bgd,
                SUM(ttl_cmm/1000000) AS ttl_cmm
                /*--------------------*/

            FROM PS_BUDGET
            WHERE year $condition
            $type
            GROUP BY year";

        return $query;
    }

    public function costPerMT($string){
        $string = '/(SELECT SUM(net_weigh/1000000) FROM inquiry_receive WHERE MONTH(posting_date) = '.$string.')';
        return $string;
    }

    public function table1($condition,$year){
        $query ="SELECT
        /*--------------------*/
        SUM(jan_act/1000000) AS jan_act,
        SUM(jan_bgd/1000000) AS jan_bgd,
        SUM(jan_cmm/1000000) AS jan_cmm,
        /*--------------------*/
        SUM(feb_act/1000000) AS feb_act,
        SUM(feb_bgd/1000000) AS feb_bgd,
        SUM(feb_cmm/1000000) AS feb_cmm,
        /*--------------------*/
        SUM(mar_act/1000000) AS mar_act,
        SUM(mar_bgd/1000000) AS mar_bgd,
        SUM(mar_cmm/1000000) AS mar_cmm,
        /*--------------------*/
        SUM(apr_act/1000000) AS apr_act,
        SUM(apr_bgd/1000000) AS apr_bgd,
        SUM(apr_cmm/1000000) AS apr_cmm,
        /*--------------------*/
        SUM(mei_act/1000000) AS mei_act,
        SUM(mei_bgd/1000000) AS mei_bgd,
        SUM(mei_cmm/1000000) AS mei_cmm,
        /*--------------------*/
        SUM(jun_act/1000000) AS jun_act,
        SUM(jun_bgd/1000000) AS jun_bgd,
        SUM(jun_cmm/1000000) AS jun_cmm,
        /*--------------------*/
        SUM(jul_act/1000000) AS jul_act,
        SUM(jul_bgd/1000000) AS jul_bgd,
        SUM(jul_cmm/1000000) AS jul_cmm,
        /*--------------------*/
        SUM(aug_act/1000000) AS aug_act,
        SUM(aug_bgd/1000000) AS aug_bgd,
        SUM(aug_cmm/1000000) AS aug_cmm,
        /*--------------------*/
        SUM(sep_act/1000000) AS sep_act,
        SUM(sep_bgd/1000000) AS sep_bgd,
        SUM(sep_cmm/1000000) AS sep_cmm,
        /*--------------------*/
        SUM(oct_act/1000000) AS oct_act,
        SUM(oct_bgd/1000000) AS oct_bgd,
        SUM(oct_cmm/1000000) AS oct_cmm,
        /*--------------------*/
        SUM(nov_act/1000000) AS nov_act,
        SUM(nov_bgd/1000000) AS nov_bgd,
        SUM(nov_cmm/1000000) AS nov_cmm,
        /*--------------------*/
        SUM(dec_act/1000000) AS dec_act,
        SUM(dec_bgd/1000000) AS dec_bgd,
        SUM(dec_cmm/1000000) AS dec_cmm,
        /*--------------------*/
        SUM(ttl_act/1000000) AS ttl_act,
        SUM(ttl_bgd/1000000) AS ttl_bgd,
        SUM(ttl_cmm/1000000) AS ttl_cmm
        /*--------------------*/

        FROM PS_BUDGET
        WHERE wbs LIKE $condition
        AND $year
        ";
        return $query;
    }
}
