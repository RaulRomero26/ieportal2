<?php
    require_once 'config/config.php';
    require_once 'libraries/FormValidator.php';
    require_once 'libraries/Base.php';
    require_once 'libraries/Controller.php';
    require_once 'libraries/Core.php';

    //librarie de PDF
    require_once('libraries/fpdf.php');
    //libreria MPDF
    require_once(BASE_PATH.'public/vendor/autoload.php');
    require_once('libraries/MY_PDF.php');
    /*Se añade esta nueva libreria para usar los PDF de vehiculo*/
    require_once(BASE_PATH.'public/vendor/setasign/fpdi/src/autoload.php');
    /* Se comenta para que pueda funcioanr el PDF de vehiculos
    spl_autoload_register(function($nameClass){
        require_once 'libraries/' . '$nameClass' . '.php';
    });*/
?>