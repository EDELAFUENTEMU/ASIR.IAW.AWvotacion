<?php
function recapcha($token,$action){
    require 'privatekey.php';

    //conexion con google para contrastar recapcha
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $keySecret, 'response' => $token)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $arrResponse = json_decode($response, true);

    // ¿Es spam o no?
    if($arrResponse["success"] == false){ //no pasa los filtros spam -> fuera
        header("location:index.php?error=spam&error=".$arrResponse['error-codes'][0]);
        exit;
    }elseif($arrResponse["action"] != $action && $arrResponse["score"] <= 0.4){ //no cumple los minimos anti-spam
        header("location:index.php?spam=true&score=".$arrResponse['score']);
        exit;
    }else{
        return 1;
    }
}
?>