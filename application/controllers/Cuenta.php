<?php

/**
 * 
 */
class Cuenta extends Controller
{
	public $Usuario;
	public function __construct(){
		$this->Usuario = $this->model('Usuario');
	}

	public function index(){

		if (!isset($_SESSION['userdata'])) {
            header("Location: ".base_url."Inicio");
            exit();
        }

		$data = [
					'titulo' 		=> 'Mi cuenta',
					'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/system/cuenta/index.css">',
            		'extra_js'  => '<script src="'. base_url . 'public/js/system/cuenta/index.js"></script>'
				];
		

        $id_user = $_SESSION['userdata']->Id_Usuario;
        if (!(is_numeric($id_user))) //seguridad si se ingresa parámetro inválido
        	header("Location: ".base_url."Inicio");



        /*PROCESO DE ACTUALIZAR INFORMACIÓN DE USUARIO SI HUBO POST*/
        if(isset($_POST['editarInfo'])){	//post para editar los cambios en la info del usuario
        	
	    	//validación del password   
    		$validation = isset($_POST['Password']) & (trim($_POST['Password']) != "");
	        //comprueba si todos los campos requeridos existen en el post
	        if ($validation) {
	        	$success = $this->Usuario->updateUserPassword($_POST);

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
	        			$foto_name = $this->updateImageUser($_FILES,$_SESSION['userdata']->Id_Usuario);
	        			if ($foto_name) {
	        				$this->Usuario->updateImgNameUser($foto_name,$_SESSION['userdata']->Id_Usuario);
	        				$data['resultStatus'] = '<div class="row" style="color: var(--green-darken-1); font-size: 26px;">
											    <div class="col-12 text-center">
											        Informacion actualizada correctamente
											    </div>
											</div>';
							//se actualiza la variable de session
							$_SESSION['userdata'] = $this->Usuario->getUserById($_SESSION['userdata']->Id_Usuario);
	        			}else{
	        				$data['resultStatus'] = '<div class="row" style="color: var(--orange-darken-2); font-size: 26px;">
											    <div class="col-12 text-center">
											        Información sin cambios
											    </div>
											</div>';
	        			}
	        			
	        			
	        			break;
	        		case '1':	//actualizacion correcta
	        			$foto_name = $this->updateImageUser($_FILES,$_SESSION['userdata']->Id_Usuario);
	        			if ($foto_name) {
	        				$this->Usuario->updateImgNameUser($foto_name,$_SESSION['userdata']->Id_Usuario);
	        			}
	        			$data['resultStatus'] = '<div class="row" style="color: var(--green-darken-1); font-size: 26px;">
											    <div class="col-12 text-center">
											        Informacion actualizada correctamente
											    </div>
											</div>';
						//se actualiza la variable de session
						$_SESSION['userdata'] = $this->Usuario->getUserById($_SESSION['userdata']->Id_Usuario);
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
	    }
        /*FIN POST*/
       	$infoUser = $this->Usuario->getUserById($id_user);
	    if (!$infoUser) {
	    	header("Location: ".base_url."Inicio");
	    }
	    else{
	    	
	    	$infoUser->Fecha_Format = $this->formatearFecha($infoUser->Fecha_Registro_Usuario);
	    	$data['infoUser'] = $infoUser;
	    	$this->view("templates/header",$data);
            $this->view("system/cuenta/cuentaView",$data);
            $this->view("templates/footer",$data);
	    }
	    

	}
	//funcion para editar la info del usuario (solo foto y/o contraseña)
	public function editarInfo(){

	}
	//función para darle un formato a la fecha y hora exacta de creación del usuario en cestión
	public function formatearFecha($fecha = "1997-01-04 13:30:00"){
		//$fecha = "2020-01-20 15:30:00";
		//$date = new DateTime($fecha);
		//se asigna hora local en México
		setlocale(LC_TIME, 'es_CO.UTF-8');
        $results = strftime("%A, %d  de %B del %G", strtotime($fecha))." a las ".date('g:i a', strtotime($fecha));;

		return $results;
	}

	//función para actualizar la imagen del usuario
	public function updateImageUser($files = null, $id_user = null){

		//validación del File
	    $MAX_SIZE = 8000000;
	    $allowed_mime_type_arr = array('jpeg','png','jpg');
	    //Nota: En fetch no funciona get_mime_by_extension()
	    
	    //se checa si se cumplen todas las condiciones para un file correcto
	    if((isset($files['foto_file']['name'])) && ($files['foto_file']['name']!="") && ($files['foto_file']['size']<=$MAX_SIZE)){
	    	$arrayAux = explode('.', $files['foto_file']['name']);
	    	$mime = end($arrayAux); //obtiene la extensión del file

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
	//Función para borrar contenido de carpeta de fotos de usuario
    public function removeOnlyFilesDir($dir,$ind) { //si ind == 1 no borra el directorio original, caso contrario, si lo borra
           $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
              (is_dir("$dir/$file")) ? $this->removeOnlyFilesDir("$dir/$file",false) : unlink("$dir/$file");
            }

            if ($ind) return;
            else return rmdir($dir);
    }
}

?>