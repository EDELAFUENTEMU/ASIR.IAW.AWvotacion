<?php 
 if(isset($_GET['spam']) && $_GET['spam']==true){
     printf('No has pasado los filtros antiSpam. Por favor, vuelva a intentarlo despues de un rato');
 }


/*config_key*/
$servidor = "localhost";
$usuario = "server";
$password = "Server01%";
$db = "iaw_navidad";


//conexion bbdd
$mbd = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $password);  
$mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//opciones de usuarios
$options='';
foreach ($mbd->query('SELECT user FROM user') as $row) {
    $options.="<option value='".$row["user"]."'>".$row["user"]."</option>";
}


?>
 <html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="css.css">
    <title>WebApp Votación Proyectos ASIR 19/20</title>
    <style>
        
        
    </style>
  </head>
  <script src="https://www.google.com/recaptcha/api.js?render=6Ld_k9AUAAAAAHAQgGQSPXnOzh5x6VoCNp0Ku7aj"></script>
  <script>
    //envia los datos del formulario
    function send(){
        var form=document.getElementsByTagName('form')[0];
        var name=document.getElementsByName('name')[0].value;
        var pw=document.getElementsByName('pw')[0].value;
        var regex_name=/^[a-zA-Z ]*$/;
        var regex_id=/^[a-zA-Z]*\.[a-zA-Z0-9]*$/;
        
        if(regex_name.test(name) && regex_id.test(pw)){
            //recapcha-se ocupa del spam 
            grecaptcha.execute('6Ld_k9AUAAAAAHAQgGQSPXnOzh5x6VoCNp0Ku7aj', {action: 'homepage'}).then(function(token) {
                document.getElementsByName('token')[0].value=token ;
                form.submit();
            });
        }
    }
   </script>
    
  <body>
     <div class="container-fluid m-0 p-0">
         <header class=" fixed-top row pt-1 pb-1 pl-5 justify-content-between" >
            <img src="img/logo.png" alt="logo" srcset="img/logo.svg">
            <!--<span>Proyecto IES Virgen de la Paz</span>-->
         </header>
         <div class="row justify-content-center align-items-center">
            <form class="col-sm-4 bg-light p-4 rounded shadow-lg" method="post" action="authentication.php">
              <h5 class=" display-4 border-bottom">Acceso</h5>
              <div class="form-group input-group">
                <div class="input-group-prepend"><div class="input-group-text">Usuario:</div></div>
                <select class="form-control" name="name">
                    <?php printf($options); ?>
                </select>
             </div>
             <div class="form-group">
               <div class="input-group">
                <div class="input-group-prepend"><div class="input-group-text">ID AulaVirtual:</div></div>
                <input type="text" class="form-control" id="password" name='pw' placeholder="juan.manuel2" value='eduardo.fuente2' ?>
               </div>
               <small class="form-text text-muted text-right"><b>juan.manuel2</b>@educa.madrid.org</small>
            </div>
              <input type="hidden" name="token">
              <input type="hidden" name="action" value="homepage">
              <button type="button" class="btn btn-primary" name="button" onclick="send()">Acceder</button>
              <br><a href="" class="form-text text-right">Acceder con certificado dígital</a>
             </form>
         </div>
         
        <footer class="fixed-bottom">
            <div id="tecnologias" class="p-1 row justify-content-center">
                 <ul class="m-0">
                    <li class="col-auto"><img src="img/bootstrap.png"></li>
                    <li class="col-auto"><img src="img/ajax.png"></li>
                    <li class="col-auto"><img src="img/highchart.png"></li>
                    <li class="col-auto"><img src="img/html5.png"></li>
                    <li class="col-auto"><img src="img/css.png"></li>
                    <li class="col-auto"><img src="img/openstreetmap.png"></li>
                 </ul>
             </div>
             <!--<div class="row justify-content-center">
                <small class="text-light">with power</small>
             </div>-->
            <div class="row" id="foot">
                <span class=" col-12 text-center text-muted">&copy 2020 AppWeb Votacion por Ismael, Rodrigo y Eduardo</span>
            </div>
        </footer>
      </div>
  </body>
</html>