<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use PhpParser\Node\Stmt\TryCatch;

use App\Models\Users;
use App\Models\UserRoles;
use CodeIgniter\I18n\Time;

class User extends BaseController
{
    public function index()
    {
        $data['title'] = "Manage Users";
        $Users = new Users();
        $data['users'] = $Users->findAll();

        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/manage', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function get($id)
    {
        header('Content-Type: application/json');
        $user = new Users();
        $user = $user->find($id);
        return $this->response->setJSON($user);
    }

    public function update()
    {
        $user = new Users();
        $userData = $user->find($this->request->getPost('id'));
        $password = $this->request->getPost('password');
        $userData['username'] = $this->request->getPost('username');
        $userData['fullname'] = $this->request->getPost('fullname');
        $userData['email'] = $this->request->getPost('email');
        $userData['status'] = $this->request->getPost('status') ? 1 : 0;
        $userData['internal'] = $this->request->getPost('internal') ? 1 : 0;
        $userData['password'] = password_hash($password, PASSWORD_DEFAULT);

        $userData['updated_at'] = Time::now();
        $user->save($userData);
        return redirect()->to("/admin/user")->with('message', "User berhasil diupdate");
    }

    public function login()
    {
        $data['title'] = "Login Page";

        echo view('templates/header', $data);
        echo view('pages/login', $data);
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function signIn()
    {
        try {
            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

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
                    $session = session();
                    $session->set('isLoggedIn', true);
                    $session->set('fullname', $user['fullname']);
                    $session->set('username', $username);
                    $session->set('roles', $role);
                    $session->set('contractor_id', $contractor_id);
                    $session->set('profile_image', $user['profile_image']);
                    $session->set('contractor_id', $contractor_id);
                    if ($str_role != '') {
                        $session->set('access', "~" . $str_role . "~");
                    } else {
                        $session->set('access', $str_role);
                    }
                    return redirect()->to('/');
                } else {
                    return redirect()->to("/login")->with('error', "Username/Password salah");
                }
            }


            if (getenv("CI_ENVIRONMENT") == 'development') {
                // assign user role
                // TODO: ASSIGN ARRAY TIAP ROLES


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
                $session = session();
                $session->set('isLoggedIn', true);
                $session->set('fullname', $user['fullname']);
                $session->set('username', $username);
                $session->set('roles', $role);
                $session->set('contractor_id', $contractor_id);
                $session->set('profile_image', $user['profile_image']);
                $session->set('contractor_id', $contractor_id);
                if ($str_role != '') {
                    $session->set('access', "~" . $str_role . "~");
                } else {
                    $session->set('access', $str_role);
                }
                return redirect()->to('/');
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
                    'last_login' => new Time('now'),
                ]);


                $filter = "(sAMAccountName=$username)";
                $result = ldap_search($ldap, "dc=hasnurgroup,dc=local", $filter);
                $info = ldap_get_entries($ldap, $result);
                $session = session();
                $session->set('isLoggedIn', true);
                $session->set('fullname', $info[0]["cn"][0]);
                $session->set('username', $username);
                $session->set('roles', $role);
                $session->set('profile_image', $user['profile_image']);
                // $session->set('contractor_id', $contractor_id['contractor_id']);
                if ($str_role != '') {
                    $session->set('access', "~" . $str_role . "~");
                } else {
                    $session->set('access', $str_role);
                }

                log_message('info', 'User {id} successfully logged in', $info);

                return redirect()->to('/');
            }
        } catch (\Throwable $th) {
            if ($th->getCode() == 1) {
                $message = $th->getMessage();
            } else {
                $message = ldap_error($ldap ?? "Something gone wrong");
            }
            return redirect()->to("/login")->with('error', $message);
        }
        ldap_close($ldap);
    }

    public function add()
    {
        $username = $this->request->getVar('username');
        $fullname = $this->request->getVar('fullname');
        $email = $this->request->getVar('email');
        $status = $this->request->getVar('status') == 'on' ? 1 : 0;
        $rolename = $this->request->getVar('rolename');

        $internal = $this->request->getVar('internal') == 'on' ? 1 : 0;
        $password = password_hash($this->request->getVar('password'), PASSWORD_DEFAULT);

        $model = new Users();
        $model->save([
            'username' => $username,
            'fullname' => $fullname,
            'email' => $email,
            'status' => $status,
            'rolename' => $rolename,
            'password' => $password,
            'internal' => $internal,
            'created_at' => new Time('now'),
        ]);
        return redirect()->to("/admin/user")->with('message', 'A user has been created');
    }

    public function delete($id)
    {
        $model = new Users();
        $model->delete($id);
        return redirect()->to("/admin/user")->with('message', 'A user has been deleted');
    }

    public function logout()
    {
        $session = session();
        $session->destroy();
        return redirect()->to('/login');
    }

    public function get_user_data()
    {
        $data['title'] = "Me";
        echo view('pages/me/index', $data);
    }

    public function upload_image()
    {
        $validationRule = [
            'user_profile' => [
                'rules' => 'uploaded[user_profile]|is_image[user_profile]'
            ],
        ];
        if (!$this->validate($validationRule)) {
            return redirect()->to("/me")->with('message', implode(";", $this->validator->getErrors()));
        }
        $doc_file = $this->request->getFile('user_profile');
        $username = $this->request->getVar('username');
        $original_filename = $doc_file->getName();
        $original_ext = $doc_file->getExtension();
        $path = FCPATH . 'assets/users/' . $username . $original_filename;
        $doc_file->move(FCPATH . 'assets/users', $username . $original_filename);
        if ($doc_file->isValid() && !$doc_file->hasMoved()) {
            $doc_file->move($path);
        }
        $path = 'assets/users/' . $username . $original_filename;

        try {
            $userModel = new Users();
            $builder = $userModel->builder();
            $builder->where('username', $username);
            $builder->update(
                [
                    "profile_image" => $path
                ]
            );
            $message = "Image successfully added";
            session()->set('profile_image', $path);
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/me")->with('message', $message);
    }
}
