<?php
function mdb(){
    require 'privatekey.php';

    try{
        //conexion bbdd
        $mbd = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $password);  
        $mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $mbd;
    }catch (PDOException $e) {
        die($e->getMessage() );
    }
}
?>
