<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;


function _mail($to,$name,$pin,$type,$phone){
    //en phone va la ruta del fichero
    
    switch ($type) {
        case 'phone':
            $Subject = 'Solicitud de PIN de '.$name;
            $body = 'https://api.whatsapp.com/send?phone=+34'.$phone.'&text='.urlencode("Hola $name! Su PIN de acceso es $pin .");
            $to = 'eduardo.fuente2';
            break;
        case 'email':
            $Subject='Portal Votaciones: PIN de Acceso Unico';
            $body = 'Hola '.$name.'! El PIN de acceso al portal de votación es: '.$pin.'.';
            break;
        case 'cert':
            $Subject='Portal Votaciones: Certificado de voto';
            $body = 'Hola '.$name.'! Su voto se ha contabilizado correctamente. Le adjuntamos su certificado de voto. Gracias por participar';
            break;
    }
    
    
    require_once 'vendor/autoload.php';
    require 'privatekey.php';

    // Envio PIN al correo institucional 
    $mail = new PHPMailer();
    $mail->CharSet = "utf-8";
    //Parametros SMTP
    $mail->isSMTP();
    $mail->SMTPDebug = 2;
    $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
    );
    $mail->Host = $HostSMTP;
    $mail->SMTPAuth = TRUE;
    $mail->SMTPSecure = 'tls';
    $mail->Username = $e_Username;
    $mail->Password = $e_Password;
    $mail->Port = 587;
    $mail->SMTPKeepAlive = true;  
    $mail->Mailer = "smtp"; 
    //Contenido    
    $mail->setFrom($e_From, 'Proyecto ASIR 19/20');
    $mail->addAddress($to.'@educa.madrid.org');
    $mail->Subject = $Subject;
    $mail->Body = $body;
    
    if($type=='cert'){
        if(!$mail->AddAttachment($phone,'Certificado.pdf')){return "{status:0, content:$mail->ErrorInfo}";}
    }

    //envio o error
    if (!$mail->send()){
       echo "{status:0, content:$mail->ErrorInfo}";
    }else{
       echo "{status:1, content:good}";
    }
}
_mail('eduardo.fuente2','eduardo de la fuente','223232','phone','6840548812');

?>
