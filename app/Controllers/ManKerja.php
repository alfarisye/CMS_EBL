<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;
use App\Models\K3lhReport;
use App\Models\Type;
use App\Models\Tambahtenaga;
use App\Models\TenagaKerja;
use App\Models\ManStockholder;
use App\Models\Formtambahkerja;
use App\Models\ManForm;
use App\Models\JamKerja;
use App\Models\budgetcsr;
use CodeIgniter\I18n\Time;
use App\Models\DocReminder as DocReminderModel;

class ManKerja extends BaseController
{
    public function index()
    {
        $data['title'] = "Tenaga Kerja";

        $bulan = $_GET['bulan'] ?? false;
        $tahun = $_GET['tahun'] ?? false;

        $tenaga_kerja = new TenagaKerja();
        $builder = $tenaga_kerja->builder();
        $builder->select('T_tenagakerja.*, T_stockholder.stockholder')
            ->join('T_stockholder', 'T_tenagakerja.id_stockholder = T_stockholder.id_stockholder')
            ->where("T_tenagakerja.Deletion_status", "0");
        if ($bulan && $tahun) {
            // dd($bulan, $tahun);
            $builder->where("month_jamkerja", $bulan);
            $builder->where("year_jamkerja", $tahun);
            $data['tenaga_kerja'] = $builder->get()->getResultArray();
        } else {

            $data['tenaga_kerja'] = $builder->get()->getResultArray();
        }


        $tambah_tenaga = new Tambahtenaga();
        $builder = $tambah_tenaga->builder();
        $builder->select('t_tambahtenaga.*');
        $data['tambah_tenaga'] = $builder->get()->getResultArray();

        $man_form = new ManForm();
        $builder = $man_form->builder();
        $builder->select('T_Form.*')
            ->where("T_Form.Deletion_status", "0");
        $data['man_form'] = $builder->get()->getResultArray();

        $stackholder = new ManStockholder();
        $builder = $stackholder->builder();
        $builder->select('T_stockholder.*')
            ->where("T_stockholder.Deletion_status", "0");
        $data['stackholder'] = $builder->get()->getResultArray();

        $data['today'] = Time::now()->format('Y-m-d');

        $data['year'] = range(date('Y'), date('Y') - 5);
        //dd($data);



        echo view('pages/man-tenagakerja', $data);
    }

    public function add()
    {
        try {
            $bulan = (int) $this->request->getVar('bulan');
            $tahun = (int) $this->request->getVar('tahun');
            $id_stock = $this->request->getVar('id_stockholder');
            $id_form = $this->request->getVar('id_form');


            //$created_at = Time::now();
            $tenaga_kerja = new TenagaKerja();
            $tenaga_kerja->save([
                'month_jamkerja' => $bulan,
                'year_jamkerja' => $tahun,
                'id_stockholder' => $id_stock,
                'id_form' => $id_form,
                'create_by' => session()->get('username'),
                'create_on' => Time::now()->format('Y-m-d H:i:s')
            ]);

            $id_tenagakerja = $tenaga_kerja->getInsertID();
            $value = $this->request->getVar('valueParameter');
            $idForm = $this->request->getVar('idForm');

            //dd($id_KualitasAir,$value,$idParameter);

            $tambahformtenaga = new Tambahtenaga();
            $tambahformtenaga->transStart();
            foreach ($value as $id => $val) {
                //dd($val,$id); 
                $tambahformtenaga->save([
                    "id_tenagakerja" => $id_tenagakerja,
                    "value" => $val,
                    "id_form" => $idForm[$id]
                ]);
            }
            $tambahformtenaga->transComplete();



            $message = "Tenaga Kerja report has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/Manpower")->with('message', $message);
    }



    public function delete($id_tenagakerja)
    {
        $tenaga_kerja = new TenagaKerja();

        $tenaga_kerja->update(
            $id_tenagakerja,
            [
                'Deletion_status' => '1',
                'change_by' => session()->get('username'),
                'change_on' => Time::now()->format('Y-m-d H:i:s')
            ]
        );
        return redirect()->to("/Manpower")->with('message', 'Tenaga Kerja has been deleted');
    }

