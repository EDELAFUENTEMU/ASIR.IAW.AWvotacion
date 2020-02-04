//funciones pag_index
function send_index(){//index
        var form=document.getElementsByTagName('form')[0];
        //recapcha-se ocupa del spam 
        grecaptcha.execute('6Ld_k9AUAAAAAHAQgGQSPXnOzh5x6VoCNp0Ku7aj', {action: 'homepage'}).then(function(token) {
            if(token==''){
                alert('error conexion recapcha');
            }else{
                document.getElementsByName('token')[0].value=token ;
                form.submit();
            }
        });
}

//funciones pag autentificacion
function send_pin(){
    var form = document.getElementsByTagName('form')[0];
    //recapcha-se ocupa del spam 
    grecaptcha.execute('6Ld_k9AUAAAAAHAQgGQSPXnOzh5x6VoCNp0Ku7aj', {action: 'homepage'}).then(function(token) {
        document.getElementsByName('token')[0].value=token;
        form.submit();
    });
            
}

function enableSubmit(){
   document.getElementsByName('pin')[0].disabled=false;
   document.getElementsByName('button')[0].disabled=false;
}
function getPin(type){
   asinc(msg,'type='+type,'pin.php');
   document.getElementById('rEmail').innerHTML='Reenviar por eMail';
   document.getElementById('rPhone').innerHTML='Reenviar por Whatsapp';
   enableSubmit();
}
function msg(json){
    //pendiente > Leer json y sacar bien si el status 1
       alert(json);
}

//****************************************
//funciones pag votacion

//evita que selecciones varias veces un mismo grupo o que selecciones a tu gid
function validar_opciones(id,gid){

       //Genero un array con todos los grupos seleccionados
        var grupos_seleccionados = new Array(); 
        var items = Array(12,10,8,6,4,2);
        for (var i of items){        
                grupos_seleccionados.push(document.getElementById("p"+i).value); 
        }

        //Creo un objeto, de manera que quito los valores repetidos
        objGrupos = new Set(grupos_seleccionados); 
        if(grupos_seleccionados.length != objGrupos.size){ //Si todos los seleccionados son distintos, la longitud de ambos debera ser
            document.getElementById(id.target.name)[0].selected = true;
            alert('Ya has seleccionado anteriormente ese grupo! Un voto por grupo. Gracias');
        }

        //compruebo que no coincida con el voto a su grupo
        if(document.getElementById(id.target.name).value == gid ){
            document.getElementById(id.target.name)[0].selected = true;
            alert('No puedes votar a tu propio grupo.');
        }
};    

function comentario(){
    function placeholder(){
        var element = document.getElementById('p12');
        var opcion = element.options[element.selectedIndex].text;
        var opcion = opcion.split(':');
        var msg = '¿Por qué has elegido "'+opcion[0]+'" de '+opcion[1]+' como mejor proyecto?';
        //if(element.value!=<?php echo $_SESSION['gid'] ?>){
            document.getElementById('comentario').placeholder=msg;
        //}
    }
    document.getElementById('p12').addEventListener('change',function(){comentario()()});
    return placeholder;
}

function participacion(data){
      data = JSON.parse(data);
      var element = document.getElementById('progressbar');
      element.style.width=data.participacion+'%';
      element.innerHTML='Participación del '+data.participacion+'%';
}

//******************
//funciones pag resultados

function gResult(){
        Highcharts.chart('s_resultados', {
            chart: {
                type: 'bar',
                height: 500,
                backgroundColor:null
            },
            title: {
                text: ''
            },
            data: {
                csvURL: 'http://server.asir/eu/ASIR.IAW.MerryChristmas/definitivo/asset/resource/puntuacion.php',
                enablePolling: true,
                dataRefreshRate: 1
            },
            plotOptions: {
                pie: {
                    colorByPoint: true,
                    startAngle: -90,
                    endAngle: 90,
                    center: ['50%', '60%'],
                    size: '80%'
                },
                series: {
                    dataLabels: {
                        enabled: true,
                        format: '{point.y:.0f} pto.',
                        style: {
                            fontWeight: 'bold',
                            color: 'white'
                        }
                    }
                }
            },
            tooltip: {
                valueDecimals: 1,
                valueSuffix: '%'
            },
            xAxis: {
                type: 'Grupos',
                labels: {
                    style: {
                        fontSize: '10px',
                        color: 'white'
                    }
                }
            }
        });

}

//tabla de comentarios
function tComentarios(data){
    data = JSON.parse(data);
    console.log(data);
     var comentarios='<div class="col-12 th border-bottom">Panel de Comentarios</div>';
     Object.keys(data).forEach(function(index,value){
          var fecha = new Date (data[index].date);
          var dd = fecha.getDate();
          var MM = fecha.getMonth()+1; 
          var hh = fecha.getHours();
          var mm = fecha.getMinutes();	
          comentarios += "<div class='col-12 border-bottom comentario'>"+
              '<img src="asset/img/emoji/'+data[index].emoji+'.png">'+
              "<span class='font-weight-light font-italic'>"+dd+"/"+MM+"/20 "+hh+":"+mm+"  ID: #"+data[index].hash+"#</span><br><p style='text-indent: 2em;'>"+data[index].comentario+"</p></div>"
     })
     var element = document.getElementById('comentarios').innerHTML = comentarios;
}

//grafica de participación
function gParticipacion(data){
      data = JSON.parse(data);
      var element = document.getElementById('progressbar');
      element.style.width=data.participacion+'%';
      element.innerHTML='Participación del '+data.participacion+'%';
}



//******************
//funciones comuntes
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
    ajax.open('POST', "asset/resource/"+destination, true);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(parametros);
}

function control_errores(){
	var url = window.location.search;
	if (/^\?error=[^0]/.test(url)) {        
        var nodo = document.createElement('div');
        nodo.innerHTML='<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error!</strong> '+decodeURIComponent(url.slice(7))+'.<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
        nodo.setAttribute('id','alert');
        document.getElementsByTagName('body')[0].insertBefore(nodo,document.getElementsByTagName('body')[0].firstElementChild);
       
	}
}
document.addEventListener("DOMContentLoaded", control_errores);