<?php
session_start();
require 'asset/lib/validate_session.php';
require 'asset/lib/privatekey.php';
require 'asset/lib/validate_post.php';
require 'asset/lib/conexion_db.php';
require 'asset/lib/recapcha.php';


//si recibo un pin y el antispam lo pasa
if(validate_post(['pin','token','action']) &&  recapcha($_POST['token'],$_POST['action']) ){
    
      // ¿el pin es correcto?
        $mdb = mdb();
        $sql='SELECT COUNT(*) AS ROWS FROM user WHERE user="'.$_SESSION['Name'].'" and id_correo="'.$_SESSION['IDmail'].'" and pin="'.intval($_POST['pin']).'" and limit_PIN >= UNIX_TIMESTAMP() LIMIT 1';
        foreach($mdb->query($sql) as $row)
        if($row['ROWS'] == 0){
            //el pin es incorrecto -> mensaje de error 
            header('location: authentication.php?error=El%20PIN%20de%20acceso%20es%20incorrecto.%20Intentelo%20de%20nuevo%20o%20solicite%20uno%20nuevo');
            exit;
        }else{
           //el pin es correcto -> votacion
            $_SESSION['pin']=1;
            $e = "votacion.php?error=0&".$row['ROWS'];
            header("location: $e");
            exit;
        }  
}
?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>WebApp Votación Proyectos ASIR 19/20</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="asset/css.css">
    <script src="https://www.google.com/recaptcha/api.js?render=6Ld_k9AUAAAAAHAQgGQSPXnOzh5x6VoCNp0Ku7aj"></script>
    <script src="asset/js.js"></script>
 </head>
 <body>
     <div class="container-fluid m-0 p-0">
        <!-- header-->
         <header class=" fixed-top row pt-1 pb-1 pl-5 pr-5 justify-content-between  align-items-center">
            <img src="asset/img/logo.png" alt="logo" srcset="asset/img/logo.svg">
            <span ><?php printf($_SESSION['Name']); ?><img src="asset/img/emoji/<?php printf($_SESSION['Emoji']); ?>.png"></span>
         </header>
         
        <!-- panel informat-->
         <div class="row justify-content-center d-flex align-items-center" style="height:70vh" id='form'>
            <div class="col-sm-5 bg-light p-4 rounded shadow-lg">
              <h3 class=" display-4 border-bottom">PIN de Acceso Temporal</h3>
              <form method="post" action="">

                  <div class="form-group input-group">
                        <!--<div class="input-group-prepend"><div class="input-group-text">Correo:</div></div>-->
                        <input type="text" class="form-control col-9" name="email" readonly value='<?php printf($_SESSION['IDmail']."@educa.madrid.org"); ?>'>
                        <input type="text" class="form-control col-3" name="email" readonly value='<?php printf($_SESSION['tlf']); ?>'>

                  </div>
              

                  <p class=""><?php printf($_SESSION['Name']); ?>, por motivos de seguridad es preciso que indique el medio por donde decide recibir el pin de acceso temporal. 
                      <ul>
                      <li>El PIN enviado sera válido para los próximos 20 min.</li>
                      <li>Debe esperar 2 minutos antes de solicitar un nuevo PIN.</li>
                      </ul>
                  </p>
              

            
                 <input type="hidden" name="token">
                 <input type="hidden" name="action" value="homepage">
                 
                 <div class="form-group input-group">
                     <button type="button" class="btn btn-outline-primary col" id='rEmail' onclick="getPin('email');">Enviar por eMail</button>
                     <button type="button" class="btn btn-outline-primary ml-2 col-6" id='rPhone' onclick="getPin('phone');">Enviar por Whatsapp</button>
                 </div>         
                
                
                <div class="form-group input-group col-6 mx-auto">
                    <div class="input-group-prepend">
                        <div class="input-group-text">PIN</div>
                    </div>
                    <input type="text" class="form-control" name="pin" placeholder="" disabled pattern="^[0-9]{6}$">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-success" value="submit" name="button" disabled onclick="send_pin();">Acceder</button>
                    </div>
                </div>
                
                <a href="" onclick="alert('Contacte con su proveedor de soluciones para una óptima solución. xD');return false;">Ayuda! No recibo el correo.</a>
                
                <br><a href="#" onclick="enableSubmit();return false;">Tengo un código</a>
             </form>             
             
         </div>
     </div> 
     <footer class="fixed-bottom row" id="foot">
         <span class=" col-12 text-center text-muted">Copyright 2020 Ismael, Rodrigo y Eduardo</span>
     </footer> 
  </body>
</html>