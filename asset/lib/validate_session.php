<?php 
if(!isset($_SESSION['Name'])){
    header('location: index.php?error=Has%20intentado%20acceder%20a%20un%20recurso%20protegido');
    exit;
}
?>