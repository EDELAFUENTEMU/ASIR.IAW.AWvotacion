<?php

$servidor = "localhost";
$usuario = "server";
$password = "Server01%";
$db = "iaw_navidad";

$mbd = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $password);  
$mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = 'SELECT TRUNCATE(((SELECT COUNT(*) FROM user WHERE voted = 1)/count(*))*100,0) AS participacion FROM user LIMIT 1';
foreach ($mbd->query($sql) as $row) {
    $json = array('participacion'=>$row['participacion']);
}
echo(json_encode($json));