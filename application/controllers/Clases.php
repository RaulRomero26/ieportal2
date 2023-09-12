<?php
class Clases extends Controller
{

    
    public $Clase;
    public $numColumnsGC; //número de columnas por cada filtro
    public $Catalogo;
    public $FV;

    public function __construct(){
       
        $this->Clase = $this->model('Clase');
        $this->numColumnsGC = [6, 8];  //se inicializa el número de columns por cada filtro
        $this->Catalogo = $this->model('Catalogo');
        $this->FV = new FormValidator();
    }

    public function index(){
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Clases[2] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }

        $data = [
            'titulo'    => 'IE Admin | Clases',
            'extra_css' => '<link rel="stylesheet" href="' . base_url . 'public/css/system/clases/index.css">',
            'extra_js'  => '<script src="' . base_url . 'public/js/system/clases/index.js"></script>'
        ];

         //PROCESO DE FILTRADO DE LA INFORMACION
         if (isset($_GET['filtro']) && is_numeric($_GET['filtro']) && $_GET['filtro'] >= MIN_FILTRO_CIE && $_GET['filtro'] <= MAX_FILTRO_CIE) { //numero de filtro
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
        $data['columns_GC'] = $_SESSION['userdata']->columns_GC;

        //PROCESAMIENTO DE RANGO DE FOLIOS
        if (isset($_POST['rango_inicio']) && isset($_POST['rango_fin'])) {
            $_SESSION['userdata']->rango_inicio_iec = $_POST['rango_inicio'];
            $_SESSION['userdata']->rango_fin_iec = $_POST['rango_fin'];
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

        $where_sentence = $this->Clase->generateFromWhereSentence($cadena, $filtro);
        $extra_cad = ($cadena != "") ? ("&cadena=" . $cadena) : ""; //para links conforme a búsqueda

        $no_of_records_per_page = NUM_MAX_REG_PAGE; //total de registros por pagination
        $offset = ($numPage - 1) * $no_of_records_per_page; // desplazamiento conforme a la pagina

        $results_rows_pages = $this->Clase->getTotalPages($no_of_records_per_page, $where_sentence);   //total de páginas de acuerdo a la info de la DB
        $total_pages = $results_rows_pages['total_pages'];

        if ($numPage > $total_pages) {
            $numPage = 1;
            $offset = ($numPage - 1) * $no_of_records_per_page;
        } //seguridad si ocurre un error por url     

        $rows_GestorCasos = $this->Clase->getDataCurrentPage($offset, $no_of_records_per_page, $where_sentence);    //se obtiene la información de la página actual

        //guardamos la tabulacion de la información para la vista
        $data['infoTable'] = $this->generarInfoTable($rows_GestorCasos, $filtro);
        //guardamos los links en data para la vista
        $data['links'] = $this->generarLinks($numPage, $total_pages, $extra_cad, $filtro);
        //número total de registros encontrados
        $data['total_rows'] = $results_rows_pages['total_rows'];
        //filtro actual para Fetch javascript
        $data['filtroActual'] = $filtro;
        $data['dropdownColumns'] = $this->generateDropdownColumns($filtro);

        switch ($filtro) {
            case '1':
                $data['filtroNombre'] = "Todas las Clases Activas";
                break;
            case '2':
                $data['filtroNombre'] = "Todas las Clases Inactivas";
                break;
        }

        $this->view('templates/header', $data);
        $this->view('system/clases/ClasesView', $data);
        $this->view('templates/footer', $data);
    }

    public function nuevo(){
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Clases[3] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }

        $catalogos = [
            'profesores' => $this->getProfesores(),
            'alumnos' => $this->getAlumnos(),
            'niveles' => $this->getNiveles(),
        ];

        $data = [
            'titulo'    => 'IE ADMIN | Crear Clase',
            'extra_css' => '<link rel="stylesheet" href="' . base_url . 'public/css/system/Remisiones/index.css">',
            'extra_js'  =>  '<script src="' . base_url . 'public/js/system/clases/tabla_alumnos.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/clases/tabla_horarios_clase.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/clases/crear_clases.js"></script>',
            'catalogos' =>  $catalogos,
        ];
        $this->view('templates/header', $data);
        $this->view('system/clases/nuevaClaseView', $data);
        $this->view('templates/footer', $data);
    }


