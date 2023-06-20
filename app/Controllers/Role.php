<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Roles;
use CodeIgniter\I18n\Time;

class Role extends BaseController
{
    public function index()
    {
        $routes_raw = (\Config\Services::routes()->getRoutes());
        unset($routes_raw['logout']);
        unset($routes_raw['login']);
        unset($routes_raw['/']);
        $routes = array_keys($routes_raw);
        $filter = array_map(function($arr) {
            $segments = explode('/', $arr);
            $segment_1 = $segments[0];
            return $segment_1;
        }, $routes);
        $unique_routes = array_unique($filter);
        array_push($unique_routes, "timesheet");
        $data['title'] = "Manage Role";
        $Roles = new Roles();
        $data['roles'] = $Roles->findAll();
        $data['routes'] = $unique_routes;
        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/manage-role', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }

    public function get($id)
    {
        header('Content-Type: application/json');
        $routes_raw = (\Config\Services::routes()->getRoutes());
        unset($routes_raw['logout']);
        unset($routes_raw['login']);
        unset($routes_raw['/']);
        $routes = array_keys($routes_raw);
        $filter = array_map(function($arr) {
            $segments = explode('/', $arr);
            $segment_1 = $segments[0];
            return $segment_1;
        }, $routes);
        $unique_routes = array_unique($filter);
        array_push($unique_routes, "timesheet");
        $routes = array_values($unique_routes);
        $role = new Roles();
        $role = $role->find($id);
        return $this->response->setJSON(['data' => $role, 'routes' => $routes]);
    }

    public function add()
    {
        $name = $this->request->getVar('name');
        $description = $this->request->getVar('description');
        $status = $this->request->getVar('status') == 'on' ? 1 : 0;
        $uri_group = $this->request->getVar('routes') ?? [];
        if (count($uri_group) < 1) {
            return redirect()->to("/admin/role")->with('message', 'Role must have minimum 1 access');
        }
        $str_uri = join("|",$uri_group);
        
        $model = new Roles();
        $model->save([
            'name' => $name,
            'description' => $description,
            'status' => $status,
            'created_at' => Time::now(),
            'uri_group' => $str_uri
        ]);
        return redirect()->to("/admin/role")->with('message', 'A role has been created');
    }

    public function delete($id)
    {
        $model = new Roles();
        $model->delete($id);
        return redirect()->to("/admin/role")->with('message', 'A role has been deleted');
    }

    public function update()
    {
        $role = new Roles();
        $roleData = $role->find($this->request->getPost('id'));
        $roleData['name'] = $this->request->getPost('name');
        $roleData['description'] = $this->request->getPost('description');
        $roleData['status'] = $this->request->getPost('status') ? 1 : 0;
        $roleData['updated_at'] = Time::now();
        $uri_group = $this->request->getVar('routes') ?? [];
        if (count($uri_group) < 1) {
            return redirect()->to("/admin/role")->with('message', 'Role must have minimum 1 access');
        }

        $str_uri = join("|",$uri_group);
        
        $roleData['uri_group'] = $str_uri;
        $role->save($roleData);
        return redirect()->to("/admin/role")->with('message', "Role berhasil diupdate");
    }
}
