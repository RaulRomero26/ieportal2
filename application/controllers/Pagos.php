<?php
/*
    Filtros (pagos):
    1  - Pagos Maestros
    2  - Pagos Alumnos

*/

use Mpdf\Tag\Img;

class Pagos extends Controller
{

    public $Catalogo;
    public $Pago;
    public $numColumnsRem; //número de columnas por cada filtro
    public $FV;

    public function __construct()
    {
        $this->Catalogo = $this->model('Catalogo');
        $this->Pago = $this->model('Pago');
        $this->numColumnsRem = [8,7,8,7];  //se inicializa el número de columns por cada filtro
        $this->FV = new FormValidator();
    }

    public function index()
    {
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Pagos[2] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }

        $data = [
            'titulo'    => 'Sistema de Pagos | Pagos',
            'extra_css' => '<link rel="stylesheet" href="' . base_url . 'public/css/system/gestorCasos/index.css">',
            'extra_js'  => '<script src="' . base_url . 'public/js/system/pagos/index.js"></script>
                            <script src="' . base_url . 'public/js/system/juridico/consumoDetenidosJuridico.js"></script>'
        ];

        //PROCESO DE FILTRADO DE EVENTOS DELICTIVOS
        if (isset($_GET['filtro']) && is_numeric($_GET['filtro']) && $_GET['filtro'] >= MIN_FILTRO_PA && $_GET['filtro'] <= MAX_FILTRO_PA) { //numero de filtro
            if ($_GET['filtro'] >= 13 && $_GET['filtro'] <= 14) { //si son filtros de validación
                if ($_SESSION['userdata']->Modo_Admin == '1' || $_SESSION['userdata']->Nivel_User == '1') { //si cuenta con los permisos necesarios
                    $filtro = $_GET['filtro'];
                } else { //si no cuenta con los permisos lo dirige a la vista general
                    $filtro = 1;
                }
            } else {
                $filtro = $_GET['filtro'];
            }
        } else {
            $filtro = 1;
        }

        //PROCESAMIENTO DE LAS COLUMNAS 
        $this->setColumnsSession($filtro);
        $data['columns_REM'] = $_SESSION['userdata']->columns_REM;

        //PROCESAMIENTO DE RANGO DE FOLIOS
        if (isset($_POST['rango_inicio']) && isset($_POST['rango_fin'])) {
            $_SESSION['userdata']->rango_inicio_rem = $_POST['rango_inicio'];
            $_SESSION['userdata']->rango_fin_rem = $_POST['rango_fin'];
        }

        //PROCESO DE PAGINATION
        if (isset($_GET['numPage'])) { //numero de pagination
            $numPage = $_GET['numPage'];
            if (!(is_numeric($numPage))) //seguridad si se ingresa parámetro inválido
                $numPage = 1;
        } else {
            $numPage = 1;
        }
        //cadena auxiliar por si se trata de una paginacion conforme a una busqueda dada anteriormente
        $cadena = "";
        if (isset($_GET['cadena'])) { //numero de pagination
            $cadena = $_GET['cadena'];
            $data['cadena'] = $cadena;
        }

        $where_sentence = $this->Pago->generateFromWhereSentence($cadena, $filtro);
        $extra_cad = ($cadena != "") ? ("&cadena=" . $cadena) : ""; //para links conforme a búsqueda

        $no_of_records_per_page = NUM_MAX_REG_PAGE; //total de registros por pagination
        $offset = ($numPage - 1) * $no_of_records_per_page; // desplazamiento conforme a la pagina

        $results_rows_pages = $this->Pago->getTotalPages($no_of_records_per_page, $where_sentence);   //total de páginas de acuerdo a la info de la DB
        $total_pages = $results_rows_pages['total_pages'];

        if ($numPage > $total_pages) {
            $numPage = 1;
            $offset = ($numPage - 1) * $no_of_records_per_page;
        } //seguridad si ocurre un error por url     

        $rows_Pagos = $this->Pago->getDataCurrentPage($offset, $no_of_records_per_page, $where_sentence);    //se obtiene la información de la página actual

        //guardamos la tabulacion de la información para la vista
        $data['infoTable'] = $this->generarInfoTable($rows_Pagos, $filtro);
        //guardamos los links en data para la vista
        $data['links'] = $this->generarLinks($numPage, $total_pages, $extra_cad, $filtro);
        //número total de registros encontrados
        $data['total_rows'] = $results_rows_pages['total_rows'];
        //filtro actual para Fetch javascript
        $data['filtroActual'] = $filtro;
        $data['dropdownColumns'] = $this->generateDropdownColumns($filtro);

