<?php
    if(!isset($_REQUEST['filtroActual']) || !is_numeric($_REQUEST['filtroActual']) || !($_REQUEST['filtroActual']>=MIN_FILTRO_HIS) || !($_REQUEST['filtroActual']<=MAX_FILTRO_HIS)){
        $filtroActual = 1;
    }else{
        $filtroActual = $_REQUEST['filtroActual'];
    }
    switch($filtroActual){
        case '1':
            $filtro = 'Vista general';
        break;
        case '2':
            $filtro = 'Creación de Grupo Delictivos';
        break;
        case '3':
            $filtro = 'Edición de Grupo Delictivosn';
        break;
        case '4':
            $filtro = 'Consulta de Ficha General';
        break;
        case '5':
            $filtro = 'Consulta de Ficha de Grupo';
        break;
        case '6':
            $filtro = 'Búsqueda por Término';
        break;
    }
?>

<!DOCTYPE html>
<html lang="es">
  <head>
  	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge">
  	<meta name="viewport" content="width=device-width, initial-scale=1">
  	<title><?= $data['titulo']?></title>
	
	<!-- Bootstrap CSS CDN -->
	<link rel="stylesheet" href="<?php echo base_url ?>public/css/bootstrap/bootstrap.css">
	<!-- ----- ----- ----- Root CSS ----- ----- ----- -->
	<link rel="stylesheet" href="<?php echo base_url ?>public/css/general/root.css">
	<!-- Our Custom CSS -->
	<link rel="stylesheet" href="<?php echo base_url ?>public/css/template/style.css">
	
	<!--Material Icons-->
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<meta name="theme-color" content="#6D0725" />
	<link rel='shortcut icon' type='image/ico' href='<?php echo base_url ?>public/media/icons/favicon.ico'/>
	    
	<script defer src="<?php echo base_url?>public/js/template/header/solid.js"></script>
	<script defer src="<?php echo base_url?>public/js/template/header/fontawesome.js"></script>
	<?php echo (isset($data['extra_css']))?$data['extra_css']:'';?>

  </head>
  <body>
    <div class="row mt-3">
        <div class="col-md-12 text-center">
            <div class="row d-flex align-items-center">
                <div class="col-md-6">
                    <img src="<?= base_url?>public/media/images/banner.png" height="60">
                </div>
                <div class="col-md-6">
                    <img src="<?= base_url?>public/media/images/logo_transito.png" height="60">		 
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-5">
        <h5 class="title-width text-center my-3">Historial: <span><?php echo $filtro?></span></h5>
        <div class="row d-flex justify-content-center">
            <div class="col-auto">
                <table class="table table-responsive">
                    <thead class="thead-myTable text-center">
                        <tr id="id_thead" >
                            <?php
                                echo (isset($data['infoTable']['header']))?$data['infoTable']['header']:"";
                            ?>
                        </tr>
                    </thead>
                    <tbody id="id_tbody" class="text-justify">
                        <?php
                            echo (isset($data['infoTable']['body']))?$data['infoTable']['body']:"";
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">

		document.addEventListener("DOMContentLoaded", function(event) {
			window.print()
		});
		
	</script>
  </body>
</html>