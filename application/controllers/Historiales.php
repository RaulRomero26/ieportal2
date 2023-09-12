<?php
	/*
		Filtros
		1  - Todos
		2  - Inicio de sesión
		3  - Crear remisión
		4  - Editar remisión
		5  - Validar remisión
		6  - Ver remisión
		7  - Consultar remisión
		8  - Crear inspección
		9  - Editar inspección
		10 - Ver inspección
		11 - Consultar inspección
		12 - Crear IO
		13 - Editar IO
		14 - Ver IO
		15 - Consultar IO
		16 - Crear Puesta
		17 - Crear Anexo
		18 - Editar puesta
		19 - Ver puesta
		20 - Consultar puesta
		21 - Concluir puesta
	*/
	class Historiales extends Controller
	{
		public $Historial;

		public function __construct()
		{
			$this->Historial = $this->model('Historial');
			$this->numColumnsHIS = [6,6,6,6,6,6,6,6,6,6,6];
		}

		public function index()
		{
			if(!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin !=1)){
				header("Location: ". base_url ."Inicio");
				exit();
			}

			$data = [
				'titulo'    => 'IE ADMIN | Historial',
				'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/admin/index.css">',
				'extra_js'  => '<script src="'. base_url . 'public/js/system/historial/index.js"></script>'
			];

			if(isset($_GET['filtro']) && is_numeric($_GET['filtro']) && $_GET['filtro'] >= MIN_FILTRO_HIS && $_GET['filtro'] <= MAX_FILTRO_HIS){
				$filtro = $_GET['filtro'];
			}else{
				$filtro = 1;
			}

			$this->setColumnsSession($filtro);
			if(isset($_POST['rango_inicio']) && isset($_POST['rango_fin'])){
				$_SESSION['userdata']->rango_inicio_his = $_POST['rango_inicio'];
            	$_SESSION['userdata']->rango_fin_his = $_POST['rango_fin'];
			}

			if(isset($_GET['numPage'])){
				$numPage = $_GET['numPage'];
				if(!(is_numeric($numPage))){
					$numPage = 1;
				}
			}else{
				$numPage = 1;
			}

			$cadena = "";
			if(isset($_GET['cadena'])){
				$cadena = $_GET['cadena'];
				$data['cadena'] = $cadena;
			}

			$where_sentence = $this->Historial->generateWhereSentence($cadena,$filtro);
			$extra_cad = ($cadena != "") ? ("&cadena=".$cadena) : "";

			$no_of_records_per_page = NUM_MAX_REG_PAGE;
			$offset = ($numPage -1 ) * $no_of_records_per_page;

			$results_rows_pages = $this->Historial->getTotalPages($no_of_records_per_page,$where_sentence);
			$total_pages = $results_rows_pages['total_pages'];

			if($numPage > $total_pages){
				$numPage = 1;
				$offset = ($numPage -1) * $no_of_records_per_page;
			}

			$rows_his = $this->Historial->getDataCurrentPage($offset, $no_of_records_per_page,$where_sentence);

			$data['infoTable'] = $this->generateInfoTable($rows_his,$filtro);
			$data['links'] = $this->generateLinks($numPage, $total_pages, $extra_cad, $filtro);
			$data['total_rows'] = $results_rows_pages['total_rows'];
			$data['filtroActual'] = $filtro;
			$data['dropdownColumns'] = $this->generateDropdownColumns($filtro);

			switch($filtro){
				case '1':
					$data['filtroNombre'] = "Todos";
				break;
				case '2':
					$data['filtroNombre'] = "Creación de un Grupo Delictivo";
				break;
				case '3':
					$data['filtroNombre'] = "Edición de un Grupo Delictivo";
				break;
				case '4':
					$data['filtroNombre'] = "Consulta de Ficha General";
				break;
				case '5':
					$data['filtroNombre'] = "Consulta de Ficha de Grupo";
				break;
				case '6':
					$data['filtroNombre'] = "Búsqueda por Término";
				break;
			}


			$data['prueba'] = $rows_his;

			$this->view('templates/header', $data);
			$this->view('system/historial/historialView', $data);
			$this->view('templates/footer', $data);
		}

		public function generateInfoTable($rows,$filtro=1)
		{
			$infoTable['header'] = "";
			$infoTable['body'] = "";
			$infoTable['header'].='
				<th class="column1">Usuario</th>
				<th class="column2">Fecha y hora</th>
				<th class="column3">Ip Acceso</th>
				<th class="column4">Movimiento</th>
				<th class="column5">Descripción</th>
			';
			foreach($rows as $row){
				switch($row->Movimiento){
					case '1':
						$movimiento = $row->Movimiento.'. Creación';
					break;
					case '2':
						$movimiento = $row->Movimiento.'. Edición';
					break;
					case '3':
						$movimiento = $row->Movimiento.'. Consulta General';
					break;
					case '4':
						$movimiento = $row->Movimiento.'. Consulta de Grupo';
					break;
					case '5':
						$movimiento = $row->Movimiento.'. Búsqueda';
					break;
					case '6':
						$movimiento = $row->Movimiento.'. Descarga';
					break;
				}
				$infoTable['body'].='<tr>';
					$infoTable['body'].='
					<td class="column1">'.$row->User_Name.'</td>
					<td class="column2">'.$row->Fecha_Hora.'</td>
					<td class="column3">'.$row->Ip_Acceso.'</td>
					<td class="column4">'.$movimiento.'</td>
					<td class="column5">'.$row->Descripcion.'</td>
				';
				$infoTable['body'].='</tr>';
			}

			return $infoTable;
		}

		public function generateLinks($numPage, $total_pages, $extra_cad ="", $filtro = 1)
		{
			$links = "";

			if($numPage>1){
				$links.='<li>
							<a class="page-link" href="'.base_url.'Historiales/?numPage=1'.$extra_cad.'&filtro='.$filtro.'" data-toggle="tooltip" data-placement="top" title="Primera página">
								<i class="material-icons">first_page</i>
							</a>
						</li>';
				$links.='<li class="page-item">
							<a class="page-link" href=" '.base_url.'Historiales/?numPage='.($numPage-1).$extra_cad.'&filtro='.$filtro.' " data-toggle="tooltip" data-placement="top" title="Página anterior">
								<i class="material-icons">navigate_before</i>
							</a>
						</li>';
			}
			
			$LINKS_EXTREMOS = GLOBAL_LINKS_EXTREMOS;
			for($ind=($numPage-$LINKS_EXTREMOS); $ind<=($numPage+$LINKS_EXTREMOS); $ind++){
				if(($ind>=1) && ($ind<= $total_pages)){
					$activeLink = ($ind == $numPage) ? 'active':'';

					$links.='<li class="page-item '.$activeLink.' ">
								<a class="page-link" href=" '.base_url.'Historiales/?numPage='.($ind).$extra_cad.'&filtro='.$filtro.' ">
									'.($ind).'
								</a>
							</li>';
				}
			}

			if($numPage<$total_pages){
				$links.= '<li class="page-item">
                            <a class="page-link" href=" '.base_url.'Historiales/?numPage='.($numPage+1).$extra_cad.'&filtro='.$filtro.' " data-toggle="tooltip" data-placement="top" title="Siguiente página">
                            	<i class="material-icons">navigate_next</i>
                            </a>
                        </li>';
                $links.= '<li class="page-item">
                            <a class="page-link" href=" '.base_url.'Historiales/?numPage='.($total_pages).$extra_cad.'&filtro='.$filtro.' " data-toggle="tooltip" data-placement="top" title="Última página">
                           		<i class="material-icons">last_page</i>
                            </a>
                        </li>';
			}

			return $links;
		}

		public function generateDropdownColumns($filtro=1)
		{	
			$dropdownColumn = "";

			$campos = ['Usuario','Fecha y Hora','Ip Acceso','Movimiento', 'Descripción'];

			$ind = 1;
			foreach($campos as $campo){
				$checked = ($_SESSION['userdata']->columns_HIS['column'.$ind] == 'show') ? 'checked':'';
				$dropdownColumn.=   '<div class="form-check">
										<input class="form-check-input checkColumns" type="checkbox" value="'.$_SESSION['userdata']->columns_HIS['column'.$ind].'" onchange="hideShowColumn(this.id);" id="column'.$ind.'" '.$checked.'>
										<label class="form-check-label" for="column'.$ind.'">
											'.$campo.'
										</label>
									</div>';
				$ind++;
			}

			$dropdownColumn.= 	'<div class="dropdown-divider">
                            	</div>
                                <div class="form-check">
                                    <input id="checkAll" class="form-check-input" type="checkbox" value="hide" onchange="hideShowAll(this.id);" id="column'.$ind.'" checked>
                                    <label class="form-check-label" for="column'.$ind.'">
                                        Todo
                                    </label>
                                </div>';

			return $dropdownColumn;
		}

		public function setColumnsSession($filtroActual=1)
		{
			if(isset($_SESSION['userdata']->filtro_HIS) && $_SESSION['userdata']->filtro_HIS >= MIN_FILTRO_HIS && $_SESSION['userdata']->filtro_HIS <= MAX_FILTRO_HIS)	{
				if($_SESSION['userdata']->filtro_HIS != $filtroActual){
					$_SESSION['userdata']->filtro_HIS = $filtroActual;
					unset($_SESSION['userdata']->columns_HIS);
					for($i=0;$i<$this->numColumnsHIS[$_SESSION['userdata']->filtro_HIS - 1]; $i++){
						$_SESSION['userdata']->columns_HIS['column'.($i+1)] = 'show';
					}
				}
			}else{
				$_SESSION['userdata']->filtro_HIS = $filtroActual;
				unset($_SESSION['userdata']->columns_HIS);
				for($i=0;$i<$this->numColumnsHIS[$_SESSION['userdata']->filtro_HIS - 1]; $i++){
					$_SESSION['userdata']->columns_HIS['column'.($i+1)] = 'show';
				}
			}
		}

		public function setColumnFetch(){
			if(isset($_POST['columnName']) && isset($_POST['valueColumn'])){
				$_SESSION['userdata']->columns_HIS[$_POST['columnName']] = $_POST['valueColumn'];
				echo json_encode('ok');
			}
		}

		public function removeRangosFechasSesion()
		{
			if(isset($_REQUEST['filtroActual'])){
				unset($_SESSION['userdata']->rango_inicio_his);
				unset($_SESSION['userdata']->rango_fin_his);

				header("Location: ".base_url."Historiales/?filtro=".$_REQUEST['filtroActual']);
				exit();
			}
		}

		public function buscarPorCadena()
		{
			if(isset($_POST['cadena'])){
				$cadena = trim($_POST['cadena']);
				$filtroActual = trim($_POST['filtroActual']);

				$results = $this->Historial->getHistorialByCadena($cadena,$filtroActual);
				$extra_cad = ($cadena != "")?("&cadena=".$cadena):"";

				$dataReturn['infoTable'] = $this->generateInfoTable($results['rows_Hisroriales'],$filtroActual);
				$dataReturn['links'] = $this->generateLinks($results['numPage'],$results['total_pages'],$extra_cad,$filtroActual);

				$dataReturn['export_links'] = $this->generateExportLinks($extra_cad,$filtroActual);
				$dataReturn['total_rows'] = "Total registros: ".$results['total_rows'];
				$dataReturn['dropdownColumns'] = $this->generateDropdownColumns($filtroActual);

				echo json_encode($dataReturn);
			}
		}

		public function generateExportLinks($extra_cad = "",$filtro = 1)
		{
			if($extra_cad != ""){
				$dataReturn['csv']   =  base_url.'Historiales/exportarInfo/?tipo_export=CSV'.$extra_cad.'&filtroActual='.$filtro;
				$dataReturn['excel'] =  base_url.'Historiales/exportarInfo/?tipo_export=EXCEL'.$extra_cad.'&filtroActual='.$filtro;
				$dataReturn['pdf']   =  base_url.'Historiales/exportarInfo/?tipo_export=PDF'.$extra_cad.'&filtroActual='.$filtro;
			}else{
				$dataReturn['csv']   =  base_url.'Historiales/exportarInfo/?tipo_export=CSV'.$extra_cad.'&filtroActual='.$filtro;
				$dataReturn['excel'] =  base_url.'Historiales/exportarInfo/?tipo_export=EXCEL'.$extra_cad.'&filtroActual='.$filtro;
				$dataReturn['pdf']   =  base_url.'Historiales/exportarInfo/?tipo_export=PDF'.$extra_cad.'&filtroActual='.$filtro;
			}

			return $dataReturn;
		}

		public function exportarInfo()
		{
			if(!isset($_REQUEST['tipo_export'])){
				header("Location: ".base_url."Inicio");
				exit();
			}

			if(!isset($_REQUEST['filtroActual']) || !is_numeric($_REQUEST['filtroActual']) || !($_REQUEST['filtroActual']>=MIN_FILTRO_HIS) || !($_REQUEST['filtroActual']<=MAX_FILTRO_HIS)){
				$filtroActual = 1;
			}else{
				$filtroActual = $_REQUEST['filtroActual'];
			}

			$from_where_sentence = "";

			if(isset($_REQUEST['cadena'])){
				$from_where_sentence = $this->Historial->generateWhereSentence($_REQUEST['cadena'],$filtroActual);
			}else{
				$from_where_sentence = $this->Historial->generateWhereSentence("",$filtroActual);
			}

			$tipo_export = $_REQUEST['tipo_export'];

			if($tipo_export == 'EXCEL'){
				$rows_HIS = $this->Historial->getAllInfoHistorialByCadena($from_where_sentence);
				$filename = 'HIS_general';
				$csv_data = "Usuario,Fecha y Hora,Ip Acceso,Movimiento,Descripción\n";
				foreach ($rows_HIS as $row) {
					switch($row->Movimiento){
						case '1':
							$movimiento = $row->Movimiento.'. Creación';
						break;
						case '2':
							$movimiento = $row->Movimiento.'. Edición';
						break;
						case '3':
							$movimiento = $row->Movimiento.'. Consulta General';
						break;
						case '4':
							$movimiento = $row->Movimiento.'. Consulta de Grupo';
						break;
						case '5':
							$movimiento = $row->Movimiento.'. Búsqueda';
						break;
						case '6':
							$movimiento = $row->Movimiento.'. Descarga';
						break;
					}
					$csv_data.= $row->User_Name.",\"".
								$row->Fecha_Hora.",\",\"".
								$row->Ip_Acceso."\",\"".
								$movimiento."\",\"".
								$row->Descripcion."\"\n";
				}
				
				$csv_data = utf8_decode($csv_data);

				header("Content-Description: File Transfer");
				header("Content-Type: application/force-download");
				header("Content-Disposition: attachment; filename=".$filename."historiales.csv");
				echo $csv_data;
			}elseif($tipo_export == 'PDF'){
				$data = [
					'titulo'    => 'Historial',
				];

				$rows_HIS = $this->Historial->getAllInfoHistorialByCadena($from_where_sentence);
				$data['infoTable'] = $this->generateInfoTable($rows_HIS,$filtroActual);

				$this->view('system/historial/His_general_view',$data);
			}else{
				header("Location: ".base_url."Historiales");
				exit();
			}
		}
	}
?>