        switch ($filtro) {
            case '1':
                $data['filtroNombre'] = "Maestros";
                break;
            case '2':
                $data['filtroNombre'] = "Alumnos";
                break;
            case '3':
                $data['filtroNombre'] = "Pagos maestros pendientes";
                break;
            case '4':
                $data['filtroNombre'] = "Pagos alumnos pendientes";
                break;
            
        }

        $this->view('templates/header', $data);
        $this->view('system/pagos/pagosView', $data);
        $this->view('templates/footer', $data);
    }
    public function getMaestro(){
        if (isset($_POST['id_maestro'])) {
            $id_maestro = $_POST['id_maestro'];
            $data = $this->Pago->getMaestro($id_maestro);
            echo json_encode($data);
        }
        else {
            header("Location: " . base_url . "Inicio");
            exit();
        }
    }

    public function getAlumno(){
        if (isset($_POST['id_alumno'])) {
            $id_alumno = $_POST['id_alumno'];
            $data = $this->Pago->getAlumno($id_alumno);
            echo json_encode($data);
        }
        else {
            header("Location: " . base_url . "Inicio");
            exit();
        }
    }
    //-----------------Funcion para editar remision-----------------------------------

    public function editarPagosMaestro()
    {
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Pagos[1] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }


        $data = [
            'titulo'                => ' IE | Editar Pagos Maestros',
            'titulo_1'              => 'Editar',
            'extra_css'             => '<link rel="stylesheet" href="' . base_url . 'public/css/system/gestorCasos/editargruposView.css">
                                        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">',

            'extra_js'              => '<script src="' . base_url . 'public/js/system/pagos/pagosMaestro.js"></script>'
        ];

        $this->view('templates/header', $data);
        $this->view('system/pagos/pagosMaestrosView', $data);
        $this->view('templates/footer', $data);
    }
    public function verPagosMaestro()
    {
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Pagos[2] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }


        $data = [
            'titulo'                => ' IE | Ver Pagos Maestros',
            'titulo_1'              => 'Ver',
            'extra_css'             => '<link rel="stylesheet" href="' . base_url . 'public/css/system/gestorCasos/editargruposView.css">
                                        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">',

            'extra_js'              => '<script src="' . base_url . 'public/js/system/pagos/verpagosMaestro.js"></script>'
        ];

        $this->view('templates/header', $data);
        $this->view('system/pagos/pagosMaestrosView-ReadOnly', $data);
        $this->view('templates/footer', $data);
    }

    public function editarPagosAlumno()
    {
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Pagos[1] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }


        $data = [
            'titulo'                => ' IE | Editar Pagos Alumnos',
            'titulo_1'              => 'Editar',
            'extra_css'             => '<link rel="stylesheet" href="' . base_url . 'public/css/system/gestorCasos/editargruposView.css">
                                        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">',

            'extra_js'              => '<script src="' . base_url . 'public/js/system/pagos/pagosAlumno.js"></script>'
        ];

        $this->view('templates/header', $data);
        $this->view('system/pagos/pagosAlumnosView', $data);
        $this->view('templates/footer', $data);
    }

    public function verPagosAlumno()
    {
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Pagos[2] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }
        $data = [
            'titulo'                => ' IE | Ver Pagos Alumnos',
            'titulo_1'              => 'Ver',
            'extra_css'             => '<link rel="stylesheet" href="' . base_url . 'public/css/system/gestorCasos/editargruposView.css">
                                        <link href="https://fonts.googleapis.com/css2?family=Material+Icons" rel="stylesheet">',

            'extra_js'              => '<script src="' . base_url . 'public/js/system/pagos/verpagosAlumno.js"></script>'
        ];

        $this->view('templates/header', $data);
        $this->view('system/pagos/pagosAlumnosView-ReadOnly', $data);
        $this->view('templates/footer', $data);
    }



    /*-----------------------FUNCIONES PARA FILTRADO Y BÚSQUEDA-----------------------*/

    //función para generar la paginación dinámica
    public function generarLinks($numPage, $total_pages, $extra_cad = "", $filtro = 1)
    {
        //$extra_cad sirve para determinar la paginacion conforme a si se realizó una busqueda
        //Creación de links para el pagination
        $links = "";

        //FLECHA IZQ (PREV PAGINATION)
        if ($numPage > 1) {
            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'Pagos/index/?numPage=1' . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Primera página">
                                <i class="material-icons">first_page</i>
                            </a>
                        </li>';
            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'Pagos/index/?numPage=' . ($numPage - 1) . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Página anterior">
                                <i class="material-icons">navigate_before</i>
                            </a>
                        </li>';
        }

        //DESPLIEGUE DE PAGES NUMBER
        $LINKS_EXTREMOS = GLOBAL_LINKS_EXTREMOS; //numero máximo de links a la izquierda y a la derecha
        for ($ind = ($numPage - $LINKS_EXTREMOS); $ind <= ($numPage + $LINKS_EXTREMOS); $ind++) {
            if (($ind >= 1) && ($ind <= $total_pages)) {

                $activeLink = ($ind == $numPage) ? 'active' : '';

                $links .= '<li class="page-item ' . $activeLink . ' ">
                                <a class="page-link" href=" ' . base_url . 'Pagos/index/?numPage=' . ($ind) . $extra_cad . '&filtro=' . $filtro . ' ">
                                    ' . ($ind) . '
                                </a>
                            </li>';
            }
        }

        //FLECHA DERECHA (NEXT PAGINATION)
        if ($numPage < $total_pages) {

            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'Pagos/index/?numPage=' . ($numPage + 1) . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Siguiente página">
                            <i class="material-icons">navigate_next</i>
                            </a>
                        </li>';
            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'Pagos/index/?numPage=' . ($total_pages) . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Última página">
                            <i class="material-icons">last_page</i>
                            </a>
                        </li>';
        }

        return $links;
    }

    //función para generar la información de la tabla de forma dinámica
    public function generarInfoTable($rows, $filtro = 1)
    {
        $permisos_Editar = ($_SESSION['userdata']->Pagos[1] == '1') ? 'd-flex justify-content-center' : 'mi_hide';
        $permisos_Ver = ($_SESSION['userdata']->Pagos[2] == '1') ? 'd-flex justify-content-center' : 'mi_hide';
        $permisos_FormatoFicha = ($_SESSION['userdata']->Pagos[2] == '1') ? 'd-flex justify-content-center' : 'mi_hide';
        //se genera la tabulacion de la informacion por backend
        $infoTable['header'] = "";
        $infoTable['body'] = "";


        switch ($filtro) {
            case '1': //general
                $infoTable['header'] .= '
                        <th class="column1">ID</th>
                        <th class="column2">Nombre</th>
                        <th class="column3">Apellido Paterno</th>
                        <th class="column4">Apellido Materno</th>
                        <th class="column5">Correo</th>
                        <th class="column6">Activo</th>
                        <th class="column7">Nivel</th>
                        <th class="column8">Telefono</th>
                        
                    ';
                foreach ($rows as $row) {
                    $infoTable['body'] .= '<tr id="tr' . $row->Id_maestro . '">';
                    $infoTable['body'] .= '  <td class="column1">' . $row->Id_maestro . '</td>
                                            <td class="column2">' . $row->Nombre . '</td>
                                            <td class="column3">' . $row->Apellido_paterno . '</td>
                                            <td class="column4">' . $row->Apellido_materno . '</td>
                                            <td class="column5">' . $row->Correo . '</td>
                                            <td class="column6">' . $row->Activo . '</td>
                                            <td class="column7">' .$row->Nivel . '</td>
                                            <td class="column8">' .$row->Telefono . '</td>
                                            

                        ';
                    //se comprueba si el registro ya tiene un dictamen previamente llenado o si no existe genera un link para nuevo
                    
                        
                        $infoTable['body'] .= '<td class="d-flex">
                                                    <a class="myLinks mb-3' . $permisos_Editar . '" data-toggle="tooltip" data-placement="right" title="Editar registro" href="' . base_url . 'Pagos/editarPagosMaestro/?id_maestro=' . $row->Id_maestro . '">
                                                        <i class="material-icons">edit</i>
                                                    </a>';
                       
                        $infoTable['body'] .= '
                                                <a class="myLinks mt-3' . $permisos_Ver . '" data-toggle="tooltip" data-placement="right" title="Ver registro" href="' . base_url . 'Pagos/verPagosMaestro/?id_maestro=' . $row->Id_maestro . '">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                                
                                            </td>';
                    


                    $infoTable['body'] .= '</tr>';
                }
                break;
            case '2': //peticionarios
                $infoTable['header'] .= '
                        <th class="column1">ID</th>
                        <th class="column2">Nombre</th>
                        <th class="column3">Apellido Paterno</th>
                        <th class="column4">Apellido Materno</th>
                        <th class="column5">Edad</th>
                        <th class="column6">Telefono</th>
                        <th class="column7">Correo</th>
                    ';
                foreach ($rows as $row) {
                    $infoTable['body'] .= '<tr id="tr' . $row->Id_alumno . '">';
                    $infoTable['body'] .= '  <td class="column1">' . $row->Id_alumno . '</td>
                                            <td class="column2">' . $row->Nombre . '</td>
                                            <td class="column3">' . $row->Apellido_paterno . '</td>
                                            <td class="column4">' .$row->Apellido_materno . '</td>
                                            <td class="column5">' . $row->Edad . '</td>
                                            <td class="column6">' . ($row->Telefono) . '</td>
                                            <td class="column7">' . ($row->Correo) . '</td>

                        ';
                    //se comprueba si el registro ya tiene un dictamen previamente llenado o si no existe genera un link para nuevo
                    
                        $infoTable['body'] .= '<td class="d-flex">
                                                    <a class="myLinks mb-3' . $permisos_Editar . '" data-toggle="tooltip" data-placement="right" title="Editar registro" href="' . base_url . 'Pagos/editarPagosAlumno/?id_alumno=' . $row->Id_alumno . '">
                                                        <i class="material-icons">edit</i>
                                                    </a>';
                        
                        $infoTable['body'] .= '
                                                <a class="myLinks mt-3' . $permisos_Ver . '" data-toggle="tooltip" data-placement="right" title="Ver registro" href="' . base_url . 'Pagos/verPagosAlumno/?id_alumno=' . $row->Id_alumno . '">
                                                    <i class="material-icons">visibility</i>
                                                </a>
                                                
                                            </td>';
                    


                    $infoTable['body'] .= '</tr>';
                }
                break;
                case '3': //general
                    $infoTable['header'] .= '
                            <th class="column1">ID</th>
                            <th class="column2">Nombre</th>
                            <th class="column3">Apellido Paterno</th>
                            <th class="column4">Apellido Materno</th>
                            <th class="column5">Correo</th>
                            <th class="column6">Activo</th>
                            <th class="column7">Nivel</th>
                            <th class="column8">Telefono</th>
                            
                        ';
                    foreach ($rows as $row) {
                        $infoTable['body'] .= '<tr id="tr' . $row->Id_maestro . '">';
                        $infoTable['body'] .= '  <td class="column1">' . $row->Id_maestro . '</td>
                                                <td class="column2">' . $row->Nombre . '</td>
                                                <td class="column3">' . $row->Apellido_paterno . '</td>
                                                <td class="column4">' . $row->Apellido_materno . '</td>
                                                <td class="column5">' . $row->Correo . '</td>
                                                <td class="column6">' . $row->Activo . '</td>
                                                <td class="column7">' .$row->Nivel . '</td>
                                                <td class="column8">' .$row->Telefono . '</td>
                                                
    
                            ';
                        //se comprueba si el registro ya tiene un dictamen previamente llenado o si no existe genera un link para nuevo
                        
                            
                            $infoTable['body'] .= '<td class="d-flex">
                                                        <a class="myLinks mb-3' . $permisos_Editar . '" data-toggle="tooltip" data-placement="right" title="Editar registro" href="' . base_url . 'Pagos/editarPagosMaestro/?id_maestro=' . $row->Id_maestro . '">
                                                            <i class="material-icons">edit</i>
                                                        </a>';
                           
                            $infoTable['body'] .= '
                                                    <a class="myLinks mt-3' . $permisos_Ver . '" data-toggle="tooltip" data-placement="right" title="Ver registro" href="' . base_url . 'Pagos/verPagosMaestro/?id_maestro=' . $row->Id_maestro . '">
                                                        <i class="material-icons">visibility</i>
                                                    </a>
                                                    
                                                </td>';
                        
    
    
                        $infoTable['body'] .= '</tr>';
                    }
                    break;
                case '4': //peticionarios
                    $infoTable['header'] .= '
                            <th class="column1">ID</th>
                            <th class="column2">Nombre</th>
                            <th class="column3">Apellido Paterno</th>
                            <th class="column4">Apellido Materno</th>
                            <th class="column5">Edad</th>
                            <th class="column6">Telefono</th>
                            <th class="column7">Correo</th>
                        ';
                    foreach ($rows as $row) {
                        $infoTable['body'] .= '<tr id="tr' . $row->Id_alumno . '">';
                        $infoTable['body'] .= '  <td class="column1">' . $row->Id_alumno . '</td>
                                                <td class="column2">' . $row->Nombre . '</td>
                                                <td class="column3">' . $row->Apellido_paterno . '</td>
                                                <td class="column4">' .$row->Apellido_materno . '</td>
                                                <td class="column5">' . $row->Edad . '</td>
                                                <td class="column6">' . ($row->Telefono) . '</td>
                                                <td class="column7">' . ($row->Correo) . '</td>
    
                            ';
                        //se comprueba si el registro ya tiene un dictamen previamente llenado o si no existe genera un link para nuevo
                        
                            $infoTable['body'] .= '<td class="d-flex">
                                                        <a class="myLinks mb-3' . $permisos_Editar . '" data-toggle="tooltip" data-placement="right" title="Editar registro" href="' . base_url . 'Pagos/editarPagosAlumno/?id_alumno=' . $row->Id_alumno . '">
                                                            <i class="material-icons">edit</i>
                                                        </a>';
                            
                            $infoTable['body'] .= '
                                                    <a class="myLinks mt-3' . $permisos_Ver . '" data-toggle="tooltip" data-placement="right" title="Ver registro" href="' . base_url . 'Pagos/verPagosAlumno/?id_alumno=' . $row->Id_alumno . '">
                                                        <i class="material-icons">visibility</i>
                                                    </a>
                                                    
                                                </td>';
                        
    
    
                        $infoTable['body'] .= '</tr>';
                    }
                    break;
        }



        $infoTable['header'] .= '<th >Operaciones</th>';
        //$infoTable['header'].='<th >Ver</th>';

        return $infoTable;
    }

    public function editPagosFetch(){
       
        $pagos = json_decode($_POST['pagos_table']);
        $success = true;
        
        if ($success) {
           
            $success_2 = $this->Pago->editarPagos($_POST);
            if ($success_2['status']) {//$success_2['status']

                //$user = $_SESSION['userdata']->Id_Usuario;
                //$ip = $this->obtenerIp();
                //$descripcion = 'Edición de un grupo delictivo: ' . $caso;
                //$this->GestorCaso->historial($user, $ip, 2, $descripcion);

                $data_p['status'] =  true;
            
                } else{
                    $data_p['status'] =  false;
                }
        }
        echo json_encode($data_p);
            
    }
    public function editPagosAFetch(){
       
        $pagos = json_decode($_POST['pagos_table']);
        $success = true;
        
        if ($success) {
           
            $success_2 = $this->Pago->editarPagosA($_POST);
            if ($success_2['status']) {//$success_2['status']

                //$user = $_SESSION['userdata']->Id_Usuario;
                //$ip = $this->obtenerIp();
                //$descripcion = 'Edición de un grupo delictivo: ' . $caso;
                //$this->GestorCaso->historial($user, $ip, 2, $descripcion);

                $data_p['status'] =  true;
            
                } else{
                    $data_p['status'] =  false;
                }
        }
        echo json_encode($data_p);
            
    }
    //función para generar los links respectivos dependiendo del filtro y/o cadena de búsqueda
    public function generarExportLinks($extra_cad = "", $filtro = 1)
    {
        if ($extra_cad != "") {
            $dataReturn['csv'] =  base_url . 'Pagos/exportarInfo/?tipo_export=CSV' . $extra_cad . '&filtroActual=' . $filtro;
            $dataReturn['excel'] =  base_url . 'Pagos/exportarInfo/?tipo_export=EXCEL' . $extra_cad . '&filtroActual=' . $filtro;
            $dataReturn['pdf'] =  base_url . 'Pagos/exportarInfo/?tipo_export=PDF' . $extra_cad . '&filtroActual=' . $filtro;
            //return $dataReturn;
        } else {
            $dataReturn['csv'] =  base_url . 'Pagos/exportarInfo/?tipo_export=CSV' . $extra_cad . '&filtroActual=' . $filtro;
            $dataReturn['excel'] =  base_url . 'Pagos/exportarInfo/?tipo_export=EXCEL' . $extra_cad . '&filtroActual=' . $filtro;
            $dataReturn['pdf'] =  base_url . 'Pagos/exportarInfo/?tipo_export=PDF' . $extra_cad . '&filtroActual=' . $filtro;
        }
        return $dataReturn;
    }

    //función fetch para buscar por la cadena introducida dependiendo del filtro
    public function buscarPorCadena()
    {
        /*Aquí van condiciones de permisos*/

        if (isset($_POST['cadena'])) {
            $cadena = trim($_POST['cadena']);
            $filtroActual = trim($_POST['filtroActual']);

            $results = $this->Pago->getRemisionDByCadena($cadena, $filtroActual);
            if (strlen($cadena) > 0) {
                $user = $_SESSION['userdata']->Id_Usuario;
                $ip = $this->obtenerIp();
                $descripcion = 'Consulta realizada: ' . $cadena . '';
                $this->Pago->historial($user, $ip, 6, $descripcion);
            }
            $extra_cad = ($cadena != "") ? ("&cadena=" . $cadena) : ""; //para links conforme a búsqueda

            //$dataReturn = "jeje";

            $dataReturn['infoTable'] = $this->generarInfoTable($results['rows_Rems'], $filtroActual);
            $dataReturn['links'] = $this->generarLinks($results['numPage'], $results['total_pages'], $extra_cad, $filtroActual);
            $dataReturn['export_links'] = $this->generarExportLinks($extra_cad, $filtroActual);
            $dataReturn['total_rows'] = "Total registros: " . $results['total_rows'];
            $dataReturn['dropdownColumns'] = $this->generateDropdownColumns($filtroActual);


            echo json_encode($dataReturn);
        } else {
            header("Location: " . base_url . "Inicio");
            exit();
        }
    }
    public function obtenerIp()
    {
        $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $hosts = gethostbynamel($hostname);
        if (is_array($hosts)) {
            foreach ($hosts as $ip) {
                return $ip;
            }
        } else {
            return $ip = '0.0.0.0';
        }
    }
    //función para exportar la inforación dependiendo del filtro 
    public function exportarInfo()
    {
        /*checar permisos*/

        if (!isset($_REQUEST['tipo_export'])) {
            header("Location: " . base_url . "Inicio");
            exit();
        }
        //se recupera el catalogo actual para poder consultar conforme al mismo
        if (!isset($_REQUEST['filtroActual']) || !is_numeric($_REQUEST['filtroActual']) || !($_REQUEST['filtroActual'] >= MIN_FILTRO_PA) || !($_REQUEST['filtroActual'] <= MAX_FILTRO_PA))
            $filtroActual = 1;
        else
            $filtroActual = $_REQUEST['filtroActual'];

        $from_where_sentence = "";
        //se genera la sentencia from where para realizar la correspondiente consulta
        if (isset($_REQUEST['cadena']))
            $from_where_sentence = $this->Pago->generateFromWhereSentence($_REQUEST['cadena'], $filtroActual);
        else
            $from_where_sentence = $this->Pago->generateFromWhereSentence("", $filtroActual);
        //var_dump($_REQUEST);
        $tipo_export = $_REQUEST['tipo_export'];

        if ($tipo_export == 'EXCEL') {
            //se realiza exportacion de usuarios a EXCEL
            $rows_Rem = $this->Pago->getAllInfoRemisionDByCadena($from_where_sentence);
            switch ($filtroActual) {
                case '1':
                    $filename = "Historial_pagos_maestros";
                    $csv_data = "Id pago, Periodo inicio, Periodo fin, Fecha de pago, Monto, Descripción, Id profesor,Nombre profesor,Activo,Correo,Telefono,Nivel\n";
                    //se recuperan los objetos y narrativas de elementos dependiendo de los resultados
                    $pagos_maestro = $this->Pago->getTodosApartados($rows_Rem);
                    foreach ($rows_Rem as $key => $row) {

                    //$cancelada = $Apartados_Remision['Ficha'][$key]->fcancelada;
                        error_reporting(E_ERROR | E_PARSE); //evita mostrar errores de php, aparecen errores del arreglo de narrativa de detenido (algunos no tienen narrativa) 
                        $csv_data .= 
                            ($pagos_maestro['Pagos'][$key]->Id_pagosm) . ",\"" .
                            ($pagos_maestro['Pagos'][$key]->Fecha_inicio) . "\",\"" .
                            ($pagos_maestro['Pagos'][$key]->Fecha_final) . "\",\"" .
                            ($pagos_maestro['Pagos'][$key]->Fecha) . "\",\"" .
                            ($pagos_maestro['Pagos'][$key]->Monto) . "\",\"" .
                            ($pagos_maestro['Pagos'][$key]->Descripcion) . "\",\"" .
                            ($row->Id_maestro) . "\",\"" .
                            ($row->Nombre) . "\",\"" .
                            ($row->Activo) . "\",\"" .
                            ($row->Correo) . "\",\"" .
                            ($row->Telefono) . "\",\"" .
                            ($row->Nivel) . "\"\n";
                    }
                    break;
                case '2':
                    $filename = "Historial_pagos_alumnos";
                    $csv_data = "Id pago, Periodo inicio, Periodo fin, Fecha de pago, Monto, Descripción, Id alumno,Nombre profesor,Activo,Correo,Telefono,Nivel,Tipo de pago\n";

                    $pagos_maestro = $this->Pago->getTodosApartadosA($rows_Rem);
                    foreach ($rows_Rem as $key => $row) {

                    //$cancelada = $Apartados_Remision['Ficha'][$key]->fcancelada;
                        error_reporting(E_ERROR | E_PARSE); //evita mostrar errores de php, aparecen errores del arreglo de narrativa de detenido (algunos no tienen narrativa) 
                        $csv_data .= 
                            ($pagos_maestro['Pagos'][$key]->Id_pagosm) . ",\"" .
                            ($pagos_maestro['Pagos'][$key]->Fecha_inicio) . "\",\"" .
                            ($pagos_maestro['Pagos'][$key]->Fecha_final) . "\",\"" .
                            ($pagos_maestro['Pagos'][$key]->Fecha) . "\",\"" .
                            ($pagos_maestro['Pagos'][$key]->Monto) . "\",\"" .
                            ($pagos_maestro['Pagos'][$key]->Descripcion) . "\",\"" .
                            ($row->Id_alumno) . "\",\"" .
                            ($row->Nombre) . "\",\"" .
                            ($row->Activo) . "\",\"" .
                            ($row->Correo) . "\",\"" .
                            ($row->Telefono) . "\",\"" .
                            ($row->Nivel) . "\",\"" .
                            ($row->Tipo_pago) . "\"\n";
                    }
                    break;
                case '3':
                    $filename = "Pagos_pendientes_maestros";
                    $csv_data = "Ficha,Núm. Remisión,Fecha y Hora,Detenido,Domicilio Detenido,Ubicacion Hechos,Remitido a\n";

                    foreach ($rows_Rem as $row) {
                        $csv_data .= $row->Ficha . ",\"" .
                            $row->No_Remision . "\",\"" .
                            $row->Fecha_Hora . "\",\"" .
                            mb_strtoupper($row->Nombre_Detenido) . "\",\"" .
                            mb_strtoupper($row->Domicilio_Detenido) . "\",\"" .
                            mb_strtoupper($row->Ubicacion_Hechos) . "\",\"" .
                            mb_strtoupper($row->Instancia) . "\"\n";
                    }
                    break;
                case '4':
                    $filename = "Pagos_pendientes_alumnos";
                    $csv_data = "Ficha,Núm. Remisión,Fecha y Hora,Detenido,Elemento,Cargo,Placa,Unidad,Tipo Llamado\n";

                    foreach ($rows_Rem as $row) {
                        $auxllamado = ($row->Tipo_Llamado == '0') ? "En apoyo" : "Primer respondiente";
                        $csv_data .= $row->Ficha . ",\"" .
                            $row->No_Remision . "\",\"" .
                            $row->Fecha_Hora . "\",\"" .
                            mb_strtoupper($row->Nombre_Detenido) . "\",\"" .
                            mb_strtoupper($row->Nombre_Elemento) . "\",\"" .
                            mb_strtoupper($row->Cargo) . "\",\"" .
                            mb_strtoupper($row->Placa) . "\",\"" .
                            mb_strtoupper($row->No_Unidad) . "\",\"" .
                            mb_strtoupper($auxllamado) . "\"\n";
                    }
                    break;
            }
            //se genera el archivo csv o excel
            $csv_data = utf8_decode($csv_data); //escribir información con formato utf8 por algún acento
            header("Content-Description: File Transfer");
            header("Content-Type: application/force-download");
            header("Content-Disposition: attachment; filename=" . $filename . ".csv");
            echo $csv_data;
            //header("Location: ".base_url."UsersAdmin");

        } elseif ($tipo_export == 'PDF') {
            $rows_Rem = $this->Remision->getAllInfoRemisionDByCadena($from_where_sentence);


            header("Content-type: application/pdf");
            header("Content-Disposition: inline; filename=usuarios.pdf");
            echo $this->generarPDF($rows_Rem, $_REQUEST['cadena'], $filtroActual);
        } else {
            header("Location: " . base_url . "Inicio");
            exit();
        }
    }

    //funcion para borrar variable sesión para filtro de rangos de fechas
    public function removeRangosFechasSesion()
    {

        if (isset($_REQUEST['filtroActual'])) {
            unset($_SESSION['userdata']->rango_inicio_rem);
            unset($_SESSION['userdata']->rango_fin_rem);

            header("Location: " . base_url . "Pagos/index/?filtro=" . $_REQUEST['filtroActual']);
            exit();
        } else {
            header("Location: " . base_url . "Cuenta");
            exit();
        }
    }

    //función que filtra las columnas deseadas por el usuario
    public function generateDropdownColumns($filtro = 1)
    {
        //parte de permisos

        $dropDownColumn = '';
        //generación de dropdown dependiendo del filtro
        switch ($filtro) {
            case '1':
                $campos = ['ID', 'Nombre', 'Apellido Paterno', 'Apellido Materno', 'Correo', 'Activo', 'Nivel', 'Telefono'];
                break;
            case '2':
                $campos = ['ID', 'Nombre', 'Apellido Paterno', 'Apellido Materno', 'Edad', 'Telefono', 'Correo'];
                break;
            case '3':
                $campos = ['ID', 'Nombre', 'Apellido Paterno', 'Apellido Materno', 'Correo', 'Activo', 'Nivel', 'Telefono'];
                break;
            case '4':
                $campos = ['ID', 'Nombre', 'Apellido Paterno', 'Apellido Materno', 'Edad', 'Telefono', 'Correo'];
                break;
        }
        //gestión de cada columna
        $ind = 1;
        foreach ($campos as $campo) {
            $checked = ($_SESSION['userdata']->columns_REM['column' . $ind] == 'show') ? 'checked' : '';
            $dropDownColumn .= ' <div class="form-check">
                                    <input class="form-check-input checkColumns" type="checkbox" value="' . $_SESSION['userdata']->columns_REM['column' . $ind] . '" onchange="hideShowColumn(this.id);" id="column' . $ind . '" ' . $checked . '>
                                    <label class="form-check-label" for="column' . $ind . '">
                                        ' . $campo . '
                                    </label>
                                </div>';
            $ind++;
        }
        $dropDownColumn .= '     <div class="dropdown-divider">
                                </div>
                                <div class="form-check">
                                    <input id="checkAll" class="form-check-input" type="checkbox" value="hide" onchange="hideShowAll(this.id);" id="column' . $ind . '" checked>
                                    <label class="form-check-label" for="column' . $ind . '">
                                        Todo
                                    </label>
                                </div>';
        return $dropDownColumn;
    }

    //función para checar los cambios de filtro y poder asignar los valores correspondientes de las columnas a la session
    public function setColumnsSession($filtroActual = 1)
    {
        //si el filtro existe y esta dentro de los parámetros continua
        if (isset($_SESSION['userdata']->filtro_REM) && $_SESSION['userdata']->filtro_REM >= MIN_FILTRO_PA && $_SESSION['userdata']->filtro_REM <= MAX_FILTRO_PA) {
            //si cambia el filtro se procde a cambiar los valores de las columnas que contiene el filtro seleccionado
            if ($_SESSION['userdata']->filtro_REM != $filtroActual) {
                $_SESSION['userdata']->filtro_REM = $filtroActual;
                unset($_SESSION['userdata']->columns_REM); //se borra las columnas del anterior filtro
                //se asignan las nuevas columnas y por default se muestran todas (atributo show)
                for ($i = 0; $i < $this->numColumnsRem[$_SESSION['userdata']->filtro_REM - 1]; $i++)
                    $_SESSION['userdata']->columns_REM['column' . ($i + 1)] = 'show';
            }
        } else { //si no existe el filtro entonces se inicializa con el primero por default
            $_SESSION['userdata']->filtro_REM = $filtroActual;
            unset($_SESSION['userdata']->columns_REM);
            for ($i = 0; $i < $this->numColumnsRem[$_SESSION['userdata']->filtro_REM - 1]; $i++)
                $_SESSION['userdata']->columns_REM['column' . ($i + 1)] = 'show';
        }
        //echo "filtro: ".var_dump($_SESSION['userdata']->filtro_REM)."<br>br>";
        //echo "columns: ".var_dump($_SESSION['userdata']->columns_REM)."<br>br>";
    }

    //función fetch que actualiza los valores de las columnas para la session
    public function setColumnFetch()
    {
        if (isset($_POST['columName']) && isset($_POST['valueColumn'])) {
            $_SESSION['userdata']->columns_REM[$_POST['columName']] = $_POST['valueColumn'];
            echo json_encode("ok");
        } else {
            header("Location: " . base_url . "Inicio");
            exit();
        }
    }

}
