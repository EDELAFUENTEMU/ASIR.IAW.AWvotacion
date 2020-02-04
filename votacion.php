<?php 

session_start();
require 'asset/lib/validate_session.php';
require 'asset/lib/validate_post.php';
require 'asset/lib/conexion_db.php';
require 'asset/lib/pdf.php';
require 'asset/lib/mail.php';
$mdb = mdb(); 




$keys = ['p12', 'p10', 'p8', 'p6', 'p4', 'p2', 'comentario','emoji'];

///estoy recibiendo todos los datos por post
if(validate_post($keys)){ 
    $points = ['p12', 'p10', 'p8', 'p6', 'p4', 'p2'];
    $vPoints = array();
    foreach ($points as $index){
        $vPoints[] = $_POST[$index];
    }

    if(count($points) != count(array_unique($vPoints))){
        header('location: votacion.php?error=no%20puede%20votar%20a%20un%20grupo%20m%C3%A1s%20de%20una%20vez');
        exit;
    }elseif(in_array($_SESSION['gid'],$vPoints)){
        header('location: votacion.php?error=no%20puede%20votarse%20a%20si%20mismo');
        exit;
    }else{//no hay valores repetidos ni vota a su equipo
        try{
            global $mdb;
            
            //el usuario ya voto?
            $sql='SELECT voted FROM user WHERE user="'.$_SESSION['Name'].'" and id_correo="'.$_SESSION['IDmail'].'"and 1="'.$_SESSION['pin'].'" LIMIT 1';
            foreach($mdb->query($sql) as $row){
                if($row['voted']){ //ya voto
                    header('location: resultados.php?error=Nos%20consta%20que%20usted%20ya%20voto');
                    exit;
                }
            }            
            
            $mdb->beginTransaction();    
            $sql = $mdb->prepare('INSERT INTO votos(hash, p12, p10, p8, p6, p4, p2, comentario, emoji) VALUES (:hash , :p12 , :p10 , :p8 , :p6 , :p4 , :p2 , :comentario , :emoji)');
            
            $hash='#';
            foreach ($keys as $key){
                $sql->bindParam(':'.$key , $_POST[$key]);
                $hash .= $_POST[$key].'#';
            } 
            $hash =md5($hash.time());
            $sql->bindParam(':hash' ,$hash );

            $voto = $sql->execute(); 
            $uVoted = $mdb->exec('UPDATE `user` SET `voted`= 1 WHERE `id_correo`="'.$_SESSION['IDmail'].'"'); 
            $mdb->commit();
            
            if( $voto==true && $uVoted == 1 ){
               //se genera el certificado pdf 
                createPDF($_SESSION['Name'],time(),$hash,$_POST['p12'],$_POST['p10'],$_POST['p8'],$_POST['p6'],$_POST['p4'],$_POST['p2']);
                
                $path=realpath('./asset/lib/CSV/'.$hash.'.pdf');            
                if(file_exists($path)){
                
                    $result = _mail($_SESSION['IDmail'],$_SESSION['Name'],'','cert',$path);
                    
                    $result =  json_decode($result,true);
                    if($result['status']){
                        header('location: resultados.php?error=Su%20voto%20se%20ha%20contabilizado%20correctamente.%20Puede%20encontrar%20su%20justificante%20de%20voto%20en%20su%20correo%20electr%C3%B3nico.%20');
                        exit;
                    }else{
                        header('location: resultados.php?error='.$result['$content']);
                        exit;
                    }
                }else{
                    header('location: resultados.php?error=Su%20voto%20se%20ha%20contabilizado%20correctamente.%20Puede%20encontrar%20su%20justificante%20de%20voto%20en%20su%20correo%20electr%C3%B3nico.%20');
                    exit; 
                }
                
            }else{
                header('location: votacion.php?error=Error%20al%20procesar%20su%20votaci%C3%B3n.%20Intentelo%20de%20nuevo');
                exit;
            }
            
        }catch (PDOException $e) { //error en la ejecución
            header('location: votacion.php?error=q'.$e->getMessage());   
            exit;
        }
    }
}


