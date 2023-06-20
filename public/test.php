<?php
/**
 * This example shows making an SMTP connection with authentication.
 */

//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

require 'PHPMailer/PHPMailerAutoload.php';

//Create a new PHPMailer instance
$mail = new PHPMailer;
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
            'crypto_method' => STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
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
    } else {
        echo "Email GAGAL dikirim";
        // $data = array("status"=>"false","message"=>"Sukses");
        // header('Content-Type: application/json; charset=utf-8');
        // echo json_encode($data);
        // return "false";
        echo $mail->ErrorInfo . PHP_EOL;
    }
} catch (\Throwable $th) {
    echo  " Message could not be sent. Mailer Error: {$mail->ErrorInfo}, error: {$th->getMessage()}" . PHP_EOL;
}
