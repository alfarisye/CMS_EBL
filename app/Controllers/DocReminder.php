<?php

namespace App\Controllers;
ini_set('max_execution_time', 0);


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

use App\Controllers\BaseController;

use App\Models\DocReminder as DocReminderModel;
use App\Models\GroupEmail as GroupEmailModel;
use App\Models\SendEmailLog as SendEmailLogModel;
use CodeIgniter\I18n\Time;

class DocReminder extends BaseController
{
    protected $helpers = ['form'];

    private function generateCode()
    {
        $DocReminder = new DocReminderModel();
        $builder = $DocReminder->builder();
        $builder->select('MAX(code) as max_id');
        $max_id = $builder->get()->getRowArray();
        if ($max_id['max_id'] == null) {
            $new_id = "EMA" . "-0001";
            return $new_id;
        } else {
            $new_id = "EMA-" .  str_pad(substr($max_id['max_id'], -4) + 1, 4, "0", STR_PAD_LEFT);
            return $new_id;
        }
    }

    private function logSendEmail($email, $subject, $message, $doc_id, $status, $error_message = '')
    {
        $SendEmailLog = new SendEmailLogModel();
        $SendEmailLog->save([
            "email_address" => $email,
            "subject" => $subject,
            "message" => $message,
            "doc_reminder_id" => $doc_id,
            "status" => $status,
            "error_message" => $error_message,
            "created_at" => Time::now(),
        ]);
    }

    public function index()
    {
        $data['title'] = "Document Reminder";

        $DocReminder = new DocReminderModel();
        $data['doc_reminder'] = $DocReminder
            ->select('doc_reminder.*, gt.group_name AS to_name, ge.group_name AS cc_name')
            ->join('group_email AS gt', 'gt.group_id = group_email_id', 'left')
            ->join('group_email AS ge', 'ge.group_id = group_email_cc', 'left')
            ->where("deletion_status", "0")->findAll();

        $GroupEmail = new GroupEmailModel();
        $data['group_email'] = $GroupEmail->where("status", 1)->findAll();
        echo view('pages/doc-reminder', $data);
    }

    public function getJson($year)
    {
        $DocReminderModel = new DocReminderModel();
        $builder = $DocReminderModel->builder();
        $undelivered = $builder
            ->select('MONTHNAME(remind_on) AS month, COUNT(1) AS val')
            ->where('deletion_status', '0')
            ->where('email_status', 'undelivered')
            ->where('YEAR(remind_on)', $year)
            ->groupBy('MONTHNAME(remind_on)');
        $data['undelivered'] = ($undelivered->get()->getResultArray());
        $delivered = $builder
            ->select('MONTHNAME(remind_on) AS month, COUNT(1) AS val')
            ->where('deletion_status', '0')
            ->where('email_status', 'delivered')
            ->where('YEAR(remind_on)', $year)
            ->groupBy('MONTHNAME(remind_on)');
        $data['delivered'] = ($delivered->get()->getResultArray());

        $total = $builder
            ->select('MONTHNAME(remind_on) AS month, COUNT(1) AS val')
            ->where('deletion_status', '0')
            ->where('YEAR(remind_on)', $year)
            ->groupBy('MONTHNAME(remind_on)');
        $data['total'] = ($total->get()->getResultArray());

        return $this->response->setJSON($data);
    }

