<?php
	/**
	 * 
	 */
class UsersAdmin extends Controller
{
	public $Usuario;
	public $numColumnsUSER;

	public function __construct()
	{
		$this->Usuario = $this->model('Usuario');   //se instancia model Usuario y ya puede ser ocupado en el controlador
		$this->numColumnsUSER = [5,5,5];
	}


	public function index(){
		//comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1) {
            header("Location: ".base_url."Inicio");
            exit();
        }

        //Titulo de la pagina y archivos css y js necesarios
		$data = [
            'titulo'    => 'IE Portal | Usuarios',
            'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/admin/index.css">',
            'extra_js'  => '<script src="'. base_url . 'public/js/admin/index.js"></script>'
        ];

        //PROCESO DE FILTRADO DE EVENTOS DELICTIVOS

        if (isset($_GET['filtro']) && is_numeric($_GET['filtro']) && $_GET['filtro']>=MIN_FILTRO_GC && $_GET['filtro']<=MAX_FILTRO_GC) { //numero de catálogo
            $filtro = $_GET['filtro'];
        } 
        else {
            $filtro = 1;
        }

        //PROCESAMIENTO DE LAS COLUMNAS 
        $this->setColumnsSession($filtro);
        $data['columns_USER'] = $_SESSION['userdata']->columns_USER;

        //PROCESAMIENTO DE RANGO DE FOLIOS
        if (isset($_POST['rango_inicio']) && isset($_POST['rango_fin'])) {
            $_SESSION['userdata']->rango_inicio_user = $_POST['rango_inicio'];
            $_SESSION['userdata']->rango_fin_user = $_POST['rango_fin'];
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

        $where_sentence = $this->Usuario->generateFromWhereSentence($cadena,$filtro);
        $extra_cad = ($cadena != "")?("&cadena=".$cadena):""; //para links conforme a búsqueda

        $no_of_records_per_page = NUM_MAX_REG_PAGE; //total de registros por pagination
        $offset = ($numPage-1) * $no_of_records_per_page; // desplazamiento conforme a la pagina

        $results_rows_pages = $this->Usuario->getTotalPages($no_of_records_per_page,$where_sentence);   //total de páginas de acuerdo a la info de la DB
        $total_pages = $results_rows_pages['total_pages'];

        if ($numPage>$total_pages) {$numPage = 1; $offset = ($numPage-1) * $no_of_records_per_page;} //seguridad si ocurre un error por url     
        
        $rows_Users = $this->Usuario->getDataCurrentPage($offset,$no_of_records_per_page,$where_sentence);    //se obtiene la información de la página actual

        //guardamos la tabulacion de la información para la vista
        $data['infoTable'] = $this->generarInfoTable($rows_Users,$filtro);
        //guardamos los links en data para la vista
        $data['links'] = $this->generarLinks($numPage,$total_pages,$extra_cad,$filtro);
        //número total de registros encontrados
        $data['total_rows'] = $results_rows_pages['total_rows'];
        //filtro actual para Fetch javascript
        $data['filtroActual'] = $filtro;
        $data['dropdownColumns'] = $this->generateDropdownColumns($filtro);

        switch ($filtro) {
            case '1': $data['filtroNombre'] = "Todos los usuarios"; break;
            case '2': $data['filtroNombre'] = "Administradores"; break;
            case '3': $data['filtroNombre'] = "Otros"; break;
        }

        $this->view("templates/header",$data);
        $this->view("admin/usersAdminView",$data);
        $this->view("templates/footer",$data);
	}

	public function crearUser(){
		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1) {
            header("Location: ".base_url."Login");
        }

