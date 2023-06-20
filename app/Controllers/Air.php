<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\NewAllocation;
use App\Models\Type;
use App\Models\Category;
use App\Models\Parakualitas;
use App\Models\Parameter;
use CodeIgniter\I18n\Time;
use Config\Database;
use App\Models\KualitasAir;

class Air extends BaseController
{
    public function index()
    {
        $data['title'] = "K3LH - Kualitas Air";
        
        $kuali_air = new KualitasAir();
        $builder = $kuali_air->builder();
        $builder->select('T_KualitasAir.*')
                ->orderBy("date DESC");
        $data['kuali_air']=$builder->get()->getResultArray();
        // dd($data['kuali_air']);

        $air_para = new Parameter();
        $builder = $air_para->builder();
        $builder->select('T_Parameter.*')
                ->where("T_Parameter.Deletion_status", "0");
        $data['air_para']=$builder->get()->getResultArray();

        $para_kuali = new Parakualitas();
        $builder = $para_kuali->builder();
        $builder->select('t_parakualitas.*');
        $data['para_kuali']=$builder->get()->getResultArray();


        //dd($data);

        $kuali_air = new KualitasAir();
        $data['kuali_air'] = $kuali_air
            ->where("Deletion_status", "0")->findAll();
        
        //dd($data);
        echo view('pages/kualitas_air', $data);
    }

