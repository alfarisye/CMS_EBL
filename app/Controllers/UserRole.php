<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\UserRoles;
use App\Models\Users;
use App\Models\Roles;
use CodeIgniter\I18n\Time;
use PhpParser\Node\Stmt\TryCatch;

class UserRole extends BaseController
{
    public function index()
    {
        $data['title'] = "Manage User Role";
        $UserRole = new UserRoles();
        $builder = $UserRole->builder();
        $builder->select('user_roles.*, users.username, roles.name AS role_name');
        $builder->join('users', 'users.id = user_roles.user_id');
        $builder->join('roles', 'roles.id = user_roles.role_id');
        $data['userRoles'] = $builder->get()->getResultArray();
        $Users = new Users();
        $data['users'] = $Users->findAll();
        $Roles = new Roles();
        $data['roles'] = $Roles->findAll();
        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/manage-user-role', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function delete($id)
    {
        $model = new UserRoles();
        $model->delete($id);
        return redirect()->to("/admin/user-role")->with('message', 'A user role has been deleted');
    }

    public function add()
    {
        $user_id = $this->request->getVar('user_id');
        $role_id = $this->request->getVar('role_id');
        $status = $this->request->getVar('status') == 'on' ? 1 : 0;
        $message = "";
        try {
            $model = new UserRoles();
            $model->save([
                'user_id' => $user_id,
                'role_id' => $role_id,
                'status' => $status,
                'created_at' => new Time('now'),
            ]);
            $message = 'A user - role has been created';
        } catch (\Throwable $th) {
            //throw $th;
            $message = "Cannot create new user role, only 1 role per user. no duplicate entry";
        }
        return redirect()->to("/admin/user-role")->with('message', $message);
        
    }

    public function get($id)
    {
        header('Content-Type: application/json');
        $userRole = new UserRoles();
        $usr = $userRole->find($id);
        return $this->response->setJSON($usr);
    }

    public function update()
    {
        $userRole = new UserRoles();
        $data = $userRole->find($this->request->getPost('id'));
        $data['user_id'] = $this->request->getPost('user_id');
        $data['role_id'] = $this->request->getPost('role_id');
        $data['status'] = $this->request->getPost('status') ? 1 : 0;
        $data['updated_at'] = Time::now();
        $userRole->save($data);
        return redirect()->to("/admin/user-role")->with('message', "User role berhasil diupdate");
    }
}
