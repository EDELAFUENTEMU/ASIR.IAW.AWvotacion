<?php 

require 'asset/lib/conexion_db.php';
require 'asset/lib/validate_post.php';
require 'asset/lib/recapcha.php';
$mbd = mdb(); 

function authentication($name,$id){
    // El usuario existe?
    global $mbd; 
    $sql='SELECT gid, tlf, voted FROM user WHERE user="'.$name.'" and id_correo="'.$id.'" LIMIT 1';
    $result = $mbd->query($sql);
    if(count($result->fetchAll(PDO::FETCH_ASSOC)) > 0){
        
        // El usuario ha votado?
        foreach ($mbd->query($sql) as $row) {
            session_start();
            $_SESSION['IDmail']=$id;
            $_SESSION['Name']=$name;
            $_SESSION['Emoji']=rand(1,6);
            $_SESSION['tlf']=$row['tlf'];
            $_SESSION['gid']=$row['gid'];

            if($row['voted']){ 
                //el usuario ya voto -> estadisticas
                header('location: resultados.php?error=0');
                exit;
            }else{
                //el usuario existe y no ha votado -> autentificacion -> pin
                header('location: authentication.php?error=0'); 
                exit;
            }
        }
    }else{
        header('location: index.php?error=El%20usuario%20o%20contrase%C3%B1a%20no%20es%20v%C3%A1lido');
        exit;
    }
}

function main(){
    $post = array('name','id','token','action');
    // Si recibo datos por post y paso la prueba de spam -> intento autentificarme
    if(validate_post($post) && recapcha($_POST['token'],$_POST['action'])){ 
        authentication($_POST['name'],$_POST['id']);
    }
}
main();


function options (){ //select -> opciones de usuarios
    global $mbd;
    $options='';
    foreach ($mbd->query('SELECT user FROM user ORDER BY user') as $row) {
        $options.="<option value='".$row["user"]."'>".$row["user"]."</option>";
    }
    return $options;
}

?>
 <html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="asset/css.css">
    <title>WebApp Votación Proyectos ASIR 19/20</title>
  </head>
  <script src="https://www.google.com/recaptcha/api.js?render=6Ld_k9AUAAAAAHAQgGQSPXnOzh5x6VoCNp0Ku7aj"></script>
  <script src="asset/js.js"></script>
  <script>
      window.addEventListener("load", function(){
          var form=document.getElementsByTagName('form')[0]; 
          form.addEventListener("submit",function(event){
              event.preventDefault();
              send_index();
          });
      });
  </script>

  <body>
     <div class="container-fluid m-0 p-0">
         <header class=" fixed-top row pt-1 pb-1 pl-5 justify-content-between" >
            <img src="asset/img/logo.png" alt="logo" srcset="asset/img/logo.svg">
         </header>
         <div class="row justify-content-center align-items-center">
            <form class="col-sm-4 bg-light p-4 rounded shadow-lg" method="post" action="" id="form_index" onsubmit="send_index();">
              <h5 class=" display-4 border-bottom">Acceso</h5>
              <div class="form-group input-group">
                <div class="input-group-prepend"><div class="input-group-text">Usuario:</div></div>
                <select class="form-control" name="name" required>
                    <option selected disabled hidden value="">Seleccione un usuario</option>
                    <?php printf(options()); ?>
                </select>
             </div>
             <div class="form-group">
               <div class="input-group">
                <div class="input-group-prepend"><div class="input-group-text">ID AulaVirtual:</div></div>
                <input type="text" class="form-control" id="password" name='id' placeholder="nombre.apellido" pattern="^[a-zA-Z0-9\.]*$" required ?>
               </div>
               <small class="form-text text-muted text-right"><b>nombre.apellido</b>@educa.madrid.org</small>
            </div>
              <input type="hidden" name="token">
              <input type="hidden" name="action" value="homepage">
              <button type="submit" class="btn btn-primary" name="button">Acceder</button>
             </form>
         </div>
         
        <footer class="fixed-bottom">
            <div id="tecnologias" class="p-1 row justify-content-center">
                 <ul class="m-0">
                    <li class="col-auto"><img src="asset/img/bootstrap.png" alt="BootStrap" title="BootStrap"></li>
                    <li class="col-auto"><img src="asset/img/ajax.png" alt="AJAX" title="AJAX"></li>
                    <li class="col-auto"><img src="asset/img/highchart.png" alt="Highchart" title="Highchart"></li>
                    <li class="col-auto"><img src="asset/img/html5.png" alt="HTML5" title="HTML5"></li>
                    <li class="col-auto"><img src="asset/img/css.png" alt="CSS" title="CSS3"></li>
                    <li class="col-auto"><img src="asset/img/recaptcha.png" alt="Recaptcha" title="Recaptcha"></li>
                 </ul>
            </div>
            <div class="row" id="foot">
                <span class=" col-12 text-center text-muted">&copy 2020 AppWeb Votacion por Ismael, Rodrigo y Eduardo</span>
            </div>
        </footer>
      </div>
  </body>
</html>