<?php
header("Content-type: text/html; charset=utf8"); 

$msg='';
$servidor = "localhost";
$usuario = "server";
$password = "Server01%";
$db = "iaw_navidad";

$conn = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $password);      
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        
function query_paises(){
    global $conn, $msg;
    $sql = 'SELECT acronimo, nom_pais FROM paises ORDER BY nom_pais ASC';
    $paises = array();
    $resultado = $conn->query($sql);
    foreach ($resultado as $row) {
        $paises[]= ['acronimo' => $row["acronimo"],
                    'nom_pais' => utf8_encode($row["nom_pais"])];
    }
    $paises = json_encode($paises);
    return $paises;
} //lista de paises


function query_puntaciones(){
    global $conn, $msg;
    $sql = 'SELECT paises.acronimo as code, (p10*10+p8*8+p6*6+p4*4+p2*2)/100 as value, comentarios, nom_pais FROM puntuaciones INNER JOIN paises on paises.acronimo = puntuaciones.acronimo ORDER BY value DESC';
    $result = $conn->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    return json_encode($result, JSON_NUMERIC_CHECK);
} //puntuaciones y comentarios por paises

function query_ultUsuario(){
    global $conn, $msg;
    $sql = 'SELECT usuario FROM votos LIMIT 8';
    $resultado = $conn->query($sql);
    return json_encode($resultado);
}
function query_usuario($usuario){
    global $conn, $msg;
    $sql = 'SELECT count(usuario) as usuarios FROM votos WHERE usuario="'.$usuario.'"';
    $resultado = $conn->query($sql);
    foreach ($resultado as $row){
        $num = $row['usuarios'];
    }
    if($num>=1){
        return true; //el usuario existe
    }else{
        return false; //el usuario no existe
    }
} //¿el usuario existe?

function insert($usuario,$p10,$p8,$p6,$p4,$p2,$comentario){
    global $conn, $msg;
    $data = [  'usuario' => $usuario,
                'p10' => $p10,
                'p8' => $p8,
                'p6' => $p6,
                'p4' => $p4,
                'p2' => $p2,
                'comentario' => $comentario
             ];
    $sql = "INSERT INTO votos (usuario, p10, p8, p6, p4, p2, comentarios) VALUES (:usuario, :p10, :p8, :p6, :p4, :p2, :comentario)";
    $stmt=$conn->prepare($sql);
    $stmt->execute($data);
    
    if($stmt->rowCount() > 0 ){//se ha insertado la fila correctamente
        crear_cookie($usuario);
        $msg='Datos agregados correctamente';
    }else{
        $msg='Error al insertar los datos! Intentelo nuevamente.';
    }
}

//COOKIE
function crear_cookie($usuario){
    setcookie('user',$usuario,time()+31536000);
}
function validar_cookie(){
    if(isset($_COOKIE['user'])){
        return true; //existe la cookie. Votacion previa. 
    }else{
        return false; //no existe
    }
}


if(isset($_POST['request'])){
    switch ($_POST['request']){
        case 'puntuaciones':
            printf(query_puntaciones()); //[{"code":"es","value":0.44,"comentarios":"voto","nom_pais":"Espa\u00f1a"},{"code":"fr","value":0.22,"comentarios":"votofr","nom_...
        break;
        case 'paises':
            printf(query_paises()); //[{"acronimo":"af","nom_pais":"Afganist\u00c3\u00a1n"},{"acronimo":"al","nom_pais":"Albania"},...
        break;
        case 'insert':
            if(isset($_POST['button'])){
                if(!validar_cookie() && !query_usuario($_POST['usuario'])){
                    insert($_POST['usuario'],$_POST['p10'],$_POST['p8'],$_POST['p6'],$_POST['p4'],$_POST['p2'],$_POST['comentario']);
                }else{
                    $msg='El usuario existe o anteriormente has votado';
                }
                printf($msg);
            }
        break;
        case 'pais_residencia':
            if(isset($_POST['lat']) && isset($_POST['lon'])){
                printf(pais_residencia($_POST['lat'],$_POST['lon']));
            }
    }
}

function pais_residencia($lat,$lon){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    
    if($lat != 'null' && $lon != 'null'){
        curl_setopt($curl, CURLOPT_URL, 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&zoom=3&lat='.$lat.'&lon='.$lon);
    }else{
        $ip = $_SERVER["REMOTE_ADDR"]; $ip = '89.141.236.122'; //ip de testeo.
        curl_setopt($curl, CURLOPT_URL, 'http://ip-api.com/json/'.$ip.'?fields=16387&lang=es');
    }    
    $resp = curl_exec($curl);
    curl_close($curl);
    $resp = json_decode($resp,true);
    
    if(isset($resp['address'])){
        $txt = $resp['address']; //si es el primer caso- >lat/long {"country":"Espa\u00f1a","country_code":"es"}
    }else if(isset($resp['country'])){ //si es el segundo caso -> ip
        $txt = ['country' => $resp['country'],
                'country_code' => strtolower($resp['countryCode'])
                ]; 
    }else{
        $txt = 'error';
    }
    return json_encode($txt);
}   
   

?>