    private function generateId()
    {
        $kuali_air = new KualitasAir();
        $builder = $kuali_air->builder();
        $builder->select('MAX(no_data) as max_id');

        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "K-A"  . "0001";
            return $new_id;
        } else {
            $new_id = "K-A" . str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function add()
    {
        try {
           
            $location = $this->request->getVar('location');
            $date = $this->request->getVar('date');
            $debit_air = $this->request->getVar('debit_air');
            $generate_id = $this->generateId();
    
            //$created_at = Time::now();
            $kuali_air = new KualitasAir();
            $kuali_air->save([
                'no_data' => $generate_id,
                'location' => $location,
                'date' => $date,
                'debit_air' => $debit_air,
                'create_by' => session()->get('username'),
                'create_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);
            
            $id_KualitasAir = $kuali_air->getInsertID();
            $value = $this->request->getVar('valueParameter');
            $idParameter = $this->request->getVar('idParameter');
            
            //dd($id_KualitasAir,$value,$idParameter);

            $Parakualitas = new Parakualitas();
            $Parakualitas->transStart();
            foreach($value as $id => $val) {
                //dd($val,$id);
                $Parakualitas->save([
                    "id_KualitasAir" => $id_KualitasAir,
                    "value" => $val,
                    "id_Parameter" => $idParameter[$id]             
            ]);
        }
        $Parakualitas->transComplete();

           

            $message = "Kualitas Air report has been created";
        } catch(\Throwable $th) {
            $message = $th->getMessage();
        }
        
        return redirect()->to("/kualitasair")->with('message', $message);
    }
    public function delete($id_KualitasAir)
    {
        $kuali_air = new KualitasAir();
        //dd($id);             
        $kuali_air->update($id_KualitasAir,
        [
            'Deletion_status' => '1',
            'change_by' => session()->get('username'),
            'change_on'=> Time::now()->format('Y-m-d H:i:s')
        ]);   
        return redirect()->to("/kualitasair")->with('message', 'CSR Budget has been deleted');
        
    }

    public function edit($id_KualitasAir)
    {
        $data['title'] = "Edit - Kualitas Air";
        
        
        $kuali_air = new KualitasAir();
        $builder = $kuali_air->builder();
        $builder->select("T_KualitasAir.*");
        $builder->where("id_KualitasAir", $id_KualitasAir);
        $data['kuali_air'] = $builder->get()->getRowArray();
        //dd($data);

        $air_para = new Parameter();
        $builder = $air_para->builder();
        $builder->select('T_Parameter.*');
        $data['air_para']=$builder->get()->getResultArray();
        

        $para_kuali = new Parakualitas();
        $builder = $para_kuali->builder();
        $builder->select('t_parakualitas.*');
        $builder->where("id_KualitasAir", $id_KualitasAir);
        $data['para_kuali']=$builder->get()->getResultArray();
        
        
        
        $data['year'] = range(date('Y'), date('Y') - 5);
           

        echo view('pages/edit-kualitasair', $data);
    }
    

    public function update()
    {
            $location = $this->request->getVar('location');
            $id_KualitasAir = $this->request->getVar('id_KualitasAir');
            $date = $this->request->getVar('date');
            $debit_air = $this->request->getVar('debit_air');
            $generate_id = $this->generateId();

           
    
            //$created_at = Time::now();
            $kuali_air = new KualitasAir();
            $kuali_air->save([

                'no_data' => $generate_id,
                'location' => $location,
                'id_KualitasAir'=>$id_KualitasAir,
                'date' => $date,
                'debit_air' => $debit_air,
                'change_by' => session()->get('username'),
                'change_on'=> Time::now()->format('Y-m-d H:i:s')                
            ]);

            
            $value = $this->request->getVar('valueParameter');
            $idParameter = $this->request->getVar('idParameter');
            
            //dd($id_KualitasAir,$value,$idParameter);

            $Parakualitas = new Parakualitas();
            $Parakualitas->where("id_KualitasAir", $id_KualitasAir)->delete();
            $Parakualitas->transStart();
            foreach($value as $id => $val) {
                //dd($val,$id);
                $Parakualitas->save([
                    "id_KualitasAir" => $id_KualitasAir,
                    "value" => $val,
                    "id_Parameter" => $idParameter[$id]             
            ]);
        }
        $Parakualitas->transComplete();
            
        return redirect()->to("/kualitasair")->with('message', 'CSR Budget has been updated');
    }

    public function monitoring()
    {

        $db = Database::connect();

        $kuali_air = new KualitasAir();
        $builder = $kuali_air->builder();
        $builder->select('T_KualitasAir.*');
        $data['kuali_air']=$builder->get()->getResultArray();

        $Ph = new KualitasAir();
        $builder = $Ph->builder();
        $builder->select("AVG(value) AS rata, tp.id_Parameter, tpr.nama_parameter")
                ->JOIN("t_parakualitas tp", "T_KualitasAir.id_KualitasAir = tp.id_KualitasAir")
                ->JOIN("T_Parameter tpr", "tp.id_Parameter = tpr.id_Parameter")
                ->WHERE("T_KualitasAir.Deletion_status != 1")
                ->WHERE("tp.id_Parameter = 1")
                ->groupBy("tp.id_Parameter");
        $data['Ph']=$builder->get()->getRowArray();

        $Tss = new KualitasAir();
        $builder = $Tss->builder();
        $builder->select("AVG(value) AS rata, tp.id_Parameter, tpr.nama_parameter")
                ->JOIN("t_parakualitas tp", "T_KualitasAir.id_KualitasAir = tp.id_KualitasAir")
                ->JOIN("T_Parameter tpr", "tp.id_Parameter = tpr.id_Parameter")
                ->WHERE("T_KualitasAir.Deletion_status != 1")
                ->WHERE("tp.id_Parameter = 2")
                ->groupBy("tp.id_Parameter");
        $data['Tss']=$builder->get()->getRowArray();

        $debit_air = new KualitasAir();
        $builder = $debit_air->builder();
        $builder->select("AVG(debit_air) AS rata")
                ->where("T_KualitasAir.Deletion_status = 0");
        $data['debit_air']=$builder->get()->getRowArray();

        $Ph_chart = new KualitasAir();
        $builder = $Ph_chart->builder();
        $Ph_chart = $db->query("SELECT AVG(tpk.value) AS rata, tp.id_Parameter, tp.nama_parameter, MONTH(DATE) AS bulan, YEAR(DATE) AS tahun 
                                FROM T_KualitasAir tka
                                INNER JOIN t_parakualitas tpk ON tka.id_KualitasAir = tpk.id_KualitasAir
                                INNER JOIN T_Parameter tp ON tpk.id_Parameter = tp.id_Parameter
                                AND tka.Deletion_status != 1
                                AND tp.id_Parameter = 1
                                GROUP BY bulan, tahun")->getResultArray();
        $data['Ph_chart'] = $Ph_chart;
        
        $tss_chart= $db->query("SELECT AVG(tpk.value) AS rata, tp.id_Parameter, tp.nama_parameter, MONTH(DATE) AS bulan, YEAR(DATE) AS tahun 
                                FROM T_KualitasAir tka
                                INNER JOIN t_parakualitas tpk ON tka.id_KualitasAir = tpk.id_KualitasAir
                                INNER JOIN T_Parameter tp ON tpk.id_Parameter = tp.id_Parameter
                                AND tka.Deletion_status != 1
                                AND tp.id_Parameter = 2
                                GROUP BY bulan, tahun")->getResultArray();
        $data['tss_chart'] = $tss_chart;

        $debit_chart= $db->query("SELECT AVG(tka.debit_air) AS rata, month(DATE) AS bulan, YEAR(DATE) AS tahun 
                                FROM T_KualitasAir tka
                                where tka.Deletion_status != 1 
                                GROUP BY bulan, tahun")->getResultArray();
        $data['debit_chart'] = $debit_chart;
        
       //dd($data);

        $data['year'] = range(date('Y'), date('Y') - 4);

        $data['air'] = "test";

        $data['title'] = "Kualitas Air Dashboard";

        echo view('pages/KualitasAir-Dashboard', $data);
    }
}
