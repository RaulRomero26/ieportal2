<?php
/*
    Catálogos:
    1  - Tatuajes
*/
	/*
		Movimientos historial
	42	- ELIMINAR REGISTRO CATALOGO 
    43  - CREAR REGISTRO CATALOGO
    44  - VER REGISTRO CATALOGO
    45  - ACTUALIZAR REGISTRO CATALOGO
    46  - CONSULTAR REGISTRO CATALOGO 
    47  - EXPORTACION DE EXCEL
	*/
class Catalogos extends Controller
{
	public $Catalogo;
	public $Historial;

	public function __construct()
	{
		$this->Catalogo = $this->model("Catalogo");
		$this->Historial = $this->model('Historial');
	}

	public function index(){

		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1) {
            header("Location: ".base_url."Login");
            exit();
        }

        //Titulo de la pagina y archivos css y js necesarios
		$data = [
            'titulo'    => 'IE ADMIN | Catálogos',
            'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/system/catalogos/index.css">',
            'extra_js'  => '<script src="'. base_url . 'public/js/system/catalogos/index.js"></script>'
        ];


        $this->view("templates/header", $data);
        $this->view("system/catalogos/catalogosView", $data);
        $this->view("templates/footer", $data);
    }

    public function crudCatalogo(){
    	if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1) {
                header("Location: ".base_url."Login");
                exit();
            }

			$datos_prim = [
				'nivel' => $this->getNivel(),
				'tipo_pago' => $this->getTipoPago(),
			];

            //Titulo de la pagina y archivos css y js necesarios
			$data = [
                'titulo'    => 'IE ADMIN | Catálogos',
                'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/system/catalogos/index.css">
                				<link rel="stylesheet" href="'. base_url . 'public/css/system/catalogos/crud.css">',
                'extra_js'  => '<script src="'. base_url . 'public/js/system/catalogos/crud.js"></script>'
            ];

            
            //PROCESO DE FILTRADO DE CATALOGO
            if (isset($_GET['catalogoActual']) && is_numeric($_GET['catalogoActual']) && $_GET['catalogoActual']>=MIN_CATALOGO && $_GET['catalogoActual']<=MAX_CATALOGO) { //numero de catálogo
		        $catalogoActual = $_GET['catalogoActual'];
		    } 
		    else {
		        $catalogoActual = 1;
		    }
            //PROCESO DE PAGINATION
			if (isset($_GET['numPage'])) { //numero de pagination
		        $numPage = $_GET['numPage'];
		        if (!(is_numeric($numPage))) //seguridad si se ingresa parámetro inválido
		        	$numPage = 1;
		    } 
		    else {
		        $numPage = 1;
		    }
		    //cadena auxiliar por si se trata de una paginacion conforme a una busqueda dada anteriormente
		    $cadena = "";
		    if (isset($_GET['cadena'])) { //numero de pagination
		        $cadena = $_GET['cadena'];
		        $data['cadena'] = $cadena;
		    }

		    $from_where_sentence = $this->Catalogo->generateFromWhereSentence($catalogoActual,$cadena);
		    $extra_cad = ($cadena != "")?("&cadena=".$cadena):""; //para links conforme a búsqueda

		    $no_of_records_per_page = NUM_MAX_REG_PAGE; //total de registros por pagination
		    $offset = ($numPage-1) * $no_of_records_per_page; // desplazamiento conforme a la pagina

		    $results_rows_pages = $this->Catalogo->getTotalPages($no_of_records_per_page,$from_where_sentence);	//total de páginas de acuerdo a la info de la DB
		    $total_pages = $results_rows_pages['total_pages'];

		    if ($numPage>$total_pages) {$numPage = 1; $offset = ($numPage-1) * $no_of_records_per_page;} //seguridad si ocurre un error por url 	
		    
		    $cat_rows = $this->Catalogo->getDataCurrentPage($offset,$no_of_records_per_page,$from_where_sentence);	//se obtiene la información de la página actual

		    //guardamos la tabulacion de la información para la vista
		    $data['infoTable'] = $this->generarInfoTable($cat_rows,$catalogoActual,$datos_prim);
			//guardamos los links en data para la vista
			$data['links'] = $this->generarLinks($numPage,$total_pages,$extra_cad,$catalogoActual);
			//número total de registros encontrados
			$data['total_rows'] = $results_rows_pages['total_rows'];
			$data['catalogoActual'] = $catalogoActual;


            $this->view("templates/header", $data);
            $this->view("system/catalogos/catalogosCrudView", $data);
            $this->view("templates/footer", $data);
    }

    public function buscarPorCadena(){
		if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1)) {
			header("Location: ".base_url."Inicio");
			exit();
		}

		if (isset($_POST['cadena']) && isset($_POST['catalogoActual'])) {
			$cadena = trim($_POST['cadena']); 
			$catalogoActual = trim($_POST['catalogoActual']);

			$results = $this->Catalogo->getCatalogoByCadena($cadena,$catalogoActual);
			$extra_cad = ($cadena != "")?("&cadena=".$cadena):""; //para links conforme a búsqueda
			
			if(strlen($cadena)>0){
				$nombreCatalogo= $this->getNombreCatalogo($catalogoActual);
                $this->Historial->insertHistorial(46,'CONSULTA EN CATALOGO: '.$nombreCatalogo.' SE CONSULTO: '.$cadena);
            }
			//$dataReturn = "jeje";

			$dataReturn['infoTable'] = $this->generarInfoTable($results['cat_rows'],$catalogoActual);
			$dataReturn['links'] = $this->generarLinks($results['numPage'],$results['total_pages'],$extra_cad,$catalogoActual);
			$dataReturn['export_links'] = $this->generarExportLinks($extra_cad,$catalogoActual);
			$dataReturn['total_rows'] = "Total registros: ".$results['total_rows'];

			
			echo json_encode($dataReturn);
		}
		else{
			header("Location: ".base_url."Inicio");
		}
	}

	public function generarExportLinks($extra_cad = "",$catalogoActual = 1){
		if ($extra_cad != "") {
			$dataReturn['csv'] =  base_url.'Catalogos/exportarInfo/?tipo_export=CSV'.$extra_cad.'&catalogoActual='.$catalogoActual;
			$dataReturn['excel'] =  base_url.'Catalogos/exportarInfo/?tipo_export=EXCEL'.$extra_cad.'&catalogoActual='.$catalogoActual;
			$dataReturn['pdf'] =  base_url.'Catalogos/exportarInfo/?tipo_export=PDF'.$extra_cad.'&catalogoActual='.$catalogoActual;
			//return $dataReturn;
		}
		else{
			$dataReturn['csv'] =  base_url.'Catalogos/exportarInfo/?tipo_export=CSV'.$extra_cad.'&catalogoActual='.$catalogoActual;
			$dataReturn['excel'] =  base_url.'Catalogos/exportarInfo/?tipo_export=EXCEL'.$extra_cad.'&catalogoActual='.$catalogoActual;
			$dataReturn['pdf'] =  base_url.'Catalogos/exportarInfo/?tipo_export=PDF'.$extra_cad.'&catalogoActual='.$catalogoActual;
		}
		return $dataReturn;
	}


    public function generarLinks($numPage,$total_pages,$extra_cad = "",$catalogoActual = 1){
			//$extra_cad sirve para determinar la paginacion conforme a si se realizó una busqueda
			//Creación de links para el pagination
			$links = "";

			//FLECHA IZQ (PREV PAGINATION)
			if ($numPage>1) {
				$links.= '<li class="page-item">
							<a class="page-link" href=" '.base_url.'Catalogos/crudCatalogo/?numPage=1'.$extra_cad.'&catalogoActual='.$catalogoActual.' " data-toggle="tooltip" data-placement="top" title="Primera página">
								<i class="material-icons">first_page</i>
							</a>
						</li>';
				$links.= '<li class="page-item">
							<a class="page-link" href=" '.base_url.'Catalogos/crudCatalogo/?numPage='.($numPage-1).$extra_cad.'&catalogoActual='.$catalogoActual.' " data-toggle="tooltip" data-placement="top" title="Página anterior">
								<i class="material-icons">navigate_before</i>
							</a>
						</li>';
			}

			//DESPLIEGUE DE PAGES NUMBER
			$LINKS_EXTREMOS = GLOBAL_LINKS_EXTREMOS; //numero máximo de links a la izquierda y a la derecha
			for ($ind=($numPage-$LINKS_EXTREMOS); $ind<=($numPage+$LINKS_EXTREMOS); $ind++) {
				if(($ind>=1) && ($ind <= $total_pages)){

					$activeLink = ($ind == $numPage)? 'active':'';

					$links.= '<li class="page-item '.$activeLink.' ">
								<a class="page-link" href=" '.base_url.'Catalogos/crudCatalogo/?numPage='.($ind).$extra_cad.'&catalogoActual='.$catalogoActual.' ">
									'.($ind).'
								</a>
							</li>';
				}
			}

			//FLECHA DERECHA (NEXT PAGINATION)
			if ($numPage<$total_pages) {

				$links.= '<li class="page-item">
							<a class="page-link" href=" '.base_url.'Catalogos/crudCatalogo/?numPage='.($numPage+1).$extra_cad.'&catalogoActual='.$catalogoActual.' " data-toggle="tooltip" data-placement="top" title="Siguiente página">
							<i class="material-icons">navigate_next</i>
							</a>
						</li>';
				$links.= '<li class="page-item">
							<a class="page-link" href=" '.base_url.'Catalogos/crudCatalogo/?numPage='.($total_pages).$extra_cad.'&catalogoActual='.$catalogoActual.' " data-toggle="tooltip" data-placement="top" title="Última página">
							<i class="material-icons">last_page</i>
							</a>
						</li>';
			}

			return $links;
	}

	public function generarInfoTable($catalogoRows,$catalogoActual = 1,$data_prim=1){
			//se genera la tabulacion de la informacion por backend
			$infoTable['header'] = "";
			$infoTable['body'] = "";
	  		$infoTable['formBody'] = $this->generateFormCatalogo($catalogoActual,$data_prim);

	  			
  			switch ($catalogoActual) {
				// cambiar para caso 1-alumno
				//2-profesor
				//3-clases
  				case '1':
  					$infoTable['header'] .= '
  							<th >Id Alumno</th>
  							<th >ID Clase</th>
							  <th >Nombre</th>
							  <th >Apellido paterno</th>
							  <th >Apellido materno</th>
							  <th >Correo</th>
							  <th >Activo</th>
							  <th >Edad</th>
							  <th >Ciudad Origen</th>
							  <th >Teléfono</th>
							  <th >Tipo de pago</th>
							  <th >Nivel</th>
  							<th >Contador</th>
  						';
  					foreach ($catalogoRows as $row) {
  						$infoTable['body'].= '<tr id="tr'.$row->Id_alumno.'">';
  						$infoTable['body'].= '	<td >'.$row->Id_alumno.'</td>
						  						<td >'.$row->Id_clase.'</td>
												<td >'.$row->Nombre.'</td>
												<td >'.$row->Apellido_paterno.'</td>
												<td >'.$row->Apellido_materno.'</td>
												<td >'.$row->Correo.'</td>
												<td >'.$row->Activo.'</td>
												<td >'.$row->Edad.'</td>
												<td >'.$row->Ciudad_origen.'</td>
												<td >'.$row->Telefono.'</td>
												<td >'.$row->Tipo_pago.'</td>
										        <td >'.$row->Nivel.'</td>
										        <td >'.$row->Contador.'</td>
					        ';
					    $infoTable['body'].= '	<td >
						    						<div class="d-flex justify-content-center" id="operaciones">
						    							<button data-toggle="tooltip" data-placement="top" title="Editar registro" class="btn btn-icon btn-edit mr-1 edit-icon" onclick="editAction('.$catalogoActual.','.$row->Id_alumno.')"><i class="material-icons">edit</i></button>
						    							<button data-toggle="tooltip" data-placement="top" title="Eliminar registro" class="btn btn-icon btn-delete delete-icon" onclick="deleteAction('.$catalogoActual.','.$row->Id_alumno.')"><i class="material-icons">delete</i></button>
						    						</div>
					    						</td>';
					    $infoTable['body'].= '</tr>';
	  				}
  					
  					break;
				case '2':
					$infoTable['header'] .= '
							<th >Id Maestro</th>
							<th >Nombre</th>
							<th >Apellido paterno</th>
							<th >Apellido materno</th>
							<th >Correo</th>
							<th >Activo</th>
							<th >Nivel</th>
							<th >Telefono</th>
							<th >Días disponibles</th>
							<th >Horaro disponible</th>
						';
					foreach ($catalogoRows as $row) {
						$infoTable['body'].= '<tr id="tr'.$row->Id_maestro.'">';
						$infoTable['body'].= '	<td >'.$row->Id_maestro.'</td>
												<td >'.$row->Nombre.'</td>
												<td >'.$row->Apellido_paterno.'</td>
												<td >'.$row->Apellido_materno.'</td>
												<td >'.$row->Correo.'</td>
												<td >'.$row->Activo.'</td>
												<td >'.$row->Nivel.'</td>
												<td >'.$row->Telefono.'</td>
												<td >'.$row->Dias_disponible.'</td>
												<td >'.$row->Horario_disponible.'</td>
							';
						$infoTable['body'].= '	<td >
													<div class="d-flex justify-content-center" id="operaciones">
														<button data-toggle="tooltip" data-placement="top" title="Editar registro" class="btn btn-icon btn-edit mr-1 edit-icon" onclick="editAction('.$catalogoActual.','.$row->Id_maestro.')"><i class="material-icons">edit</i></button>
														<button data-toggle="tooltip" data-placement="top" title="Eliminar registro" class="btn btn-icon btn-delete delete-icon" onclick="deleteAction('.$catalogoActual.','.$row->Id_maestro.')"><i class="material-icons">delete</i></button>
													</div>
												</td>';
						$infoTable['body'].= '</tr>';
					}
					
				break;
				case '3':
					$infoTable['header'] .= '
							<th >Id Tipo de clase</th>
							<th >Descripción</th>
						';
					foreach ($catalogoRows as $row) {
						$infoTable['body'].= '<tr id="tr'.$row->Id_tipo_clase .'">';
						$infoTable['body'].= '	<td >'.$row->Id_tipo_clase.'</td>
												<td >'.$row->Descripcion.'</td>
							';
						$infoTable['body'].= '	<td >
													<div class="d-flex justify-content-center" id="operaciones">
														<button data-toggle="tooltip" data-placement="top" title="Editar registro" class="btn btn-icon btn-edit mr-1 edit-icon" onclick="editAction('.$catalogoActual.','.$row->Id_tipo_clase .')"><i class="material-icons">edit</i></button>
														<button data-toggle="tooltip" data-placement="top" title="Eliminar registro" class="btn btn-icon btn-delete delete-icon" onclick="deleteAction('.$catalogoActual.','.$row->Id_tipo_clase .')"><i class="material-icons">delete</i></button>
													</div>
												</td>';
						$infoTable['body'].= '</tr>';
					}
					
				break;
				case '4':
					$infoTable['header'] .= '
							<th >Id Tipo de pago</th>
							<th >Descripción</th>
						';
					foreach ($catalogoRows as $row) {
						$infoTable['body'].= '<tr id="tr'.$row->Id_tipo_pago .'">';
						$infoTable['body'].= '	<td >'.$row->Id_tipo_pago.'</td>
												<td >'.$row->Descripcion.'</td>
							';
						$infoTable['body'].= '	<td >
													<div class="d-flex justify-content-center" id="operaciones">
														<button data-toggle="tooltip" data-placement="top" title="Editar registro" class="btn btn-icon btn-edit mr-1 edit-icon" onclick="editAction('.$catalogoActual.','.$row->Id_tipo_pago .')"><i class="material-icons">edit</i></button>
														<button data-toggle="tooltip" data-placement="top" title="Eliminar registro" class="btn btn-icon btn-delete delete-icon" onclick="deleteAction('.$catalogoActual.','.$row->Id_tipo_pago .')"><i class="material-icons">delete</i></button>
													</div>
												</td>';
						$infoTable['body'].= '</tr>';
					}
					
				break;
				case '5':
					$infoTable['header'] .= '
							<th >Id Método de pago</th>
							<th >Descripción</th>
						';
					foreach ($catalogoRows as $row) {
						$infoTable['body'].= '<tr id="tr'.$row->Id_metodo_pago  .'">';
						$infoTable['body'].= '	<td >'.$row->Id_metodo_pago .'</td>
												<td >'.$row->Descripcion.'</td>
							';
						$infoTable['body'].= '	<td >
													<div class="d-flex justify-content-center" id="operaciones">
														<button data-toggle="tooltip" data-placement="top" title="Editar registro" class="btn btn-icon btn-edit mr-1 edit-icon" onclick="editAction('.$catalogoActual.','.$row->Id_metodo_pago  .')"><i class="material-icons">edit</i></button>
														<button data-toggle="tooltip" data-placement="top" title="Eliminar registro" class="btn btn-icon btn-delete delete-icon" onclick="deleteAction('.$catalogoActual.','.$row->Id_metodo_pago  .')"><i class="material-icons">delete</i></button>
													</div>
												</td>';
						$infoTable['body'].= '</tr>';
					}
					
				break;
				case '6':
					$infoTable['header'] .= '
							<th >Id Pago por hora</th>
							<th >Descripción</th>
							<th >Cantidad</th>
						';
					foreach ($catalogoRows as $row) {
						$infoTable['body'].= '<tr id="tr'.$row->Id_pago_hora  .'">';
						$infoTable['body'].= '	<td >'.$row->Id_pago_hora .'</td>
												<td >'.$row->Descripcion.'</td>
												<td >'.$row->Cantidad.'</td>
							';
						$infoTable['body'].= '	<td >
													<div class="d-flex justify-content-center" id="operaciones">
														<button data-toggle="tooltip" data-placement="top" title="Editar registro" class="btn btn-icon btn-edit mr-1 edit-icon" onclick="editAction('.$catalogoActual.','.$row->Id_pago_hora  .')"><i class="material-icons">edit</i></button>
														<button data-toggle="tooltip" data-placement="top" title="Eliminar registro" class="btn btn-icon btn-delete delete-icon" onclick="deleteAction('.$catalogoActual.','.$row->Id_pago_hora  .')"><i class="material-icons">delete</i></button>
													</div>
												</td>';
						$infoTable['body'].= '</tr>';
					}
					
				break;
				
  			}
  			$infoTable['header'].='<th >Operaciones</th>';
			$nombreCatalogo= $this->getNombreCatalogo($catalogoActual);
			$this->Historial->insertHistorial(44,'VER CATALOGO: '.$nombreCatalogo);
	  		return $infoTable;
	}

	public function exportarInfo(){
		if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1)) {
			header("Location: ".base_url."Inicio");
		}

		if (!isset($_REQUEST['tipo_export'])) {
			header("Location: ".base_url."UsersAdmin");
		}
		//se recupera el catalogo actual para poder consultar conforme al mismo
		if (!is_numeric($_REQUEST['catalogoActual']) || !($_REQUEST['catalogoActual']>=MIN_CATALOGO) || !($_REQUEST['catalogoActual']<=MAX_CATALOGO)) 
				$catalogoActual = 1;
			else
				$catalogoActual = $_REQUEST['catalogoActual'];

		$from_where_sentence = "";
		//se genera la sentencia from where para realizar la correspondiente consulta
		if (isset($_REQUEST['cadena'])) 
			$from_where_sentence = $this->Catalogo->generateFromWhereSentence($catalogoActual,$_REQUEST['cadena']);
		else
			$from_where_sentence = $this->Catalogo->generateFromWhereSentence($catalogoActual,"");

		
		
		//var_dump($_REQUEST);
		$tipo_export = $_REQUEST['tipo_export'];

		if ($tipo_export == 'EXCEL') {
			//se realiza exportacion de usuarios a EXCEL
			$cat_rows = $this->Catalogo->getAllInfoCatalogoByCadena($from_where_sentence);
			switch ($catalogoActual) {
				case '1':
					$filename = "alumnos";
					$csv_data="Id,Id clase, Nombre, Apellido paterno, Apellido materno,Correo, Edad, Ciudad Origen,Telefono, Tipo de pago, Nivel, Contador de faltas\n";
					foreach ($cat_rows as $row) {
						$csv_data.= mb_strtoupper($row->Id_alumno).",\"".
									mb_strtoupper($row->Id_clase)."\",\"".
									mb_strtoupper($row->Nombre)."\",\"".
									mb_strtoupper($row->Apellido_paterno)."\",\"".
									mb_strtoupper($row->Apellido_materno)."\",\"".
									mb_strtoupper($row->Correo)."\",\"".
									mb_strtoupper($row->Edad)."\",\"".
									mb_strtoupper($row->Ciudad_origen)."\",\"".
									mb_strtoupper($row->Telefono)."\",\"".
									mb_strtoupper($row->Tipo_pago)."\",\"".
									mb_strtoupper($row->Nivel)."\",\"".
									mb_strtoupper($row->Contador)."\"\n";
					}
					break;
					case '2':
						$filename = "maestros";
						$csv_data="Id, Nombre, Apellido paterno, Apellido materno,Correo,Telefono, Días disponible, Horario disponible\n";
						foreach ($cat_rows as $row) {
							$csv_data.= mb_strtoupper($row->Id_maestro).",\"".
										mb_strtoupper($row->Nombre)."\",\"".
										mb_strtoupper($row->Apellido_paterno)."\",\"".
										mb_strtoupper($row->Apellido_materno)."\",\"".
										mb_strtoupper($row->Correo)."\",\"".
										mb_strtoupper($row->Telefono)."\",\"".
										mb_strtoupper($row->Dias_disponible)."\",\"".
										mb_strtoupper($row->Horario_disponible)."\"\n";
						}
						break;
					case '3':
						$filename = "tipo clase";
						$csv_data="Id, Descripción\n";
						foreach ($cat_rows as $row) {
							$csv_data.= mb_strtoupper($row->Id_tipo_clase).",\"".
										mb_strtoupper($row->Descripcion)."\"\n";
						}
						break;
					case '4':
						$filename = "tipo de pago";
						$csv_data="Id, Descripción\n";
						foreach ($cat_rows as $row) {
							$csv_data.= mb_strtoupper($row->Id_tipo_pago).",\"".
										mb_strtoupper($row->Descripcion)."\"\n";
						}
						break;
					case '5':
						$filename = "metodo de pago";
						$csv_data="Id, Descripción\n";
						foreach ($cat_rows as $row) {
							$csv_data.= mb_strtoupper($row->Id_metodo_pago).",\"".
										mb_strtoupper($row->Descripcion)."\"\n";
						}
						break;
					case '6':
						$filename = "pago por hora";
						$csv_data="Id, Descripción, Cantidad\n";
						foreach ($cat_rows as $row) {
							$csv_data.= mb_strtoupper($row->Id_pago_hora).",\"".
										mb_strtoupper($row->Descripcion)."\",\"".
										mb_strtoupper($row->Cantidad)."\"\n";
						}
						break;
				
			}
			//se genera el archivo csv o excel
			$csv_data = utf8_decode($csv_data); //escribir información con formato utf8 por algún acento
			header("Content-Description: File Transfer");
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=".$filename.".csv");
			echo $csv_data;
			$nombreCatalogo= $this->getNombreCatalogo($catalogoActual);
			$this->Historial->insertHistorial(47,'EXPORTACION DE EXCEL CATALOGO: '.$nombreCatalogo);
			//header("Location: ".base_url."UsersAdmin");

		}
		elseif($tipo_export == 'PDF'){
			$cat_rows = $this->Catalogo->getAllInfoCatalogoByCadena($from_where_sentence);
			

			header("Content-type: application/pdf");
			header("Content-Disposition: inline; filename=usuarios.pdf");
			echo $this->generarPDF($cat_rows,$_REQUEST['cadena'],$catalogoActual);
		}
		else{
			header("Location: ".base_url."Inicio");
		}
	}

	public function generarPDF($cat_rows,$cadena = "",$catalogoActual = '1'){
		//require('../libraries/PDF library/fpdf16/fpdf.php');
		switch ($catalogoActual) {
			case '1': $filename="Tatuajes";break;
		}

		$data['subtitulo']      = 'Catálogo: '.$filename;

		if ($cadena != "") {
			$data['msg'] = 'todos los registros con filtro: '.$cadena.'';
		}
		else{
			$data['msg'] = 'todos los registros del catálogo';
		}


		//---Aquí va la info según sea el catálogo seleccionado
		switch ($catalogoActual) {
			case '1': 
				$data['columns'] =  [
	                            'Id',
	                            'Tipo tatuaje',
	                            'Descripción'
                            ];  
       	 		$data['field_names'] = [
	                            'Id_Tatuaje',
	                            'Tipo_Tatuaje',
	                            'Descripcion'
                            ]; 
			break;
		}

		$data['rows'] = $cat_rows;
		//se carga toda la plantilla con la información enviada por parámetro
        $plantilla = MY_PDF::getPlantilla($data);
        //se carga el css de la plantilla
        $css = file_get_contents(base_url.'public/css/template/pdf_style.css');
        // Create an instance of the class:
        $mpdf = new \Mpdf\Mpdf([]);
        // se inserta el css y html cargado
        $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($plantilla, \Mpdf\HTMLParserMode::HTML_BODY);
        // se muestra en pantalla
        $mpdf->Output();
	}

	//función para generar los campos para adición o edición de registros del catálogo
	public function generateFormCatalogo($catalogoActual = 1,$data_prim=1){
		$formBody = "";
		switch ($catalogoActual) {
			case '1':
				$formBody.='
							<div class="col-12 col-md-1 form-group">
							    <label for="id_alumno">Id:</label>
							    <input type="text" class="form-control" id="id_alumno" value="1" readonly>
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="nombre">Nombre:</label>
							    <input type="text" class="form-control" id="nombre">
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="apellido_p">Apellido paterno:</label>
							    <input type="text" class="form-control" id="apellido_p">
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="apellido_m">Apellido materno:</label>
							    <input type="text" class="form-control" id="apellido_m">
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="activo">Activo:</label>
							    <select class="custom-select custom-select-sm" id="activo" name="activo">
									<option value="1">1</option>
									<option value="0">0</option>
								</select>
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="correo">Correo:</label>
							    <input type="email" class="form-control" id="correo">
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="edad">Edad:</label>
							    <input type="number" class="form-control" id="edad">
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="ciudad">Ciudad origen:</label>
							    <input type="text" class="form-control" id="ciudad">
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="telefono">Telefono:</label>
							    <input type="text" class="form-control" id="telefono">
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="tipo_pago">Tipo de pago:</label>
								<select class="custom-select custom-select-sm" id="tipo_pago" name="tipo_pago">
								';
							foreach ($data_prim['tipo_pago'] as $item){
								$formBody.='<option value="'.$item->Descripcion.'">'.$item->Descripcion.'</option>';
							}
							$formBody.='</select>
							</div>
							<div class="col-12 col-md-3 form-group">
							    <label for="nivel">Nivel:</label>
								<select class="custom-select custom-select-sm" id="nivel" name="nivel">
								';
							foreach ($data_prim['nivel'] as $item){
								$formBody.='<option value="'.strtoupper($item->Descripcion).'">'.strtoupper($item->Descripcion).'</option>';
							}
					$formBody.='</select>
					</div>';
				break;
			case '2':
				$formBody.='
							<div class="col-12 col-md-1 form-group">
								<label for="id_maestro">Id:</label>
								<input type="text" class="form-control" id="id_maestro" value="1" readonly>
							</div>
							<div class="col-12 col-md-3 form-group">
								<label for="nombre_m">Nombre:</label>
								<input type="text" class="form-control" id="nombre_m">
							</div>
							<div class="col-12 col-md-3 form-group">
								<label for="apellido_pm">Apellido paterno:</label>
								<input type="text" class="form-control" id="apellido_pm">
							</div>
							<div class="col-12 col-md-3 form-group">
								<label for="apellido_mm">Apellido materno:</label>
								<input type="text" class="form-control" id="apellido_mm">
							</div>
							<div class="col-12 col-md-2 form-group">
								<label for="activo_m">Activo:</label>
								<select class="custom-select custom-select-sm" id="activo_m" name="actactivo_mivo">
									<option value="1">1</option>
									<option value="0">0</option>
								</select>
							</div>
							<div class="col-12 col-md-3 form-group">
								<label for="correo_m">Correo:</label>
								<input type="email" class="form-control" id="correo_m">
							</div>
							<div class="col-12 col-md-3 form-group">
								<label for="telefono_m">Telefono:</label>
								<input type="text" class="form-control" id="telefono_m">
							</div>
							<div class="col-12 col-md-3 form-group">
								<label for="nivel_m">Nivel:</label>
								<select class="custom-select custom-select-sm" id="nivel_m" name="nivel_m">
								';
							foreach ($data_prim['nivel'] as $item){
								$formBody.='<option value="'.strtoupper($item->Descripcion).'">'.strtoupper($item->Descripcion).'</option>';
							}
						$formBody.='</select>
						</div> 
							<div class="col-12 col-md-12 form-group">
							<div class="alert alert-warning mi_hide" role="alert" id="alertEditHorario">
								Está realizando edición a un elemento.
							</div>
							<span class="span_error" id="Horario_error"></span>
								<label for="dia_disponible">Dia disponible:</label>
								<select class="custom-select custom-select-sm" id="dia_disponible" name="nivel_m">
									<option value="LUN">Lunes</option>
									<option value="MAR">Martes</option>
									<option value="MIE">Miercoles</option>
									<option value="JUE">Jueves</option>
									<option value="VIE">Viernes</option>
									<option value="SAB">Sabado</option>
								</select>
								<div class="invalid-feedback" id="dia_disponible-invalid">
										El día es requerido.
								</div>
								
							</div>
							<div class="col-12 col-md-5 form-group">
								<label for="hora_disponible">Hora disponible:</label>
								<input type="time" class="form-control" id="hora_disponible">
								<div class="invalid-feedback" id="hora_disponible-invalid">
										La hora es requerida.
								</div>
							</div>
							<div class="col-12 col-md-2 form-group">
								<button type="button" class="btn btn-primary button-movil-plus" onclick="onFormOtroSubmit()">+</button>
							</div>
							<div class="col-12 col-md-6 form-group">
							<label for="TableHorarios">Horario disponible:</label>
								<div class="table-responsive">
									<table class="table table-bordered" id="TableHorarios">
										<thead class="thead-dark">
											<tr>
												<th scope="col">Día</th>
												<th scope="col">Hora</th>
												<th scope="col"></th>
												<th scope="col"></th>
											</tr>
										</thead>
										<tbody id="tbody_horarios">
										</tbody>
									</table>
								</div>
							</div>
							';
							
				break;
			case '3':
				$formBody.='
							<div class="col-12 col-md-1 form-group">
								<label for="id_tipo_clase">Id:</label>
								<input type="text" class="form-control" id="id_tipo_clase" value="1" readonly>
							</div>
							<div class="col-12 col-md-4 form-group">
								<label for="id_descripcion">Descripción:</label>
								<textarea class="form-control" id="id_descripcion" rows="2"></textarea>
							</div>
							';
				break;
			case '4':
				$formBody.='
							<div class="col-12 col-md-1 form-group">
								<label for="id_tipo_pago">Id:</label>
								<input type="text" class="form-control" id="id_tipo_pago" value="1" readonly>
							</div>
							<div class="col-12 col-md-4 form-group">
								<label for="id_descripcion">Descripción:</label>
								<textarea class="form-control" id="id_descripcion" rows="2"></textarea>
							</div>
							';
				break;
			case '5':
				$formBody.='
							<div class="col-12 col-md-1 form-group">
								<label for="id_metodo_pago">Id:</label>
								<input type="text" class="form-control" id="id_metodo_pago" value="1" readonly>
							</div>
							<div class="col-12 col-md-4 form-group">
								<label for="id_descripcion">Descripción:</label>
								<textarea class="form-control" id="id_descripcion" rows="2"></textarea>
							</div>
							';
				break;
			case '6':
				$formBody.='
							<div class="col-12 col-md-1 form-group">
								<label for="id_pago_hora">Id:</label>
								<input type="text" class="form-control" id="id_pago_hora" value="1" readonly>
							</div>
							<div class="col-12 col-md-4 form-group">
								<label for="id_descripcion">Descripción:</label>
								<textarea class="form-control" id="id_descripcion" rows="2"></textarea>
							</div>
							<div class="col-12 col-md-3 form-group">
								<label for="cantidad_pago">Cantidad:</label>
								<input type="number" class="form-control" id="cantidad_pago">
							</div>
							';
				break;
		}
		return $formBody;
	}

	//función Fetch para crear o actualizar en catálogo seleccionado
	public function sendFormFetch(){
		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1) {
            header("Location: ".base_url."Login");
            exit();
        }

        if (!isset($_POST['postForm'])) {
        	header("Location: ".base_url."Catalogos");
        }
        
        //variable de respuesta al insertar o actualizar
        $response = $this->Catalogo->InsertOrUpdateCatalogo($_POST); //se manda el POST y todo el desmadre se realiza en el modelo

		if($response == "Success"){
			$catalogo = $_POST['catalogo'];
			$action   = $_POST['action'];
			switch ($action) { //switch de action 1-insertar  2-actualizar
				case '1':
						$nombreCatalogo= $this->getNombreCatalogo($catalogo);
						$this->Historial->insertHistorial(43,'SE CREO UN REGISTRO DEL CATALOGO: '.$nombreCatalogo);
				break;
				case '2':
					$nombreCatalogo= $this->getNombreCatalogo($catalogo);
					$this->Historial->insertHistorial(45,'SE ACTUALIZO UN REGISTRO DEL CATALOGO: '.$nombreCatalogo);
				break;
			}
		}


        echo json_encode($response);
	}

	//función Fetch para crear o actualizar en catálogo seleccionado
	public function deleteFormFetch(){
		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1) {
            header("Location: ".base_url."Login");
            exit();
        }

        if (!isset($_POST['deletePostForm'])) {
        	header("Location: ".base_url."Catalogos");
        }
        
        //variable de respuesta al insertar o actualizar
        $response = $this->Catalogo->deleteCatalogoRow($_POST); //se manda el POST y todo el desmadre se realiza en el modelo
		if($response == "Success"){
			$catalogo = $_POST['catalogo'];
			$nombreCatalogo= $this->getNombreCatalogo($catalogo);
			$this->Historial->insertHistorial(42,'SE ELIMINO EL REGISTRO: '.$_POST['Id_Reg'] .'  DEL CATALOGO: '.$nombreCatalogo);

		}

        echo json_encode($response);
	}
	
	public function getNombreCatalogo($catalogoActual)
	{	
		$nombreCatalogo="";
		switch ($catalogoActual) {
			case '1':
				$nombreCatalogo="catalogo_tatuaje";
			break;
		}
		return $nombreCatalogo;
	}
	
	/*Se añaden funciones para catalogo de colonias y calles*/
	/*SOlo dejo estas funcion para tomar como referencia*/
	public function getColonias()
    {
        $data = $this->Catalogo->getColonias();
        echo json_encode($data);
    }
    
	public function getSubmarcasTermino(){
		$data = $this->Catalogo->getSubmarcaCatalogo($_POST['termino']);
        echo json_encode($data);
	}
	public function getClase()
    {
        $data = $this->Catalogo->getCatalogoClase();
        return $data;
    }
	public function getNivel()
    {
        $data = $this->Catalogo->getSimpleCatalogo("Descripcion", "nivel");
        return $data;
    }
	public function getTipoPago()
    {
        $data = $this->Catalogo->getSimpleCatalogo("Descripcion", "tipo_pago");
        return $data;
    }
	

}

?>