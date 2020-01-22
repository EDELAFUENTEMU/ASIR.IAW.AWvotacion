<?php 
$servidor = "localhost";
$usuario = "server";
$password = "Server01%";
$db = "iaw_navidad";
$gid=1;//quitar en produccion

if(!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['pin'])){
    //han entrado de chiripa -> fuera
    header('location: index.php?datapost=false');   
    exit;
}else{
    $name = $_POST['name'];
    $email= str_replace('@educa.madrid.org','',$_POST['email']);
    $pin = intval($_POST['pin']);
}
//conexion bbdd
$mbd = new PDO("mysql:host=$servidor;dbname=$db;charset=utf8", $usuario, $password);  
$mbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ¿ha metido bien el pin?
$sql='SELECT gid FROM user WHERE user="'.$name.'" and id_correo="'.$email.'" and pin ="'.$pin.'"';
$result = $mbd->query($sql);
if($result->fetchColumn() == 0){
    //no existe ese usuario -> fuera
    header('location: index.php?pin=false');
    exit;
}else{
    foreach ($result as $row) {
        $gid = $row['gid'];
    }
}

//obtiene los grupos
function options_group(){
    global $mbd;
    $options_group='<option value="'.rand().'" selected disabled hidden>Seleccione un grupo</option>';
    $sql='SELECT gid, proyecto FROM `grupos` WHERE 1';
    foreach ($mbd->query($sql) as $row) {
        $options_group.="<option value='".$row["gid"]."'>".$row["proyecto"]."</option>";
    }
    return $options_group;
}

?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="css.css">
    <style>
        body#votos{
            background-image: none;
        }
    </style>
    <title>Votación</title>
  </head>
  <body class="container-fluid bg-dark" onload="init();" id='votos'>
        <header class="fixed-top" >
           <div class="row pl-5 pt-1 pb-1 justify-content-between">
            <img src="img/logo.png" alt="logo" srcset="img/logo.svg">
            <!--<span>Proyecto IES Virgen de la Paz</span>-->
            </div>
            <div class="row progress fixed-top" style="margin-top:65px;" >
                  <div class="progress-bar progress-bar-success pl-0" role="progressbar" aria-valuenow="40"
                  aria-valuemin="0" aria-valuemax="100" style="width:0%" id="progressbar">
                  </div>
            </div>
        </header>
        <div class="container mt-5">
            <div class="row align-items-center">
              <article class="col-sm-6">
                  <h4 class="display-4">Proceso de votación</h4>
                  <p>Estimado/a <?php echo $name; ?> va usted a proceder a votar. <br> Siguiendo los criterios explicados por la profesorea, ordene de menor a mayor los grupos atendiendo a su puntuación, <br> Recuerde:
                  <ul>
                      <li>No puede votarse a si mismo.</li>
                      <li>El voto es anónimo. No así los comentarios (son públicos) </li>
                      <li>Una vez efectuado el voto, no puede modificar su elección ni volver a votar</li>
                      <li>Puede garantizar que su voto a sido contabilizado mediante su id de voto</li>
                      <li>La votación es en tiempo real</li>
                  </ul>
                  </p> 
              </article>
              <div class="col-sm-6 fill bg-dark">
                 <form name="formulario" action="resultados.php" method="post">
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
                        <textarea name="comentario" class="form-control mb-3" id="comentario" rows="3" placeholder="Comentario..."></textarea>
                        <button type="submit" name='button' class="btn btn-primary mb-2 col-12" value="submit">Enviar Voto</button>
                </form>
            </div>
          </div>
          <footer class="fixed-bottom row" id='foot'>
              <span class=" col-12 text-center text-muted">Copyright 2020 Ismael, Rodrigo y Eduardo</span>
          </footer>
          
          
          
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <script type="application/javascript">
        function init(){
            comentario();
            for (x = 2; x<=10; x=x+2){ 
                document.getElementById('p'+x).addEventListener('change',function(){validar_opciones(event);});
            }
        }
        
        
        //evita que selecciones varias veces un mismo grupo o que selecciones a tu gid
        function validar_opciones(id){

               //Genero un array con todos los grupos seleccionados
                var grupos_seleccionados = new Array(); 
                for (i = 2; i<=10; i=i+2){ 
                    grupos_seleccionados.push(document.getElementById("p"+i).value); 
                }
                
                //Creo un objeto, de manera que quito los valores repetidos
                objGrupos = new Set(grupos_seleccionados); 
                if(grupos_seleccionados.length != objGrupos.size){ //Si todos los seleccionados son distintos, la longitud de ambos debera ser igual
                    document.getElementById(id.target.name)[0].selected = true;
                    alert('Ya has seleccionado anteriormente ese grupo! Un voto por grupo. Gracias');
                }
            
                //compruebo que no coincida con el voto a su grupo
                if(document.getElementById(id.target.name).value == <?php echo $gid ?>){
                    document.getElementById(id.target.name)[0].selected = true;
                    alert('No puedes votar a tu propio grupo.');
                }
        };    

        function comentario(){
            function placeholder(){
                var element = document.getElementById('p10');
                var opcion = element.options[element.selectedIndex].text;
                var msg = '¿Por qué has elegido '+opcion+' como mejor proyecto?';
                if(element.value!=<?php echo $gid ?>){
                    document.getElementById('comentario').placeholder=msg;
                }
            }
            document.getElementById('p10').addEventListener('change',function(){comentario()()});
            return placeholder;
        }
        
        function participacion(data){
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
        asinc(participacion,'','participacion.php');
        
    </script>
    </body>
</html>
