<?php
     class Login extends Controller{
        public $Usuario;    //variable para instanciar el modelo de Usuario
        public $Clase; 

        public function __construct(){
            $this->Usuario = $this->model('Usuario');   //se instancia model Usuario y ya puede ser ocupado en el controlador
            $this->Clase = $this->model('Clase');
        }

        public function index(){
            if (isset($_SESSION['userdata']->User_Name)) {
                header("Location: ".base_url."Inicio");
            }
            /* Se añade el llamado al archivo extra que contiene la función para hacer visible la contraseña*/
            $data = [
                'titulo'    => 'IE Portal | Inicio de sesión',
                'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/login/style.css">',
                'extra_js'  => '<script src="' . base_url . 'public/js/login/principal.js"></script>'
            ];

            //$this->view('templates/header', $data);
            $this->view('login/loginView', $data);
            //$this->view('templates/footer', $data);
        }

        public function login(){
            if (isset($_SESSION['userdata'])) {
                header("Location: ".base_url."Inicio");
            }

            if (isset($_POST['enviarLogin'])) { //comprobacion de post correcto
                //echo var_dump($_POST);
                //echo "<br><br><br>";
                $success = $this->Usuario->loginUser($_POST);

                if ($success) {
                    
                    $this->setDataSession($success);
                    

                    //Se definen las variables pará pasarlas al modelo e insertarlas en la tabla historial
                    $user = $_SESSION['userdata']->Id_Usuario;
                    $ip = $this->obtenerIp();
                    $this->Clase->historial($user,$ip,1,NULL);
                    
                    header("Location: ".base_url."Inicio");
                }
                else{
                    $data = [
                        'titulo'    => 'Sistema de remisiones | Inicio de sesión',
                        'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/login/style.css">',
                        'extra_js'  => ''
                    ];

                    $data['post'] = $_POST;
                    $data['ErrorMessage'] = "Error en usuario o contraseña";
                    $this->view('login/loginView', $data);
                }
            }
            else{
                header("Location: ".base_url."Login");
            }
            
        }

        public function setDataSession($data){
            print_r($data);//igual a succes
            $_SESSION['userdata'] = $data;
            if($_SESSION['userdata']->Clases[3] == '1' && $_SESSION['userdata']->Clases[1] == '1'){
                $hoy = date("Y-m-d");
                $_SESSION['userdata']->rango_inicio_iec = $hoy;
                $_SESSION['userdata']->rango_fin_iec = $hoy;
            }
            return;
        }

        public function logOut(){
            if (!isset($_SESSION['userdata'])) {
                header("Location: ".base_url."Login");
            }

            
            unset($_SESSION['userdata']);
            header("Location: ".base_url."Login");
        }
        public function obtenerIp()
        {
            $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $hosts = gethostbynamel($hostname);
            if (is_array($hosts)) {
                foreach ($hosts as $ip) {
                    return $ip;
                }
            }else{
                return $ip = '0.0.0.0';
            }
        }

        /**
         * Login GET 
         */
        public function loginGet(){
            if (isset($_SESSION['userdata'])) {
                header("Location: ".base_url."Inicio");
            }

            $success = $this->Usuario->loginUser($_GET);

            if ($success) {
                //echo "Login correcto";
                $this->setDataSession($success);
                //echo var_dump($_SESSION['userdata']);

                //Se definen las variables pará pasarlas al modelo e insertarlas en la tabla historial
                $user = $_SESSION['userdata']->Id_Usuario;
                $ip = $this->obtenerIp();
                $this->Clase->historial($user,$ip,1,NULL);
                
                header("Location: ".base_url."Inicio");
            }
            else{
                $data = [
                    'titulo'    => 'Sistema de remisiones | Inicio de sesión',
                    'extra_css' => '<link rel="stylesheet" href="'. base_url . 'public/css/login/style.css">',
                    'extra_js'  => ''
                ];

                $data['post'] = $_GET;
                $data['ErrorMessage'] = "Error en usuario o contraseña";
                $this->view('login/loginView', $data);
            }
        }

        /* ----- ----- ----- Endpoint Fetch ----- ----- ----- */
        public function loginFetch(){
            if (isset($_SESSION['userdata'])) {
                $data_p['status']     = true;
                $data_p['loginExist'] = true;
                echo json_encode($data_p);
            }else{
                $success = $this->Usuario->loginUser($_POST);

                if ($success) {
                    $this->setDataSession($success);
                    $user = $_SESSION['userdata']->Id_Usuario;
                    $ip = $this->obtenerIp();
                    $this->Clase->historial($user,$ip,1,NULL);
                    
                    $data_p['status'] = true;
                    echo json_encode($data_p);
                }
                else{
                    $data_p['status'] = false;
                    $data_p['error_message'] = 'Usuario o contraseña incorrectos';

                    echo json_encode($data_p);
                }
            }
        }
     }
