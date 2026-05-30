<?php
require_once('function/phpmailer/class.phpmailer.php');

$mail = new PHPMailer;

$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->Mailer = "mail";                                     // Set mailer to use SMTP
$mail->Host = 'sg2plcpnl0097.prod.sin2.secureserver.net';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'shafiq@wishaproperty.com';                 // SMTP username
$mail->Password = 'l%YJGbWlhmCJ';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 465;                                    // TCP port to connect to

$mail->From = 'shafiq@wishaproperty.com';
$mail->FromName = 'Mailer';
$mail->addAddress('shafiqruet@gmail.com', 'Joe User');     // Add a recipient

$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

$mail->WordWrap = 50;

$mail->isHTML(true);

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}