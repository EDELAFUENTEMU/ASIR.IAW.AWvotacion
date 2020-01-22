<?php
header("Content-Type: application/csv") ;

$servidor = "localhost";
$usuario = "server";
$password = "Server01%";
$db = "iaw_navidad";

$mbd = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $password);  
$mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$csv_end = "  
";  
$sql = 'SELECT grupo, p10*10+p8*8+p6*6+p4*4+p2*2 AS puntos FROM `puntuaciones` WHERE 1';
echo 'grupo,puntos'.$csv_end;
foreach ($mbd->query($sql) as $row) {
    echo($row['grupo'].','.$row['puntos'].$csv_end);
}
