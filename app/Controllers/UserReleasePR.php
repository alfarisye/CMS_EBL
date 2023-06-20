<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\Users;
use App\Models\Roles;
use CodeIgniter\I18n\Time;
use PhpParser\Node\Stmt\TryCatch;

class UserReleasePR extends BaseController
{
    public function index()
    {
        $data['title'] = "User Release PR";
        $db=\Config\Database::connect();
     
        $data['users']= $db->query("select tb1.id,tb1.username,tb1.email,tb1.fullname,tb1.relcode_pr,tb1.relcode_po,tb3.description from users tb1 
        left join user_roles tb2 on tb2.user_id=tb1.id 
        left join roles tb3 on tb3.id=tb2.role_id 
        where (tb1.relcode_pr IS NOT NULL AND tb1.relcode_pr!='') 
        ")->getResult();
        $data['list_users']= $db->query("select tb1.id,tb1.username,tb1.email,tb1.fullname,tb1.relcode_pr,tb1.relcode_po,tb3.description from users tb1 
        left join user_roles tb2 on tb2.user_id=tb1.id 
        left join roles tb3 on tb3.id=tb2.role_id 
        ")->getResult();
        $data['release_code']= $db->query("select id,FRGCO,FRGCT from t_t16fd")->getResult();
        echo view('templates/header', $data);
        echo view('templates/navbar', $data);
        echo view('templates/sidebar', $data);
        echo view('pages/manage/user-release-pr', $data);
        echo view('templates/cp');
        echo view('templates/js');
        echo view('templates/footer', $data);
    }


    public function update()
    {
        $db=\Config\Database::connect();
        $release_code = $this->request->getPost('release_code');
        $id=$this->request->getPost('id_user');
        if($release_code){
            $relcode_pr=implode(',',$release_code);
            $db->query("update users set relcode_pr='$relcode_pr' where id='$id'");
            return redirect()->to("/admin/user-release-pr")->with('message', "Data Berhasil di Update");
        }else{
            $db->query("update users set relcode_pr='' where id='$id'");
            return redirect()->to("/admin/user-release-pr")->with('message', "Data Berhasil di Update");
        }
    }
}
