<?php 
$servidor = "localhost";
$usuario = "server";
$password = "Server01%";
$db = "iaw_navidad";

$hash='#';
$flag=false;

//¿Inicio sesion previamente?
session_start();
if(!isset($_SESSION['IDmail'])){ 
    //careces de sid=> han entrado de chiripa -> fuera
    //header('location: index.php?sid=false');   
    //exit;
    $_SESSION['IDmail']='eduardo.fuente2';
}

//recopila los datos de la votacion
if(isset($_POST['button'])){
  try{
    //conexion bbdd
    $mbd = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $password);  
    $mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $mbd->beginTransaction();
    
    $sql = $mbd->prepare('INSERT INTO votos(hash, p10, p8, p6, p4, p2, comentario) VALUES (:hash , :p10 , :p8 , :p6 , :p4 , :p2 , :comentario )');
    
    //recorro los valores q recogo del formulario
    $keys = array('p10', 'p8', 'p6', 'p4', 'p2', 'comentario');
    foreach ($keys as $key){
        if(isset($_POST[$key])){
            $sql->bindParam(':'.$key , $_POST[$key]);
            $hash .= $_POST[$key].'#';
        }else{
            $flag=true;
        }
    }
    
    $hash =md5($hash);
    $sql->bindParam(':hash' ,$hash );
        
    if(!$flag){//no hay ningun error -> ejecuta
        $sql->execute(); 
        $mbd->exec('UPDATE `user` SET `voted`= 1 WHERE `id_correo`="'.$_SESSION['IDmail'].'"'); 
    }
    $mbd->commit();
 } catch (PDOException $e) { //error en la ejecución
    //header('location: index.php?error=false?e'.$e->getMessage());   
    //exit;
    echo $e->getMessage();
 }
}
?>

<html>
  <head>
    <title>AppWeb Votación</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <style>
        body, html { padding: 0; margin: 0; }
        body{
            min-height: 100vh; 
            /*background-image: url(img/bg.jpg);*/
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: white;
        }
        ul > li > img{
            height: 60px; width: 60px;
        }
        header{ background-color:  rgba(0, 0, 0, 0.2) }
        footer{ background-color: #001739d9 !important }
        
    </style>
    
  </head>
  <body class="container-fluid bg-dark" onload="init()">
        <header class=" row pt-3 pb-3 justify-content-between">
            <img src="img/logo.svg" alt="triangle with equal sides">
        </header>
        <div class="row progress shadow"> <!-- barra de participacion -->
              <div class="progress-bar progress-bar-success pl-0" role="progressbar" aria-valuenow="40"
              aria-valuemin="0" aria-valuemax="100" style="width:0%" id="progressbar">
              </div>
        </div>
        <div class="container mt-5">
            <div class="row align-items-start">
              <div class="col-md-6 ">
                  <div class="display-4 border-bottom">Resultados preliminares</div>
                  <div id="s_resultados"></div>
              </div>
              <div class="col-md-6 ">
                  <table id="comentarios" class="table table-hover"></table>   
              </div>
            </div>
        </div>
        <footer class="fixed-bottom row"><span class=" col-12 text-center text-muted">Copyright 2020 Ismael, Rodrigo y Eduardo</span></footer>

   </body>
   
   <!--Dependencias js-->
   <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
   <script src="https://code.highcharts.com/highcharts.js"></script>
   <script src="https://code.highcharts.com/modules/data.js"></script>
   
   <script type="application/javascript">
   function init(){
       asinc(gParticipacion,'','participacion.php');
       asinc(tComentarios,'','comentarios.php');
       setInterval(function(){
           asinc(gParticipacion,'','participacion.php');
           asinc(tComentarios,'','comentarios.php');
       },12000);
       gResult();
       
   }
   function gResult(){
          Highcharts.chart('s_resultados', {

            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false,
                type: 'pie',
                backgroundColor:null
            },
            title: {
                text: 'Browser<br>shares<br>2017',
                align: 'center',
                verticalAlign: 'middle',
                y: 60
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: true,
                        distance: -50,
                        style: {
                            fontWeight: 'bold',
                            color: 'white'
                        }
                    },
                    startAngle: -90,
                    endAngle: 90,
                    center: ['50%', '75%'],
                    size: '110%'
                }
            },
            data: {
                csvURL: 'http://server.asir/eu/ASIR.IAW.MerryChristmas/Servidor/puntuacion.php',
                enablePolling: true,
                dataRefreshRate: 1
            }
        });
   }
     
    //tabla de comentarios
    function tComentarios(data){
        data = JSON.parse(data);
        console.log(data);
         var table='<th scope="col">Fecha</th><th scope="col">Hash</th><th scope="col">Voto</th>';
         Object.keys(data).forEach(function(index,value){
              table += "<tr><td>"+data[index].date+"</td><td>"+data[index].hash+"</td><td>"+data[index].comentario+"</td></tr>"
         })
         var element = document.getElementById('comentarios').innerHTML = table;
    }
       
    //grafica de participación
    function gParticipacion(data){
          data = JSON.parse(data);
          var element = document.getElementById('progressbar');
          element.style.width=data.participacion+'%';
          element.innerHTML='Participación del '+data.participacion+'%';
    }
       
    //Se encarga de hacer las peticiones asincronas.
    function asinc(callback,parametros,destination){
        if (window.XMLHttpRequest) {
            ajax = new XMLHttpRequest();
         } else {
            ajax = new ActiveXObject("Microsoft.XMLHTTP");// code for old IE browsers
        }
        ajax.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                callback(this.responseText);
            }
        }
        ajax.open('POST', "../servidor/"+destination, true);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(parametros);
    }
        
        
                
 
    </script>
    </body>
</html>
