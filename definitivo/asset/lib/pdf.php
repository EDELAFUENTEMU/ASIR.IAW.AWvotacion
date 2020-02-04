<?php 

use Spipu\Html2Pdf\Html2Pdf;
          
$uri_logo=realpath('../lib/ssl/logo_black.png');
    $uri_firma=realpath('../img/ajax.png');
    $uri_cert=realpath('../lib/ssl/ACwebAppVotacion.crt');
    $uri_key=realpath('../lib/ssl/kAC.key');
echo $uri_logo;

function createPDF($fullname,$fecha_voto,$hash,$p12,$p10,$p8,$p6,$p4,$p2){
    setlocale(LC_TIME, "es_ES");     
    require_once 'vendor/autoload.php';

    $fecha = strftime("%d de %B de %Y");;
    $fecha_voto = strftime("%d de %B de %Y a las %H:%M:%S",$fecha_voto);

    global $mdb;
    $grupos = array();
    foreach($mdb->query('SELECT * FROM `grupos`') as $row){
        $grupos[$row['gid']]=$row['proyecto'];
    }
    
    $doc="<cert
        src='/var/www/html/eu/ASIR.IAW.MerryChristmas/definitivo/asset/lib/ssl/ACwebAppVotacion.crt'
        privkey='/var/www/html/eu/ASIR.IAW.MerryChristmas/definitivo/asset/lib/ssl/kAC.key'
        name='IT AWVotacion'
        location='ESP'
        reason='Escrutinio'
        contactinfo='isma@rodrido.eduardo'
    >
    <div style='margin:auto; width:100%; margin-bottom:22px;'>
        <img src='/var/www/html/eu/ASIR.IAW.MerryChristmas/definitivo/asset/lib/ssl/logo_black.png' alt='logo' >
        </div>
        <div style='display:block; text-align:center; border:1px solid black; padding:3px; margin-bottom:45px;'>
            <h2>Certificado de Voto</h2>
        </div>
        <div style='width:80%; margin:auto;'>
            <p>D. Ismael Diez Robles con DNI 12345678Z,  responsable del departamento de escrutinio del proyecto AppWeb Votación del IES Virgen de la Paz<h1>certifica:</h1></p>

            <p>Qué D./Dª. $fullname el día $fecha_voto realizo la siguiente votación:</p>
            <ul>
                <li>12 Puntos: $grupos[$p12]  </li>
                <li>10 Punto: $grupos[$p10]  </li>
                <li>8 Punto: $grupos[$p8] </li>
                <li>6 Punto: $grupos[$p6] </li>
                <li>4 Punto: $grupos[$p4] </li>
                <li>2 Punto: $grupos[$p2]  </li>
            </ul>
            <p>Siendo el identificado único de voto: <b>$hash</b></p>
            <p>Esta certificación se expide a solicitud del participante y a efectos de revisión del escrutinio.</p>
            <div style='text-aling:right; display:block'><p style='text-align:right'>En Alcobendas, a $fecha .</p></div>
            <img src='/var/www/html/eu/ASIR.IAW.MerryChristmas/definitivo/asset/lib/ssl/firma.jpg' style='float-right'>
        </div>
        <div style='margin-top:150px;padding-top:3px;border-top:1pz solid black;font-size:12;color:gray;'>
            <span><i><b>Documento firmado dígitalmente</b><br>Puede garantizar la autenticidad del presente documento a través de la firma dígital incrustada o en su defecto, en la siguiente url: https://implantaciondeapliacionesweb.000webhostapp.com/CSV/$hash</i></span> 
        </div>
    </cert>";

    $html2pdf = new Html2Pdf();
    $html2pdf->writeHTML($doc);
    $html2pdf->output(__DIR__.'/CSV/'.$hash.'.pdf','F');
}


?>