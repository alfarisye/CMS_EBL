<?php

namespace App\Controllers;

use App\Controllers\BaseController;

use App\Models\GroupEmail as GroupEmailModel;
use App\Models\GroupEmails as GroupEmailsModel;

class GroupEmail extends BaseController
{
    public function index()
    {
        $data['title'] = "Group Email";

        $GroupEmail = new GroupEmailModel();
        $data['group_email'] = $GroupEmail->where("status", 1)->findAll();
        echo view('pages/group-email', $data);
    }

    public function add()
    {
        $group_name = $this->request->getVar('group_name');
        $GroupEmail = new GroupEmailModel();
        $GroupEmail->save([
            'group_name' => $group_name
        ]);

        $group_id = $GroupEmail->getInsertID();
        $email_lists = $this->request->getVar('group_email');

        $GroupEmails = new GroupEmailsModel();
        
        $GroupEmails->transStart();
        foreach($email_lists as $email) {
            $GroupEmails->save([
                "group_id" => $group_id,
                "email" => $email,
            ]);
        }
        $GroupEmails->transComplete();

        return redirect()->to("/group-email")->with('message', "Group Email berhasil ditambahkan");
    }

    public function delete($id)
    {
        $GroupEmail = new GroupEmailModel();
        // $GroupEmail->where("group_id", $id)->delete();
        $GroupEmail->update($id, [
            'status' => false
        ]);

        return redirect()->to("/group-email")->with('message', "Group Email berhasil dihapus");
    }

    public function edit($id)
    {
        $data['title'] = "Edit Group Email";
        $GroupEmail = new GroupEmailModel();
        $data['group_email'] = $GroupEmail->where("group_id", $id)->first();

        $GroupEmails = new GroupEmailsModel();
        $data['group_emails'] = $GroupEmails->where("group_id", $id)->findAll();

        echo view('pages/group-email-edit', $data);
    }

    public function update()
    {
        $group_id = $this->request->getVar('group_id');
        $group_name = $this->request->getVar('group_name');
        $email_lists = $this->request->getVar('group_email');
        $GroupEmail = new GroupEmailModel();

        $GroupEmail->save([
            'group_id' => $group_id,
            'group_name' => $group_name
        ]);

        $GroupEmails = new GroupEmailsModel();
        $GroupEmails->where("group_id", $group_id)->delete();
        $GroupEmails->transStart();
        foreach($email_lists as $email) {
            $GroupEmails->save([
                "group_id" => $group_id,
                "email" => $email,
            ]);
        }
        $GroupEmails->transComplete();
        return redirect()->back()->with('message', "Group Email berhasil diubah");
    }


}
