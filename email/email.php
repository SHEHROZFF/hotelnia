<?php
include('smtp/PHPMailerAutoload.php');

// echo smtp_mailer('shakeebrazamuhammad@gmail.com', 'test mail', 'hello world');

function smtp_mailer($to, $subject, $msg, $attachments = []) {
    $mail = new PHPMailer(); 
    $mail->IsSMTP(); 
    $mail->SMTPAuth   = true; 
    $mail->SMTPSecure = 'ssl';  
    $mail->Host       = "smtp.gmail.com";
    $mail->Port       = 465;  
    $mail->IsHTML(true);
    $mail->CharSet    = 'UTF-8';
    // $mail->SMTPDebug = 3;

    $mail->Username   = "man411210@gmail.com";
    $mail->Password   = "dsahhfifikerwgzz";  
    $mail->SetFrom("man411210@gmail.com");
    $mail->Subject    = $subject;
    $mail->Body       = $msg;
    $mail->AddAddress($to);

    // attachments if any
    if (is_array($attachments)) {
        foreach ($attachments as $filePath) {
            if (file_exists($filePath)) {
                $mail->addAttachment($filePath);
            } else {
                error_log('Attachment missing: ' . $filePath);
            }
        }
    }

    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer'       => false,
            'verify_peer_name'  => false,
            'allow_self_signed' => true 
        ]
    ];

    if (!$mail->Send()) {
        error_log('Mailer Error: ' . $mail->ErrorInfo);
        return $mail->ErrorInfo;
    } else {
        return 'sent';  // all-lowercase
    }
}

?>