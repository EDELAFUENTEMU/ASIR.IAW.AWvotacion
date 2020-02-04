<?php
session_start();
require '../lib/validate_session.php';
require '../lib/conexion_db.php';
$mbd = mdb();


$sql = 'SELECT hash , comentario , datetime as date, emoji FROM `votos` WHERE 1 ORDER BY datetime DESC';
$json = array();
foreach ($mbd->query($sql) as $row) {
    $json[] = ['hash'=>$row['hash'],
               'comentario'=>$row['comentario'],
               'date'=>$row['date'],
               'emoji'=>$row['emoji']];
}
echo(json_encode($json));