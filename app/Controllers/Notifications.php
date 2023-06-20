<?php

namespace App\Controllers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use App\Controllers\BaseController;
use App\Models\GLogs;

use App\Models\Notification\Notification;
use CodeIgniter\I18n\Time;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Files\File;
use CodeIgniter\HTTP\Files\UploadedFile;

class Notifications extends BaseController
{
    use ResponseTrait;
    public $GLogs;
    public function __construct()
    {
        $this->GLogs = new Glogs();
    }

    public function send_mail()
    {
        // $mpdf = new \Mpdf\Mpdf();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir().DIRECTORY_SEPARATOR.'mpdf']);
        $mail = new PHPMailer(true);
        // $mail->SMTPDebug = 4;
        $to=isset($_POST['to'])?$_POST['to']:"taufikakbarmalikitkj@gmail.com";
        $cc=isset($_POST['cc'])?$_POST['cc']:"HIT_Analyst_Ebl@hasnurgroup.com";
        $subject=isset($_POST['subject'])?$_POST['subject']:"Subjek kosong";
        $message=isset($_POST['message'])?$_POST['message']:"Message Kosong";
        $priority=isset($_POST['priority'])?$_POST['priority']:3;
        $from=isset($_POST['from'])?$_POST['from']:'helpdeskputing@hasnurgroup.com';
        $attach=isset($_POST['attach'])?true:false;
        $pdf=isset($_POST['pdf'])?$_POST['pdf']:false;
        $token=isset($_POST['token'])?$_POST['token']:'false';
        if($token=='Hasnur123'){

            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPKeepAlive = true;
            $mail->isSMTP();
            $mail->Host       = getenv('SMTP_HOST');                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = getenv('SMTP_USER');                     //SMTP username
            $mail->Password   = getenv('SMTP_PASS');                               //SMTP password
            $mail->Port       = getenv('SMTP_PORT');
            $mail->Priority   = $priority; // 1 = High, 2 = Medium, 3 = Low
            // $mail->SMTPSecure = "ssl"; // comment ini jika pakai hasnur
             $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->SMTPSecure = "tls";
            $mail->setFrom($from);

            // $mail->setFrom(getenv('SMTP_USER'));

            // $date = Time::parse(date("Y/m/d"))->format('d F Y');
            $mail->addAddress($to);
            $cc=str_replace(' ', '', $cc);
            if(strlen($cc)>4){
                $var = explode(',',$cc);
                foreach($var as $row){
                    $mail->addCC($row);
                }
            }
            if($pdf){
                $mpdf->WriteHTML($pdf);
                $pdf_content = $mpdf->Output('', 'S');
                $mail->addStringAttachment($pdf_content, 'attachment.pdf', 'base64', 'application/pdf');
            }
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->isHTML(true);
            if ($mail->send()) {
                // $data = array("status"=>"true","message"=>"Sukses");
                // header('Content-Type: application/json; charset=utf-8');
                // echo json_encode($data);
                // return "true";
                // return $this->respond(array("status"=>"true","message"=>"Sukses"), 200);
                echo Time::now() .  ' Email was sent.' . PHP_EOL;
            } else {
                 return $this->respond("error", 500);
            }
        }else{
             return $this->respond("error", 500);
        }
       
    }


    public function send_mail_eproc()
    {
        $mail = new PHPMailer(true);
        // $mail->SMTPDebug = 4;
        $to=isset($_POST['to'])?$_POST['to']:"taufikakbarmalikitkj@gmail.com";
        $cc=isset($_POST['cc'])?$_POST['cc']:"HIT_Analyst_Ebl@hasnurgroup.com";
        $subject=isset($_POST['subject'])?$_POST['subject']:"Subjek kosong";
        $message=isset($_POST['message'])?$_POST['message']:"Message Kosong";
        $priority=isset($_POST['priority'])?$_POST['priority']:3;
        $attach=isset($_POST['attach'])?true:false;
        $pdf=isset($_POST['pdf'])?$_POST['pdf']:false;
        $token=isset($_POST['token'])?$_POST['token']:'false';
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPKeepAlive = true;
            $mail->isSMTP();
            $mail->Host       = "fmail.hasnurgroup.com";                  
            $mail->SMTPAuth   = true;                                   
            $mail->Username   = "eproc.hrs";                   
            $mail->Password   = "Password123";                               
            $mail->Port       = 25;
            $mail->Priority   = $priority; // 1 = High, 2 = Medium, 3 = Low
            $mail->SMTPSecure = "tls";
            $mail->setFrom('eproc@hasnurgroup.com');
            $mail->addAddress($to);
            $mail->addCC($cc);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->isHTML(true);
            if ($mail->send()) {
                echo Time::now() .  ' Email was sent.' . PHP_EOL;
            } else {
                 return $this->respond("error", 500);
            }
       
    }

