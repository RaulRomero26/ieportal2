<?php
    
    define('app_path', dirname(dirname(__FILE__)));     //Ruta de la app
    define('BASE_PATH', realpath(dirname(__FILE__) . '/../..').'/'); //BASE_PATH del proyecto
    define('base_url', 'http://localhost/ieportal2/'); //Ruta de la url

    define('site_name', 'IE Portal');       //Nombre del sitio

    //Configuración de acceso a la base de datos
    define ('DB_HOST', 'localhost');
    define ('DB_USER', 'root');
    define ('DB_PASSWORD', '');
    define ('DB_NAME', 'ieportal');

    //key de encryptación de información
    define ('CRYPTO_KEY', 'planeacion_xdlol123');
    //valores globales del número máximo de registros por Pagination
    define ('NUM_MAX_REG_PAGE', 7);
    define ('GLOBAL_LINKS_EXTREMOS', 4);
     //globals CLASES IE
     define('MIN_FILTRO_CIE', 1);
     define('MAX_FILTRO_CIE', 2);
    //globales USUARIOS
    define('MIN_FILTRO_USER', 1);
    define('MAX_FILTRO_USER', 3);
    //globales HISTORIAL
    define('MIN_FILTRO_HIS',1);
    define('MAX_FILTRO_HIS',11);
    //definiciones de catalogo
    define('MIN_CATALOGO', 1);
    define('MAX_CATALOGO', 10);

    //definiciones de pagos
    define('MIN_FILTRO_PA', 1);
    define('MAX_FILTRO_PA', 10);

    //Zona horaria
    date_default_timezone_set ('America/Mexico_City');

    session_start();//configuracion de sesiones
?>
