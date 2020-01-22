<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../lib/vendor/autoload.php';


//config_key
require 'privatekey.php';


//conexion bbdd
$mbd = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $password);  
$mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


//¿han entrado de chiripa?
if(!isset($_POST['name']) || !isset($_POST['pw']) || !isset($_POST['token']) || !isset($_POST['action'])){
    //han entrado de chiripa -> fuera
    header('location: index.php?datapost=false');
    exit;
}else{
    $name=$_POST['name'];
    $pw=$_POST['pw'];
    $token = $_POST['token'];
    $action = $_POST['action'];
}

//    conexion con google para contrastar recapcha
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $keySecret, 'response' => $token)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$arrResponse = json_decode($response, true);
 
// ¿Es spam o no?
if($arrResponse["success"] == '0' && $arrResponse["action"] != $action && $arrResponse["score"] <= 0.4){
    //no pasa los filtros spam -> fuera
    header('location: index.php?spam=true');
    exit;
}

// El usuario existe?
$sql='SELECT count(*) FROM user WHERE user="'.$name.'" and id_correo="'.$pw.'"';
$result = $mbd->query($sql);
if($result->fetchColumn() == 0){ 
    //no existe ese usuario -> fuera
    header('location: index.php?user=false');
    exit;
}else{
    session_start();
    $_SESSION['IDmail']=$pw;
    echo $_SESSION['IDmail'];
}

// El usuario ya ha votado
$sql='SELECT voted FROM user WHERE user="'.$name.'" and id_correo="'.$pw.'"';
$result = $mbd->query($sql)->fetch(PDO::FETCH_ASSOC);
if($result['voted']){ 
    //el usuario ya voto -> estadisticas
    header('location: resultados.php');
    exit;
}


// ¿han transcurrido los 2'?
if(isset($_COOKIE[str_replace(".","_",$pw)])){
    //existe la cookie = no han transcurrido los 2'
    $tResta= (time() - $_COOKIE[str_replace(".","_",$pw)])-120;
    printf('Aun no han transcurrido los 2:00. Quedan '.date("i:s", $tResta));
}else{
    
    //Genero un PIN Aleatorio
    $pin = 000001;//random_int(0,999999);
    
    $uPIN = $mbd->exec('UPDATE user SET PIN="'.$pin.'" WHERE id_correo="'.$pw.'"');
    if ($uPIN >0){    

        // Envio PIN al correo institucional 
        $mail = new PHPMailer();
        //Parametros SMTP
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
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
        $mail->addAddress($pw.'@educa.madrid.org');
        $mail->Subject = 'Portal Votaciones: PIN de Acceso Unico';
        $mail->Body = 'Hola '.$name.'! El PIN de acceso al portal de votación es: '.$pin.'.';

        //envio o error
        if (false)//!$mail->send())
        {
           //PHPMailer error. 
           echo $mail->ErrorInfo;
        }else{
            //creamos la cookie ['manual.garcia2',456789898,4567890976]
            setcookie(urlencode($pw),time(),time()+120);
        }
    }
    
 
}

?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="css.css">
    <title>WebApp Votación Proyectos ASIR 19/20</title>
  </head>
  <body>
     <div class="container-fluid m-0 p-0">
         <header class=" fixed-top row pt-1 pb-1 pl-5 justify-content-between">
            <img src="img/logo.png" alt="logo" srcset="img/logo.svg">
            <!--<span>Proyecto IES Virgen de la Paz</span>-->
         </header>
         <div class="row justify-content-center d-flex align-items-center" style="height:70vh" id='form'>
            <div class="col-sm-5 bg-light p-4 rounded shadow-lg">
              <h3 class=" display-4 border-bottom">PIN de Acceso</h3>
              <p class="">Para garantizar su identidad se acaba de enviar un email con la clave de acceso. Por favor, compruebelo. No olvide mirar la carpeta spam!
                  <ul>
                  <li>El PIN solo es valido durante 5h</li>
                  <li>Debe esperar 2 minutos antes de solicitar un nuevo PIN</li>
                  </ul>
              </p>
              
              <form method="post" action="votacion.php">
                  <div class="form-group input-group">
                    <div class="input-group-prepend"><div class="input-group-text">Usuario:</div></div>
                    <input type="text" class="form-control" name="name" readonly value='<?php printf($name); ?>'>
                 </div>
                 <div class="form-group input-group">
                    <div class="input-group-prepend"><div class="input-group-text">Correo:</div></div>
                    <input type="text" class="form-control" name="email" readonly value='<?php printf($pw."@educa.madrid.org"); ?>'>
                </div>
                <div class="form-group input-group">
                    <div class="input-group-prepend"><div class="input-group-text">PIN</div></div>
                    <input type="text" class="form-control" name="pin" placeholder="000000" value="000001">
                </div>
                <button type="submit" class="btn btn-primary" value="submit">Acceder</button>
                <button type="button" class="btn btn-primary" disabled>Reenviar por Whatsapp</button>
                <br><br><a href="" onclick="alert('Contacte con su proveedor de soluciones informaticas para una óptima solución');return false;">Ayuda! No recibo el correo.</a>
             </form>
         </div>
     </div> 
     <footer class="fixed-bottom row" id="foot"><span class=" col-12 text-center text-muted">Copyright 2020 Ismael, Rodrigo y Eduardo</span></footer>
  </body>
  <!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/7.7.0/firebase-app.js"></script>


</html>