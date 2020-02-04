<?php
header("Content-Type: text/plain") ;

session_start();
require '../lib/validate_session.php';
require '../lib/conexion_db.php';
$mbd = mdb();

$csv_end = "  
";  
$sql = 'SELECT proyecto, p12*12+p10*10+p8*8+p6*6+p4*4+p2*2 AS puntos, p10 FROM `puntuaciones` INNER JOIN grupos ON `grupo`= gid WHERE 1';
//$sql = 'SELECT proyecto, p12 , p10, p8, p6 , p4 , p2 AS puntos, p10 FROM `puntuaciones` INNER JOIN grupos ON `grupo`= gid WHERE 1';
echo 'grupo,puntos'.$csv_end;
foreach ($mbd->query($sql) as $row) {
    echo($row['proyecto'].','.$row['puntos'].$csv_end);
}