    public function editarClase(){
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Clases[3] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }

        $catalogos = [
            'profesores' => $this->getProfesores(),
            'alumnos' => $this->getAlumnos(),
            'niveles' => $this->getNiveles(),
        ];

        $data = [
            'titulo'    => 'IE ADMIN | Editar Clase',
            'extra_css' => '<link rel="stylesheet" href="' . base_url . 'public/css/system/Remisiones/index.css">',
            'extra_js'  => '<script src="' . base_url . 'public/js/system/clases/tabla_alumnos.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/clases/tabla_horarios_clase.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/clases/actualizar_clase.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/clases/delegar_cancelar.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/clases/pasar_lista_clase.js"></script>',
            'catalogos' =>  $catalogos,
        ];
        $this->view('templates/header', $data);
        $this->view('system/clases/editarClaseFullView', $data);
        $this->view('templates/footer', $data);
    }

    public function verClase(){
        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Clases[3] != '1')) {
            header("Location: " . base_url . "Inicio");
            exit();
        }

        $catalogos = [
            'profesores' => $this->getProfesores(),
            'alumnos' => $this->getAlumnos(),
            'niveles' => $this->getNiveles(),
        ];

        $data = [
            'titulo'    => 'IE ADMIN | Ver Clase',
            'extra_css' => '<link rel="stylesheet" href="' . base_url . 'public/css/system/Remisiones/index.css">',
            'extra_js'  => '<script src="' . base_url . 'public/js/system/clases/tabla_alumnos.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/clases/tabla_horarios_clase.js"></script>'.
                            '<script src="' . base_url . 'public/js/system/clases/actualizar_clase.js"></script>',
            'catalogos' =>  $catalogos,
        ];
        $this->view('templates/header', $data);
        $this->view('system/Clases/claseView-ReadOnly', $data);
        $this->view('templates/footer', $data);
    }


    /* ---------------------- AQUI VAN LAS FUNCIONES CREADORAS LAS QUE LLAMAN A HACER INCERCIONES EDICIONES A LA BASE --------------------- */

    public function insertarClase(){

        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Clases[2] != '1')) {
        header("Location: " . base_url . "Inicio");
        exit();
        }

        if (isset($_POST)){
            $success = $this->Clase->insertNuevaClase($_POST);
            echo json_encode($success);
        } else{
            echo "post vacio";
        }
  }

    public function actualizarClase(){

        //comprobar los permisos para dejar pasar al módulo
        if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Clases[2] != '1')) {
        header("Location: " . base_url . "Inicio");
        exit();
        }

        if (isset($_POST)){
            $success = $this->Clase->actualizarClase($_POST);
            echo json_encode($success);
        } else{
            echo "post vacio";
        }
  }

  /* __________________________________________FUNCION PARA PASAR LISTA _________________________________ */

  public function pasarLista(){

    //comprobar los permisos para dejar pasar al módulo
    if (!isset($_SESSION['userdata']) || ($_SESSION['userdata']->Modo_Admin != 1 && $_SESSION['userdata']->Clases[2] != '1')) {
    header("Location: " . base_url . "Inicio");
    exit();
    }

    if (isset($_POST)){
        $success = $this->Clase->pasarLista($_POST);
        echo json_encode($success);
    } else{
        echo "post vacio";
    }
}





    /* ----------------------------------------FUNCIONES DE FILTROS ------------------------------------- */
    //función para checar los cambios de filtro y poder asignar los valores correspondientes de las columnas a la session
    public function setColumnsSession($filtroActual = 1){
        //si el filtro existe y esta dentro de los parámetros continua
        if (isset($_SESSION['userdata']->filtro_GC) && $_SESSION['userdata']->filtro_GC >= MIN_FILTRO_GC && $_SESSION['userdata']->filtro_GC <= MAX_FILTRO_GC) {
            //si cambia el filtro se procde a cambiar los valores de las columnas que contiene el filtro seleccionado
            if ($_SESSION['userdata']->filtro_GC != $filtroActual) {
                $_SESSION['userdata']->filtro_GC = $filtroActual;
                unset($_SESSION['userdata']->columns_GC); //se borra las columnas del anterior filtro
                //se asignan las nuevas columnas y por default se muestran todas (atributo show)
                for ($i = 0; $i < $this->numColumnsGC[$_SESSION['userdata']->filtro_GC - 1]; $i++)
                    $_SESSION['userdata']->columns_GC['column' . ($i + 1)] = 'show';
            }
        } else { //si no existe el filtro entonces se inicializa con el primero por default
            $_SESSION['userdata']->filtro_GC = $filtroActual;
            unset($_SESSION['userdata']->columns_GC);
            for ($i = 0; $i < $this->numColumnsGC[$_SESSION['userdata']->filtro_GC - 1]; $i++)
                $_SESSION['userdata']->columns_GC['column' . ($i + 1)] = 'show';
        }
        //echo "filtro: ".var_dump($_SESSION['userdata']->filtro_REM)."<br>br>";
        //echo "columns: ".var_dump($_SESSION['userdata']->columns_GC)."<br>br>";
    }

    //función fetch que actualiza los valores de las columnas para la session
    public function setColumnFetch(){
        if (isset($_POST['columName']) && isset($_POST['valueColumn'])) {
            $_SESSION['userdata']->columns_GC[$_POST['columName']] = $_POST['valueColumn'];
            echo json_encode("ok");
        } else {
            header("Location: " . base_url . "Inicio");
            exit();
        }
    }
        
    
    public function generarLinks($numPage, $total_pages, $extra_cad = "", $filtro = 1){
        //$extra_cad sirve para determinar la paginacion conforme a si se realizó una busqueda
        //Creación de links para el pagination
        $links = "";

        //FLECHA IZQ (PREV PAGINATION)
        if ($numPage > 1) {
            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=1' . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Primera página">
                                <i class="material-icons">first_page</i>
                            </a>
                        </li>';
            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=' . ($numPage - 1) . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Página anterior">
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
                                <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=' . ($ind) . $extra_cad . '&filtro=' . $filtro . ' ">
                                    ' . ($ind) . '
                                </a>
                            </li>';
            }
        }

        //FLECHA DERECHA (NEXT PAGINATION)
        if ($numPage < $total_pages) {

            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=' . ($numPage + 1) . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Siguiente página">
                            <i class="material-icons">navigate_next</i>
                            </a>
                        </li>';
            $links .= '<li class="page-item">
                            <a class="page-link" href=" ' . base_url . 'GestorCasos/index/?numPage=' . ($total_pages) . $extra_cad . '&filtro=' . $filtro . ' " data-toggle="tooltip" data-placement="top" title="Última página">
                            <i class="material-icons">last_page</i>
                            </a>
                        </li>';
        }

        return $links;
    }

            //función para generar la información de la tabla de forma dinámica
    public function generarInfoTable($rows, $filtro = 1){
        $permisos_Editar = ($_SESSION['userdata']->Clases[1] == '1') ? 'd-flex justify-content-center' : 'mi_hide';
        $permisos_Ver = ($_SESSION['userdata']->Clases[2] == '1') ? 'd-flex justify-content-center' : 'mi_hide';
        $permisos_FormatoFicha = ($_SESSION['userdata']->Clases[2] == '1') ? 'd-flex justify-content-center' : 'mi_hide';
        //se genera la tabulacion de la informacion por backend
        $infoTable['header'] = "";
        $infoTable['body'] = "";


        switch ($filtro) {
            case '1': //clases activas
                $infoTable['header'] .= '
                        <th class="column1">ID CLASE</th>
                        <th class="column2">PROFESOR</th>
                        <th class="column3">HORARIO</th>
                        <th class="column4">NIVEL</th>
                        <th class="column5">TIPO</th>
                        <th class="column6">ESTATUS</th>

                    ';
                foreach ($rows as $row) {
                    $infoTable['body'] .= '<tr id="tr' . $row->id_clase . '">';
                    $infoTable['body'] .= '  <td class="column1">' . $row->id_clase . '</td>
                                            <td class="column2">' . $row->Nombre .' '. $row->Apellido_paterno.' '.$row->Apellido_materno.'</td>
                                            <td class="column3">' . $row->horarios_clase . '</td>
                                            <td class="column4">' .  $row->nivel . '</td>
                                            <td class="column5">' . $row->tipo_clase . '</td>
                                            <td class="column6">' . $row->estatus . '</td>


                        ';
                    //se comprueba si el registro ya tiene un dictamen previamente llenado o si no existe genera un link para nuevo
                    
                        if ($_SESSION['userdata']->Modo_Admin == '1' || $_SESSION['userdata']->Clases[1]=='1') { //validacion de tabs validados completaente y/o permisos de validacion o modo admin
                            $infoTable['body'] .= '<td class="d-flex">
                                                    <a class="myLinks mb-3' . $permisos_Editar . '" data-toggle="tooltip" data-placement="right" title="Editar registro" href="' . base_url . 'Clases/editarClase/?id_clase=' . $row->id_clase . '">
                                                        <i class="material-icons">edit</i>
                                                    </a>';
                        } else {
                            $infoTable['body'] .= '<td class="d-flex">';
                        }
                        $infoTable['body'] .= '
                                                <a class="myLinks mt-3' . $permisos_FormatoFicha . '" data-toggle="tooltip" data-placement="right" title="Generar ficha" href="' . base_url . 'Clases/generarFichaIndividual/?id_clase=' . $row->id_clase . '" target="_blank">
                                                    <i class="material-icons">file_present</i>
                                                </a>
                                            </td>';
                    


                    $infoTable['body'] .= '</tr>';
                }
                break;
           
            case '2': //clases inactivas
                $infoTable['header'] .= '
                        <th class="column1">ID CLASE</th>
                        <th class="column2">PROFESOR</th>
                        <th class="column3">HORARIO</th>
                        <th class="column4">NIVEL</th>
                        <th class="column5">TIPO</th>
                        <th class="column6">ESTATUS</th>

                    ';
                foreach ($rows as $row) {
                    $infoTable['body'] .= '<tr id="tr' . $row->id_clase . '">';
                    $infoTable['body'] .= '  <td class="column1">' . $row->id_clase . '</td>
                                            <td class="column2">' . $row->Nombre .' '. $row->Apellido_paterno.' '.$row->Apellido_materno.'</td>
                                            <td class="column3">' . $row->horarios_clase . '</td>
                                            <td class="column4">' .  $row->nivel . '</td>
                                            <td class="column5">' . $row->tipo_clase . '</td>
                                            <td class="column6">' . $row->estatus . '</td>


                        ';
                    //se comprueba si el registro ya tiene un dictamen previamente llenado o si no existe genera un link para nuevo
                    
                        if ($_SESSION['userdata']->Modo_Admin == '1' || $_SESSION['userdata']->Clases[1]=='1') { //validacion de tabs validados completaente y/o permisos de validacion o modo admin
                            $infoTable['body'] .= '<td class="d-flex">
                                                    <a class="myLinks mb-3' . $permisos_Editar . '" data-toggle="tooltip" data-placement="right" title="Editar registro" href="' . base_url . 'Clases/editarClase/?id_clase=' . $row->id_clase . '">
                                                        <i class="material-icons">edit</i>
                                                    </a>';
                        } else {
                            $infoTable['body'] .= '<td class="d-flex">';
                        }
                        $infoTable['body'] .= '
                                                <a class="myLinks mt-3' . $permisos_FormatoFicha . '" data-toggle="tooltip" data-placement="right" title="Generar ficha" href="' . base_url . 'Clases/generarFichaIndividual/?id_clase=' . $row->id_clase . '" target="_blank">
                                                    <i class="material-icons">file_present</i>
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

    //función que filtra las columnas deseadas por el usuario
    public function generateDropdownColumns($filtro = 1){
        //parte de permisos

        $dropDownColumn = '';
        //generación de dropdown dependiendo del filtro
        switch ($filtro) {
            case '1':
                $campos = ['ID CLASE', 'PROFESOR', 'HORARIO', 'NIVEL', 'TIPO', 'ESTATUS'];
                break;
            case '2':
                $campos = ['ID CLASE', 'PROFESOR', 'HORARIO', 'NIVEL', 'TIPO', 'ESTATUS'];
                break;
        }
        //gestión de cada columna
        $ind = 1;
        foreach ($campos as $campo) {
            $checked = ($_SESSION['userdata']->columns_GC['column' . $ind] == 'show') ? 'checked' : '';
            $dropDownColumn .= ' <div class="form-check">
                                    <input class="form-check-input checkColumns" type="checkbox" value="' . $_SESSION['userdata']->columns_GC['column' . $ind] . '" onchange="hideShowColumn(this.id);" id="column' . $ind . '" ' . $checked . '>
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

    public function generarExportLinks($extra_cad = "", $filtro = 1)
    {
        if ($extra_cad != "") {
            $dataReturn['csv'] =  base_url . 'Clases/exportarInfo/?tipo_export=CSV' . $extra_cad . '&filtroActual=' . $filtro;
            $dataReturn['excel'] =  base_url . 'Clases/exportarInfo/?tipo_export=EXCEL' . $extra_cad . '&filtroActual=' . $filtro;
            $dataReturn['pdf'] =  base_url . 'Clases/generarFicha/?tipo_export=PDF' . $extra_cad . '&filtroActual=' . $filtro;
            //return $dataReturn;
        } else {
            $dataReturn['csv'] =  base_url . 'Clases/exportarInfo/?tipo_export=CSV' . $extra_cad . '&filtroActual=' . $filtro;
            $dataReturn['excel'] =  base_url . 'Clases/exportarInfo/?tipo_export=EXCEL' . $extra_cad . '&filtroActual=' . $filtro;
            $dataReturn['pdf'] =  base_url . 'Clases/generarFicha/?tipo_export=PDF' . $extra_cad . '&filtroActual=' . $filtro;
        }
        return $dataReturn;
    }

    //funcion para borrar variable sesión para filtro de rangos de fechas
    public function removeRangosFechasSesion(){

        if (isset($_REQUEST['filtroActual'])) {
            unset($_SESSION['userdata']->rango_inicio_iec);
            unset($_SESSION['userdata']->rango_fin_iec);

            header("Location: " . base_url . "GestorCasos/index/?filtro=" . $_REQUEST['filtroActual']);
            exit();
        } else {
            header("Location: " . base_url . "Cuenta");
            exit();
        }
    }

/*--------------------------------------FUNCIONES UPDATE TABS -------------------------------------- */

    //función fetch para buscar por la cadena introducida dependiendo del filtro
    public function buscarPorCadena()
    {
        /*Aquí van condiciones de permisos*/

        if (isset($_POST['cadena'])) {
            $cadena = trim($_POST['cadena']);
            $filtroActual = trim($_POST['filtroActual']);

            $results = $this->Clase->getClaseDByCadena($cadena, $filtroActual);
            $extra_cad = ($cadena != "") ? ("&cadena=" . $cadena) : ""; //para links conforme a búsqueda

            $dataReturn['infoTable'] = $this->generarInfoTable($results['rows_Rems'], $filtroActual);
            $dataReturn['links'] = $this->generarLinks($results['numPage'], $results['total_pages'], $extra_cad, $filtroActual);
            $dataReturn['export_links'] = $this->generarExportLinks($extra_cad, $filtroActual);
            $dataReturn['total_rows'] = "Total registros: " . $results['total_rows'];
            $dataReturn['dropdownColumns'] = $this->generateDropdownColumns($filtroActual);
            $user = $_SESSION['userdata']->Id_Usuario;
            $ip = $this->obtenerIp();
            $descripcion = 'Busqueda por término: ' . $cadena;
            $this->Clase->historial($user, $ip, 5, $descripcion);
            
            echo json_encode($dataReturn);
        } else {
            header("Location: " . base_url . "Inicio");
            exit();
        }
    }

    public function exportarInfo()
    {
        if (!isset($_REQUEST['tipo_export'])) {
            header("Location: " . base_url . "Inicio");
            exit();
        }
        if (!isset($_REQUEST['filtroActual']) || !is_numeric($_REQUEST['filtroActual']) || !($_REQUEST['filtroActual'] >= MIN_FILTRO_GC) || !($_REQUEST['filtroActual'] <= MAX_FILTRO_GC))
            $filtroActual = 1;
        else
            $filtroActual = $_REQUEST['filtroActual'];
        $from_where_sentence = "";
        if (isset($_REQUEST['cadena'])){
            $from_where_sentence = $this->Clase->generateFromWhereSentence($_REQUEST['cadena'], $filtroActual);
            $cadena=$_REQUEST['cadena'];
        }
        else{
            $from_where_sentence = $this->Clase->generateFromWhereSentence("", $filtroActual);
            $cadena="";
        }
        $rows_Veh = $this->Clase->getAllInfoRemisionDByCadena($from_where_sentence);
        switch ($filtroActual) {
            case '1':
                $filename = "GestorCasos";
                $csv_data = "Nombre, Apellido Paterno, Apellido Materno, Categoria, Sexo, CURP, UDC, UTC, Alias, Perfil de FB, Descripcion, Antecedentes, Estatus, Nombre de la banda, Principales delitos, Peligrosidad, Zonas, Colonias, Actividades Ilegales, Fecha de creación \n";
                $rows_Veh = $this->GestorCaso->getAllInfoRemisionDByCadena($from_where_sentence);
                $ids_= array(); $j_cantidad=0;
                for($i=0;$i<count($rows_Veh);$i++){
                    if(!(in_array($rows_Veh[$i]->ID_BANDA, $ids_))){  
                        $data[$j_cantidad] = $this->GestorCaso->getGrupoIndivicual($rows_Veh[$i]->ID_BANDA);
                        array_push($ids_, $rows_Veh[$i]->ID_BANDA);
                        $j_cantidad++;
                    }
                }
              //  print_r($data);//$csv_data;
                for($ii=0;$ii<count($data);$ii++){
                    for($ij=0;$ij<count($data[$ii][0]['integrantes']);$ij++){  
                        $csv_data .= "\" ".$data[$ii][0]['integrantes'][$ij]->NOMBRE . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->APELLIDO_PATERNO) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->APELLIDO_MATERNO) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->TIPO) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->SEXO) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->CURP) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->UDC) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->UTC) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->ALIAS) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->PERFIL_FACEBOOK) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->DESCRIPCION) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->ANTECEDENTES_PERSONA) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['integrantes'][$ij]->ESTATUS) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['grupo']->NOMBRE_BANDA) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['grupo']->PRINCIPALES_DELITOS) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['grupo']->PELIGROSIDAD) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['grupo']->ZONAS) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['grupo']->COLONIAS) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['grupo']->ACTIVIDADES_ILEGALES) . "\",\"" .
                            mb_strtoupper($data[$ii][0]['grupo']->FECHAHORA) . "\"\n";
                    }
                }
                break;
                case '2':
                    $filename = "GestorCasos";
                    $csv_data = "Nombre, Apellido Paterno, Apellido Materno, Categoria, Sexo, CURP, UDC, UTC, Alias, Perfil de FB, Descripcion, Antecedentes, Estatus, Nombre de la banda, Principales delitos, Peligrosidad, Zonas, Colonias, Actividades Ilegales, Fecha de creación \n";
                    $rows_Veh = $this->GestorCaso->getAllInfoRemisionDByCadena($from_where_sentence);
                    $ids_= array(); $j_cantidad=0;
                    for($i=0;$i<count($rows_Veh);$i++){
                        if(!(in_array($rows_Veh[$i]->ID_BANDA, $ids_))){  
                            $data[$j_cantidad] = $this->GestorCaso->getGrupoIndivicual($rows_Veh[$i]->ID_BANDA);
                            array_push($ids_, $rows_Veh[$i]->ID_BANDA);
                            $j_cantidad++;
                        }
                    }
                  //  print_r($data);//$csv_data;
                    for($ii=0;$ii<count($data);$ii++){
                        for($ij=0;$ij<count($data[$ii][0]['integrantes']);$ij++){  
                            $csv_data .= "\" ".$data[$ii][0]['integrantes'][$ij]->NOMBRE . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->APELLIDO_PATERNO) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->APELLIDO_MATERNO) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->TIPO) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->SEXO) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->CURP) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->UDC) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->UTC) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->ALIAS) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->PERFIL_FACEBOOK) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->DESCRIPCION) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->ANTECEDENTES_PERSONA) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['integrantes'][$ij]->ESTATUS) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['grupo']->NOMBRE_BANDA) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['grupo']->PRINCIPALES_DELITOS) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['grupo']->PELIGROSIDAD) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['grupo']->ZONAS) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['grupo']->COLONIAS) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['grupo']->ACTIVIDADES_ILEGALES) . "\",\"" .
                                mb_strtoupper($data[$ii][0]['grupo']->FECHAHORA) . "\"\n";
                        }
                    }
                    break;
        }
        $csv_data = utf8_decode($csv_data); //escribir información con formato utf8 por algún acento
        header("Content-Description: File Transfer");
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=" . $filename . ".csv");
        $user = $_SESSION['userdata']->Id_Usuario;
        $ip = $this->obtenerIp();
        $descripcion = 'Generacion de excel: ' . $cadena;
        $this->Clase->historial($user, $ip, 6, $descripcion);
        echo $csv_data;
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
    /* ------------ FUNCION PARA OBTENER TODA LA INFO DE UNA CLASE ------------------ */
    
    public function getClase()
    {
        $data = $this->Clase->getClase($_POST);
        echo json_encode($data); 
    }

    /* ------------ FUNCIONES DE OBTENCION DE CATALOGOS PARA LA CREACION DE CLASES ------------ */

    public function getAlumnos()
    {
        $data = $this->Catalogo->getAlumnos();
        return $data;
    }

    public function getProfesores()
    {
        $data = $this->Catalogo->getProfesores();
        return $data;
    }

    public function getNiveles()
    {
        $data = $this->Catalogo->getSimpleCatalogo('Descripcion','nivel');
        return $data;
    }

    public function delegarClase(){
       
        
        $success = true;
        
        if ($success) {
           
            $success_2 = $this->Clase->delegarClase($_POST);
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

    
    public function cancelarClase(){
       
        
        $success = true;
        
        if ($success) {
           
            $success_2 = $this->Clase->cancelarClase($_POST);
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

}
?>