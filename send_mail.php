<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Inclure les fichiers nécessaires de PHPMailer
require 'includes/PHPMailer-master/src/Exception.php';
require 'includes/PHPMailer-master/src/PHPMailer.php';
require 'includes/PHPMailer-master/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'douaelamrani3@gmail.com';        
    $mail->Password = 'jhrq wopc jaxo ujop';    
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    
    $mail->setFrom('douaelamrani3@gmail.com', 'Mon Site');
    $mail->addAddress('lamranidouae1@gmail.com'); 

   
    $mail->isHTML(true);
    $mail->Subject = 'Test PHPMailer avec Gmail';
    $mail->Body = 'Bonjour ! Ceci est un test d\'envoi d\'email depuis XAMPP avec <b>PHPMailer</b>.';

    $mail->send();
    echo ' ';
} catch (Exception $e) {
    echo "  {$mail->ErrorInfo}";
}
?>
