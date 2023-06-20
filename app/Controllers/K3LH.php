<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\K3lhReport;
use App\Models\Type;
use App\Models\Category;
use CodeIgniter\I18n\Time;
use Config\Database;

class K3LH extends BaseController
{
    public function index()
    {
        $data['title'] = "Report - K3LH";

        $tgl_mulai = $_GET['tgl_awal'] ?? false;
        $tgl_akhir = $_GET['tgl_akhir'] ?? false;

        $K3lhReport = new K3lhReport();
        $builder = $K3lhReport->builder();
        $builder->select('t_k3lh.*, t_type.id_type, t_type.type as type_text, t_category.*')
            ->join('t_type', 't_type.id_type = t_k3lh.Type')
            ->join('t_category', 't_category.Id_category = t_k3lh.ty_category');
        if ($tgl_mulai && $tgl_akhir) {
            $builder->where("t_k3lh.date BETWEEN '$tgl_mulai' AND '$tgl_akhir'")
                ->where("Deletion_status", "0")
                ->orderBy("date DESC");
            $data['K3lhReport'] = $builder->get()->getResultArray();
        } else {
            $K3lhReport = new K3lhReport();
            $data['K3lhReport'] = $builder
                ->where("Deletion_status", "0")->get()->getResultArray();
        }


        $Category = new Category();
        $data['category'] = $Category->findAll();
        //dd($data);

        $Type = new Type();
        $data['type'] = $Type->findAll();

        $data['today'] = Time::now()->format('d-m-y');

        echo view('pages/k3lh-table', $data);
    }

    public function action($action)
    {
        $Category = new Category();

        $categorydata = $Category->where('id_type', $action)->findAll();

        echo json_encode($categorydata);
    }