    public function add()
    {
        $validationRule = [
            'file_data' => [
                'rules' => 'uploaded[file_data]'
                    . '|max_size[file_data,2048]'
            ],
        ];
        if (!$this->validate($validationRule)) {
            return redirect()->to("/doc-reminder")->with('message', implode(";", $this->validator->getErrors()));
        }
        $doc_file = $this->request->getFile('file_data');
        $original_filename = $doc_file->getName();
        $original_ext = $doc_file->getExtension();
        if (!$doc_file->hasMoved()) {
            $filepath = WRITEPATH . 'uploads/' . $doc_file->store();
        } else {
            $filepath = "";
        }

        try {
            $doc_reminder = new DocReminderModel();

            $doc_no = $this->request->getVar('doc_no');
            $group_email_id = $this->request->getVar('group_email');
            $group_email_cc = $this->request->getVar('group_email_cc');
            $email_type = $this->request->getVar('email_type') ?? null;
            $doc_desc = $this->request->getVar('doc_desc');
            $due_date = $this->request->getVar('due_date');
            $remind_on = $this->request->getVar('remind_on');
            $due_date_parsed = Time::parse($due_date);
            $reminder_date = $due_date_parsed->subMonths($remind_on)->format('Y-m-d');
            $code = $this->generateCode();
            $doc_reminder->save([
                'code' => $code,
                'doc_no' => $doc_no,
                'group_email_id' => $group_email_id,
                'group_email_cc' => $group_email_cc,
                'email_type' => $email_type,
                'doc_desc' => $doc_desc,
                'due_date' => $due_date,
                'remind_on' => $reminder_date,
                'upload_file_path' => $filepath,
                'upload_file_name' => $original_filename,
                'upload_file_type' => $original_ext,
                'created_by' => session()->get('username'),
                'created_on' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            $message = "Document Reminder successfully added";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }

        return redirect()->to("/doc-reminder")->with('message', $message);
    }

    public function download($id)
    {
        try {
            $doc_reminder = new DocReminderModel();
            $doc_reminder = $doc_reminder->where('code', $id)->first();
            $file_path = $doc_reminder['upload_file_path'];
            $file_name = $doc_reminder['upload_file_name'];
            return $this->response->download($file_path, null)->setFileName($file_name);
        } catch (\Throwable $th) {
            return redirect()->to("/doc-reminder")->with('message', $th->getMessage());
        }
    }

    public function delete($id)
    {
        $doc_reminder = new DocReminderModel();
        $doc_reminder_data = $doc_reminder->where('code', $id)->first();
        if ($doc_reminder_data['email_status'] == 'undelivered') {
            $doc_reminder->update($doc_reminder_data['id'], [
                'deletion_status' => '1',
                'updated_by' => session()->get('username'),
                'updated_on' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            return redirect()->to("/doc-reminder")->with('message', 'A Document Reminder has been deleted');
        } else {
            return redirect()->to("/doc-reminder")->with('message', 'You cannot delete a Document Reminder that has been used, please contact your administrator');
        }
    }

    public function edit($id)
    {
        $data['title'] = "Edit Document Reminder";
        $DocReminder = new DocReminderModel();
        $data['doc_reminder'] = $DocReminder->where("code", $id)->first();
        
        if ($data['doc_reminder']['email_status'] == 'delivered') {
            return redirect()->to("/doc-reminder")->with('message', 'You cannot edit a Document Reminder that has been delivered');
        }

        $GroupEmail = new GroupEmailModel();
        $data['group_email'] = $GroupEmail->findAll();

        $data['code'] = $id;

        echo view('pages/doc-reminder-edit', $data);
    }

    public function update()
    {
        $doc_file = $this->request->getFile('file_data');
        $validationRule = [
            'file_data' => [
                'rules' => 'max_size[file_data,2048]'
            ],
        ];
        if (!$this->validate($validationRule)) {
            return redirect()->back()->with('message', implode(";", $this->validator->getErrors()));
        }

        $doc_no = $this->request->getVar('doc_no');
        $group_email_id = $this->request->getVar('group_email');
        $group_email_cc = $this->request->getVar('group_email_cc');
        $email_type = $this->request->getVar('email_type');
        $doc_desc = $this->request->getVar('doc_desc');
        $due_date = $this->request->getVar('due_date');
        $remind_on = $this->request->getVar('remind_on');
        $due_date_parsed = Time::parse($due_date);
        $reminder_date = $due_date_parsed->subMonths($remind_on)->format('Y-m-d');
        $id = $this->request->getVar('id');

        if ($doc_file->isValid()) {
            $original_filename = $doc_file->getName();
            $original_ext = $doc_file->getExtension();
            if (!$doc_file->hasMoved()) {
                $filepath = WRITEPATH . 'uploads/' . $doc_file->store();
            } else {
                $filepath = "";
            }
            try {
                $doc_reminder = new DocReminderModel();
                $doc_reminder_data = $doc_reminder->find($id);
                $doc_reminder->update($doc_reminder_data['id'], [
                    'doc_no' => $doc_no,
                    'group_email_id' => $group_email_id,
                    'group_email_cc' => $group_email_cc,
                    'email_type' => $email_type,
                    'doc_desc' => $doc_desc,
                    'due_date' => $due_date,
                    'remind_on' => $reminder_date,
                    'upload_file_path' => $filepath,
                    'upload_file_name' => $original_filename,
                    'upload_file_type' => $original_ext,
                    'updated_by' => session()->get('username'),
                    'updated_on' => Time::now()->format('Y-m-d H:i:s'),
                ]);
                $message = "Document Reminder successfully updated";
            } catch (\Throwable $th) {
                $message = $th->getMessage();
            }
            return redirect()->to("/doc-reminder")->with('message', $message);
        }
        try {
            $doc_reminder = new DocReminderModel();
            $doc_reminder_data = $doc_reminder->find($id);
            $doc_reminder->update($doc_reminder_data['id'], [
                'doc_no' => $doc_no,
                'group_email_id' => $group_email_id,
                'group_email_cc' => $group_email_cc,
                'email_type' => $email_type,
                'doc_desc' => $doc_desc,
                'due_date' => $due_date,
                'remind_on' => $reminder_date,
                'updated_by' => session()->get('username'),
                'updated_on' => Time::now()->format('Y-m-d H:i:s'),
            ]);
            $message = "Document Reminder successfully updated";
        } catch (\Throwable $th) {
            $message = $th->getMessage();
        }
        return redirect()->to("/doc-reminder")->with('message', $message);
    }

    public function dashboard()
    {
        $data['title'] = "Document Reminder Dashboard";
        $DocReminderModel = new DocReminderModel();
        $builder = $DocReminderModel->builder();
        $undelivered = $builder
            ->select('MONTHNAME(remind_on) AS month, COUNT(1) AS val')
            ->where('deletion_status', '0')
            ->where('email_status', 'undelivered')
            ->where('YEAR(remind_on)', date('Y'))
            ->groupBy('MONTHNAME(remind_on)');
        $data['undelivered'] = json_encode($undelivered->get()->getResultArray());
        $delivered = $builder
            ->select('MONTHNAME(remind_on) AS month, COUNT(1) AS val')
            ->where('deletion_status', '0')
            ->where('email_status', 'delivered')
            ->where('YEAR(remind_on)', date('Y'))
            ->groupBy('MONTHNAME(remind_on)');
        $data['delivered'] = json_encode($delivered->get()->getResultArray());

        $total = $builder
            ->select('MONTHNAME(remind_on) AS month, COUNT(1) AS val')
            ->where('deletion_status', '0')
            ->where('YEAR(remind_on)', date('Y'))
            ->groupBy('MONTHNAME(remind_on)');
        $data['total'] = json_encode($total->get()->getResultArray());

        $data['total_count'] = $builder->where('deletion_status', '0')->countAllResults();
        $data['undelivered_count'] = $builder->where('deletion_status', '0')->where('email_status', 'undelivered')->countAllResults();
        $data['delivered_count'] = $builder->where('deletion_status', '0')->where('email_status', 'delivered')->countAllResults();

        $data['year'] = range(date('Y'), date('Y') - 4);

        echo view('pages/doc-reminder-dashboard', $data);
    }

    /**
     * 
     * For Sending Email
     * 
     * 
     */
    public function sendEmail()
    {
        $mail = new PHPMailer(true);
        // $mail->SMTPDebug = 4;

        $DocReminder = new DocReminderModel();
        $builder = $DocReminder->builder();
        $builder->select('doc_reminder.*, gt.email AS to, gc.email AS cc');
        $builder->join('group_email AS to', 'doc_reminder.group_email_id = to.group_id', 'left');
        $builder->join('group_email AS cc', 'doc_reminder.group_email_cc = cc.group_id', 'left');
        $builder->join('group_emails gt', 'gt.group_id = to.group_id', 'left');
        $builder->join('group_emails gc', 'gc.group_id = cc.group_id', 'left');
        $builder->where('doc_reminder.email_status', 'undelivered');
        $builder->where('doc_reminder.deletion_status', '0');
        $builder->where('doc_reminder.remind_on =', Time::now()->format('Y-m-d'));
        $doc_reminder_data = $builder->get()->getResultArray();
        $isi_email = [];
        $site_url = site_url();

        foreach ($doc_reminder_data as $doc_reminder) {
            $isi_email[$doc_reminder['id']]['code'] = $doc_reminder['code'];
            $isi_email[$doc_reminder['id']]['subject'] = "Document reminder for " . $doc_reminder['doc_no'];
            if (!in_array($doc_reminder['to'], $isi_email[$doc_reminder['id']]['to'] ?? []) && $doc_reminder['to'] != null) {
                $isi_email[$doc_reminder['id']]['to'][] = $doc_reminder['to'];
            }
            if (!in_array($doc_reminder['cc'], $isi_email[$doc_reminder['id']]['cc'] ?? []) && $doc_reminder['cc'] != null) {
                $isi_email[$doc_reminder['id']]['cc'][] = $doc_reminder['cc'];
            }
            $isi_email[$doc_reminder['id']]['doc_no'] = $doc_reminder['doc_no'];
            $isi_email[$doc_reminder['id']]['doc_desc'] = $doc_reminder['doc_desc'];
            $isi_email[$doc_reminder['id']]['due_date'] = $doc_reminder['due_date'];
        }
        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPKeepAlive = true;
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = getenv('SMTP_USER');                     //SMTP username
            $mail->Password   = getenv('SMTP_PASS');                               //SMTP password
            $mail->Port       = getenv('SMTP_PORT');
            $mail->Priority   = 1;
            // $mail->SMTPSecure = "ssl"; // comment ini jika pakai hasnur
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            // $mail->SMTPSecure = "tls";
            $mail->setFrom('helpdeskputing@hasnurgroup.com');
            // $mail->setFrom('works@mangga.dev');
            
            foreach ($isi_email as $key => $value) {
                $date = Time::parse($value['due_date'])->format('d F Y');
                $message = "
                    <h1>Document Reminder</h1>
                    <p>Document No: {$value['doc_no']} </p>
                    <p>Document Description: {$value['doc_desc']} </p>
                    <p>Due Date: {$date} </p>
                    <hr>
                    <p>For more information please visit the <a href='{$site_url}'>website</a></p>
                    <br>
                    <p>Thank you</p>
                ";
                foreach ($value['to'] ?? [] as $to) {
                    $mail->addAddress($to);
                }
                foreach ($value['cc'] ?? [] as $cc) {
                    $mail->addCC($cc);
                }
                $mail->Subject = $value['subject'];
                $mail->Body = $message;
                $mail->isHTML(true);
                if ($mail->send()) {
                    $DocReminder->update($key, ['email_status' => 'delivered']);
                    $this->logSendEmail('', '', '', $key, 'success');
                    echo Time::now() .  ' Email was sent.' . PHP_EOL;
                } else {
                    $this->logSendEmail('', '', '', $key, 'error', $mail->ErrorInfo);
                    echo Time::now() . " " .  $mail->ErrorInfo . PHP_EOL;
                }
            }
        } catch (\Throwable $th) {
            echo Time::now() .  " Message could not be sent. Mailer Error: {$mail->ErrorInfo}, error: {$th->getMessage()}" . PHP_EOL;
        }
    }

    public function sendEmailTest()
    {
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 4;
        try {
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = getenv('SMTP_USER');                     //SMTP username
            $mail->Password   = getenv('SMTP_PASS');                               //SMTP password
            $mail->Port       = getenv('SMTP_PORT');
            // $mail->SMTPSecure = "ssl";
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom('helpdesk.puting@hasnurgroup.com');
            $mail->addAddress('fresh.note3774@fastmail.com');
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Here is the subject';
            $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
            $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
            echo 'Message has been sent';
        } catch (\Throwable $th) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    public function test_cli()
    {
        $output = shell_exec("/usr/bin/php index.php send_reminder");
        echo "<pre>$output</pre>";
    }
}
