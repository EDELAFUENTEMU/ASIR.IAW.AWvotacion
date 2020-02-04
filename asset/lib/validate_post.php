<?php 
function validate_post($data){
    $pattern=array( 'name'=>'/^[a-zA-ZÀ-ÿ ]+$/',
                    'id'=> '/^[a-zA-Z0-9\.]+$/',
                    'token' => '/.+/',
                    'action' => '/^homepage$/',
                    'type' => '/^(phone|email)$/',
                    'pin' => '/^[0-9]{6}$/',
                    'p12' => '/^[0-9]+$/',
                    'p10' => '/^[0-9]+$/',
                    'p8' => '/^[0-9]+$/',
                    'p6' => '/^[0-9]+$/',
                    'p4' => '/^[0-9]+$/',
                    'p2' => '/^[0-9]+$/',
                    'comentario' => '/^[A-Za-z0-9À-ÿ,\.-_ ]{0,255}$/',
                    'emoji' => '/^[0-9]+$/'
    );
    foreach ($data as $value){
        if(!isset($_POST[$value]) || !preg_match($pattern[$value],$_POST[$value])){
            return false;
        }
    }
    return true;
}
?>