<?php 
session_start();
require 'asset/lib/validate_session.php';
?>

<html>
  <head>
    <title>AppWeb Votación</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="asset/css.css">    
  </head>
  <body class="container-fluid bg-dark" onload="init()" id='resultados'>
        <header class="row pt-1 pb-1 pl-5 pr-5 justify-content-between  align-items-center">
            <img src="asset/img/logo.png" alt="logo" srcset="asset/img/logo.svg">
            <span ><?php printf($_SESSION['Name']); ?><img src="asset/img/emoji/<?php printf($_SESSION['Emoji']); ?>.png"></span>
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
              <div class="col-md-6" id="comentarios">
              
              </div>
            </div>
        </div>
        <footer class="fixed-bottom row" id='foot'>
              <span class=" col-12 text-center text-muted">Copyright 2020 Ismael, Rodrigo y Eduardo</span>
        </footer>
   </body>
   
   <!--Dependencias js-->
   <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
   <script src="https://code.highcharts.com/highcharts.js"></script>
   <script src="https://code.highcharts.com/modules/data.js"></script>
   <script src="asset/js.js"></script>
   <script type="application/javascript">
       
   function init(){
       asinc(gParticipacion,'','participacion.php');
       asinc(tComentarios,'','comentarios.php');
       setInterval(function(){
           asinc(gParticipacion,'','participacion.php');
           asinc(tComentarios,'','comentarios.php');
       },30000);
       gResult();      
   }      
    </script>
    </body>
</html>
