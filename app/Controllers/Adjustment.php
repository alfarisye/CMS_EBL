<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Timesheets;
use App\Models\Adjustments;
use App\Models\AdjustmentsLog;
use App\Models\Contractors;
use CodeIgniter\I18n\Time;
use CodeIgniter\HTTP\Response;

use Config\Database;

/** 
 * @author Ferry Pratama
 * 
 * Untuk menampilkan data WB monthly in CMS + adjustment 
 * 
 * @info
 * 1. perhitungan per bulan dimulai dari 1-25 januari , 26-25 tiap bulan.
 * 
 * 
 */
class Adjustment extends BaseController
{

    public function __construct()
    {
    }


    public function index()
    {
        $db = Database::connect();
        $data['title'] = "Adjustment Data";
        $data['year'] = range(date('Y'), date('Y') - 4);

        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM T_Adjustment A WHERE A.deletion_status IS NULL ORDER BY  id, transaksi, month, year DESC, month ASC");
        $data['getAdjust'] = $query->getResultArray();
        $query = $db->query("SELECT * FROM md_contractors");
        $data['id_contractors'] = $query->getResultArray();


        echo view('pages/adjustment_wb', $data);
    }
    public function add()
    {
        try {

            $contractor = $this->request->getVar('contractor');
            $transaksi = $this->request->getVar('transaksi');
            $bulan = $this->request->getVar('bulan');
            $tahun = $this->request->getVar('tahun');
            $qty = $this->request->getVar('qty');

            $change_by = session()->get('username');
            // $month = Time::parse($bulan);
            // $month = $parsed_prd->getMonth();
            // $year = $parsed_prd->getYear();
            // $generate_id = $this->generateId($date_production)

            $db = \Config\Database::connect();
            $query = "INSERT INTO `T_Adjustment` (`id_contractor`, `transaksi`, `month`, `year`, `qty`, `change_by`)
                          VALUES (:id_contractor:, :transaksi:, :month:,:year:, :qty:, :change_by:);";
            $qinsert = $db->query($query, [
                'id_contractor' => $contractor,
                'transaksi' => $transaksi,
                'month' => $bulan,
                'year' => $tahun,
                'qty' => $qty,
                'change_by' => $change_by,
            ]);
            if ($qinsert) {
                $message = "Data Added Successfully!";
            } else {
                $message = "Add Data Failed";
            }
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("operation/adjustment_wb")->with('message', $message);
    }


    public function edit($id)
    {
        $data['title'] = "Edit Distance";

        // $db = \Config\Database::connect();
        // $query = $db->query("SELECT * FROM T_Adjustment A WHERE A.id = '$id'");
        // $data['EditAdjust'] = $query->getResultArray();
        $distance = new Adjustments();
        $builder = $distance->builder();
        $builder->select('*');
        $builder->where('id', $id);
        $data['dataku'] = $builder->get()->getRowArray();

        $db = Database::connect();
        $query = $db->query("SELECT * FROM md_contractors");
        $data['id_contractors'] = $query->getResultArray();

        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        // echo view('operation/adjustment_wb_edit', $data);
        echo view('pages/adjustment_wb_edit', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function update()
    {
        $db = Database::connect();
        $id = $this->request->getVar('id');
        $Adjust = $db->query("SELECT id_contractor, transaksi, month, year, qty, Transporter_Description, change_by, change_on, deletion_status FROM T_Adjustment WHERE id = $id")->getRowArray();
        $backup = array(
            "id_contractor" => $Adjust["id_contractor"],
            "transaksi" => $Adjust["transaksi"],
            "month" => $Adjust["month"],
            "year" => $Adjust["year"],
            "qty" => $Adjust["qty"],
            "Transporter_Description" => $Adjust["Transporter_Description"],
            "change_by" => $Adjust["change_by"],
            "change_on" => $Adjust["change_on"],
            "deletion_status" => $Adjust["deletion_status"],
            "status_log" => "UPDATE"
        );
        // var_dump($Adjust);
        // $log = $db->query("SELECT * FROM T_Adjustment_log")->getRowArray();
        // var_dump($log);
        // $ModelLog = new AdjustmentsLog(); //backup data ke log sebelum update
        // $ModelLog->insert($backup);
        $db->table('T_Adjustment_log')->insert($backup);

        $contractor = $this->request->getVar('contractor');
        $transaksi = $this->request->getVar('transaksi');
        $bulan = $this->request->getVar('bulan');
        $tahun = $this->request->getVar('tahun');
        $qty = $this->request->getVar('qty');
        $change_by = session()->get('username');
        $change_on = Time::now();

        $data = [
            'id_contractor' => $contractor,
            'transaksi' => $transaksi,
            'month' => $bulan,
            'year' => $tahun,
            'qty' => $qty,
            'change_by' => $change_by,
            'change_on' => $change_on,
        ];
        // var_dump($data);
        $insert_data = array_filter($data, function ($var) {
            return $var != null;
        });
        // var_dump($insert_data);
        $ModelUpdate = new Adjustments();
        $ModelUpdate->update($id, $insert_data);
        $message = "Adjustment Updated Successfully!";
        return redirect()->to("operation/adjustment_wb")->with('message', $message);
    }

    public function delete($id)
    {
        $db = Database::connect();
        $Adjust = new Adjustments();
        $change_by = session()->get('username');
        $delete_by = session()->get('username');
        $change_on = Time::now();
        $Adjust->update($id, ['deletion_status' => $delete_by, 'change_by' => $change_by, 'change_on' => $change_on]);

        $Adjust = $db->query("SELECT id_contractor, transaksi, month, year, qty, Transporter_Description, change_by, change_on, deletion_status FROM T_Adjustment WHERE id = $id")->getRowArray();
        $backup = array(
            "id_contractor" => $Adjust["id_contractor"],
            "transaksi" => $Adjust["transaksi"],
            
            "month" => $Adjust["month"],
            "year" => $Adjust["year"],
            "qty" => $Adjust["qty"],
            "Transporter_Description" => $Adjust["Transporter_Description"],
            "change_by" => $Adjust["change_by"],
            "change_on" => $Adjust["change_on"],
            "deletion_status" => $Adjust["deletion_status"],
            "status_log" => "DELETE"
        );
        $db->table('T_Adjustment_log')->insert($backup);

        return redirect()->to("operation/adjustment_wb")->with('message', 'Adjustment has been deleted');
    }
    public function bg_adjust()
    {
        $db = Database::connect();
        $ModelUpdate = new Adjustments();
        // $db->query("TRUNCATE TABLE T_Adjustment");

        /////////////////// Inquery Transfer - CrushCoal
        $data_transfer = $db->query("SELECT SUM(a.Net_Weigh) AS qty, a.month, id_contractor, a.year AS year, a.transaksi
            FROM  
            (SELECT Net_Weigh,
            (CASE WHEN DAY(Posting_Date) > 25 AND MONTH(Posting_Date) < 12 THEN MONTH(Posting_Date) + 1 
                WHEN MONTH(Posting_Date) = 12 THEN MONTH(Posting_Date) ELSE MONTH(Posting_Date) END) AS month, YEAR(Posting_Date) AS YEAR,
                (SELECT id FROM md_contractors WHERE contractor_name LIKE CONCAT('%', 'OFN' ,'%')) AS id_contractor,
            'CrushCoal' AS transaksi
            FROM inquiry_transfer) a
            GROUP BY a.month, a.id_contractor, a.year")->getResultArray();

        if ($data_transfer) {
            echo " CrushCoal : <br>";
            foreach ($data_transfer as $row) {
                // check if data with the same id_contractor, transaksi, year, and month already exist in the T_Adjustment table
                $existing_data = $db->query("SELECT id, change_on FROM T_Adjustment WHERE id_contractor = ? AND transaksi = ? AND year = ? AND month = ?", [$row['id_contractor'], $row['transaksi'], $row['year'], $row['month']])->getRow();

                if ($existing_data) {
                    // if change_on is null or empty, update the existing data
                    if (empty($existing_data->change_on)) {
                        $ModelUpdate->update($existing_data->id, $row);
                        echo "Id : " . $row['id_contractor'] . " Updated Data Success <br>";
                    } else {
                        // if change_on is not null or empty, skip the data
                        echo "Id : " . $row['id_contractor'] . " Data Already Exist and has been Adjusted <br>";
                    }
                } else {
                    // if no data with the same id_contractor, transaksi, year, and month exist, insert a new data
                    $ModelUpdate->insert($row);
                    echo "Id : " . $row['id_contractor'] . " Inserted New Data Success <br>";
                }
            }
        } else {
            echo "Data Not Found";
        }

        /////////////////// Inquery Transfer - Hauling to Port
        $data_hauling = $db->query("SELECT SUM(a.Net_Weigh) AS qty, a.month, a.year AS year, a.Transporter_Id AS transporter , Transporter_Description,  a.transaksi, 
        (SELECT MAX(id) FROM md_contractors WHERE contractor_name = a.Transporter_Id) AS id_contractor
        FROM	
        (SELECT Net_Weigh,
        CASE WHEN Transporter_Id LIKE 'GMT%' THEN 'PT GMT'
        WHEN Transporter_Id = 'HRS' THEN 'PT HRS'
        ELSE Transporter_Id END AS Transporter_Id, Transporter_Description, 
            (CASE 
                WHEN DAY(Posting_Date) > 25 AND MONTH(Posting_Date) < 12 THEN MONTH(Posting_Date) + 1 
                WHEN MONTH(Posting_Date) = 12 THEN MONTH(Posting_Date) 
                ELSE MONTH(Posting_Date) 
            END) AS month, 
            YEAR(Posting_Date) AS YEAR,
            'Hauling to Port' AS transaksi
        FROM inquiry_transfer) a
        WHERE a.Transporter_Id != '' AND a.Transporter_Description != '' AND a.Transporter_Id IS NOT NULL AND a.Transporter_Description IS NOT NULL
        GROUP BY a.month, a.year, a.Transporter_Id, Transporter_Description, id_contractor
        ORDER BY a.year, a.month;")->getResultArray();
        if ($data_hauling) {
            echo " Hauling to Port : <br>";
            foreach ($data_hauling as $row) {
                $existing_data = $db->query("SELECT id, change_on FROM T_Adjustment WHERE id_contractor = ? AND transaksi = ? AND year = ? AND month = ?", [$row['id_contractor'], $row['transaksi'], $row['year'], $row['month']])->getRow();
                if ($existing_data) {
                    if (empty($existing_data->change_on)) {
                        $ModelUpdate->update($existing_data->id, $row);
                        echo "Id : " . $row['id_contractor'] . " Updated Data Success <br>";
                    } else {
                        echo "Id : " . $row['id_contractor'] . " Data Already Exist and has been Adjusted <br>";
                    }
                } else {
                    $ModelUpdate->insert($row);
                    echo "Id : " . $row['id_contractor'] . " Inserted New Data Success <br>";
                }
            }
        } else {
            echo "Data Not Found";
        }


        ///////////////////// Inquery Receive - Coal Getting
        $data_getting = $db->query("SELECT SUM(a.Net_Weigh) AS qty, a.month, a.id_contractor, a.year, a.transaksi FROM  
        (SELECT Net_Weigh, Transporter_Id, Transporter_Description,
        (CASE WHEN DAY(Posting_Date) > 25 AND MONTH(Posting_Date) < 12 THEN MONTH(Posting_Date) + 1 
            WHEN MONTH(Posting_Date) = 12 THEN MONTH(Posting_Date) ELSE MONTH(Posting_Date) END) AS month, YEAR(Posting_Date) AS YEAR,
        (CASE WHEN Transporter_id = 'FAB' THEN 'CK' ELSE Transporter_id END) AS temp,
        (SELECT id FROM md_contractors WHERE contractor_name LIKE CONCAT('%', temp,'%') AND Transporter_id != '') AS id_contractor,
        'Coal Getting' AS transaksi
        FROM inquiry_receive) a
        WHERE a.Transporter_Id != '' AND a.Transporter_Description != '' AND a.Transporter_Id IS NOT NULL AND a.Transporter_Description IS NOT NULL
        GROUP BY a.month, a.id_contractor, a.year")->getResultArray();
        if ($data_getting) {
            echo " Coal Getting : <br>";
            foreach ($data_getting as $row) {
                $existing_data = $db->query("SELECT id, change_on FROM T_Adjustment WHERE id_contractor = ? AND transaksi = ? AND year = ? AND month = ?", [$row['id_contractor'], $row['transaksi'], $row['year'], $row['month']])->getRow();
                if ($existing_data) {
                    if (empty($existing_data->change_on)) {
                        $ModelUpdate->update($existing_data->id, $row);
                        echo "Id : " . $row['id_contractor'] . " Updated Data Success <br>";
                    } else {
                        echo "Id : " . $row['id_contractor'] . " Data Already Exist and has been Adjusted <br>";
                    }
                } else {
                    $ModelUpdate->insert($row);
                    echo "Id : " . $row['id_contractor'] . " Inserted New Data Success <br>";
                }
            }
        } else {
            echo "Data Not Found";
        }
        /////////////////// timesheets - Distance CG 
        $data_CG = $db->query("SELECT (SUM(a.prd_cg_total * a.prd_cg_distance) / SUM(a.prd_cg_total)) AS qty, a.month, a.id_contractor, a.year AS year, a.transaksi FROM  
            (SELECT prd_cg_total, prd_cg_distance,
            (CASE WHEN DAY(prd_date) > 25 AND MONTH(prd_date) < 12 THEN MONTH(prd_date) + 1 
                WHEN MONTH(prd_date) = 12 THEN MONTH(prd_date) ELSE MONTH(prd_date) END) AS month, YEAR(prd_date) AS YEAR,
            'Distance CG' AS transaksi, id_contractor
            FROM timesheets
            WHERE status = 'approved' AND deleted_at IS NULL) a
            GROUP BY a.id_contractor, a.month, a.year")->getResultArray();
        if ($data_CG) {
            echo " Distance CG : <br>";
            foreach ($data_CG as $row) {
                $existing_data = $db->query("SELECT id, change_on FROM T_Adjustment WHERE id_contractor = ? AND transaksi = ? AND year = ? AND month = ?", [$row['id_contractor'], $row['transaksi'], $row['year'], $row['month']])->getRow();
                if ($existing_data) {
                    if (empty($existing_data->change_on)) {
                        $ModelUpdate->update($existing_data->id, $row);
                        echo "Id : " . $row['id_contractor'] . " Updated Data Success <br>";
                    } else {
                        echo "Id : " . $row['id_contractor'] . " Data Already Exist and has been Adjusted <br>";
                    }
                } else {
                    $ModelUpdate->insert($row);
                    echo "Id : " . $row['id_contractor'] . " Inserted New Data Success <br>";
                }
            }
        } else {
            echo "Data Not Found";
        }





        /////////////////// timesheets - Overburden
        $data_overburden = $db->query("SELECT SUM(a.prd_ob_total) AS qty, a.month, a.id_contractor, a.year AS year, a.transaksi FROM  
            (SELECT prd_ob_total,
            (CASE WHEN DAY(prd_date) > 25 AND MONTH(prd_date) < 12 THEN MONTH(prd_date) + 1 
                WHEN MONTH(prd_date) = 12 THEN MONTH(prd_date) ELSE MONTH(prd_date) END) AS month, YEAR(prd_date) AS YEAR,
            'Overburden' AS transaksi, id_contractor
            FROM timesheets
        	  WHERE status = 'approved' AND deleted_at IS NULL) a
            GROUP BY a.id_contractor, a.month, a.year")->getResultArray();
        if ($data_overburden) {
            echo " Overburden : <br>";
            foreach ($data_overburden as $row) {
                $existing_data = $db->query("SELECT id, change_on FROM T_Adjustment WHERE id_contractor = ? AND transaksi = ? AND year = ? AND month = ?", [$row['id_contractor'], $row['transaksi'], $row['year'], $row['month']])->getRow();
                if ($existing_data) {
                    if (empty($existing_data->change_on)) {
                        $ModelUpdate->update($existing_data->id, $row);
                        echo "Id : " . $row['id_contractor'] . " Updated Data Success <br>";
                    } else {
                        echo "Id : " . $row['id_contractor'] . " Data Already Exist and has been Adjusted <br>";
                    }
                } else {
                    $ModelUpdate->insert($row);
                    echo "Id : " . $row['id_contractor'] . " Inserted New Data Success <br>";
                }
            }
        } else {
            echo "Data Not Found";
        }
        /////////////////// timesheets - Distance OB
        $data_OB = $db->query("SELECT (SUM(a.prd_ob_total * a.prd_ob_distance) / SUM(a.prd_ob_total)) AS qty, a.month, a.id_contractor, a.year AS year, a.transaksi FROM  
            (SELECT prd_ob_total, prd_ob_distance,
            (CASE WHEN DAY(prd_date) > 25 AND MONTH(prd_date) < 12 THEN MONTH(prd_date) + 1 
                WHEN MONTH(prd_date) = 12 THEN MONTH(prd_date) ELSE MONTH(prd_date) END) AS month, YEAR(prd_date) AS YEAR,
            'Distance OB' AS transaksi, id_contractor
            FROM timesheets
            WHERE status = 'approved' AND deleted_at IS NULL) a
            GROUP BY a.id_contractor, a.month, a.year")->getResultArray();
        if ($data_OB) {
            echo " Distance OB : <br>";
            foreach ($data_OB as $row) {
                $existing_data = $db->query("SELECT id, change_on FROM T_Adjustment WHERE id_contractor = ? AND transaksi = ? AND year = ? AND month = ?", [$row['id_contractor'], $row['transaksi'], $row['year'], $row['month']])->getRow();
                if ($existing_data) {
                    if (empty($existing_data->change_on)) {
                        $ModelUpdate->update($existing_data->id, $row);
                        echo "Id : " . $row['id_contractor'] . " Updated Data Success <br>";
                    } else {
                        echo "Id : " . $row['id_contractor'] . " Data Already Exist and has been Adjusted <br>";
                    }
                } else {
                    $ModelUpdate->insert($row);
                    echo "Id : " . $row['id_contractor'] . " Inserted New Data Success <br>";
                }
            }
        } else {
            echo "Data Not Found";
        }
    }
}
