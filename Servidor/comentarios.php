<?php

$servidor = "localhost";
$usuario = "server";
$password = "Server01%";
$db = "iaw_navidad";

$mbd = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $password);  
$mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = 'SELECT hash,`comentario`,datetime as date FROM `votos` WHERE 1 ORDER BY datetime';
$json = array();
foreach ($mbd->query($sql) as $row) {
    $json[] = ['hash'=>$row['hash'],
               'comentario'=>$row['comentario'],
               'date'=>$row['date']];
}
echo(json_encode($json));