    public function send_mail2()
    {
        $to=isset($_POST['to'])?$_POST['to']:"taufikakbarmalikitkj@gmail.com";
        $cc=isset($_POST['cc'])?$_POST['cc']:"HIT_Analyst_Ebl@hasnurgroup.com";
        $subject=isset($_POST['subject'])?$_POST['subject']:"Subjek kosong";
        $message=isset($_POST['message'])?$_POST['message']:"Message Kosong";
        $priority=isset($_POST['priority'])?$_POST['priority']:3;
        $from=isset($_POST['from'])?$_POST['from']:'helpdeskputing@hasnurgroup.com';
        $pdf=isset($_POST['pdf'])?$_POST['pdf']:false;
        $token=isset($_POST['token'])?$_POST['token']:'false';
        if($token=='Hasnur123'){
            $url = 'http://192.168.20.200/cms-ebl/public/index.php/api/send/mail';
            
            if($pdf){
                $data = [
                    'to' => $to,
                    'cc' => $cc,
                    'subject' => $subject,
                    'message' => $message,
                    'priority' => $priority,
                    'from' => $from,
                    'token'=>$token,
                    'pdf'=>$pdf
                ];
            }else{
                $data = [
                    'to' => $to,
                    'cc' => $cc,
                    'subject' => $subject,
                    'message' => $message,
                    'priority' => $priority,
                    'from' => $from,
                    'token'=>$token,
                ];
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            echo $result;
        }else{
             return $this->respond("error", 500);
        }
       
    }

    public function send_mail3()
    {
        $to=isset($_POST['to'])?$_POST['to']:"taufikakbarmalikitkj@gmail.com";
        $cc=isset($_POST['cc'])?$_POST['cc']:"HIT_Analyst_Ebl@hasnurgroup.com";
        $subject=isset($_POST['subject'])?$_POST['subject']:"Subjek kosong";
        $message=isset($_POST['message'])?$_POST['message']:"Message Kosong";
        $priority=isset($_POST['priority'])?$_POST['priority']:3;
        $pdf=isset($_POST['pdf'])?$_POST['pdf']:false;
        $token=isset($_POST['token'])?$_POST['token']:'false';
        if($token=='Hasnur123'){
            $url = 'http://192.168.20.200/cms-ebl/public/index.php/api/send/mail/eproc';
            
            if($pdf){
                $data = [
                    'to' => $to,
                    'cc' => $cc,
                    'subject' => $subject,
                    'message' => $message,
                    'priority' => $priority,
                    'token'=>$token,
                    'pdf'=>$pdf
                ];
            }else{
                $data = [
                    'to' => $to,
                    'cc' => $cc,
                    'subject' => $subject,
                    'message' => $message,
                    'priority' => $priority,
                    'token'=>$token,
                ];
            }
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            echo $result;
        }else{
             return $this->respond("error", 500);
        }
       
    }

    public function get_send_mail(){
        echo "Start Email <br>";

        $mail = new PHPMailer(true);
        $mail->SMTPDebug = 4;
        $to='Taufik.Maliki@hasnurgroup.com';
        $cc='Muhammad.Angga@hasnurgroup.com';
        // $cc='';
        $subject="Subjek test email ";
        $message="Message test email";
        $attach=false;
        try {
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->SMTPKeepAlive = true;
            $mail->isSMTP();
            $mail->Host       = "192.168.14.55";                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = "helpdeskputing";                     //SMTP username
            $mail->Password   = 'M13r@h@yu123!#';                               //SMTP password
            $mail->Port       = 25;
            $mail->Priority   = 1;
            // $mail->SMTPSecure = "ssl"; // comment ini jika pakai hasnur
            echo "192.168.14.55";
             $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                    // 'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
                )
            );
            $mail->SMTPSecure = "tls";
            $mail->setFrom('helpdeskputing@hasnurgroup.com');

            // $mail->setFrom(getenv('SMTP_USER'));
            
            // $date = Time::parse(date("Y/m/d"))->format('d F Y');
            $mail->addAddress($to);
            $mail->addCC($cc);
            if($attach){
                $arr=explode("/",$attach);
                $attach=str_replace("\\","\\\\",$attach);
                $mail->AddAttachment($attach, $arr[count($arr)-1]);
            }

            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->isHTML(true);
            if ($mail->send()) {
                echo "Email Berhasil dikirim";
                // $data = array("status"=>"true","message"=>"Sukses");
                // header('Content-Type: application/json; charset=utf-8');
                // echo json_encode($data);
                // return "true";
                // return $this->respond(array("status"=>"true","message"=>"Sukses"), 200);
                echo Time::now() .  ' Email was sent.' . PHP_EOL;
            } else {
                echo "Email GAGAL dikirim";
                // $data = array("status"=>"false","message"=>"Sukses");
                // header('Content-Type: application/json; charset=utf-8');
                // echo json_encode($data);
                // return "false";
                echo Time::now() . " " .  $mail->ErrorInfo . PHP_EOL;
            }
        } catch (\Throwable $th) {
            echo Time::now() .  " Message could not be sent. Mailer Error: {$mail->ErrorInfo}, error: {$th->getMessage()}" . PHP_EOL;
        }
    }

    public function send_notification(){
        $data=$this->request->getJSON();
        $Notification = new Notification();
        $Notification->save($data);
        return $this->respond($data, 200);
    }
    
    public function get_notification(){
        $username=$_GET['username']?$_GET['username']:'-';
        $db = \Config\Database::connect();
        $query = $db->query("select tb1.*,tb2.fullname as to_name,tb3.fullname as from_name from Notification tb1 
        left join users tb2 on tb2.username=tb1.user_id_to
        left join users tb3 on tb3.username=tb1.user_id_from
        where tb1.user_id_to LIKE '%$username' OR tb1.user_id_cc LIKE '%$username' order by tb1.id desc");
        return $this->respond($query->getResult(), 200);
    }

    public function update_notification($id)
    {
        $username=$_GET['username']?$_GET['username']:'-';
        $db = \Config\Database::connect();
         $db->query("update Notification set status=CONCAT(status,',$username') where id='$id'");
         return $this->respond(array(array('status'=>true,"qry"=>"update Notification set status='1' where id='$id'")), 200);
    }

// Mailer Route
// $routes->post('/api/send/mail', 'Notifications::send_mail'); //GET
// $routes->get('/api/send/mail', 'Notifications::get_send_mail'); //GET
// $routes->post('/api/send/notification', 'Notifications::send_notification'); //GET
// $routes->get('/api/get/notification', 'Notifications::get_notification'); //GET
// $routes->put('/api/put/notification/(:any)', 'Notifications::update_notification/$1');

}