    private function generateId()
    {
        $K3lhReport = new K3lhReport();
        $builder = $K3lhReport->builder();
        $builder->select('MAX(acc_no) as max_id');

        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "ACC"  . "-0001";
            return $new_id;
        } else {
            $new_id = "ACC-" . str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    public function add()
    {
        try {
            $date = $this->request->getVar('date');
            $Type = $this->request->getVar('Type');
            $ty_category = $this->request->getVar('ty_category');
            $Description = $this->request->getVar('Description');


            $generate_id = $this->generateId();

            $K3lhReport = new K3lhReport();
            $K3lhReport->save([
                'acc_no' => $generate_id,
                'date' => $date,
                'Type' => $Type,
                'Create_by' => session()->get('username'),
                'Create_on' => Time::now()->format('d-m-y H:i:s'),
                'ty_category' => $ty_category,
                'Description' => $Description,
            ]);



            $message = "Accident report has been created";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/k3lh")->with('message', $message);
    }

    public function monitoring()
    {

        $db = Database::connect();

        $K3lhReport = new K3lhReport();
        $builder = $K3lhReport->builder();
        $data['K3lhReport'] = $builder->select('t_k3lh.*, t_type.id_type, t_type.type as type_text, t_category.*')
            ->join('t_type', 't_type.id_type = t_k3lh.Type')
            ->where("Deletion_status", "0")
            ->join('t_category', 't_category.Id_category = ty_category')->get()->getResultArray();
        $Type = new Type();
        $data['type'] = $Type->findAll();

        $Category = new Category();
        $data['category'] = $Category->findAll();

        $data['title'] = "SHE Accident Monitoring";
        $K3lhReport = new K3lhReport();
        $builder = $K3lhReport->builder();
        $losttime = $builder
            ->where("Deletion_status", "0")
            ->where('Type', '1');
        $data['losttime'] = json_encode($losttime->countAllResults());
        $potential = $builder
            ->where("Deletion_status", "0")
            ->where('Type', '2');
        $data['potential'] = json_encode($potential->countAllResults());
        $nonpotential = $builder
            ->where("Deletion_status", "0")
            ->where('Type', '3');
        $data['nonpotential'] = json_encode($nonpotential->countAllResults());


        // k3lh data grouped
        $k3lh_grouped = $builder
            ->select("COUNT(1) AS total, category, tt.`type`")
            ->join("t_category tc", "tc.Id_category = t_k3lh.ty_category")
            ->join("t_type tt", "tc.id_type = tt.id_type")
            ->where("Deletion_status", "0")
            ->groupBy("ty_category, tc.id_type,")
            ->get()->getResultArray();

        $categories = $Category->builder();
        $categories_data = $categories->select("category, tt.type")
            ->join("t_type tt", "tt.id_type = t_category.id_type")
            ->groupBy("type, category")
            ->get()->getResultArray();

        foreach ($categories_data as $cd) {
            $data['grouped_data'][$cd['type']][$cd['category']] = 0;
        }

        foreach ($k3lh_grouped as $k) {
            $data['grouped_data'][$k['type']][$k['category']] = (int) $k['total'];
        }

        $data['categories_data'] = $categories_data;

        //dd($data['grouped_data']);
        //chart

        //non potensial

        $nonearmiss = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                    INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                    INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                    WHERE tk.Deletion_status = '0'
                                    AND tk.`Type` = 3
                                    and tk.ty_category = 9
                                    GROUP BY tk.ty_category, tk.`Type` , MONTH(tk.date)")->getResultArray();
        $grouped_near = array();
        foreach ($nonearmiss as $c) {
            $grouped_near[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_near[$c['Type']])) {
                    $grouped_near[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['nonearmiss'] = $grouped_near;

        $nofirstaid = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                    INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                    INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                    WHERE tk.Deletion_status = '0'
                                    AND tk.`Type` = 3
                                    and tk.ty_category = 10
                                    GROUP BY tk.ty_category, tk.`Type` , MONTH(tk.date)")->getResultArray();
        $grouped_aid = array();
        foreach ($nofirstaid as $c) {
            $grouped_aid[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_aid[$c['Type']])) {
                    $grouped_aid[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['nofirstaid'] = $grouped_aid;

        //dd($grouped_aid);

        $nomedic = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                    INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                    INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                    WHERE tk.Deletion_status = '0'
                                    AND tk.`Type` = 3
                                    and tk.ty_category = 11
                                    GROUP BY tk.ty_category, tk.`Type` , MONTH(tk.date)")->getResultArray();
        $grouped_nomedic = array();
        foreach ($nomedic as $c) {
            $grouped_nomedic[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_nomedic[$c['Type']])) {
                    $grouped_nomedic[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['nomedic'] = $grouped_nomedic;

        $nofire = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                    INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                    INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                    WHERE tk.Deletion_status = '0'
                                    AND tk.`Type` = 3
                                    and tk.ty_category = 12
                                    GROUP BY tk.ty_category, tk.`Type` , MONTH(tk.date)")->getResultArray();
        $grouped_nofire = array();
        foreach ($nofire as $c) {
            $grouped_nofire[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_nofire[$c['Type']])) {
                    $grouped_nofire[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['nofire'] = $grouped_nofire;

        $nopropertidemage = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                        INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                        INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                        WHERE tk.Deletion_status = '0'
                                        AND tk.`Type` = 3
                                        and tk.ty_category = 13
                                        GROUP BY tk.ty_category, tk.`Type`, MONTH(tk.date)")->getResultArray();
        $grouped_nondemage = array();
        foreach ($nopropertidemage as $c) {
            $grouped_nondemage[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_nondemage[$c['Type']])) {
                    $grouped_nondemage[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['nopropertidemage'] = $grouped_nondemage;

        //dd($grouped_nondemage);

        $ringan = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                        INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                        INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                        WHERE tk.Deletion_status = '0'
                                        AND tk.`Type` = 1
                                        and tk.ty_category = 1
                                        GROUP BY tk.ty_category, tk.`Type`, MONTH(tk.date)")->getResultArray();
        $grouped_ringan = array();
        foreach ($ringan as $c) {
            $grouped_ringan[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_ringan[$c['Type']])) {
                    $grouped_ringan[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['ringan'] = $grouped_ringan;

        $berat = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                        INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                        INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                        WHERE tk.Deletion_status = '0'
                                        AND tk.`Type` = 1
                                        and tk.ty_category = 2
                                        GROUP BY tk.ty_category, tk.`Type`, MONTH(tk.date)")->getResultArray();
        $grouped_berat = array();
        foreach ($berat as $c) {
            $grouped_berat[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_berat[$c['Type']])) {
                    $grouped_berat[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['berat'] = $grouped_berat;

        $mati = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                        INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                        INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                        WHERE tk.Deletion_status = '0'
                                        AND tk.`Type` = 1
                                        and tk.ty_category = 3
                                        GROUP BY tk.ty_category, tk.`Type`, MONTH(tk.date)")->getResultArray();
        $grouped_mati = array();
        foreach ($mati as $c) {
            $grouped_mati[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_mati[$c['Type']])) {
                    $grouped_mati[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['mati'] = $grouped_mati;

        $potnear = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                        INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                        INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                        WHERE tk.Deletion_status = '0'
                                        AND tk.`Type` = 2
                                        and tk.ty_category = 4
                                        GROUP BY tk.ty_category, tk.`Type`, MONTH(tk.date)")->getResultArray();
        $grouped_potnear = array();
        foreach ($potnear as $c) {
            $grouped_potnear[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_potnear[$c['Type']])) {
                    $grouped_potnear[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['potnear'] = $grouped_potnear;

        $potfirst = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                        INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                        INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                        WHERE tk.Deletion_status = '0'
                                        AND tk.`Type` = 2
                                        and tk.ty_category = 5
                                        GROUP BY tk.ty_category, tk.`Type`, MONTH(tk.date)")->getResultArray();
        $grouped_potfirst = array();
        foreach ($potfirst as $c) {
            $grouped_potfirst[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_potfirst[$c['Type']])) {
                    $grouped_potfirst[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['potfirst'] = $grouped_potfirst;

        $potmedical = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                        INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                        INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                        WHERE tk.Deletion_status = '0'
                                        AND tk.`Type` = 2
                                        and tk.ty_category = 6
                                        GROUP BY tk.ty_category, tk.`Type`, MONTH(tk.date)")->getResultArray();
        $grouped_potmedical = array();
        foreach ($potmedical as $c) {
            $grouped_potmedical[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_potmedical[$c['Type']])) {
                    $grouped_potmedical[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['potmedical'] = $grouped_potmedical;

        $potfire = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category , tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                        INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                        INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                        WHERE tk.Deletion_status = '0'
                                        AND tk.`Type` = 2
                                        and tk.ty_category = 7
                                        GROUP BY tk.ty_category, tk.`Type`, MONTH(tk.date)")->getResultArray();
        $grouped_potfire = array();
        foreach ($potfire as $c) {
            $grouped_potfire[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_potfire[$c['Type']])) {
                    $grouped_potfire[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['potfire'] = $grouped_potfire;

        $potdemage = $db->query("SELECT COUNT(tk.ty_category) AS total, tk.ty_category, tk.`Type`, tc.category, tt.`type`, MONTH(tk.date) AS bulan  FROM t_k3lh tk
                                        INNER JOIN t_category tc ON tk.ty_category = tc.Id_category
                                        INNER JOIN t_type tt ON tk.`Type`= tt.id_type
                                        WHERE tk.Deletion_status = '0'
                                        AND tk.`Type` = 2
                                        and tk.ty_category = 8
                                        GROUP BY tk.ty_category, tk.`Type`, MONTH(tk.date)")->getResultArray();
        $grouped_potdemage = array();
        foreach ($potdemage as $c) {
            $grouped_potdemage[$c['Type']][$c['bulan']] = $c;
            for ($i = 1; $i <= 12; $i++) {
                if (!array_key_exists($i, $grouped_potdemage[$c['Type']])) {
                    $grouped_potdemage[$c['Type']][$i] = array('bulan' => $i);
                }
            }
        }
        $data['potdemage'] = $grouped_potdemage;

        //dd($grouped_potdemage);

        //dd($categories_data, $k3lh_grouped, $data['grouped_data'], $hitung_report_nontimeinjur);

        $data['year'] = range(date('Y'), date('Y') - 4);



        //var_dump($data);

        echo view('pages/she-accident-monitoring', $data);
    }

    public function getCard($year, $month)
    {
        $K3lhReport = new K3lhReport();
        $builder = $K3lhReport->builder();
        $Category = new Category();
        // k3lh data grouped
        $k3lh_grouped = $builder
            ->select("COUNT(1) AS total, category, tt.`type`")
            ->join("t_category tc", "tc.Id_category = t_k3lh.ty_category")
            ->join("t_type tt", "tc.id_type = tt.id_type")
            ->where("Deletion_status", "0")
            ->where("YEAR(date) = $year")
            ->where("MONTH(date) = $month")
            ->groupBy("ty_category, tc.id_type")
            ->get()->getResultArray();

        $categories = $Category->builder();
        $categories_data = $categories->select("category, tt.type")
            ->join("t_type tt", "tt.id_type = t_category.id_type")
            ->groupBy("type, category")
            ->get()->getResultArray();

        foreach ($categories_data as $cd) {
            $data['grouped_data'][$cd['type']][$cd['category']] = 0;
        }

        foreach ($k3lh_grouped as $k) {
            $data['grouped_data'][$k['type']][$k['category']] = (int) $k['total'];
        }

        //dd($data);

        $view = view('templates/k3lh-cards', $data);
        return $this->response->setStatusCode(200)->setContentType('text/plain')->setBody($view);
    }


    public function delete($id)
    {
        $K3lhReport = new K3lhReport();
        // dd($K3lhReport);
        $K3lhReport->update(
            $id,
            [
                'Deletion_status' => '1',
                'Change_by' => session()->get('username'),
                'Change_on' => Time::now()->format('d-m-y H:i:s')
            ]
        );


        return redirect()->to("/k3lh")->with('message', 'Accident has been deleted');
    }



    public function edit($id)
    {
        $data['title'] = "Report - K3LH";

        $K3lhReport = new K3lhReport();
        $builder = $K3lhReport->builder();
        $builder->select("t_k3lh.*")
            ->join("t_category tc", "tc.Id_category = t_k3lh.ty_category");
        $builder->where("id", $id);
        $data['K3lhReport'] = $builder->get()->getRowArray();

        $Type = new Type();
        $data['type'] = $Type->findAll();

        $Category = new Category();
        $data['category'] = $Category->where("id_type", $data['K3lhReport']['Type'])->findAll();


        echo view('pages/k3lh-edit-menu', $data);
    }


    public function update()
    {
        $date = $this->request->getVar('date');
        $Type = $this->request->getVar('Type');
        $ty_category = $this->request->getVar('ty_category');
        $Description = $this->request->getVar('Description');
        $id = $this->request->getVar('id');
        $K3lhReport = new K3lhReport();
        $K3lhReport->update($id, [
            'date' => $date,
            'Type' => $Type,
            'ty_category' => $ty_category,
            'Description' => $Description,
            'Change_by' => session()->get('username'),
            'Change_on' => Time::now()->format('d-m-y H:i:s')
        ]);

        return redirect()->to("/k3lh")->with('message', 'Accident has been updated');
    }
}
