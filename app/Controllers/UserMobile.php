<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Sales\ContractOrder;
use App\Models\BlBlok;
use App\Models\BlBlokForm;
use App\Models\BlProduksi;
use App\Models\BlGeojson;
use App\Models\BlType;
use App\Models\GLogs;
use App\Models\Timesheets;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;
use App\Models\Users;
use App\Models\UserRoles;

use App\Models\DocReminder as DocReminderModel;
use App\Models\GroupEmail as GroupEmailModel;
use App\Models\SendEmailLog as SendEmailLogModel;
use PDO;




class UserMobile extends BaseController
{

    private $data_notif; //declare untuk menampung data pesan notifikasi
    private $data_userMobile; // declare untuk menampung data user untuk mendapatkan data request mereka
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        $this->GLogs = new Glogs();
    }

    public function test()
    {
        $data = [
            [
                "test" => 'asd'
            ]
        ];
        return $this->respond($data, 200);
    }
    public function getTransferWB()
    {
        // return view('index-transfer-wb');
        echo view('pages/index-transfer-wb');
    }

    public function getReceiveWB()
    {
        // return view('index-receive-wb');
        echo view('pages/index-receive-wb');
    }
    public function ApproveSales()
    {
        $id = $_GET['id'];
        $data = $_GET['data'] ?? false;
        if ($data) {
            $data = json_decode($data);

            $ContractOrder = new ContractOrder();
            $ContractOrder->find($id);
            $ContractOrder->update($id, $data);
            // return $this->respond($ContractOrder, 200);
            return $this->respond(['status' => 'success', 'message' => $ContractOrder,  "data" => $data, "id" => $id], 200);
        } else {
            return $this->respond("Error", 500);
        }
    }
    public function mobileSignIn()
    {
        $data = [
            [
                "username" => $_POST['username'],
                "password" => $_POST['password'],
            ]
        ];
        return $this->respond($data, 200);
    }
    public function signIn()
    {
        try {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $id_mobile = @$_POST['id_mobile'];
            $db = \Config\Database::connect();


            // $username = $this->request->getVar('username');
            // $password = $this->request->getVar('password');

            $model = new Users();
            $user = $model->where('username', $username)->where('status', 1)->first();

            // check if user exist
            if (!$user) {
                throw new \Exception("User not found", 1); // lempar 1 jika error bukan LDAP
            }

            $info = [
                'id'         => $username,
                'ip_address' => $this->request->getIPAddress(),
            ];

            log_message('info', 'User {id} trying to log in into system from {ip_address}', $info);

            // check if user is internal or not
            if (!$user['internal']) {
                if (password_verify($password, $user['password'])) {

                    $userRole = new UserRoles();
                    $builder = $userRole->builder();
                    $builder->select('roles.name, roles.uri_group, roles.contractor_id')
                        ->join('roles', 'roles.id = user_roles.role_id')
                        ->where('user_roles.user_id', $user['id'])
                        ->where('roles.status = 1')
                        ->where('user_roles.status = 1');
                    $result = $builder->get()->getResult();

                    $contractor_id = $result[0]->contractor_id;
                    $role = array();
                    $str_role = "^/$";
                    foreach ($result as $key => $value) {
                        $role[$key] = $value->name;
                        if ($str_role != '' && $value->uri_group != '') {
                            $str_role .= "|" . $value->uri_group;
                        } else {
                            $str_role .= $value->uri_group;
                        }
                    }
                    $model->update($user['id'], [
                        'last_login' => new Time('now'), 'id_mobile' => $id_mobile
                    ]);
                    if ($str_role) {
                        return $this->respond([
                            'status' => 'success', 'message' => 'Login Internal successfully',
                            'username' => $username, 'password' => $password,
                            'profilku' => $user
                        ], 200);
                    } else {
                        return $this->respond(['status' => 'error', 'message' => 'Failed to Login Internal'], 500);
                    }
                } else {
                    return $this->respond(['status' => 'error', 'message' => 'Wrong user/Pw for Login Internal'], 500);
                }
            }

            if (getenv("CI_ENVIRONMENT") == 'development') {

                $userRole = new UserRoles();
                $builder = $userRole->builder();
                $builder->select('roles.name, roles.uri_group, roles.contractor_id')
                    ->join('roles', 'roles.id = user_roles.role_id')
                    ->where('user_roles.user_id', $user['id'])
                    ->where('roles.status = 1')
                    ->where('user_roles.status = 1');
                $result = $builder->get()->getResult();
                // $dataku = $builder->get()->getRowArray();
                // $profilku = $dataku['user'];
                $contractor_id = $result[0]->contractor_id;
                $role = array();
                $str_role = "^/$";
                foreach ($result as $key => $value) {
                    $role[$key] = $value->name;
                    if ($str_role != '' && $value->uri_group != '') {
                        $str_role .= "|" . $value->uri_group;
                    } else {
                        $str_role .= $value->uri_group;
                    }
                }
                $model->update($user['id'], [
                    'last_login' => new Time('now'), 'id_mobile' => $id_mobile
                ]);
                if ($str_role) {
                    return $this->respond([
                        'status' => 'success', 'message' => 'Login Dev successfully',
                        'username' => $username, 'password' => $password,
                        'profilku' => $user
                    ], 200);
                } else {
                    return $this->respond(['status' => 'error', 'message' => 'Failed to Login Dev'], 500);
                }
            }

            $AD_SERVER = getenv('ad_server');
            $ldap = ldap_connect($AD_SERVER);

            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
            $bind = ldap_bind($ldap,  'hasnurgroup' . "\\" . $username, $password);

            if ($bind) {
                // assign user role
                $userRole = new UserRoles();
                $builder = $userRole->builder();
                $builder->select('roles.name, roles.uri_group')
                    ->join('roles', 'roles.id = user_roles.role_id')
                    ->where('user_roles.user_id', $user['id'])
                    ->where('roles.status = 1')
                    ->where('user_roles.status = 1');
                $result = $builder->get()->getResult();
                $role = array();
                $str_role = "^/$";
                foreach ($result as $key => $value) {
                    $role[$key] = $value->name;
                    if ($str_role != '' && $value->uri_group != '') {
                        $str_role .= "|" . $value->uri_group;
                    } else {
                        $str_role .= $value->uri_group;
                    }
                }

                $model->update($user['id'], [
                    'last_login' => new Time('now'), 'id_mobile' => $id_mobile
                ]);


                $filter = "(sAMAccountName=$username)";
                $result = ldap_search($ldap, "dc=hasnurgroup,dc=local", $filter);
                $info = ldap_get_entries($ldap, $result);

                log_message('info', 'User {id} successfully logged in', $info);

                if ($info) {
                    return $this->respond([
                        'status' => 'success', 'message' => 'Login LDAP CMS successfully',
                        'username' => $username, 'password' => $password,
                        'profilku' => $user
                    ], 200);
                } else {
                    return $this->respond(['status' => 'error', 'message' => 'Failed to Login LDAP CMS'], 500);
                }
            }
        } catch (\Throwable $th) {
            return $this->respond($th, 500);
        }
        ldap_close($ldap);
    }

    public function check_id_mobile()
    {
        $user = $_GET['user'];
        $users = new Users();
        $builder = $users->builder();
        $builder->select('*');
        $builder->where("username = '$user'");
        $data = $builder->get()->getRowArray();
        $id_mobile = $data['id_mobile'];
        return $this->respond(['id_mobile' => $id_mobile], 200); // ini cara return yang aman tnpa debugger
    }

    public function getUser()
    {

        $user = $_GET['user'];
        $users = new Users();
        $builder = $users->builder();
        $builder->select('*');
        $builder->where("username = '$user'");
        $data = $builder->get()->getRowArray();
        $email = $data['email'];
        try {
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $db = new PDO('mysql:host=192.168.14.28;dbname=ess_dev', 'razam', '');
            } else {
                $db = new PDO('mysql:host=192.168.14.28;dbname=dbmdk', 'razam', '');
            }
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "SELECT A.*
            FROM tkaryawan A
            WHERE A.Email = '$email'";
            $hasil = $db->prepare($sql);
            $hasil->execute(); //disini bisa dimaksukkan array sebagai referensi ke ? di sql query .. 
            $d = $hasil->fetchAll(PDO::FETCH_ASSOC);
            return $this->respond($d, 200);
        } catch (PDOException $e) {
            $error = "Connection failed: " . $e->getMessage();
            return $this->respond($error, 500);
        }
    }
    public function getDataTotalEss()
    {
        $user = $_GET['user'];
        $users = new Users();
        $builder = $users->builder();
        $builder->select('email');
        $builder->where("username = '$user'");
        $data = $builder->get()->getRowArray();
        $email = $data['email'];

        try {
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $db = new PDO('mysql:host=192.168.14.28;dbname=ess_dev', 'razam', '');
            } else {
                $db = new PDO('mysql:host=192.168.14.28;dbname=dbmdk', 'razam', '');
            }

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "SELECT B.*, COALESCE(C.Nama, 'unamed') AS nama_user, COALESCE(D.Nama, 'unamed') AS nama_pengganti, COALESCE(E.Nama, 'unamed') AS nama_atasan, COALESCE(F.Nama, 'unamed') AS nama_direktur
            FROM tkaryawan A
            LEFT JOIN tcuti B ON
            ((B.NRP_Pengganti = A.NRP)
            OR (B.NRP_Atasan = A.NRP)
            OR (B.NRP_Direktur = A.NRP))
            LEFT JOIN tkaryawan C ON B.NRP_User = C.NRP
            LEFT JOIN tkaryawan D ON B.NRP_Pengganti = D.NRP
            LEFT JOIN tkaryawan E ON B.NRP_Atasan = E.NRP
            LEFT JOIN tkaryawan F ON B.NRP_Direktur = F.NRP
            WHERE A.Email = '$email'
            AND (B.Apr_Direktur = '' AND B.Apr_Pengganti = '' AND B.Apr_Atasan = '')";
            $hasil = $db->prepare($sql);
            $hasil->execute(); //disini bisa dimaksukkan array sebagai referensi ke ? di sql query .. 
            $d = $hasil->fetchAll(PDO::FETCH_ASSOC);
            if ($d) {
                $count_po = count($d);
                return $this->respond($count_po, 200);
            } else {
                $error = 'Data Not Available';
                return $this->respond($error, 500);
            }
        } catch (PDOException $e) {
            $error = "Connection failed: " . $e->getMessage();
            return $this->respond($error, 500);
        }
    }
    public function getDataEss()
    {
        $user = $_GET['user'];
        $users = new Users();
        $builder = $users->builder();
        $builder->select('email');
        $builder->where("username = '$user'");
        // $data = $builder->get()->getResultArray();
        // return $this->respond($data, 200);

        $data = $builder->get()->getRowArray();
        $email = $data['email'];

        try {
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $db = new PDO('mysql:host=192.168.14.28;dbname=ess_dev', 'razam', '');
            } else {
                $db = new PDO('mysql:host=192.168.14.28;dbname=dbmdk', 'razam', '');
            }

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // $sql = "SELECT B.*
            // FROM tkaryawan A
            // LEFT JOIN tcuti B ON  (A.NRP = B.NRP_Atasan OR A.NRP = B.NRP_Pengganti OR A.NRP = B.NRP_Direktur)
            // WHERE A.Email = '$email'
            // AND ((B.NRP_Pengganti = A.NRP and B.Apr_Pengganti = '')
            // OR (B.NRP_Atasan = A.NRP and B.Apr_Atasan = '')
            // OR (B.NRP_Direktur = A.NRP AND B.Apr_Direktur = ''))";
            // $sql = "SELECT B.*, C.Nama AS nama_user, D.Nama AS nama_pengganti, E.Nama AS nama_atasan, F.Nama AS nama_direktur
            // FROM tkaryawan A
            // LEFT JOIN tcuti B ON
            // ((B.NRP_Pengganti = A.NRP)
            // OR (B.NRP_Atasan = A.NRP)
            // OR (B.NRP_Direktur = A.NRP))
            // INNER JOIN tkaryawan C ON B.NRP_User = C.NRP
            // INNER JOIN tkaryawan D ON B.NRP_Pengganti = D.NRP
            // INNER JOIN tkaryawan E ON B.NRP_Atasan = E.NRP
            // INNER JOIN tkaryawan F ON B.NRP_Direktur = F.NRP
            // WHERE A.Email = '$email'
            // AND (B.Apr_Direktur = '' AND B.Apr_Pengganti = '' AND B.Apr_Atasan = '')";
            $sql = "SELECT B.*, COALESCE(C.Nama, 'unamed') AS nama_user, COALESCE(D.Nama, 'unamed') AS nama_pengganti, COALESCE(E.Nama, 'unamed') AS nama_atasan, COALESCE(F.Nama, 'unamed') AS nama_direktur
            FROM tkaryawan A
            LEFT JOIN tcuti B ON
            ((B.NRP_Pengganti = A.NRP)
            OR (B.NRP_Atasan = A.NRP)
            OR (B.NRP_Direktur = A.NRP))
            LEFT JOIN tkaryawan C ON B.NRP_User = C.NRP
            LEFT JOIN tkaryawan D ON B.NRP_Pengganti = D.NRP
            LEFT JOIN tkaryawan E ON B.NRP_Atasan = E.NRP
            LEFT JOIN tkaryawan F ON B.NRP_Direktur = F.NRP
            WHERE A.Email = '$email'
            AND (B.Apr_Direktur = '' AND B.Apr_Pengganti = '' AND B.Apr_Atasan = '')";
            $hasil = $db->prepare($sql);
            $hasil->execute(); //disini bisa dimaksukkan array sebagai referensi ke ? di sql query .. 
            $d = $hasil->fetchAll(PDO::FETCH_ASSOC);
            if ($d) {
                return $this->respond($d, 200);
            } else {
                $error = 'Data Not Available';
                return $this->respond($error, 500);
            }
        } catch (PDOException $e) {
            $error = "Connection failed: " . $e->getMessage();
            return $this->respond($error, 500);
        }
    }
    public function ApproveCuti()
    {
        $Id_cuti = $_GET['Id_cuti'];
        $field = $_GET['field'];
        try {
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $db = new PDO('mysql:host=192.168.14.28;dbname=ess_dev', 'razam', '');
            } else {
                $db = new PDO('mysql:host=192.168.14.28;dbname=dbmdk', 'razam', '');
            }
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE tcuti A SET A.$field='V' WHERE A.Id_cuti = '$Id_cuti'";
            $hasil = $db->prepare($sql);
            $hasil->execute(); //disini bisa dimaksukkan array sebagai referensi ke ? di sql query .. 
            // $d = $hasil->fetchAll(PDO::FETCH_ASSOC);
            return $this->respond("Berhasil", 200);
        } catch (PDOException $e) {
            $error = "Connection failed: " . $e->getMessage();
            return $this->respond($error, 500);
        }
    }
    public function RejectCuti()
    {
        $Id_cuti = $_GET['Id_cuti'];
        $field = $_GET['field'];
        try {
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $db = new PDO('mysql:host=192.168.14.28;dbname=ess_dev', 'razam', '');
            } else {
                $db = new PDO('mysql:host=192.168.14.28;dbname=dbmdk', 'razam', '');
            }
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $sql = "UPDATE tcuti A SET A.$field='X' WHERE A.Id_cuti = '$Id_cuti'";
            $hasil = $db->prepare($sql);
            $hasil->execute(); //disini bisa dimaksukkan array sebagai referensi ke ? di sql query .. 
            // $d = $hasil->fetchAll(PDO::FETCH_ASSOC);

            return $this->respond("Berhasil", 200);
        } catch (PDOException $e) {
            $error = "Connection failed: " . $e->getMessage();
            return $this->respond($error, 500);
        }
    }
    public function getDataEssHistory()
    {
        $user = $_GET['user'];
        $users = new Users();
        $builder = $users->builder();
        $builder->select('email');
        $builder->where("username = '$user'");
        $data = $builder->get()->getRowArray();
        $email = $data['email'];

        try {
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $db = new PDO('mysql:host=192.168.14.28;dbname=ess_dev', 'razam', '');
            } else {
                $db = new PDO('mysql:host=192.168.14.28;dbname=dbmdk', 'razam', '');
            }
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // $sql = "SELECT B.*, C.Nama AS nama_user, D.Nama AS nama_pengganti, E.Nama AS nama_atasan, F.Nama AS nama_direktur
            // FROM tkaryawan A
            // LEFT JOIN tcuti B ON
            // ((B.NRP_Pengganti = A.NRP)
            // OR (B.NRP_Atasan = A.NRP)
            // OR (B.NRP_Direktur = A.NRP))
            // INNER JOIN tkaryawan C ON B.NRP_User = C.NRP
            // INNER JOIN tkaryawan D ON B.NRP_Pengganti = D.NRP
            // INNER JOIN tkaryawan E ON B.NRP_Atasan = E.NRP
            // INNER JOIN tkaryawan F ON B.NRP_Direktur = F.NRP
            // WHERE A.Email = '$email'
            // AND ((B.Apr_Direktur = 'V' OR B.Apr_Pengganti = 'V' OR B.Apr_Atasan = 'V')
            // OR (B.Apr_Direktur = 'X' OR B.Apr_Pengganti = 'X' OR B.Apr_Atasan = 'X'))";
            $sql = "SELECT B.*, COALESCE(C.Nama, 'unamed') AS nama_user, COALESCE(D.Nama, 'unamed') AS nama_pengganti, COALESCE(E.Nama, 'unamed') AS nama_atasan, COALESCE(F.Nama, 'unamed') AS nama_direktur
            FROM tkaryawan A
            LEFT JOIN tcuti B ON ((B.NRP_Pengganti = A.NRP) OR (B.NRP_Atasan = A.NRP) OR (B.NRP_Direktur = A.NRP))
            LEFT JOIN tkaryawan C ON B.NRP_User = C.NRP
            LEFT JOIN tkaryawan D ON B.NRP_Pengganti = D.NRP
            LEFT JOIN tkaryawan E ON B.NRP_Atasan = E.NRP
            LEFT JOIN tkaryawan F ON B.NRP_Direktur = F.NRP
            WHERE A.Email = '$email'
            AND ((B.Apr_Direktur = 'V' OR B.Apr_Pengganti = 'V' OR B.Apr_Atasan = 'V')
            OR (B.Apr_Direktur = 'X' OR B.Apr_Pengganti = 'X' OR B.Apr_Atasan = 'X')
            OR (B.Apr_Direktur = 'X' OR B.Apr_Pengganti = 'X' OR B.Apr_Atasan = '-')
            OR	(B.Apr_Direktur = 'V' OR B.Apr_Pengganti = 'V' OR B.Apr_Atasan = '-'));
            ";
            $hasil = $db->prepare($sql);
            $hasil->execute(); //disini bisa dimaksukkan array sebagai referensi ke ? di sql query .. 
            $d = $hasil->fetchAll(PDO::FETCH_ASSOC);
            if ($d) {
                return $this->respond($d, 200);
            } else {
                $error = 'Data Not Available';
                return $this->respond($error, 500);
            }
        } catch (PDOException $e) {
            $error = "Connection failed: " . $e->getMessage();
            return $this->respond($error, 500);
        }
    }
    //PO by Ferry
    public function getDataTotalPO() // Ordery BY AEDAT kalau Waiting
    {

        $user = $_GET['user'];
        $db = \Config\Database::connect();
        $query = $db->query("SELECT relcode_po FROM users WHERE username = '$user'");
        $data = $query->getRow();
        $FRGZU = $data->relcode_po;
        $FRGZU = str_replace(', ', "', '", $FRGZU); // agar ada petik atas untuk release yang lebih dari 1
        $FRGZU = "'" . $FRGZU . "'";
        if (getenv("CI_ENVIRONMENT") == 'development') {
            $MANDT = '210';
        } else {
            $MANDT = '810';
        }
        if ($FRGZU) {
            $query = $db->query("SELECT COUNT(t1.EBELN) AS count_EBELN, t1.*,  SUM(B.NETWR) AS SUM_Total_Price
                FROM t_po t1
                INNER JOIN t_po_item B
                ON t1.EBELN = B.EBELN
                WHERE (t1.REJECT IS NULL OR t1.REJECT = '')
                AND (t1.FULL_RELEASE = '' OR t1.FULL_RELEASE IS NULL)
                AND (IF((IFNULL(LENGTH(t1.frgzu),0)) = 0,(SELECT FRGC1 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 1,(SELECT FRGC2 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 2,(SELECT FRGC3 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 3,(SELECT FRGC4 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 4,(SELECT FRGC5 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 5,(SELECT FRGC6 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),''))))))) IN ($FRGZU)
                GROUP BY t1.EBELN, t1.id
                ORDER BY t1.EBELN, t1.AEDAT;");

            return $this->respond(count($query->getResult()), 200);
        } else {
            $error = 'Data Not Available';
            return $this->respond($error, 500);
        }
    }
    public function getDataPO() // Ordery BY AEDAT kalau Waiting
    {

        $user = $_GET['user'];
        $db = \Config\Database::connect();
        $query = $db->query("SELECT relcode_po FROM users WHERE username = '$user'");
        $data = $query->getRow();
        $FRGZU = $data->relcode_po;
        $FRGZU = str_replace(', ', "', '", $FRGZU); // agar ada petik atas untuk release yang lebih dari 1
        $FRGZU = "'" . $FRGZU . "'";
        if (getenv("CI_ENVIRONMENT") == 'development') {
            $MANDT = '210';
        } else {
            $MANDT = '810';
        }
        if ($FRGZU) {
            // SUM((B.NETPR / B.PEINH) * B.MENGE) AS net_price
            // var_dump($FRGZU);
            $query = $db->query("SELECT COUNT(t1.EBELN) AS count_EBELN, t1.*,  SUM(B.NETWR) AS SUM_Total_Price
                FROM t_po t1
                INNER JOIN t_po_item B
                ON t1.EBELN = B.EBELN
                WHERE (t1.REJECT IS NULL OR t1.REJECT = '')
                AND (t1.FULL_RELEASE = '' OR t1.FULL_RELEASE IS NULL)
                AND (IF((IFNULL(LENGTH(t1.frgzu),0)) = 0,(SELECT FRGC1 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 1,(SELECT FRGC2 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 2,(SELECT FRGC3 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 3,(SELECT FRGC4 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 4,(SELECT FRGC5 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 5,(SELECT FRGC6 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),''))))))) IN ($FRGZU)
                GROUP BY t1.EBELN, t1.id
                ORDER BY t1.EBELN, t1.AEDAT;");
            return $this->respond($query->getResult(), 200);
        } else {
            $error = 'Data Not Available';
            return $this->respond($error, 500);
        }
    }
    public function getDataPOhistory() // Ordery BY update_AT kalau History
    {
        $user = $_GET['user'];
        $users = new Users();
        $builder = $users->builder();
        $builder->select('*');
        $builder->where("username = '$user'");
        $data = $builder->get()->getRowArray();
        $FRGZU = $data['relcode_po'];
        $FRGZU = str_replace(', ', "', '", $FRGZU); // agar ada petik atas untuk release yang lebih dari 1
        $FRGZU = "'" . $FRGZU . "'";
        $db = \Config\Database::connect();
        if (getenv("CI_ENVIRONMENT") == 'development') {
            $MANDT = '210';
        } else {
            $MANDT = '810';
        }
        if ($FRGZU) {
            // $query = $db->query("SELECT *,
            //     (SELECT FRGC1 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,1,1) IN('X','T')) AS REL_COD1,
            //     (SELECT FRGC2 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,2,1) IN('X','T')) AS REL_COD2,
            //     (SELECT FRGC3 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,3,1) IN('X','T')) AS REL_COD3,
            //     (SELECT FRGC4 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,4,1) IN('X','T')) AS REL_COD4,
            //     (SELECT FRGC5 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,5,1) IN('X','T')) AS REL_COD5,
            //     (SELECT FRGC6 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,6,1) IN('X','T')) AS REL_COD6
            //     FROM t_po WHERE(LOEKZ ='' OR LOEKZ IS NULL) AND
            //     (((SELECT FRGC1 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) = '$FRGZU' AND SUBSTR(t_po.FRGZU,1,1) IN('X','T')) OR 
            //     ((SELECT FRGC2 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) = '$FRGZU' AND SUBSTR(t_po.FRGZU,2,1) IN('X','T')) OR
            //     ((SELECT FRGC3 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) = '$FRGZU' AND SUBSTR(t_po.FRGZU,3,1) IN('X','T')) OR
            //     ((SELECT FRGC4 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) = '$FRGZU' AND SUBSTR(t_po.FRGZU,4,1) IN('X','T')) OR
            //     ((SELECT FRGC5 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) = '$FRGZU' AND SUBSTR(t_po.FRGZU,5,1) IN('X','T')) OR
            //     ((SELECT FRGC6 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) = '$FRGZU' AND SUBSTR(t_po.FRGZU,6,1) IN('X','T')))
            //     ORDER BY UPDATED_AT DESC");
            $query = $db->query("SELECT COUNT(t_po.EBELN) AS count_EBELN, SUM(B.NETWR) AS SUM_Total_Price, t_po.*,
            (SELECT FRGC1 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,1,1) IN('X','T')) AS REL_COD1,
            (SELECT FRGC2 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,2,1) IN('X','T')) AS REL_COD2,
            (SELECT FRGC3 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,3,1) IN('X','T')) AS REL_COD3,
            (SELECT FRGC4 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,4,1) IN('X','T')) AS REL_COD4,
            (SELECT FRGC5 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,5,1) IN('X','T')) AS REL_COD5,
            (SELECT FRGC6 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX AND SUBSTR(t_po.FRGZU,6,1) IN('X','T')) AS REL_COD6
            FROM t_po
            INNER JOIN t_po_item B ON t_po.EBELN = B.EBELN
            WHERE (t_po.LOEKZ ='' OR t_po.LOEKZ IS NULL) AND
            (((SELECT FRGC1 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) IN ($FRGZU) AND SUBSTR(t_po.FRGZU,1,1) IN('X','T')) OR 
            ((SELECT FRGC2 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) IN ($FRGZU) AND SUBSTR(t_po.FRGZU,2,1) IN('X','T')) OR
            ((SELECT FRGC3 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) IN ($FRGZU) AND SUBSTR(t_po.FRGZU,3,1) IN('X','T')) OR
            ((SELECT FRGC4 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) IN ($FRGZU) AND SUBSTR(t_po.FRGZU,4,1) IN('X','T')) OR
            ((SELECT FRGC5 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) IN ($FRGZU) AND SUBSTR(t_po.FRGZU,5,1) IN('X','T')) OR
            ((SELECT FRGC6 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_po.FRGSX) IN ($FRGZU) AND SUBSTR(t_po.FRGZU,6,1) IN('X','T')))
            GROUP BY t_po.EBELN, t_po.id
            ORDER BY  UPDATED_AT, t_po.EBELN DESC");
            return $this->respond($query->getResult(), 200);
        } else {
            $error = 'Data Not Available';
            return $this->respond($error, 500);
        }
    }
    public function getDetailPO()
    {
        $EBELN = $_GET['EBELN'];
        $db = \Config\Database::connect();

        $query2 = $db->query("SELECT * FROM t_po_item A WHERE A.EBELN = '$EBELN'");

        return $this->respond($query2->getResult(), 200);
    }
    public function getVendor()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT DISTINCT A.EBELN, C.NAME1 AS vendor_name, E.NAME1 AS plant_name
        FROM t_po A
        INNER JOIN t_vendor C
        ON A.LIFNR = C.LIFNR
        INNER JOIN t_po_item D
        ON A.EBELN = D.EBELN
        INNER JOIN t_plant E
        ON D.WERKS = E.WERKS");
        return $this->respond($query->getResult(), 200);
    }
    public function getReleaseCode()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM t_t16fd");
        return $this->respond($query->getResult(), 200);
    }
    public function ApprovePO()
    {
        $user = $_GET['user'];
        $EBELN = $_GET['EBELN'];
        $db = \Config\Database::connect();
        $now = Time::now();
        $formattedDateTime = $now->toLocalizedString('yyyy-MM-dd HH:mm:ss');
        $query = $db->query("UPDATE t_po SET FRGZU = case when FRGZU is null then 'T' else concat(FRGZU, 'T') end, UPDATED_BY = '$user',  UPDATED_AT = '$formattedDateTime' WHERE EBELN = '$EBELN'");
        if ($query) {
            return $this->respond(['status' => 'success', 'message' => 'PO approved successfully'], 200);
        } else {
            return $this->respond(['status' => 'error', 'message' => 'Failed to approve PO'], 500);
        }
    }

    public function RejectPO()
    {
        $user = $_GET['user'];
        $EBELN = $_GET['EBELN'];
        $db = \Config\Database::connect();
        $now = Time::now();
        $formattedDateTime = $now->toLocalizedString('yyyy-MM-dd HH:mm:ss');
        $query = $db->query("UPDATE t_po SET REJECT = 'X',  UPDATED_BY = '$user', UPDATED_AT = '$formattedDateTime'  WHERE EBELN = '$EBELN'");
        if ($query) {
            return $this->respond(['status' => 'success', 'message' => 'PO approved successfully'], 200);
        } else {
            return $this->respond(['status' => 'error', 'message' => 'Failed to approve PO'], 500);
        }
    }


    public function CancelPO()
    {
        $user = $_GET['user'];
        $EBELN = $_GET['EBELN'];
        $db = \Config\Database::connect();
        // $query = $db->query("UPDATE t_po 
        // SET FRGZU = LEFT(FRGZU, LENGTH(FRGZU) - 1) 
        // WHERE FRGZU IS NOT NULL 
        // AND LENGTH(FRGZU) > 0 
        // AND EBELN = '$EBELN' 
        // AND RIGHT(FRGZU, 1) = 'T'");
        $query = $db->query("UPDATE t_po
        SET FRGZU = CASE 
            WHEN FRGZU IS NOT NULL AND LENGTH(FRGZU) > 0 AND RIGHT(FRGZU, 1) = 'T' THEN LEFT(FRGZU, LENGTH(FRGZU) - 1)
            WHEN FRGZU IS NOT NULL AND LENGTH(FRGZU) > 0 AND RIGHT(FRGZU, 1) = 'X' THEN CONCAT(LEFT(FRGZU, LENGTH(FRGZU) - 1), 'U')
            ELSE ''
        END,
        UPDATED_BY = '$user'
        WHERE EBELN = '$EBELN'");
        if ($query) {
            return $this->respond(['status' => 'success', 'message' => 'Cancel approved successfully'], 200);
        } else {
            return $this->respond(['status' => 'error', 'message' => 'Failed to Cancel approve PO'], 500);
        }
    }
    /// PR by Ferry
    // public function getParentPR() //data dengan rumus mtk
    // {
    //     $user = $_GET['user'];
    //     $users = new Users();
    //     $builder = $users->builder();
    //     $builder->select('*');
    //     $builder->where("username = '$user'");
    //     $data = $builder->get()->getRowArray();
    //     $FRGST = $data['relcode_pr'];

    //     $db = \Config\Database::connect();
    //     $query = $db->query("SELECT t1.BANFN, SUM(t1.BNFPO) AS total_item, SUM((t1.PREIS / t1.PEINH) * t1.MENGE) AS net_price
    //     FROM t_pr t1
    //     WHERE (t1.reject IS NULL OR t1.reject = '')
    //     AND (t1.full_release = '' OR t1.full_release IS NULL)
    //     AND (IF((IFNULL(LENGTH(t1.frgzu),0)) = 0,(SELECT FRGC1 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 1,(SELECT FRGC2 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 2,(SELECT FRGC3 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 3,(SELECT FRGC4 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 4,(SELECT FRGC5 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 5,(SELECT FRGC6 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),''))))))) = '$FRGST'
    //     GROUP BY t1.BANFN
    //     ORDER BY t1.BANFN DESC;");
    //     return $this->respond($query->getResult(), 200);
    // }
    // public function getChildPR() //data keterangan tmbahan seperti header,dll
    // {
    //     $user = $_GET['user'];
    //     $users = new Users();
    //     $builder = $users->builder();
    //     $builder->select('*');
    //     $builder->where("username = '$user'");
    //     $data = $builder->get()->getRowArray();
    //     $FRGST = $data['relcode_pr'];
    //     $db = \Config\Database::connect();
    //     $query = $db->query("SELECT DISTINCT t1.BANFN, t1.HEADER, t1.BSART, t1.T161T_BATXT, t1.AFNAM, t1.client, t1.FRGZU, t1.REJECT, t1.UPDATED_BY
    //     FROM t_pr AS t1 WHERE (reject IS NULL OR reject = '')
    //     And (full_release = '' OR full_release IS NULL)
    //     AND (IF((IFNULL(LENGTH(t1.frgzu),0)) = 0,(SELECT FRGC1 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 1,(SELECT FRGC2 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 2,(SELECT FRGC3 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 3,(SELECT FRGC4 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 4,(SELECT FRGC5 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 5,(SELECT FRGC6 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),''))))))) = '$FRGST'
    //     ORDER BY t1.BANFN DESC;");
    //     return $this->respond($query->getResult(), 200);
    // }
    public function getDataTotalPR()
    {
        try {
            $user = $_GET['user'];
            $users = new Users();
            $builder = $users->builder();
            $builder->select('*');
            $builder->where("username = '$user'");
            $data = $builder->get()->getRowArray();
            $FRGST = $data['relcode_pr'];
            $FRGST = str_replace(', ', "', '", $FRGST); // agar ada petik atas untuk release yang lebih dari 1
            $FRGST = "'" . $FRGST . "'";

            $db = \Config\Database::connect();
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $MANDT = '210';
            } else {
                $MANDT = '810';
            }
            if ($FRGST) {
                $query = $db->query("SELECT DISTINCT t1.BANFN, t1.HEADER, t1.BSART, t1.T161T_BATXT, t1.client, t1.FRGZU, t1.REJECT, t1.UPDATED_BY,
                (SELECT A.AFNAM FROM t_pr A WHERE A.BANFN = t1.BANFN LIMIT 1) AS AFNAM,
                (SELECT A.BADAT FROM t_pr A WHERE A.BANFN = t1.BANFN LIMIT 1) AS BADAT,
                (SELECT A.LFDAT FROM t_pr A WHERE A.BANFN = t1.BANFN LIMIT 1) AS LFDAT,
                (SELECT SUM(A.BNFPO) FROM t_pr A WHERE A.BANFN = t1.BANFN) AS total_item,
                -- (SELECT SUM((A.PREIS / A.PEINH) * A.MENGE) FROM t_pr A WHERE A.BANFN = t1.BANFN) AS net_price
                (SELECT SUM(A.RLWRT) FROM t_pr A WHERE A.BANFN = t1.BANFN) AS SUM_Total_Price
                FROM t_pr AS t1
                WHERE (reject IS NULL OR reject = '')
                And (full_release = '' OR full_release IS NULL)
                AND (IF((IFNULL(LENGTH(t1.frgzu),0)) = 0,(SELECT FRGC1 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 1,(SELECT FRGC2 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 2,(SELECT FRGC3 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 3,(SELECT FRGC4 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 4,(SELECT FRGC5 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 5,(SELECT FRGC6 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),''))))))) IN ($FRGST)
                ORDER BY BANFN, BADAT DESC;");

                return $this->respond(count($query->getResult()), 200);
            } else {
                $error = 'Data Not Available';
                return $this->respond($error, 500);
            }
        } catch (\Throwable $error) {
            return $this->respond($error, 500);
        }
    }
    public function getDataPR()
    {
        try {
            $user = $_GET['user'];
            $users = new Users();
            $builder = $users->builder();
            $builder->select('*');
            $builder->where("username = '$user'");
            $data = $builder->get()->getRowArray();
            $FRGST = $data['relcode_pr'];
            $FRGST = str_replace(', ', "', '", $FRGST); // agar ada petik atas untuk release yang lebih dari 1
            $FRGST = "'" . $FRGST . "'";

            $db = \Config\Database::connect();
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $MANDT = '210';
            } else {
                $MANDT = '810';
            }
            if ($FRGST) {
                $query = $db->query("SELECT DISTINCT t1.BANFN, t1.HEADER, t1.BSART, t1.T161T_BATXT, t1.client, t1.FRGZU, t1.REJECT, t1.UPDATED_BY,
                (SELECT A.AFNAM FROM t_pr A WHERE A.BANFN = t1.BANFN LIMIT 1) AS AFNAM,
                (SELECT A.BADAT FROM t_pr A WHERE A.BANFN = t1.BANFN LIMIT 1) AS BADAT,
                (SELECT A.LFDAT FROM t_pr A WHERE A.BANFN = t1.BANFN LIMIT 1) AS LFDAT,
                (SELECT SUM(A.BNFPO) FROM t_pr A WHERE A.BANFN = t1.BANFN) AS total_item,
                -- (SELECT SUM((A.PREIS / A.PEINH) * A.MENGE) FROM t_pr A WHERE A.BANFN = t1.BANFN) AS net_price
                (SELECT SUM(A.RLWRT) FROM t_pr A WHERE A.BANFN = t1.BANFN) AS SUM_Total_Price
                FROM t_pr AS t1
                WHERE (reject IS NULL OR reject = '')
                And (full_release = '' OR full_release IS NULL)
                AND (IF((IFNULL(LENGTH(t1.frgzu),0)) = 0,(SELECT FRGC1 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 1,(SELECT FRGC2 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 2,(SELECT FRGC3 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 3,(SELECT FRGC4 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 4,(SELECT FRGC5 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 5,(SELECT FRGC6 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),''))))))) IN ($FRGST)
                ORDER BY BANFN, BADAT DESC;");
                return $this->respond($query->getResult(), 200);
            } else {
                $error = 'Data Not Available';
                return $this->respond($error, 500);
            }
        } catch (\Throwable $error) {
            return $this->respond($error, 500);
        }
    }
    public function getDetailPR()
    {
        $BANFN = $_GET['BANFN'];
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM t_pr AS t1 WHERE t1.BANFN  = '$BANFN';");
        return $this->respond($query->getResult(), 200);
    }
    public function getPRhistory()
    {
        $user = $_GET['user'];
        $users = new Users();
        $builder = $users->builder();
        $builder->select('*');
        $builder->where("username = '$user'");
        $data = $builder->get()->getRowArray();
        $FRGST = $data['relcode_pr'];
        $FRGST = str_replace(', ', "', '", $FRGST); // agar ada petik atas untuk release yang lebih dari 1
        $FRGST = "'" . $FRGST . "'";
        $db = \Config\Database::connect();
        if (getenv("CI_ENVIRONMENT") == 'development') {
            $MANDT = '210';
        } else {
            $MANDT = '810';
        }
        if ($FRGST) {
            $query = $db->query("SELECT DISTINCT t_pr.BANFN, t_pr.HEADER, t_pr.BSART, t_pr.T161T_BATXT, t_pr.UPDATED_AT,
            (SELECT A.AFNAM FROM t_pr A WHERE A.BANFN = t_pr.BANFN LIMIT 1) AS AFNAM,
            (SELECT A.BADAT FROM t_pr A WHERE A.BANFN = t_pr.BANFN LIMIT 1) AS BADAT,
            (SELECT A.LFDAT FROM t_pr A WHERE A.BANFN = t_pr.BANFN LIMIT 1) AS LFDAT,
            t_pr.client, t_pr.FRGZU, t_pr.REJECT, t_pr.UPDATED_BY,
            (SELECT SUM(A.BNFPO) FROM t_pr A WHERE A.BANFN = t_pr.BANFN) AS total_item,
            -- (SELECT SUM((A.PREIS / A.PEINH) * A.MENGE) FROM t_pr A WHERE A.BANFN = t_pr.BANFN) AS net_price,
            (SELECT SUM(A.RLWRT) FROM t_pr A WHERE A.BANFN = t_pr.BANFN) AS SUM_Total_Price,
            (SELECT FRGC1 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,1,1) IN('X','T')) AS REL_COD1,
            (SELECT FRGC2 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,2,1) IN('X','T')) AS REL_COD2,
            (SELECT FRGC3 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,3,1) IN('X','T')) AS REL_COD3,
            (SELECT FRGC4 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,4,1) IN('X','T')) AS REL_COD4,
            (SELECT FRGC5 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,5,1) IN('X','T')) AS REL_COD5,
            (SELECT FRGC6 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,6,1) IN('X','T')) AS REL_COD6
            FROM t_pr WHERE(LOEKZ ='' OR LOEKZ IS NULL) AND
            (((SELECT FRGC1 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) IN ($FRGST) AND SUBSTR(t_pr.FRGZU,1,1) IN('X','T')) OR 
            ((SELECT FRGC2 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) IN ($FRGST) AND SUBSTR(t_pr.FRGZU,2,1) IN('X','T')) OR
            ((SELECT FRGC3 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) IN ($FRGST) AND SUBSTR(t_pr.FRGZU,3,1) IN('X','T')) OR
            ((SELECT FRGC4 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) IN ($FRGST) AND SUBSTR(t_pr.FRGZU,4,1) IN('X','T')) OR
            ((SELECT FRGC5 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) IN ($FRGST) AND SUBSTR(t_pr.FRGZU,5,1) IN('X','T')) OR
            ((SELECT FRGC6 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) IN ($FRGST) AND SUBSTR(t_pr.FRGZU,6,1) IN('X','T')))
            ORDER BY t_pr.UPDATED_AT, BADAT DESC;");
            return $this->respond($query->getResult(), 200);
        } else {
            $error = 'Data Not Available';
            return $this->respond($error, 500);
        }
    }
    public function getPlant()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT DISTINCT t1.BANFN, t2.NAME1 AS plant_name
        FROM t_pr t1
        INNER JOIN t_plant t2
        ON t1.WERKS = t2.WERKS");
        return $this->respond($query->getResult(), 200);
    }

    public function ApprovePR()
    {
        $user = $_GET['user'];
        $BANFN = $_GET['BANFN'];
        $db = \Config\Database::connect();
        $now = Time::now();
        $formattedDateTime = $now->toLocalizedString('yyyy-MM-dd HH:mm:ss');
        $query = $db->query("UPDATE t_pr SET FRGZU = case when FRGZU is null then 'T' else concat(FRGZU, 'T') end, UPDATED_BY = '$user', UPDATED_AT = '$formattedDateTime' WHERE BANFN = '$BANFN'");
        if ($query) {
            return $this->respond(['status' => 'success', 'message' => 'PR approved successfully'], 200);
        } else {
            return $this->respond(['status' => 'error', 'message' => 'Failed to approve PR'], 500);
        }
    }

    public function RejectPR()
    {
        $user = $_GET['user'];
        $BANFN = $_GET['BANFN'];
        $db = \Config\Database::connect();
        $now = Time::now();
        $formattedDateTime = $now->toLocalizedString('yyyy-MM-dd HH:mm:ss');
        $query = $db->query("UPDATE t_pr SET REJECT = 'X',  UPDATED_BY = '$user', UPDATED_AT = '$formattedDateTime'  WHERE BANFN = '$BANFN'");
        if ($query) {
            return $this->respond(['status' => 'success', 'message' => 'PR approved successfully'], 200);
        } else {
            return $this->respond(['status' => 'error', 'message' => 'Failed to approve PR'], 500);
        }
    }
    public function CancelPR()
    {
        $user = $_GET['user'];
        $BANFN = $_GET['BANFN'];
        $db = \Config\Database::connect();
        $query = $db->query("UPDATE t_pr
        SET FRGZU = CASE 
            WHEN FRGZU IS NOT NULL AND LENGTH(FRGZU) > 0 AND RIGHT(FRGZU, 1) = 'T' THEN LEFT(FRGZU, LENGTH(FRGZU) - 1)
            WHEN FRGZU IS NOT NULL AND LENGTH(FRGZU) > 0 AND RIGHT(FRGZU, 1) = 'X' THEN CONCAT(LEFT(FRGZU, LENGTH(FRGZU) - 1), 'U')
            ELSE ''
        END,
        UPDATED_BY = '$user'
        WHERE BANFN = '$BANFN'");
        if ($query) {
            return $this->respond(['status' => 'success', 'message' => 'Cancel approved successfully'], 200);
        } else {
            return $this->respond(['status' => 'error', 'message' => 'Failed to Cancel approve PO'], 500);
        }
    }

    // public function getParentPRhistory()
    // {
    //     $user = $_GET['user'];
    //     $users = new Users();
    //     $builder = $users->builder();
    //     $builder->select('*');
    //     $builder->where("username = '$user'");
    //     $data = $builder->get()->getRowArray();
    //     $FRGST = $data['relcode_pr'];
    //     $db = \Config\Database::connect();
    //     $query = $db->query("SELECT t1.BANFN, SUM(t1.BNFPO) AS total_item, SUM((t1.PREIS / t1.PEINH) * t1.MENGE) AS net_price
    //     FROM t_pr t1
    //     WHERE (t1.reject = 'X')
    //     OR (t1.FRGZU IS NOT NULL OR t1.FRGZU != '' OR t1.FRGZU NOT LIKE '%U%')
    //     AND (t1.full_release = '' OR t1.full_release IS NULL)
    //     AND (IF((IFNULL(LENGTH(t1.frgzu),0)) = 1,(SELECT FRGC1 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 2,(SELECT FRGC2 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 3,(SELECT FRGC3 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 4,(SELECT FRGC4 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 5,(SELECT FRGC5 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
    //     IF((IFNULL(LENGTH(t1.frgzu),0)) = 6,(SELECT FRGC6 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),''))))))) = '$FRGST'
    //     GROUP BY t1.BANFN
    //     ORDER BY t1.BANFN;");
    //     return $this->respond($query->getResult(), 200);
    // }
    // public function getChildPRhistory()
    // {
    //     $user = $_GET['user'];
    //     $users = new Users();
    //     $builder = $users->builder();
    //     $builder->select('*');
    //     $builder->where("username = '$user'");
    //     $data = $builder->get()->getRowArray();
    //     $FRGST = $data['relcode_pr'];
    //     $db = \Config\Database::connect();
    //     $query = $db->query("SELECT DISTINCT t_pr.BANFN, t_pr.HEADER, t_pr.BSART, t_pr.T161T_BATXT, t_pr.AFNAM, t_pr.client, t_pr.FRGZU, t_pr.REJECT, t_pr.UPDATED_BY,
    //     (SELECT FRGC1 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,1,1) IN('X','T')) AS REL_COD1,
    //     (SELECT FRGC2 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,2,1) IN('X','T')) AS REL_COD2,
    //     (SELECT FRGC3 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,3,1) IN('X','T')) AS REL_COD3,
    //     (SELECT FRGC4 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,4,1) IN('X','T')) AS REL_COD4,
    //     (SELECT FRGC5 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,5,1) IN('X','T')) AS REL_COD5,
    //     (SELECT FRGC6 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST AND SUBSTR(t_pr.FRGZU,6,1) IN('X','T')) AS REL_COD6
    //     FROM t_pr WHERE(LOEKZ ='' OR LOEKZ IS NULL) AND
    //     (((SELECT FRGC1 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) = '$FRGST' AND SUBSTR(t_pr.FRGZU,1,1) IN('X','T')) OR 
    //     ((SELECT FRGC2 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) = '$FRGST' AND SUBSTR(t_pr.FRGZU,2,1) IN('X','T')) OR
    //     ((SELECT FRGC3 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) = '$FRGST' AND SUBSTR(t_pr.FRGZU,3,1) IN('X','T')) OR
    //     ((SELECT FRGC4 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) = '$FRGST' AND SUBSTR(t_pr.FRGZU,4,1) IN('X','T')) OR
    //     ((SELECT FRGC5 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) = '$FRGST' AND SUBSTR(t_pr.FRGZU,5,1) IN('X','T')) OR
    //     ((SELECT FRGC6 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t_pr.FRGST) = '$FRGST' AND SUBSTR(t_pr.FRGZU,6,1) IN('X','T')))
    //     ORDER BY BANFN DESC;");
    //     return $this->respond($query->getResult(), 200);
    // }
    public function SalesOrderTotal()
    {
        $count_po = [];
        $count_pr = [];
        $count_cuti = [];
        $count_sales = [];
        $user = $_GET['user'];
        $db = \Config\Database::connect();

        $levelCheck = $db->query("SELECT level
            FROM T_SAL_APPROVAL_STEP A
            JOIN users B ON A.user_name = B.username 
            WHERE A.user_name = '$user'");
        $level = $levelCheck->getRowArray();

        // var_dump($level['level']);
        if ($level) {
            if ($level !== null && ($level['level'] == 1 || $level['level'] == 0)) {
                $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 4;");
                $result_sales = $query_sales->getResult();
            } else if ($level !== null && ($level['level'] == 2 || $level['level'] == 0)) {
                $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 5;");
                $result_sales = $query_sales->getResult();
            } else if ($level !== null && ($level['level'] == 3 || $level['level'] == 0)) {
                $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 6;");
                $result_sales = $query_sales->getResult();
            } else if ($level !== null && ($level['level'] == 4 || $level['level'] == 0)) {
                $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 7;");
                $result_sales = $query_sales->getResult();
            } else if ($level !== null && ($level['level'] == 5 || $level['level'] == 0)) {
                $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 8;");
                $result_sales = $query_sales->getResult();
            } else if ($level === null) {
                $query_sales = [];
                $result_sales = [];
            }
            $count_sales = count($result_sales);
            return $this->respond($count_sales, 200);
            // return $this->respond( 200);
        }
    }


    public function notification_mobile()
    {
        $count_po = [];
        $count_pr = [];
        $count_cuti = [];
        $count_sales = [];
        $db = \Config\Database::connect();
        // $user = $_GET['user'];
        $users = new Users();
        $builder = $users->builder();
        $builder->select('*');
        $builder->where("id_mobile IS NOT null");
        $builder->where("id_mobile != ''");
        $builder->where("id_mobile != 'undefined'");
        $data = $builder->get()->getResultArray();

        $data_player = array();
        foreach ($data as $row) {
            $data_player[] = array(
                'relcode_po' => $row['relcode_po'],
                'relcode_pr' => $row['relcode_pr'],
                'id_mobile' => $row['id_mobile'],
                'fullname' => $row['fullname'],
                'email' => $row['email'],
                'username' => $row['username']
            );
        }

        foreach ($data_player as $value) { // Check Data PO
            $relcode_po = $value['relcode_po'];
            if (!is_null($relcode_po)) {
                $relcode_po = str_replace(', ', "', '", $relcode_po); // agar ada petik atas untuk release yang lebih dari 1
            }
            $relcode_po = "'" . $relcode_po . "'";
            $id_mobile = $value['id_mobile'];
            $fullname =  $value['fullname'];
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $MANDT = '210';
            } else {
                $MANDT = '810';
            }
            if ($relcode_po) {
                $query = $db->query("SELECT COUNT(t1.EBELN) AS count_EBELN, t1.*, SUM((B.NETPR / B.PEINH) * B.MENGE) AS net_price
                            FROM t_po t1
                            INNER JOIN t_po_item B
                            ON t1.EBELN = B.EBELN
                            WHERE (t1.REJECT IS NULL OR t1.REJECT = '')
                            AND (t1.FULL_RELEASE = '' OR t1.FULL_RELEASE IS NULL)
                            AND (IF((IFNULL(LENGTH(t1.frgzu),0)) = 0,(SELECT FRGC1 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                            IF((IFNULL(LENGTH(t1.frgzu),0)) = 1,(SELECT FRGC2 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                            IF((IFNULL(LENGTH(t1.frgzu),0)) = 2,(SELECT FRGC3 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                            IF((IFNULL(LENGTH(t1.frgzu),0)) = 3,(SELECT FRGC4 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                            IF((IFNULL(LENGTH(t1.frgzu),0)) = 4,(SELECT FRGC5 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),
                            IF((IFNULL(LENGTH(t1.frgzu),0)) = 5,(SELECT FRGC6 FROM t_t16fs WHERE frggr = 'OH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGSX),''))))))) IN ($relcode_po)
                            GROUP BY t1.EBELN, t1.id
                            ORDER BY t1.EBELN;");
                $result = $query->getResult();
                $count_po = count($result);
                echo "'<br>' . $count_po. 'Notif PO success'";
                $words = explode(' ', $fullname); // split full name into an array of words
                $first_name = implode(' ', array_slice($words, 0, 2)); // select first two words and join them back into a string
                if ($count_po) {
                    $dataku = array(
                        'app_id' => '14db1899-481c-41df-b9e7-de61c1c65eba',
                        'include_player_ids' => array($id_mobile),
                        'contents' => array('en' => 'Halo ' . $first_name . ', you have ' . $count_po . ' request PO'),
                        'headings' => array('en' => 'Request PO '),
                        'data' => array('custom-data' => 'data1')
                    );

                    $fields = json_encode($dataku);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic YOUR_REST_API_KEY_HERE'
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    echo $response;
                }
            }
        }


        foreach ($data_player as $value) { // Check Data PR
            $relcode_pr = $value['relcode_pr'];
            if (!is_null($relcode_pr)) {
                $relcode_pr = str_replace(', ', "', '", $relcode_pr); // agar ada petik atas untuk release yang lebih dari 1
            }
            $relcode_pr = "'" . $relcode_pr . "'";
            $id_mobile = $value['id_mobile'];
            $fullname =  $value['fullname'];
            if (getenv("CI_ENVIRONMENT") == 'development') {
                $MANDT = '210';
            } else {
                $MANDT = '810';
            }
            if ($relcode_pr) {
                $query_pr = $db->query("SELECT DISTINCT t1.BANFN, t1.HEADER, t1.BSART, t1.T161T_BATXT, t1.client, t1.FRGZU, t1.REJECT, t1.UPDATED_BY,
                (SELECT A.AFNAM FROM t_pr A WHERE A.BANFN = t1.BANFN LIMIT 1) AS AFNAM,
                (SELECT A.BADAT FROM t_pr A WHERE A.BANFN = t1.BANFN LIMIT 1) AS BADAT,
                (SELECT A.LFDAT FROM t_pr A WHERE A.BANFN = t1.BANFN LIMIT 1) AS LFDAT,
                (SELECT SUM(A.BNFPO) FROM t_pr A WHERE A.BANFN = t1.BANFN) AS total_item,
                (SELECT SUM((A.PREIS / A.PEINH) * A.MENGE) FROM t_pr A WHERE A.BANFN = t1.BANFN) AS net_price
                FROM t_pr AS t1
                WHERE (reject IS NULL OR reject = '')
                And (full_release = '' OR full_release IS NULL)
                AND (IF((IFNULL(LENGTH(t1.frgzu),0)) = 0,(SELECT FRGC1 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 1,(SELECT FRGC2 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 2,(SELECT FRGC3 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 3,(SELECT FRGC4 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 4,(SELECT FRGC5 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),
                IF((IFNULL(LENGTH(t1.frgzu),0)) = 5,(SELECT FRGC6 FROM t_t16fs WHERE frggr = 'RH' AND MANDT = '$MANDT' AND FRGSX = t1.FRGST),''))))))) IN ($relcode_pr)
                ORDER BY BADAT DESC;");
                $result_pr = $query_pr->getResult();
                $count_pr = count($result_pr);
                echo "'<br>' . $count_pr. 'Notif PR success'";
                $words = explode(' ', $fullname); // split full name into an array of words
                $first_name = implode(' ', array_slice($words, 0, 2)); // select first two words and join them back into a string
                if ($count_pr) {
                    $dataku = array(
                        'app_id' => '14db1899-481c-41df-b9e7-de61c1c65eba',
                        'include_player_ids' => array($id_mobile),
                        'contents' => array('en' => 'Halo ' . $first_name . ', you have ' . $count_pr . ' request PR'),
                        'headings' => array('en' => 'Request PR '),
                        'data' => array('custom-data' => 'data1')
                    );

                    $fields = json_encode($dataku);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic YOUR_REST_API_KEY_HERE'
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    echo $response;
                }
            }
        }

        //Check Data Cuti
        if (getenv("CI_ENVIRONMENT") == 'development') {
            $db_ESS = new PDO('mysql:host=192.168.14.28;dbname=ess_dev', 'razam', '');
        } else {
            $db_ESS = new PDO('mysql:host=192.168.14.28;dbname=dbmdk', 'razam', '');
        }
        $db_ESS->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        foreach ($data_player as $value) { // Check Data PR
            $email = $value['email'];
            $id_mobile = $value['id_mobile'];
            $fullname =  $value['fullname'];
            if ($email) {
                $sql_cuti = "SELECT B.*, C.Nama AS nama_user, D.Nama AS nama_pengganti, E.Nama AS nama_atasan, F.Nama AS nama_direktur
                FROM tkaryawan A
                LEFT JOIN tcuti B ON
                ((B.NRP_Pengganti = A.NRP)
                OR (B.NRP_Atasan = A.NRP)
                OR (B.NRP_Direktur = A.NRP))
                INNER JOIN tkaryawan C ON B.NRP_User = C.NRP
                INNER JOIN tkaryawan D ON B.NRP_Pengganti = D.NRP
                INNER JOIN tkaryawan E ON B.NRP_Atasan = E.NRP
                INNER JOIN tkaryawan F ON B.NRP_Direktur = F.NRP
                WHERE A.Email = '$email'
                AND (B.Apr_Direktur = '' AND B.Apr_Pengganti = '' AND B.Apr_Atasan = '')";
                $hasil = $db_ESS->prepare($sql_cuti);
                $hasil->execute();
                $result = $hasil->fetchAll(PDO::FETCH_ASSOC);
                $count_cuti = count($result);
                echo "'<br>' . $count_cuti. 'Notif Cuti success'";
                $words = explode(' ', $fullname); // split full name into an array of words
                $first_name = implode(' ', array_slice($words, 0, 2)); // select first two words and join them back into a string
                if ($count_cuti) {
                    $dataku = array(
                        'app_id' => '14db1899-481c-41df-b9e7-de61c1c65eba',
                        'include_player_ids' => array($id_mobile),
                        'contents' => array('en' => 'Halo ' . $first_name . ', you have ' . $count_cuti . ' request cuti'),
                        'headings' => array('en' => 'Request Cuti'),
                        'data' => array('custom-data' => 'data1')
                    );

                    $fields = json_encode($dataku);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic YOUR_REST_API_KEY_HERE'
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    echo $response;
                }
            }
        }

        foreach ($data_player as $value) { // Check Data Sales Order

            $username = $value['username'];
            $id_mobile = $value['id_mobile'];
            $fullname =  $value['fullname'];

            $levelCheck = $db->query("SELECT level
            FROM T_SAL_APPROVAL_STEP A
            JOIN users B ON A.user_name = B.username 
            WHERE A.user_name = '$username'");
            $level = $levelCheck->getRowArray();

            // var_dump($level['level']);
            if ($level) {
                if ($level !== null && ($level['level'] == 1 || $level['level'] == 0)) {
                    $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 4;");
                    $result_sales = $query_sales->getResult();
                } else if ($level !== null && ($level['level'] == 2 || $level['level'] == 0)) {
                    $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 5;");
                    $result_sales = $query_sales->getResult();
                } else if ($level !== null && ($level['level'] == 3 || $level['level'] == 0)) {
                    $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 6;");
                    $result_sales = $query_sales->getResult();
                } else if ($level !== null && ($level['level'] == 4 || $level['level'] == 0)) {
                    $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 7;");
                    $result_sales = $query_sales->getResult();
                } else if ($level !== null && ($level['level'] == 5 || $level['level'] == 0)) {
                    $query_sales = $db->query("select * from T_SAL_CONTRACT_ORDER A WHERE A.status = 8;");
                    $result_sales = $query_sales->getResult();
                } else if ($level === null) {
                    $query_sales = [];
                    $result_sales = [];
                }

                $count_sales = count($result_sales);
                echo "'<br>' . $count_sales. 'Notif Sales Order success'";

                $words = explode(' ', $fullname); // split full name into an array of words
                $first_name = implode(' ', array_slice($words, 0, 2)); // select first two words and join them back into a string
                if ($count_sales) {
                    $dataku = array(
                        'app_id' => '14db1899-481c-41df-b9e7-de61c1c65eba',
                        'include_player_ids' => array($id_mobile),
                        'contents' => array('en' => 'Halo ' . $first_name . ', you have ' . $count_sales . ' sales order'),
                        'headings' => array('en' => 'Sales order '),
                        'data' => array('custom-data' => 'data1')
                    );
                    $fields = json_encode($dataku);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic YOUR_REST_API_KEY_HERE'
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    echo $response;
                }
            }
        }

        foreach ($data_player as $value) {
            $username = $value['username'];
            $id_mobile = $value['id_mobile'];
            $fullname =  $value['fullname'];
            $email =  $value['email'];
            // echo " $email";
            if (!is_null($email)) {
                $email = str_replace(', ', "', '", $email);
            }
            $email = "'" . $email . "'";
            if ($email) {
                $query_doc = $db->query("SELECT code FROM doc_reminder
                    INNER JOIN group_emails ON group_emails.group_id = doc_reminder.group_email_id
                    INNER JOIN users ON users.email = group_emails.email
                    WHERE doc_reminder.deletion_status = '0' AND doc_reminder.remind_on = DATE(NOW())
                    AND group_emails.email = $email ");
                $document_Reminder = $query_doc->getResult();
                $count_reminder = count($document_Reminder);

                // // Menampilkan query yang dijalankan
                // echo "Query: " . $db->getLastQuery() . "<br>";

                // // Menampilkan nilai email
                // echo "Email: " . $email . "<br>";

                // // Menampilkan nilai document_Reminder
                // var_dump($document_Reminder);
                if ($count_reminder) {
                    echo "'<br>' . $count_reminder. 'document Reminder success'";
                    $dataku = array(
                        'app_id' => '14db1899-481c-41df-b9e7-de61c1c65eba',
                        'include_player_ids' => array($id_mobile),
                        'contents' => array('en' => 'Halo ' . $username . ', You Have ' . $count_reminder . ' document'),
                        'headings' => array('en' => 'Document Reminder '),
                        'data' => array('custom-data' => 'data1')
                    );
                    $fields = json_encode($dataku);
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://onesignal.com/api/v1/notifications');
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Authorization: Basic YOUR_REST_API_KEY_HERE'
                    ));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HEADER, false);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    $response = curl_exec($ch);
                    curl_close($ch);

                    echo $response;
                }
            }
        }
    }
}