//obtiene los grupos
function options_group(){
    global $mdb;
    $options_group='<option value="'.rand().'" selected disabled hidden>Seleccione un grupo</option>';
    $sql='SELECT gid, proyecto FROM `grupos` WHERE 1';
    foreach ($mdb->query($sql) as $row) {
        $options_group.="<option value='".$row["gid"]."'>".$row["proyecto"]."</option>";
    }
    return $options_group ;
}

?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="asset/css.css">
    <title>Votación</title>
  </head>
  <body class="container-fluid" onload="init();" id='votos'>
        <header class="fixed-top" >
            <div class="row pl-5 pr-5 pt-1 pb-1 justify-content-between align-items-center">
            <img src="asset/img/logo.png" alt="logo" srcset="asset/img/logo.svg">
            <span class="text-white" ><?php printf($_SESSION['Name']); ?><img src="asset/img/emoji/<?php printf($_SESSION['Emoji']); ?>.png"></span>
            </div>
            <div class="row progress fixed-top" style="margin-top:65px;" >
                  <div class="progress-bar progress-bar-success pl-0" role="progressbar" aria-valuenow="40"
                  aria-valuemin="0" aria-valuemax="100" style="width:0%" id="progressbar">
                  </div>
            </div>
        </header>
        <div class="container mt-5">
            <div class="row">
              <article class="col-sm-6">
                  <h4 class="display-4">Proceso de votación</h4>
                  <p>Estimado/a <?php  printf($_SESSION['Name']); ?> va usted a proceder a votar. <br> Siguiendo los criterios explicados por la profesorea, ordene de menor a mayor los grupos atendiendo a su puntuación, <br> Recuerde:
                  <ul>
                      <li>No puede votarse a si mismo.</li>
                      <li>El voto es anónimo. No así los comentarios (son públicos) </li>
                      <li>Una vez efectuado el voto, no puede modificar su elección ni volver a votar</li>
                      <li>Puede garantizar que su voto a sido contabilizado mediante su id de voto en la siguiente pantalla</li>
                      <li>La votación es en tiempo real</li>
                  </ul>
                  </p> 
              </article>
              <div class="col-sm-6 fill">
                 <form name="formulario" action="" method="post">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">12 point</span>
                            </div>
                            <select name="p12" id="p12" required class="form-control"><?php echo options_group() ?></select>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">10 point</span>
                            </div>
                            <select name="p10" id="p10" required class="form-control"><?php echo options_group() ?></select>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">8 point </span>
                            </div>
                            <select name="p8" id="p8" required class="form-control"><?php echo options_group() ?></select>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">6 point </span>
                            </div>
                            <select name="p6" id="p6" required class="form-control"><?php echo options_group() ?></select>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">4 point </span>
                            </div>
                            <select name="p4" id="p4" required class="form-control"><?php echo(options_group()); ?></select>
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">2 point </span>
                            </div>
                            <select name="p2" id="p2" required class="form-control"><?php echo(options_group()); ?></select>
                        </div>
                        <input type="hidden" name="emoji" value="<?php printf($_SESSION['Emoji']) ?>">
                        <textarea name="comentario" class="form-control mb-3" id="comentario" rows="3" placeholder="Comentario..." pattern="^[A-Za-z0-9À-ÿ\u00f1\u00d1,\.-_ ]{0,255}$"></textarea>
                        <button type="submit" name='button' class="btn btn-primary mb-2 col-12" value="submit">Enviar Voto</button>
                </form>
            </div>
          </div>
          <footer class="fixed-bottom row" id='foot'>
              <span class=" col-12 text-center text-muted">Copyright 2020 Ismael, Rodrigo y Eduardo</span>
          </footer>
          
          
          
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script src="asset/js.js"></script>
    <script type="application/javascript">
        function init(){
            comentario();
            setInterval(function(){
                asinc(participacion,'','participacion.php');
            },10000);
            for (var i of [12,10,8,6,4,2]){        
                document.getElementById('p'+i).addEventListener('change',function(){validar_opciones(event,<?php echo $_SESSION['gid']; ?>);});
            }
        }
    </script>
    </body>
</html>
