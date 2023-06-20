<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\K3lhReport;
use App\Models\Type;
use App\Models\Category;
use App\Models\TenagaKerja;
use App\Models\ManStockholder;
use App\Models\Formtambahkerja;
use App\Models\ManForm;
use App\Models\JamKerja;
use App\Models\budgetcsr;
use CodeIgniter\I18n\Time;
use App\Models\DocReminder as DocReminderModel;

class ManJam extends BaseController
{
    public function index()
    {
        $data['title'] = "Jam Kerja";

        $bulan = $_GET['bulan'] ?? false;
        $tahun = $_GET['tahun'] ?? false;

        $jam_kerja = new JamKerja();
        $builder = $jam_kerja->builder();
        $builder->select('T_jamkerja.*, T_stockholder.stockholder')
            ->join('T_stockholder', 'T_jamkerja.id_stockholder = T_stockholder.id_stockholder')
            ->where("T_jamkerja.Deletion_status", "0");
        if ($bulan && $tahun) {
            // dd($bulan, $tahun);
            $builder->where("month_jamkerja", $bulan);
            $builder->where("year_jamkerja", $tahun);
            $data['jam_kerja'] = $builder->get()->getResultArray();
        } else {

            $data['jam_kerja'] = $builder->get()->getResultArray();
        }


        $from_kerja = new Formtambahkerja();
        $builder = $from_kerja->builder();
        $builder->select('t_tambahform.*');
        $data['from_kerja'] = $builder->get()->getResultArray();

        $man_form = new ManForm();
        $builder = $man_form->builder();
        $builder->select('T_Form.*')
                ->where("T_Form.Deletion_status", "0");
        $data['man_form'] = $builder->get()->getResultArray();
        //dd($data);

        $stackholder = new ManStockholder();
        $builder = $stackholder->builder();
        $builder->select('T_stockholder.*')
                ->where("T_stockholder.Deletion_status", "0");
        $data['stackholder'] = $builder->get()->getResultArray();

        $data['today'] = Time::now()->format('Y-m-d');

        $data['year'] = range(date('Y'), date('Y') - 5);
        //dd($data);



        echo view('pages/man-Jamkerja', $data);
    }

    public function add()
    {
        try {
            $bulan = (int) $this->request->getVar('bulan');
            $tahun = (int) $this->request->getVar('tahun');
            $id_stock = $this->request->getVar('id_stockholder');


            //$created_at = Time::now();
            $jam_kerja = new JamKerja();
            $jam_kerja->save([
                'month_jamkerja' => $bulan,
                'year_jamkerja' => $tahun,
                'id_stockholder' => $id_stock,
                'create_by' => session()->get('username'),
                'create_on' => Time::now()->format('Y-m-d H:i:s')
            ]);

            $id_JamKerja = $jam_kerja->getInsertID();
            $value = $this->request->getVar('valueParameter');
            $idForm = $this->request->getVar('idForm');

            //dd($id_KualitasAir,$value,$idParameter);

            $Formtambahkerja = new Formtambahkerja();
            $Formtambahkerja->transStart();
            foreach ($value as $id => $val) {
                //dd($val,$id); 
                $Formtambahkerja->save([
                    "id_JamKerja" => $id_JamKerja,
                    "value" => $val,
                    "id_form" => $idForm[$id]
                ]);
            }
            $Formtambahkerja->transComplete();



            $message = "Jam Kerja report has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/Jamkerja")->with('message', $message);
    }



    public function delete($id_JamKerja)
    {
        $jam_kerja = new JamKerja();

        $jam_kerja->update(
            $id_JamKerja,
            [
                'Deletion_status' => '1',
                'change_by' => session()->get('username'),
                'change_on' => Time::now()->format('Y-m-d H:i:s')
            ]
        );

        return redirect()->to("/Jamkerja")->with('message', 'Jam Kerja has been deleted');
    }

    public function edit($id_JamKerja)
    {
        $data['title'] = "Edit - Jam Kerja";

        $jam_kerja = new JamKerja();
        $builder = $jam_kerja->builder();
        $builder->select('T_jamkerja.*, T_stockholder.stockholder')
                ->join('T_stockholder', 'T_jamkerja.id_stockholder = T_stockholder.id_stockholder');
        $builder->where("id_JamKerja", $id_JamKerja);
        $data['jam_kerja'] = $builder->get()->getRowArray();
       


        
        $man_form = new ManForm();
        $builder = $man_form->builder();
        $builder->select('T_Form.*');
        $data['man_form'] = $builder->get()->getResultArray();
        
        $from_kerja = new Formtambahkerja();
        $builder = $from_kerja->builder();
        $builder->select('t_tambahform.*');
        $builder->where("id_JamKerja", $id_JamKerja);
        $data['from_kerja'] = $builder->get()->getResultArray();

        $stackholder = new ManStockholder();
        $data['stackholder'] = $stackholder->findAll();

        $data['today'] = Time::now()->format('Y-m-d');

        $data['year'] = range(date('Y'), date('Y') - 5);


        echo view('pages/edit-jamkerja', $data);
    }


    public function update()
    {
        
            $bulan = (int) $this->request->getVar('bulan');
            $tahun = (int) $this->request->getVar('tahun');
            $id_JamKerja = $this->request->getVar('id_JamKerja');
            $id_stock = $this->request->getVar('id_stockholder');


            //$created_at = Time::now();
            $jam_kerja = new JamKerja();
            $jam_kerja->save([
                'month_jamkerja' => $bulan,
                'year_jamkerja' => $tahun,
                'id_JamKerja' => $id_JamKerja,
                'id_stockholder' => $id_stock,
                'create_by' => session()->get('username'),
                'create_on' => Time::now()->format('Y-m-d H:i:s')
            ]);

            $value = $this->request->getVar('valueParameter');
            $idForm = $this->request->getVar('idForm');

            //dd($id_KualitasAir,$value,$idParameter);

            $Formtambahkerja = new Formtambahkerja();
            $Formtambahkerja->where("id_JamKerja", $id_JamKerja)->delete();
            $Formtambahkerja->transStart();
            foreach ($value as $id => $val) {
                //dd($val,$id); 
                $Formtambahkerja->save([
                    "id_JamKerja" => $id_JamKerja,
                    "value" => $val,
                    "id_form" => $idForm[$id]
                ]);
            }
            $Formtambahkerja->transComplete();

        return redirect()->to("/Jamkerja")->with('message', 'Jam Kerja has been updated');
    }

    public function typeform($typ)
    {
        if ($typ == 'EKSTERNAL') {
            $sql_where = "%EKSTERNAL%";
        } else if ($typ == 'INTERNAL') {
            $sql_where = "%INTERNAL%";
        } else {
            $query = "SELECT * FROM t_csrbudget";
            return $query;
        }

        $query = "SELECT * FROM t_csrbudget WHERE formtyp_bdg LIKE $sql_where";
        return $query;

        $query = "SELECT * FROM t_csractivity WHERE formtyp_act LIKE $sql_where";
        return $query;
    }
}
