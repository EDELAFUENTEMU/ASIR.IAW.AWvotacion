<?php
session_start();
require '../lib/validate_session.php';
require '../lib/conexion_db.php';
$mbd = mdb();

$sql = 'SELECT TRUNCATE(((SELECT COUNT(*) FROM user WHERE voted = 1)/count(*))*100,0) AS participacion FROM user LIMIT 1';
foreach ($mbd->query($sql) as $row) {
    $json = array('participacion'=>$row['participacion']);
}
echo(json_encode($json));