    public function edit($id_tenagakerja)
    {
        $data['title'] = "Edit - Jam Kerja";

        $tenaga_kerja = new TenagaKerja();
        $builder = $tenaga_kerja->builder();
        $builder->select('T_tenagakerja.*, T_stockholder.stockholder')
            ->join('T_stockholder', 'T_tenagakerja.id_stockholder = T_stockholder.id_stockholder');
        $builder->where("id_tenagakerja", $id_tenagakerja);
        $data['tenaga_kerja'] = $builder->get()->getRowArray();


        $man_form = new ManForm();
        $builder = $man_form->builder();
        $builder->select('T_Form.*');
        $data['man_form'] = $builder->get()->getResultArray();

        $tambah_tenaga = new Tambahtenaga();
        $builder = $tambah_tenaga->builder();
        $builder->select('t_tambahtenaga.*');
        $builder->where("id_tenagakerja", $id_tenagakerja);
        $data['tambah_tenaga'] = $builder->get()->getResultArray();

        $stackholder = new ManStockholder();
        $data['stackholder'] = $stackholder->findAll();

        $data['today'] = Time::now()->format('Y-m-d');

        $data['year'] = range(date('Y'), date('Y') - 5);


        echo view('pages/edit-Tenagakerja', $data);
    }


    public function update()
    {

        $bulan = (int) $this->request->getVar('bulan');
        $tahun = (int) $this->request->getVar('tahun');
        $id_tenagakerja = $this->request->getVar('id_tenagakerja');
        $id_stock = $this->request->getVar('id_stockholder');
        $id_form = $this->request->getVar('id_form');

        $db = Database::connect();

        //$created_at = Time::now();
        $tenaga_kerja = new TenagaKerja();
        $tenaga_kerja->save([
            'month_jamkerja' => $bulan,
            'year_jamkerja' => $tahun,
            'id_tenagakerja' => $id_tenagakerja,
            'id_stockholder' => $id_stock,
            'id_form' => $id_stock,
            'create_by' => session()->get('username'),
            'create_on' => Time::now()->format('Y-m-d H:i:s')
        ]);

        $value = $this->request->getVar('valueParameter');
        $idForm = $this->request->getVar('idForm');

        //dd($id_KualitasAir,$value,$idParameter);

        $tambahformtenaga = new Tambahtenaga();
        $tambahformtenaga->where("id_tenagakerja", $id_tenagakerja)->delete();
        $tambahformtenaga->transStart();
        foreach ($value as $id => $val) {
            //dd($val,$id); 
            $tambahformtenaga->save([
                "id_tenagakerja" => $id_tenagakerja,
                "value" => $val,
                "id_form" => $idForm[$id]
            ]);
        }
        $tambahformtenaga->transComplete();

        return redirect()->to("/Manpower")->with('message', 'Jam Kerja has been updated');
    }


