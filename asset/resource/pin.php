<?php

/*valida si ha iniciado sesion el usuario previamente. 
Comprueba q existe la variable type{email o sms}
Comprueba q haya transcurrido dos minutos desde el ultimo envio
genera un pin
un manda un email*/
session_start();

require '../lib/conexion_db.php';
require '../lib/mail.php';
$msg='';

if(isset($_SESSION['IDmail']) && isset($_SESSION['Name'])){
    if(isset($_POST['type']) && preg_match('/^(phone|email)$/', $_POST['type'])){
        $mbd = mdb(); 
        $sql='SELECT upd_PIN, tlf FROM user WHERE user="'.$_SESSION['Name'].'" and id_correo="'.$_SESSION['IDmail'].'" LIMIT 1';
        foreach ($mbd->query($sql) as $row){
            //si la fecha del ult cambio de pin + 2min es menor que el momento actual ->genero un nuevo pin
            if($row['upd_PIN']+120 < time()){
                $pin = str_pad(random_int(0,999999),6,'0',STR_PAD_LEFT);
                $limit_PIN = time() + 1200;
                $uPIN = $mbd->exec('UPDATE user SET PIN="'.$pin.'",upd_PIN="'.time().'", limit_PIN="'.$limit_PIN.'"  WHERE id_correo="'.$_SESSION['IDmail'].'"');
                if ($uPIN >0){    
                    $msg = _mail($_SESSION['IDmail'],$_SESSION['Name'],$pin,$_POST['type'],$row['tlf']);
                }
            }else{
                $msg = '{status:0, content:"Error! No han transcurrido el tiempo minimo"}';
            }
        }
    }
}
echo $msg;
?>