        //Titulo de la pagina y archivos css y js necesarios
		$data = [
            'titulo'    => 'IE Portal | Crear User',
            'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/admin/crearUser.css">',
            'extra_js'  => '<script src="'. base_url . 'public/js/admin/crearUser.js"></script>'
        ];
        //echo("FALTA GUARDAR IMAGEN Y EN SU CARPETA DE ID USER");
        if(isset($_POST['crearUser'])){	//post para editar los cambios en la info del usuario
	    	//var_dump($_POST);
	    	//echo("<br><br>");
	    	//var_dump($_FILES);
        	
    		$validation = isset($_POST['Nombre']) & isset($_POST['Ap_Paterno']) & isset($_POST['Ap_Materno']) & isset($_POST['Email']) & isset($_POST['Area']) & isset($_POST['Estatus']) & isset($_POST['User_Name']) & isset($_POST['Password']) & (trim($_POST['Nombre']) != "") & (trim($_POST['Ap_Paterno']) != "") & (trim($_POST['Ap_Materno']) != "") & (trim($_POST['Email']) != "") & (trim($_POST['Area']) != "") & (trim($_POST['Estatus']) != "") & (trim($_POST['User_Name']) != "") & (trim($_POST['Password']) != "");
	        //comprueba si todos los campos requeridos existen en el post
	        if ($validation) {
	        	
	        	if (isset($_FILES['foto_file']['name'])) 
	        		$filename = $_FILES['foto_file']['name'];
	        	
	        	$success = $this->Usuario->insertNewUser($_POST,$filename);

	        	switch ($success['success']) {
	        		case '-2':  //error en la base de datos
	        			$data['resultStatus'] = '<div class="row" style="color: var(--red-darken-1); font-size: 26px;">
											    <div class="col-12 text-center">
											        Error en la base de datos, intenta de nuevo
											    </div>
											</div>';
	        			break;
	        		case '-1':  //erro en el formulario
	        			$data['resultStatus'] = '<div class="row" style="color: var(--red-darken-1); font-size: 26px;">
											    <div class="col-12 text-center">
											        Error en formulario
											    </div>
											</div>';
						$data['errorForm'] = $success['errorForm'];
	        			break;
	        		case '1':	//actualizacion correcta
	        			//se almacena la imagen del usuario
	        			$this->uploadImageUser($_FILES,$success['id_new_user']);
						//redirecciona a la vista principal de usuarios
						$this->view("admin/userSuccessView");
	        			break;
	        		
	        	}
	        	
	        	
	        }
	        else{
	        	$data['resultStatus'] = '<div class="row" style="color: var(--red-darken-1); font-size: 26px;">
											    <div class="col-12 text-center">
											        Error en formulario, intenta de nuevo
											    </div>
											</div>';
	        }


	        $this->view("templates/header",$data);
            $this->view("admin/crearUserView",$data);
            $this->view("templates/footer",$data);

	    } 
	    else{	//no es post, vista principal de crear usuario
	        $this->view("templates/header",$data);
            $this->view("admin/crearUserView",$data);
            $this->view("templates/footer",$data);
	    }
	}

	public function editarUser(){
		//echo("TIENE DETALLE EN CAMPO AREA CORREGIR HOY");
		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1) {
            header("Location: ".base_url."Login");
        }

        //Titulo de la pagina y archivos css y js necesarios
		$data = [
            'titulo'    => 'IE Portal | Editar User',
            'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/admin/editarUser.css">',
            'extra_js'  => '<script src="'. base_url . 'public/js/admin/editarUser.js"></script>'
        ];

        if(isset($_POST['editarUser'])){	//post para editar los cambios en la info del usuario
	    	//var_dump($_POST);
	        if (isset($_POST['Id_Usuario']) & is_numeric($_POST['Id_Usuario'])) {
	        	if ($this->Usuario->getUserById($_POST['Id_Usuario'])) {

	        		$validation = isset($_POST['Id_Usuario']) & isset($_POST['Nombre']) & isset($_POST['Ap_Paterno']) & isset($_POST['Ap_Materno']) & isset($_POST['Email']) & isset($_POST['Area']) & isset($_POST['Estatus']) & isset($_POST['User_Name']) & isset($_POST['Password']) & is_numeric($_POST['Id_Usuario']) & (trim($_POST['Id_Usuario']) != "") & (trim($_POST['Nombre']) != "") & (trim($_POST['Ap_Paterno']) != "") & (trim($_POST['Ap_Materno']) != "") & (trim($_POST['Email']) != "") & (trim($_POST['Area']) != "") & (trim($_POST['Estatus']) != "") & (trim($_POST['User_Name']) != "") & (trim($_POST['Password']) != "");
			        //comprueba si todos los campos requeridos existen en el post
			        if ($validation) {
			        	$success = $this->Usuario->updateUserInfo($_POST);

			        	switch ($success['success']) {
			        		case '-2':  //error en la base de datos
			        			$data['resultStatus'] = '<div class="row" style="color: var(--red-darken-1); font-size: 26px;">
													    <div class="col-12 text-center">
													        Error en la base de datos, intenta de nuevo
													    </div>
													</div>';
			        			break;
			        		case '-1':  //erro en el formulario
			        			$data['resultStatus'] = '<div class="row" style="color: var(--red-darken-1); font-size: 26px;">
													    <div class="col-12 text-center">
													        Error en formulario
													    </div>
													</div>';
								$data['errorForm'] = $success['errorForm'];
			        			break;
			        		case '0':	//sin cambios en la informacion obtenida
			        			$foto_name = $this->updateImageUser($_FILES,$_POST['Id_Usuario']);
			        			if ($foto_name) {
			        				$this->Usuario->updateImgNameUser($foto_name,$_POST['Id_Usuario']);
			        				$data['resultStatus'] = '<div class="row" style="color: var(--green-darken-1); font-size: 26px;">
													    <div class="col-12 text-center">
													        Informacion actualizada correctamente
													    </div>
													</div>';
			        			}else{
			        				$data['resultStatus'] = '<div class="row" style="color: var(--orange-darken-2); font-size: 26px;">
													    <div class="col-12 text-center">
													        Información sin cambios
													    </div>
													</div>';
			        			}
			        			
			        			
			        			break;
			        		case '1':	//actualizacion correcta
			        			$foto_name = $this->updateImageUser($_FILES,$_POST['Id_Usuario']);
			        			if ($foto_name) {
			        				$this->Usuario->updateImgNameUser($foto_name,$_POST['Id_Usuario']);
			        			}
			        			$data['resultStatus'] = '<div class="row" style="color: var(--green-darken-1); font-size: 26px;">
													    <div class="col-12 text-center">
													        Informacion actualizada correctamente
													    </div>
													</div>';
			        			break;
			        		
			        	}
			        }
			        else{
			        	$data['resultStatus'] = '<div class="row" style="color: var(--red-darken-1); font-size: 26px;">
													    <div class="col-12 text-center">
													        Error en formulario, intenta de nuevo
													    </div>
													</div>';
			        }

			        //se carga la info del usuario para mostrar en vista pase lo que pase
			        $infoUser = $this->Usuario->getUserById($_POST['Id_Usuario']);
		        	$data['infoUser'] = $infoUser;

			        $this->view("templates/header",$data);
		            $this->view("admin/editarUserView",$data);
		            $this->view("templates/footer",$data);
	        	}
	        	else{
	        		header("Location: ".base_url."UsersAdmin");
	        	}
	        }
	        else{	//error en el input de id_usuario
	        	header("Location: ".base_url."UsersAdmin");
	        }

	    }

        else if (isset($_GET['id_user'])) { //GET para mostrar la información actual del user
	        $id_user = $_GET['id_user'];
	        if (!(is_numeric($id_user))) //seguridad si se ingresa parámetro inválido
	        	header("Location: ".base_url."UsersAdmin");
	       	$infoUser = $this->Usuario->getUserById($id_user);
		    if (!$infoUser) {
		    	header("Location: ".base_url."Inicio");
		    	exit();
		    }
		    else{

		    	$data['infoUser'] = $infoUser;
		    	$this->view("templates/header",$data);
	            $this->view("admin/editarUserView",$data);
	            $this->view("templates/footer",$data);
		    }
	    } 
	    else{	//ni post ni get
	        header("Location: ".base_url."UsersAdmin");
	    }

	}

	public function verUser(){
		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1) {
            header("Location: ".base_url."Login");
        }

        //Titulo de la pagina y archivos css y js necesarios
		$data = [
            'titulo'    => 'IE Portal | Ver User',
            'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/admin/verUser.css">',
            'extra_js'  => '<script src="'. base_url . 'public/js/admin/verUser.js"></script>'
        ];

        if (isset($_GET['id_user'])) { //GET para mostrar la información actual del user
	        $id_user = $_GET['id_user'];
	        if (!(is_numeric($id_user))) //seguridad si se ingresa parámetro inválido
	        	header("Location: ".base_url."UsersAdmin");
	       	$infoUser = $this->Usuario->getUserById($id_user);
		    if (!$infoUser) {
		    	header("Location: ".base_url."Inicio");
		    	exit();
		    }
		    else{
		    	
		    	
		    	$infoUser->Fecha_Format = $this->formatearFecha($infoUser->Fecha_Registro_Usuario);
		    	$data['infoUser'] = $infoUser;
		    	$this->view("templates/header",$data);
	            $this->view("admin/verUserView",$data);
	            $this->view("templates/footer",$data);
		    }
	    } 
	    else{	//ni post ni get
	        header("Location: ".base_url."UsersAdmin");
	    }
	}


	public function decToBin($numDec){ //decimal a binario
		$dataReturn = decbin($numDec);

		while (strlen($dataReturn) < 4) {	//se agregan ceros a la izquierda para siempre tener un string de 4 bits
			$dataReturn = "0".$dataReturn;
		}
		return $dataReturn;

	}

	public function generarInfoPermisosForTable($numBin){
		$dataReturn = '';

		//echo $numDec.\'<br>\';
		//$numBin = $this->decToBin($numDec);

		$dataReturn.= '<div><span class=\'v-a-middle\'>C: </span>';
		if ($numBin[3] == '1') {$dataReturn.=' <i class=\'material-icons check v-a-middle\'>check</i></div>';}
		else 	{$dataReturn.=' <i class=\'material-icons myClose v-a-middle\'>close</i></div>';}

		$dataReturn.= '<div><span class=\'v-a-middle\'>R: </span>';
		if ($numBin[2] == '1') {$dataReturn.=' <i class=\'material-icons check v-a-middle\'>check</i></div>';}
		else 	{$dataReturn.=' <i class=\'material-icons myClose v-a-middle\'>close</i></div>';}

		$dataReturn.= '<div><span class=\'v-a-middle\'>U: </span>';
		if ($numBin[1] == '1') {$dataReturn.=' <i class=\'material-icons check v-a-middle\'>check</i></div>';}
		else 	{$dataReturn.=' <i class=\'material-icons myClose v-a-middle\'>close</i></div>';}

		$dataReturn.= '<div><span class=\'v-a-middle\'>D: </span>';
		if ($numBin[0] == '1') {$dataReturn.=' <i class=\'material-icons check v-a-middle\'>check</i></div>';}
		else 	{$dataReturn.=' <i class=\'material-icons myClose v-a-middle\'>close</i></div>';}

		return $dataReturn;

	}

	public function formatearFecha($fecha = null){
		//$fecha = "2020-01-20 15:30:00";
		//$date = new DateTime($fecha);
		//se asigna hora local en México
		setlocale(LC_TIME, 'es_CO.UTF-8');
        $results = strftime("%A, %d  de %B del %G", strtotime($fecha))." a las ".date('g:i a', strtotime($fecha));;

		return $results;
	}

	public function permisosToCadenaExport($permisosArray){
			//cadenas permisos para exportación
			$aux_permisos = ($permisosArray[3])?"C ":"* ";
			$aux_permisos.= ($permisosArray[2])?"R ":"* ";
			$aux_permisos.= ($permisosArray[1])?"U ":"* ";
			$aux_permisos.= ($permisosArray[0])?"D ":"* ";
			return $aux_permisos;
	}

	public function uploadImageUser($files = null,$id_user = null){

		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1 || $files == null || $id_user == null) {
            header("Location: ".base_url."Login");
        }

		//validación del File
	    $MAX_SIZE = 8000000;
	    $allowed_mime_type_arr = array('jpeg','png','jpg');
	    //Nota: En fetch no funciona get_mime_by_extension()
	    
	    $arrayAux = explode('.', $files['foto_file']['name']);
	    $mime = end($arrayAux); //obtiene la extensión del file
	    //se checa si se cumplen todas las condiciones para un file correcto
	    if((isset($files['foto_file']['name'])) && ($files['foto_file']['name']!="") && ($files['foto_file']['size']<=$MAX_SIZE)){
	        if(in_array($mime, $allowed_mime_type_arr)){
	            $band = true;
	        }else{
	            $band = false;
	        }
	    }else{
	        $band = false;
	    }

	    //se crea la carpeta si aun no existe del nuevo usuario
	    $carpeta = BASE_PATH."public/media/users_img/".$id_user;
		if (!file_exists($carpeta)) 
		    mkdir($carpeta, 0777, true);
		else
			$this->removeOnlyFilesDir($carpeta,false);
		

	    if ($band) {//se sube la foto original
	    	$img_name = $files['foto_file']['name'];
	    	
	    	$ruta = BASE_PATH."public/media/users_img/".$id_user."/".$img_name;
			copy($files['foto_file']['tmp_name'],$ruta); //se guarda la imagen en la carpeta
	    }
	    else{//se sube la foto por default
	    	$img_name = "default.png";
	    	
	    	$path_fuente = BASE_PATH."public/media/images/default.png";
	    	$ruta = BASE_PATH."public/media/users_img/".$id_user."/".$img_name;
			copy($path_fuente, $ruta); //se guarda la imagen default en la carpeta
	    }
	    return $img_name;
	    
	}

	public function updateImageUser($files = null, $id_user = null){

		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1 || $files == null || $id_user == null) {
            header("Location: ".base_url."Login");
        }

		//validación del File
	    $MAX_SIZE = 8000000;
	    $allowed_mime_type_arr = array('jpeg','png','jpg');
	    //Nota: En fetch no funciona get_mime_by_extension()
	    
	    $arrayAux = explode('.', $files['foto_file']['name']);
	    $mime = end($arrayAux); //obtiene la extensión del file
	    //se checa si se cumplen todas las condiciones para un file correcto
	    if((isset($files['foto_file']['name'])) && ($files['foto_file']['name']!="") && ($files['foto_file']['size']<=$MAX_SIZE)){
	        if(in_array($mime, $allowed_mime_type_arr)){
	            $band = true;
	        }else{
	            $band = false;
	        }
	    }else{
	        $band = false;
	    }

	    if ($band) {//se sube la foto original
	    	//se crea la carpeta si aun no existe del nuevo usuario
		    $carpeta = BASE_PATH."public/media/users_img/".$id_user;
			if (!file_exists($carpeta)) 
			    mkdir($carpeta, 0777, true);
			else
				$this->removeOnlyFilesDir($carpeta,true);

	    	$img_name = $files['foto_file']['name'];
	    	$ruta = BASE_PATH."public/media/users_img/".$id_user."/".$img_name;
			copy($files['foto_file']['tmp_name'],$ruta); //se guarda la imagen en la carpeta
	    }
	    else{//se sube la foto por default
	    	$img_name = false;	
	    }

	    return $img_name;
	}

	public function comprobarCarpetaUser($id_user = null){ //funcion para evitar inconsistencias en imagenes de usuario
		if (!isset($_SESSION['userdata']) || $_SESSION['userdata']->Modo_Admin!=1 || $id_user == null) {
            header("Location: ".base_url."Login");
        }

        $carpeta = BASE_PATH."public/media/users_img/".$id_user;
		if (!file_exists($carpeta)){	//se crea una carpeta si es que no existe o si se borró por accidente del repositorio
		    mkdir($carpeta, 0777, true);
		    $img_name = "default.png";
	    	
	    	$path_fuente = BASE_PATH."public/media/images/default.png";
	    	$ruta = BASE_PATH."public/media/users_img/".$id_user."/".$img_name;
			copy($path_fuente, $ruta); //se guarda la imagen default en la carpeta
		}
	}

	//Función para borrar carpetas de grupos
    public function removeOnlyFilesDir($dir,$ind) { //si ind == 1 no borra el directorio original, caso contrario, si lo borra
           $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
              (is_dir("$dir/$file")) ? $this->removeOnlyFilesDir("$dir/$file",false) : unlink("$dir/$file");
            }

            if ($ind) return;
            else return rmdir($dir);
    }

    //-----------------------FUNCIONES AUXILIARES PARA COMPLEMENTAR LOS PRINCIPALES MÓDULOS-----------------------
    //función para generar la paginación dinámica
    public function generarLinks($numPage,$total_pages,$extra_cad = "",$filtro = 1){
            //$extra_cad sirve para determinar la paginacion conforme a si se realizó una busqueda
            //Creación de links para el pagination
            $links = "";

            //FLECHA IZQ (PREV PAGINATION)
            if ($numPage>1) {
                $links.= '<li class="page-item">
                            <a class="page-link" href=" '.base_url.'UsersAdmin/index/?numPage=1'.$extra_cad.'&filtro='.$filtro.' " data-toggle="tooltip" data-placement="top" title="Primera página">
                                <i class="material-icons">first_page</i>
                            </a>
                        </li>';
                $links.= '<li class="page-item">
                            <a class="page-link" href=" '.base_url.'UsersAdmin/index/?numPage='.($numPage-1).$extra_cad.'&filtro='.$filtro.' " data-toggle="tooltip" data-placement="top" title="Página anterior">
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
                                <a class="page-link" href=" '.base_url.'UsersAdmin/index/?numPage='.($ind).$extra_cad.'&filtro='.$filtro.' ">
                                    '.($ind).'
                                </a>
                            </li>';
                }
            }

            //FLECHA DERECHA (NEXT PAGINATION)
            if ($numPage<$total_pages) {

                $links.= '<li class="page-item">
                            <a class="page-link" href=" '.base_url.'UsersAdmin/index/?numPage='.($numPage+1).$extra_cad.'&filtro='.$filtro.' " data-toggle="tooltip" data-placement="top" title="Siguiente página">
                            <i class="material-icons">navigate_next</i>
                            </a>
                        </li>';
                $links.= '<li class="page-item">
                            <a class="page-link" href=" '.base_url.'UsersAdmin/index/?numPage='.($total_pages).$extra_cad.'&filtro='.$filtro.' " data-toggle="tooltip" data-placement="top" title="Última página">
                            <i class="material-icons">last_page</i>
                            </a>
                        </li>';
            }

            return $links;
    }

    //función para generar la información de la tabla de forma dinámica
    public function generarInfoTable($rows,$filtro = 1){
            //se genera la tabulacion de la informacion por backend
            $infoTable['header'] = "";
            $infoTable['body'] = "";
    
                
            switch ($filtro) {
                case '1': //todos los usuarios
                case '2': //administradores
                case '3': //otros
                    $infoTable['header'] .= '
                        <th class="column1">#</th>
									<th class="column2">Usuario</th>
									<th class="column3">Nombre</th>

									<th class="column4">Clases</th>
									

									<th class="column5">Modo Administrador</th>
									<th >Editar</th>
									<th >Ver</th>
									<th >Estatus</th>
									<th >Foto</th>
                    ';
                    foreach ($rows as $user) {
                        $infoTable['body'].= '<tr>';
			  			$infoTable['body'].= '	<td class="column1">'.$user->Id_Usuario.'</td>
								        <td class="column2">'.$user->User_Name.'</td>
								        <td class="column3"> '.mb_strtoupper($user->Nombre_Completo).'</td>';

						//permisos del sistema completo
						$clases = $this->generarInfoPermisosForTable($user->Clases);

						$infoTable['body'].= '	
										<td class="column4">
											<button type="button" class="btn btn-opacity" data-html="true" data-title="permisos" data-toggle="popover" data-placement="top" data-trigger="focus"  data-content="'.$clases.'"><i class="material-icons v-a-middle">accessibility</i></button>
										</td>

									';
								        
			  			
				        if($user->Modo_Admin)
				        	$infoTable['body'].= '<td class="modoDios column5">ACTIVADO</td>';
				        
				        else
				        	$infoTable['body'].= '<td class="column5">DESACTIVADO</td>';
				        

				        $infoTable['body'].= '	<td>
								        	<a class="myLinks" href="'.base_url.'usersAdmin/editarUser/?id_user='.$user->Id_Usuario.'">
								        		<i class="material-icons">edit</i>
								        	</a>
								        </td>
								        <td>
								        	<a class="myLinks" href="'.base_url.'usersAdmin/verUser/?id_user='.$user->Id_Usuario.'">
								        		<i class="material-icons">visibility</i>
								        	</a>
								        </td>';
				        
				        if($user->Estatus)
				        	$infoTable['body'].= '<td class="user-activo">ACTIVO</td>';
				        
				        else
				        	$infoTable['body'].= '<td class="user-inactivo">INACTIVO</td>';
				        
				        //$infoTable['body'].= '<td>'.$user->Fecha_Registro_Usuario.'</td>';
				        $this->comprobarCarpetaUser($user->Id_Usuario);
				        $infoTable['body'].= '<td><img src="'.base_url.'public/media/users_img/'.$user->Id_Usuario.'/'.$user->Path_Imagen_User.'" style="max-width: 50px;"></td>';
				        $infoTable['body'].= '</tr>';
                    }
                break;
            }

            return $infoTable;
    }

    //función para generar los links respectivos dependiendo del filtro y/o cadena de búsqueda
    public function generarExportLinks($extra_cad = "",$filtro = 1){
        if ($extra_cad != "") {
            $dataReturn['csv'] =  base_url.'UsersAdmin/exportarInfo/?tipo_export=CSV'.$extra_cad.'&filtroActual='.$filtro;
            $dataReturn['excel'] =  base_url.'UsersAdmin/exportarInfo/?tipo_export=EXCEL'.$extra_cad.'&filtroActual='.$filtro;
            $dataReturn['pdf'] =  base_url.'UsersAdmin/exportarInfo/?tipo_export=PDF'.$extra_cad.'&filtroActual='.$filtro;
            //return $dataReturn;
        }
        else{
            $dataReturn['csv'] =  base_url.'UsersAdmin/exportarInfo/?tipo_export=CSV'.$extra_cad.'&filtroActual='.$filtro;
            $dataReturn['excel'] =  base_url.'UsersAdmin/exportarInfo/?tipo_export=EXCEL'.$extra_cad.'&filtroActual='.$filtro;
            $dataReturn['pdf'] =  base_url.'UsersAdmin/exportarInfo/?tipo_export=PDF'.$extra_cad.'&filtroActual='.$filtro;
        }
        return $dataReturn;
    }

    //función fetch para buscar por la cadena introducida dependiendo del filtro
    public function buscarPorCadena(){
        /*Aquí van condiciones de permisos*/

        if (isset($_POST['cadena'])) {
            $cadena = trim($_POST['cadena']); 
            $filtroActual = trim($_POST['filtroActual']);

            $results = $this->Usuario->getUsersByCadena($cadena,$filtroActual);
            $extra_cad = ($cadena != "")?("&cadena=".$cadena):""; //para links conforme a búsqueda

            //$dataReturn = "jeje";

            $dataReturn['infoTable'] = $this->generarInfoTable($results['rows_Users'],$filtroActual);
            $dataReturn['links'] = $this->generarLinks($results['numPage'],$results['total_pages'],$extra_cad,$filtroActual);
            $dataReturn['export_links'] = $this->generarExportLinks($extra_cad,$filtroActual);
            $dataReturn['total_rows'] = "Total registros: ".$results['total_rows'];
            $dataReturn['dropdownColumns'] = $this->generateDropdownColumns($filtroActual);

            
            echo json_encode($dataReturn);
        }
        else{
            header("Location: ".base_url."Inicio");
            exit();
        }
    }

    //función para exportar la inforación dependiendo del filtro 
    public function exportarInfo(){
        /*checar permisos*/

        if (!isset($_REQUEST['tipo_export'])) {
            header("Location: ".base_url."Inicio");
            exit();
        }
        //se recupera el catalogo actual para poder consultar conforme al mismo
        if (!isset($_REQUEST['filtroActual']) || !is_numeric($_REQUEST['filtroActual']) || !($_REQUEST['filtroActual']>=MIN_FILTRO_GC) || !($_REQUEST['filtroActual']<=MAX_FILTRO_GC)) 
                $filtroActual = 1;
            else
                $filtroActual = $_REQUEST['filtroActual'];

        $from_where_sentence = "";
        //se genera la sentencia from where para realizar la correspondiente consulta
        if (isset($_REQUEST['cadena'])) 
            $from_where_sentence = $this->Usuario->generateFromWhereSentence($_REQUEST['cadena'],$filtroActual);
        else
            $from_where_sentence = $this->Usuario->generateFromWhereSentence("",$filtroActual);

        
        
        //var_dump($_REQUEST);
        $tipo_export = $_REQUEST['tipo_export'];

        if ($tipo_export == 'EXCEL') {
            //se realiza exportacion de usuarios a EXCEL
            $rows_USERS = $this->Usuario->getAllInfoUsersByCadena($from_where_sentence);
            switch ($filtroActual) {
                case '1': 
                case '2':
                case '3':
                    $filename = "usuarios";
					//se realiza exportacion de usuarios a EXCEL
					$users = $this->Usuario->getAllInfoUsersByCadena($from_where_sentence);
					$csv_data="#,User Name,Nombre completo,Correo,Area,Clases,Modo Admin\n";
					foreach ($users as $user) {
						//cadenas permisos para exportación
						$aux_clases = $this->permisosToCadenaExport($user->Clases);

						$aux_modo_admin = ($user->Modo_Admin)?"Activado":"Desactivado";

						$csv_data.= $user->Id_Usuario.",".
									$user->User_Name.",".
									$user->Nombre." ".$user->Ap_Paterno." ".$user->Ap_Materno.",".
									$user->Email.",".
									$user->Area.",".

									$aux_clases.",".

									$aux_modo_admin."\n";

					}
                break;
            }
            //se genera el archivo csv o excel
            $csv_data = utf8_decode($csv_data); //escribir información con formato utf8 por algún acento
            header("Content-Description: File Transfer");
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=".$filename.".csv");
            echo $csv_data;
            //header("Location: ".base_url."UsersAdmin");

        }
        elseif($tipo_export == 'PDF'){
            $rows_USERS = $this->Usuario->getAllInfoUsersByCadena($from_where_sentence);
            

            header("Content-type: application/pdf");
            header("Content-Disposition: inline; filename=usuarios.pdf");
            echo $this->generarPDF($rows_USERS,$_REQUEST['cadena'],$filtroActual);
        }
        else{
            header("Location: ".base_url."Inicio");
            exit();
        }
    }

    //función para armar el archivo PDF dependiendo del filtro y/o cadena de búsqueda
    public function generarPDF($users,$cadena = "",$filtroActual = '1'){
        //require('../libraries/PDF library/fpdf16/fpdf.php');
        switch ($filtroActual) {
            case '1': $filename="vista general";break;
            case '2': $filename="administradores";break;
            case '3': $filename="otros";break;
        }

        $data['subtitulo']      = 'Usuarios del sistema: '.$filename;

        if ($cadena != "") {
            $data['msg'] = 'todos los Usuarios con filtro: '.$cadena.'';
        }
        else{
            $data['msg'] = 'todos los Usuarios';
        }

        //---Aquí va la info según sea el filtro de ED seleccionado
        switch ($filtroActual) {
            case '1':
            case '2':
            case '3':
                $data['columns'] =  [
			                            'Id',
			                            'User name',
			                            'Nombre completo',
			                            'Clases',
			                            'Admin'
		                            ];  
		        $data['field_names'] = [
			                            'Id_Usuario',
			                            'User_Name',
			                            'Nombre_Completo',
			                            'Clases',
			                            'Modo_Admin'
		                            ]; 

		        foreach ($users as $key => $user) {
		        	$users[$key]->Nombre_Completo	= $user->Nombre." ".$user->Ap_Paterno." ".$user->Ap_Materno;
					$users[$key]->Clases 			= $this->permisosToCadenaExport($user->Clases);
					$users[$key]->Modo_Admin = ($user->Modo_Admin)?"Sí":"No";
				}
            break;
            
        }
        //---fin de la info del ED
        

        $data['rows'] = $users;
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

    //funcion para borrar variable sesión para filtro de rangos de fechas
    public function removeRangosFechasSesion(){

        if (isset($_REQUEST['filtroActual'])) {
            unset($_SESSION['userdata']->rango_inicio_user);
            unset($_SESSION['userdata']->rango_fin_user);

            header("Location: ".base_url."UsersAdmin/index/?filtro=".$_REQUEST['filtroActual']);
            exit();
        }
        else{
            header("Location: ".base_url."Inicio");
            exit();
        }
        
    }

    //función que filtra las columnas deseadas por el usuario
    public function generateDropdownColumns($filtro=1){
        //parte de permisos

        $dropDownColumn = '';
        //generación de dropdown dependiendo del filtro
        switch ($filtro) {
            case '1':
                $campos = ['Id usuario','Username','Nombre','Clases','Modo administrador'];
            break;
            case '2':
                $campos = ['Id usuario','Username','Nombre','Clases','Modo administrador'];
            break;
            case '3':
                $campos = ['Id usuario','Username','Nombre','Clases','Modo administrador'];
            break;
        }
        //gestión de cada columna
        $ind = 1;
        foreach($campos as $campo){
            $checked = ($_SESSION['userdata']->columns_USER['column'.$ind] == 'show')?'checked':'';
            $dropDownColumn.= ' <div class="form-check">
                                    <input class="form-check-input checkColumns" type="checkbox" value="'.$_SESSION['userdata']->columns_USER['column'.$ind].'" onchange="hideShowColumn(this.id);" id="column'.$ind.'" '.$checked.'>
                                    <label class="form-check-label" for="column'.$ind.'">
                                        '.$campo.'
                                    </label>
                                </div>';
            $ind++;
        }
        $dropDownColumn.= '     <div class="dropdown-divider">
                                </div>
                                <div class="form-check">
                                    <input id="checkAll" class="form-check-input" type="checkbox" value="hide" onchange="hideShowAll(this.id);" id="column'.$ind.'" checked>
                                    <label class="form-check-label" for="column'.$ind.'">
                                        Todo
                                    </label>
                                </div>';
        return $dropDownColumn;
    }

    //función para checar los cambios de filtro y poder asignar los valores correspondientes de las columnas a la session
    public function setColumnsSession($filtroActual=1){
        //si el filtro existe y esta dentro de los parámetros continua
        if (isset($_SESSION['userdata']->filtro_USER) && $_SESSION['userdata']->filtro_USER >= MIN_FILTRO_GC && $_SESSION['userdata']->filtro_USER<=MAX_FILTRO_GC ) {
            //si cambia el filtro se procde a cambiar los valores de las columnas que contiene el filtro seleccionado
            if ($_SESSION['userdata']->filtro_USER != $filtroActual) {
                $_SESSION['userdata']->filtro_USER = $filtroActual;
                unset($_SESSION['userdata']->columns_USER); //se borra las columnas del anterior filtro
                //se asignan las nuevas columnas y por default se muestran todas (atributo show)
                for($i=0;$i<$this->numColumnsUSER[$_SESSION['userdata']->filtro_USER -1];$i++) 
                    $_SESSION['userdata']->columns_USER['column'.($i+1)] = 'show';

            }
        }
        else{ //si no existe el filtro entonces se inicializa con el primero por default
            $_SESSION['userdata']->filtro_USER = $filtroActual;
            unset($_SESSION['userdata']->columns_USER);
            for($i=0;$i<$this->numColumnsUSER[$_SESSION['userdata']->filtro_USER -1];$i++)
                $_SESSION['userdata']->columns_USER['column'.($i+1)] = 'show';

            
        }
        //echo "filtro: ".var_dump($_SESSION['userdata']->filtro_USER)."<br>br>";
        //echo "columns: ".var_dump($_SESSION['userdata']->columns_USER)."<br>br>";
    }

    //función fetch que actualiza los valores de las columnas para la session
    public function setColumnFetch(){
        if (isset($_POST['columName']) && isset($_POST['valueColumn'])) {
            $_SESSION['userdata']->columns_USER[$_POST['columName']] = $_POST['valueColumn'];
            echo json_encode("ok");
        }
        else{
            header("Location: ".base_url."Inicio");
            exit();
        }
    }

    //dar formato a fecha y hora
    public function formatearFechaHora($fecha = null){
        $f_h = explode(" ", $fecha);

        setlocale(LC_TIME, 'es_CO.UTF-8'); //hora local méxico

        $results['Fecha'] = strftime("%d  de %B del %G", strtotime($f_h[0]));
        $results['Hora'] = date('g:i a', strtotime($f_h[1]));

        return $results;
    }
}

?>