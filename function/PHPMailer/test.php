<?php
require("PHPMailer.php");
require("SMTP.php");
require("Exception.php");


$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->IsSMTP();

$mail->CharSet="UTF-8";
$mail->Host = "smtp.gmail.com";
$mail->SMTPDebug = 1;
$mail->Port = 465 ; //465 or 587

$mail->SMTPSecure = 'ssl';
$mail->SMTPAuth = true;
$mail->IsHTML(true);

//Authentication
$mail->Username = "foo@gmail.com";
$mail->Password = "*******";

//Set Params
$mail->SetFrom("foo@gmail.com");
$mail->AddAddress("bar@gmail.com");
$mail->Subject = "Test";
$mail->Body = "hello";


if(!$mail->Send()) {
echo "Mailer Error: " . $mail->ErrorInfo;
} else {
echo "Message has been sent";
}