    public function monitoring()
    {
        $db = Database::connect();

        $tenaga_kerja = new TenagaKerja();
        $builder = $tenaga_kerja->builder();
        $builder->select('T_tenagakerja.*');
        $data['tenaga_kerja'] = $builder->get()->getResultArray();

        //Bagain pembuatan dasboard value tenaga kerja

        $operational_kerja = new TenagaKerja();
        $builder = $operational_kerja->builder();
        $builder->select("SUM(value) AS total, tta.id_form, tf.nama_form")
            ->JOIN("t_tambahtenaga tta", "T_tenagakerja.id_tenagakerja = tta.id_tenagakerja")
            ->JOIN("T_Form tf", "tta.id_form = tf.id_form")
            ->WHERE("T_tenagakerja.Deletion_status != 1")
            ->WHERE("tta.id_form = 1")
            ->groupBy("tta.id_form");
        $data['operational_kerja'] = $builder->get()->getRowArray();

        $Administrasi_kerja = new TenagaKerja();
        $builder = $Administrasi_kerja->builder();
        $builder->select("SUM(value) AS total, tta.id_form, tf.nama_form")
            ->JOIN("t_tambahtenaga tta", "T_tenagakerja.id_tenagakerja = tta.id_tenagakerja")
            ->JOIN("T_Form tf", "tta.id_form = tf.id_form")
            ->WHERE("T_tenagakerja.Deletion_status != 1")
            ->WHERE("tta.id_form = 2")
            ->groupBy("tta.id_form");
        $data['Administrasi_kerja'] = $builder->get()->getRowArray();

        $Pengawas_kerja = new TenagaKerja();
        $builder = $Pengawas_kerja->builder();
        $builder->select("SUM(value) AS total, tta.id_form, tf.nama_form")
            ->JOIN("t_tambahtenaga tta", "T_tenagakerja.id_tenagakerja = tta.id_tenagakerja")
            ->JOIN("T_Form tf", "tta.id_form = tf.id_form")
            ->WHERE("T_tenagakerja.Deletion_status != 1")
            ->WHERE("tta.id_form = 3")
            ->groupBy("tta.id_form");
        $data['Pengawas_kerja'] = $builder->get()->getRowArray();

        $total_kerja = new TenagaKerja();
        $builder = $total_kerja->builder();
        $builder->select("SUM(value) AS total")
            ->JOIN("t_tambahtenaga tta", "T_tenagakerja.id_tenagakerja = tta.id_tenagakerja")
            ->JOIN("T_Form tf", "tta.id_form = tf.id_form")
            ->WHERE("T_tenagakerja.Deletion_status != 1");
        $data['total_kerja'] = $builder->get()->getRowArray();

        //Bagain pembuatan dasboard value jam kerja

        $operational_jam = new JamKerja();
        $builder = $operational_jam->builder();
        $builder->select("SUM(value) AS total, ttf.id_form, tf.nama_form")
            ->JOIN("t_tambahform ttf", "T_jamkerja.id_JamKerja = ttf.id_JamKerja")
            ->JOIN("T_Form tf", "ttf.id_form = tf.id_form")
            ->WHERE("T_jamkerja.Deletion_status != 1")
            ->WHERE("ttf.id_form = 1")
            ->groupBy("ttf.id_form");
        $data['operational_jam'] = $builder->get()->getRowArray();

        $Administrasi_jam = new JamKerja();
        $builder = $Administrasi_jam->builder();
        $builder->select("SUM(value) AS total, ttf.id_form, tf.nama_form")
            ->JOIN("t_tambahform ttf", "T_jamkerja.id_JamKerja = ttf.id_JamKerja")
            ->JOIN("T_Form tf", "ttf.id_form = tf.id_form")
            ->WHERE("T_jamkerja.Deletion_status != 1")
            ->WHERE("ttf.id_form = 2")
            ->groupBy("ttf.id_form");
        $data['Administrasi_jam'] = $builder->get()->getRowArray();

        $Pengawas_jam = new JamKerja();
        $builder = $Pengawas_jam->builder();
        $builder->select("SUM(value) AS total, ttf.id_form, tf.nama_form")
            ->JOIN("t_tambahform ttf", "T_jamkerja.id_JamKerja = ttf.id_JamKerja")
            ->JOIN("T_Form tf", "ttf.id_form = tf.id_form")
            ->WHERE("T_jamkerja.Deletion_status != 1")
            ->WHERE("ttf.id_form = 3")
            ->groupBy("ttf.id_form");
        $data['Pengawas_jam'] = $builder->get()->getRowArray();

        $total_jam = new JamKerja();
        $builder = $total_jam->builder();
        $builder->select("SUM(value) AS total")
            ->JOIN("t_tambahform ttf", "T_jamkerja.id_JamKerja = ttf.id_JamKerja")
            ->JOIN("T_Form tf", "ttf.id_form = tf.id_form")
            ->WHERE("T_jamkerja.Deletion_status != 1");
        $data['total_jam'] = $builder->get()->getRowArray();

        //Bagain pembuatan dasboard value total jam  kerja perusahaan tambang

        $perusahaan_tambangjam = new JamKerja();
        $builder = $perusahaan_tambangjam->builder();
        $builder->select("SUM(value) AS total")
            ->JOIN("t_tambahform ttf", "T_jamkerja.id_JamKerja = ttf.id_JamKerja")
            ->JOIN("T_Form tf", "ttf.id_form = tf.id_form")
            ->JOIN("T_stockholder ts", "T_jamkerja.id_stockholder = ts.id_stockholder")
            ->WHERE("T_jamkerja.Deletion_status != 1")
            ->WHERE("ts.id_stockholder != 2");
        $data['perusahaan_tambangjam'] = $builder->get()->getRowArray();

        //Bagain pembuatan dasboard value total jam  kerja kontraktor
        $kontraktor_jam = new JamKerja();
        $builder = $kontraktor_jam->builder();
        $builder->select("SUM(value) AS total, ts.id_stockholder, ts.stockholder")
            ->JOIN("t_tambahform ttf", "T_jamkerja.id_JamKerja = ttf.id_JamKerja")
            ->JOIN("T_Form tf", "ttf.id_form = tf.id_form")
            ->JOIN("T_stockholder ts", "T_jamkerja.id_stockholder = ts.id_stockholder")
            ->WHERE("T_jamkerja.Deletion_status != 1")
            ->WHERE("ts.id_stockholder = 2")
            ->groupBy("ts.id_stockholder");
        $data['kontraktor_jam'] = $builder->get()->getRowArray();

        //dd($data['kontraktor_jam']);

        //Bagain pembuatan dasboard value total tenaga  kerja perushaan tambang

        $perusahaan_tambangkerja = new TenagaKerja();
        $builder = $perusahaan_tambangkerja->builder();
        $builder->select("SUM(value) AS total")
            ->JOIN("t_tambahtenaga tta", "T_tenagakerja.id_tenagakerja = tta.id_tenagakerja")
            ->JOIN("T_Form tf", "tta.id_form = tf.id_form")
            ->JOIN("T_stockholder ts", "T_tenagakerja.id_stockholder = ts.id_stockholder")
            ->WHERE("T_tenagakerja.Deletion_status != 1")
            ->WHERE("ts.id_stockholder != 2");
        $data['perusahaan_tambangkerja'] = $builder->get()->getRowArray();

        //bagian pembuatan dashboard value total tenaga kerja kontraktor

        $kontraktor_kerja = new TenagaKerja();
        $builder = $kontraktor_kerja->builder();
        $builder->select("SUM(value) AS total, ts.id_stockholder, ts.stockholder")
            ->JOIN("t_tambahtenaga tta", "T_tenagakerja.id_tenagakerja = tta.id_tenagakerja")
            ->JOIN("T_Form tf", "tta.id_form = tf.id_form")
            ->JOIN("T_stockholder ts", "T_tenagakerja.id_stockholder = ts.id_stockholder")
            ->WHERE("T_tenagakerja.Deletion_status != 1")
            ->WHERE("ts.id_stockholder = 2")
            ->groupBy("ts.id_stockholder");
        $data['kontraktor_kerja'] = $builder->get()->getRowArray();

        //bagian pembuat chart untuk perusahaan tambang tenaga kerja

        $crt_perusahaan_opra = new TenagaKerja();
        $builder = $crt_perusahaan_opra->builder();
        $crt_perusahaan_opra = $db->query("SELECT ttt.id_form, tf.nama_form, ttt.value, ts.id_stockholder, ts.stockholder, ttk.month_jamkerja AS bulan, ttk.year_jamkerja AS tahun 
                                           FROM T_tenagakerja ttk 
                                           INNER JOIN t_tambahtenaga ttt ON ttk.id_tenagakerja = ttt.id_tenagakerja
                                           INNER JOIN T_Form tf ON ttt.id_form = tf.id_form
                                           INNER JOIN T_stockholder ts ON ttk.id_stockholder = ts.id_stockholder
                                           AND ttk.Deletion_status != 1
                                           WHERE ttk.id_stockholder != 2
                                           AND  ttt.id_form = 1")->getResultArray();
        $data['crt_perusahaan_opra'] = $crt_perusahaan_opra;

        $crt_perusahaan_admin = $db->query("SELECT ttt.id_form, tf.nama_form, ttt.value, ts.id_stockholder, ts.stockholder, ttk.month_jamkerja AS bulan, ttk.year_jamkerja AS tahun 
                                           FROM T_tenagakerja ttk 
                                           INNER JOIN t_tambahtenaga ttt ON ttk.id_tenagakerja = ttt.id_tenagakerja
                                           INNER JOIN T_Form tf ON ttt.id_form = tf.id_form
                                           INNER JOIN T_stockholder ts ON ttk.id_stockholder = ts.id_stockholder
                                           AND ttk.Deletion_status != 1
                                           WHERE ttk.id_stockholder != 2
                                           AND  ttt.id_form = 2")->getResultArray();
        $data['crt_perusahaan_admin'] = $crt_perusahaan_admin;

        $crt_perusahaan_peng = $db->query("SELECT ttt.id_form, tf.nama_form, ttt.value, ts.id_stockholder, ts.stockholder, ttk.month_jamkerja AS bulan, ttk.year_jamkerja AS tahun 
                                           FROM T_tenagakerja ttk 
                                           INNER JOIN t_tambahtenaga ttt ON ttk.id_tenagakerja = ttt.id_tenagakerja
                                           INNER JOIN T_Form tf ON ttt.id_form = tf.id_form
                                           INNER JOIN T_stockholder ts ON ttk.id_stockholder = ts.id_stockholder
                                           AND ttk.Deletion_status != 1
                                           WHERE ttk.id_stockholder != 2
                                           AND  ttt.id_form = 3")->getResultArray();
        $data['crt_perusahaan_peng'] = $crt_perusahaan_peng;

        //bagian pembuat chart untuk perusahaan tambang jam kerja

        $crt_perusahaan_jamopra = new JamKerja();
        $builder = $crt_perusahaan_jamopra->builder();
        $crt_perusahaan_jamopra = $db->query("SELECT ttf.id_form, tf.nama_form, ts.id_stockholder, ts.stockholder, ttf.value, tj.month_jamkerja AS bulan, tj.year_jamkerja AS tahun 
                                              FROM T_jamkerja tj
                                              INNER JOIN t_tambahform ttf ON tj.id_JamKerja = ttf.id_JamKerja
                                              INNER JOIN T_Form tf ON ttf.id_form = tf.id_form
                                              INNER JOIN T_stockholder ts ON tj.id_stockholder = ts.id_stockholder
                                              AND tj.Deletion_status != 1
                                              WHERE ts.id_stockholder != 2 
                                              AND ttf.id_form = 1")->getResultArray();
        $data['crt_perusahaan_jamopra'] = $crt_perusahaan_jamopra;

        $crt_perusahaan_jamadmin = $db->query("SELECT ttf.id_form, tf.nama_form, ts.id_stockholder, ts.stockholder, ttf.value, tj.month_jamkerja AS bulan, tj.year_jamkerja AS tahun 
                                              FROM T_jamkerja tj
                                              INNER JOIN t_tambahform ttf ON tj.id_JamKerja = ttf.id_JamKerja
                                              INNER JOIN T_Form tf ON ttf.id_form = tf.id_form
                                              INNER JOIN T_stockholder ts ON tj.id_stockholder = ts.id_stockholder
                                              AND tj.Deletion_status != 1
                                              WHERE ts.id_stockholder != 2 
                                              AND ttf.id_form = 2")->getResultArray();
        $data['crt_perusahaan_jamadmin'] = $crt_perusahaan_jamadmin;

        $crt_perusahaan_jampeng = $db->query("SELECT ttf.id_form, tf.nama_form, ts.id_stockholder, ts.stockholder, ttf.value, tj.month_jamkerja AS bulan, tj.year_jamkerja AS tahun 
                                              FROM T_jamkerja tj
                                              INNER JOIN t_tambahform ttf ON tj.id_JamKerja = ttf.id_JamKerja
                                              INNER JOIN T_Form tf ON ttf.id_form = tf.id_form
                                              INNER JOIN T_stockholder ts ON tj.id_stockholder = ts.id_stockholder
                                              AND tj.Deletion_status != 1
                                              WHERE ts.id_stockholder != 2 
                                              AND ttf.id_form = 3")->getResultArray();
        $data['crt_perusahaan_jampeng'] = $crt_perusahaan_jampeng;

        //bagian pembuat chart untuk kontraktor tenaga kerja

        $crt_kontrak_opra = new TenagaKerja();
        $builder = $crt_kontrak_opra->builder();
        $crt_kontrak_opra = $db->query("SELECT ttt.id_form, tf.nama_form, ttt.value, ts.id_stockholder, ts.stockholder, ttk.month_jamkerja AS bulan, ttk.year_jamkerja AS tahun 
                                           FROM T_tenagakerja ttk 
                                           INNER JOIN t_tambahtenaga ttt ON ttk.id_tenagakerja = ttt.id_tenagakerja
                                           INNER JOIN T_Form tf ON ttt.id_form = tf.id_form
                                           INNER JOIN T_stockholder ts ON ttk.id_stockholder = ts.id_stockholder
                                           AND ttk.Deletion_status != 1
                                           WHERE ttk.id_stockholder = 2
                                           AND  ttt.id_form = 1")->getResultArray();
        $data['crt_kontrak_opra'] = $crt_kontrak_opra;

        $crt_kontrak_admin = $db->query("SELECT ttt.id_form, tf.nama_form, ttt.value, ts.id_stockholder, ts.stockholder, ttk.month_jamkerja AS bulan, ttk.year_jamkerja AS tahun 
                                           FROM T_tenagakerja ttk 
                                           INNER JOIN t_tambahtenaga ttt ON ttk.id_tenagakerja = ttt.id_tenagakerja
                                           INNER JOIN T_Form tf ON ttt.id_form = tf.id_form
                                           INNER JOIN T_stockholder ts ON ttk.id_stockholder = ts.id_stockholder
                                           AND ttk.Deletion_status != 1
                                           WHERE ttk.id_stockholder = 2
                                           AND  ttt.id_form = 2")->getResultArray();
        $data['crt_kontrak_admin'] = $crt_kontrak_admin;

        $crt_kontrak_peng = $db->query("SELECT ttt.id_form, tf.nama_form, ttt.value, ts.id_stockholder, ts.stockholder, ttk.month_jamkerja AS bulan, ttk.year_jamkerja AS tahun 
                                           FROM T_tenagakerja ttk 
                                           INNER JOIN t_tambahtenaga ttt ON ttk.id_tenagakerja = ttt.id_tenagakerja
                                           INNER JOIN T_Form tf ON ttt.id_form = tf.id_form
                                           INNER JOIN T_stockholder ts ON ttk.id_stockholder = ts.id_stockholder
                                           AND ttk.Deletion_status != 1
                                           WHERE ttk.id_stockholder = 2
                                           AND  ttt.id_form = 3")->getResultArray();
        $data['crt_kontrak_peng'] = $crt_kontrak_peng;

        //bagian pembuat chart untuk kontraktor Jam kerja

        $crt_kontrak_jamopra = new JamKerja();
        $builder = $crt_kontrak_jamopra->builder();
        $crt_kontrak_jamopra = $db->query("SELECT ttf.id_form, tf.nama_form, ts.id_stockholder, ts.stockholder, ttf.value, tj.month_jamkerja AS bulan, tj.year_jamkerja AS tahun 
                                              FROM T_jamkerja tj
                                              INNER JOIN t_tambahform ttf ON tj.id_JamKerja = ttf.id_JamKerja
                                              INNER JOIN T_Form tf ON ttf.id_form = tf.id_form
                                              INNER JOIN T_stockholder ts ON tj.id_stockholder = ts.id_stockholder
                                              AND tj.Deletion_status != 1
                                              WHERE ts.id_stockholder = 2 
                                              AND ttf.id_form = 1")->getResultArray();
        $data['crt_kontrak_jamopra'] = $crt_kontrak_jamopra;

        $crt_kontrak_jamadmin = $db->query("SELECT ttf.id_form, tf.nama_form, ts.id_stockholder, ts.stockholder, ttf.value, tj.month_jamkerja AS bulan, tj.year_jamkerja AS tahun 
                                              FROM T_jamkerja tj
                                              INNER JOIN t_tambahform ttf ON tj.id_JamKerja = ttf.id_JamKerja
                                              INNER JOIN T_Form tf ON ttf.id_form = tf.id_form
                                              INNER JOIN T_stockholder ts ON tj.id_stockholder = ts.id_stockholder
                                              AND tj.Deletion_status != 1
                                              WHERE ts.id_stockholder = 2 
                                              AND ttf.id_form = 2")->getResultArray();
        $data['crt_kontrak_jamadmin'] = $crt_kontrak_jamadmin;

        $crt_kontrak_jampeng = $db->query("SELECT ttf.id_form, tf.nama_form, ts.id_stockholder, ts.stockholder, ttf.value, tj.month_jamkerja AS bulan, tj.year_jamkerja AS tahun 
                                              FROM T_jamkerja tj
                                              INNER JOIN t_tambahform ttf ON tj.id_JamKerja = ttf.id_JamKerja
                                              INNER JOIN T_Form tf ON ttf.id_form = tf.id_form
                                              INNER JOIN T_stockholder ts ON tj.id_stockholder = ts.id_stockholder
                                              AND tj.Deletion_status != 1
                                              WHERE ts.id_stockholder = 2 
                                              AND ttf.id_form = 3")->getResultArray();
        $data['crt_kontrak_jampeng'] = $crt_kontrak_jampeng;


        //dd($data);      


        $data['year'] = range(date('Y'), date('Y') - 4);

        $data['air'] = "test";

        $data['title'] = "Manpower Dashboard";

        echo view('pages/Manpower-Dashboard', $data);
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
