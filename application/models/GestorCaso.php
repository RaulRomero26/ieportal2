<?php

class GestorCaso
{

    public $db; //variable para instanciar el objeto PDO
    public function __construct()
    {
        $this->db = new Base(); //se instancia el objeto con los métodos de PDO
    }

/*----------- FUNCION INSERT NUEVO EVENTO -------------------*/ 
    public function insertNuevoGrupo($post){
        $repla=['"','\''];
        $data['status'] = true;
        try{
                $name2 = '';
                $this->db->beginTransaction();  //inicia la transaction
                if(strcmp($post['foto_grupo'], "[]")!=0){
                    $foto = json_decode($post['foto_grupo']);
                    $name2 = '';$date2 = date("Ymdhis");
                    if ($foto[0]->row->typeImage == 'File') {
                        $type = $_FILES["foto_grupo"]['type'];
                        $extension = explode("/", $type);
                        if(count($extension)>1)
                            $name2 = "foto_grupo" . "." . $extension[1] . "?v=" . $date2;
                        else
                            $name2 = ".png?v=" . $date2;
                    } else {
                        $name2 = "foto_grupo" . ".png?v=" . $date2;
                    }
                }
                
                $sql = "INSERT
                        INTO atlas_grupos(
                            NOMBRE_BANDA,
                            ANTECEDENTES,
                            PRINCIPALES_DELITOS,
                            PELIGROSIDAD,
                            ZONAS,
                            COLONIAS,
                            ACTIVIDADES_ILEGALES,
                            FOTOGRAFIA
                        )
                        VALUES(
                            '".mb_strtoupper($post['nombre_grupo'])."',
                            '".str_replace($repla, '`',$post['antecedentes'])."',
                            '".$post['principal_actividad']."',
                            '".$post['peligrosidad']."',
                            '".$post['zonas_final']."',
                            '".$post['colonias_final']."',
                            '".$post['delitos_asociados']."',
                            '".$name2."'
                        )
                ";
                $this->db->query($sql);
                $this->db->execute();
                $this->db->query("SELECT LAST_INSERT_ID() as id_grupo"); //se recupera el id de ubicacion creado recientemente
                $id_grupo = $this->db->register()->id_grupo;
                $data['grupo']=$id_grupo;
                $integrantes = json_decode($post['integrantes_table']);

                $date = date("Ymdhis");

                foreach ($integrantes as $integrante) {

                    $name = '';
                            if ($integrante->row->typeImage == 'File') {
                                $type = $_FILES[$integrante->row->nameImage]['type'];
                                $extension = explode("/", $type);
                                $name = $integrante->row->nameImage . "." . $extension[1] . "?v=" . $date;
                            } else {
                                $name = $integrante->row->nameImage . ".png?v=" . $date;
                            }

                    $sql = " INSERT
                            INTO atlas_personas(
                                ID_BANDA,
                                TIPO,
                                NOMBRE,
                                APELLIDO_PATERNO,
                                APELLIDO_MATERNO,
                                SEXO,
                                CURP,
                                UDC,
                                UTC,
                                ALIAS,
                                PERFIL_FACEBOOK,
                                ANTECEDENTES_PERSONA,
                                DESCRIPCION,
                                ESTATUS,
                                PATH_IMAGEN
                            )
                            VALUES(
                                ".$id_grupo.",
                                '" . trim($integrante->row->categoria_int) . "',
                                '" . trim($integrante->row->nombre_int) . "',
                                '" . trim($integrante->row->apep_int) . "',
                                '" . trim($integrante->row->apem_int) . "',
                                '" . trim($integrante->row->sexo_int) . "',
                                '" .  mb_strtoupper(trim($integrante->row->curp_int)) . "',
                                '" . trim($integrante->row->udc_int) . "',
                                '" . trim($integrante->row->utc_int) . "',
                                '" . trim($integrante->row->alias_int) . "',
                                '" . trim($integrante->row->face_int) . "',
                                '" . trim($integrante->row->antece_int) . "',
                                '" . str_replace($repla, '`',trim($integrante->row->asociado_int)) . "',
                                '" . trim($integrante->row->estado_int) . "',
                                '" . trim($name) . "'
                            )
                    ";
                    $this->db->query($sql);
                    $this->db->execute();
                }
                $this->db->commit(); //se realiza los commits de cada query ejecutado correctamente
        }catch(Exception $e){
            echo $e;
            // echo "Error, ".$e."\nParece que hubo un error en la base de datos. Recomendación: asegúrese de no insertar emojis en algun campo del formulario";
            $data['status'] = false;
            $this->db->rollBack(); //rollBack de seguridad por si ocurre un fallo
        }
        return $data;                    
    }

    public function editarGrupo($post){
        $data['status'] = true;
    
        try{
         //   print_r($_FILES);
            $this->db->beginTransaction();  //inicia la transaction
            $foto = json_decode($post['foto_grupo']);
            $name2 = '';$date2 = date("Ymdhis");
            if ($foto[0]->row->typeImage == 'File') {
                $type = $_FILES["foto_grupo"]['type'];
                $extension = explode("/", $type);
                if(count($extension)>1)
                    $name2 = "foto_grupo" . "." . $extension[1] . "?v=" . $date2;
                else{
                    $ruta_anterior=explode("/",$post['imagen_anterior']);
                    $name2 = $ruta_anterior[count($ruta_anterior)-1]."?v=" . $date2;
                }
            } else {
                $name2 = "foto_grupo" . ".png?v=" . $date2;
            }
            $id_grupo = $post['no_grupo'];
            $data['grupo'] = $id_grupo;
            $repla=['"','\''];
            $sql = "UPDATE atlas_grupos 
                SET NOMBRE_BANDA      = '" . mb_strtoupper(trim($post['nombre_grupo'])) . "',
                    PRINCIPALES_DELITOS     = '" . trim($post['principal_actividad']) . "',
                    ACTIVIDADES_ILEGALES      = '" . trim($post['delitos_asociados']) . "',
                    PELIGROSIDAD         = '" . trim($post['peligrosidad']) . "',
                    ZONAS         = '" . trim($post['zonas_final']) . "',
                    COLONIAS   = '" . trim($post['colonias_final']) . "',
                    ANTECEDENTES           = '" . str_replace($repla, '`',$post['antecedentes']) . "',
                    FOTOGRAFIA           = '" . $name2 . "'
                    WHERE ID_BANDA = '" . $id_grupo . "'";
                $this->db->query($sql);
                $this->db->execute();
                
                $sql = "DELETE FROM atlas_personas WHERE ID_BANDA =" . $id_grupo;
                $this->db->query($sql);
                $this->db->execute();
                $date = date("Ymdhis");
                $integrantes = json_decode($post['integrantes_table']);
                foreach ($integrantes as $integrante) {
                    //print_r($integrante);
                    $name = '';
                    if ($integrante->row->typeImage == 'File') {
                        $type = $_FILES[$integrante->row->nameImage]['type'];
                        $extension = explode("/", $type);
                        $name = $integrante->row->nameImage . "." . $extension[1] . "?v=" . $date;
                    } else {
                        $name = $integrante->row->nameImage . ".png?v=" . $date;
                    }

                    $sql = " INSERT
                            INTO atlas_personas(
                                ID_BANDA,
                                TIPO,
                                NOMBRE,
                                APELLIDO_PATERNO,
                                APELLIDO_MATERNO,
                                SEXO,
                                CURP,
                                UDC,
                                UTC,
                                ALIAS,
                                PERFIL_FACEBOOK,
                                DESCRIPCION,
                                ANTECEDENTES_PERSONA,
                                ESTATUS,
                                PATH_IMAGEN
                            )
                            VALUES(
                                ".$id_grupo.",
                                '" . trim($integrante->row->categoria_int) . "',
                                '" . trim($integrante->row->nombre_int) . "',
                                '" . trim($integrante->row->apep_int) . "',
                                '" . trim($integrante->row->apem_int) . "',
                                '" . trim($integrante->row->sexo_int) . "',
                                '" . mb_strtoupper(trim($integrante->row->curp_int)) . "',
                                '" . trim($integrante->row->udc_int) . "',
                                '" . trim($integrante->row->utc_int) . "',
                                '" . trim($integrante->row->alias_int) . "',
                                '" . trim($integrante->row->face_int) . "',
                                '" . str_replace($repla, '`',trim($integrante->row->asociado_int)) . "',
                                '" . trim($integrante->row->antece_int) . "',
                                '" . trim($integrante->row->estado_int) . "',
                                '" . trim($name) . "'
                            )
                    ";
                    $this->db->query($sql);
                    $this->db->execute();
                }
                $this->db->commit(); //se realiza los commits de cada query ejecutado correctamente
        }catch(Exception $e){
            echo $e;
            // echo "Error, ".$e."\nParece que hubo un error en la base de datos. Recomendación: asegúrese de no insertar emojis en algun campo del formulario";
            $data['status'] = false;
            $this->db->rollBack(); //rollBack de seguridad por si ocurre un fallo
        }
        return $data;                    
    }

    public function getGrupo($no_grupo){
        
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM atlas_grupos WHERE ID_BANDA =" . $no_grupo;
            $this->db->query($sql);
            $data['grupo'] = $this->db->register();

            $sql = "SELECT * FROM atlas_personas WHERE ID_BANDA =" . $no_grupo;
            $this->db->query($sql);
            $data['integrantes'] = $this->db->registers();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $data;
            
    }
    //genera la consulta where dependiendo del filtro
    public function generateFromWhereSentence($cadena = "", $filtro = '1'){

        $from_where_sentence = "";
        switch ($filtro) {
            case '1':   //general
                $from_where_sentence .= "
                                        FROM atlas_filtro_1

                                        WHERE  (    ID_PERSONA LIKE '%" . $cadena . "%' OR 
                                                    NOMBRE_COMPLETO LIKE '%" . $cadena . "%' OR 
                                                    SEXO LIKE '%" . $cadena . "%' OR 
                                                    CURP LIKE '%" . $cadena . "%' OR 
                                                    UDC LIKE '%" . $cadena . "%' OR 
                                                    UTC LIKE '%" . $cadena . "%' OR 
                                                    ALIAS LIKE '%" . $cadena . "%' OR 
                                                    DESCRIPCION LIKE '%" . $cadena . "%' OR 
                                                    ANTECEDENTES_PERSONA LIKE '%" . $cadena . "%' OR 
                                                    ESTATUS LIKE '%" . $cadena . "%' OR 
                                                    NOMBRE_BANDA LIKE '%" . $cadena . "%' ) 
                                                    ";
                break;
            case '2':   //ubicacion de los hechos
                $from_where_sentence .= "
                                        FROM atlas_filtro_2

                                        WHERE  (    ID_BANDA LIKE '%" . $cadena . "%' OR 
                                                    NOMBRE_BANDA LIKE '%" . $cadena . "%' OR 
                                                    PRINCIPALES_DELITOS LIKE '%" . $cadena . "%' OR 
                                                    ACTIVIDADES_ILEGALES LIKE '%" . $cadena . "%' OR 
                                                    PELIGROSIDAD LIKE '%" . $cadena . "%' OR 
                                                    ZONAS LIKE '%" . $cadena . "%' OR 
                                                    COLONIAS LIKE '%" . $cadena . "%' OR 
                                                    ANTECEDENTES LIKE '%" . $cadena . "%') 
                                            ";
                break;
        }

        //where complemento fechas (si existe)
        //$from_where_sentence .= $this->getFechaCondition();
        //order by
        if($filtro == 1){$from_where_sentence .= " ORDER BY ID_PERSONA";}
        if($filtro == 2){$from_where_sentence .= " ORDER BY ID_BANDA";}
        return $from_where_sentence;
    }

            /*------------------FUNCIONES PARA FILTRADO Y BÚSQUEDA------------------*/
    //obtener el total de páginas y de registros de la consulta
    public function getTotalPages($no_of_records_per_page, $from_where_sentence = ""){
        //quitamos todo aquello que este fuera de los parámetros para solo obtener el substring desde FROM
        $from_where_sentence = strstr($from_where_sentence, 'FROM');

        $sql_total_pages = "SELECT COUNT(*) as Num_Pages " . $from_where_sentence; //total registros
        $this->db->query($sql_total_pages);      //prepararando query
        $total_rows = $this->db->register()->Num_Pages; //ejecutando query y recuperando el valor obtenido
        $total_pages = ceil($total_rows / $no_of_records_per_page); //calculando el total de paginations

        $data['total_rows'] = $total_rows;
        $data['total_pages'] = $total_pages;
        return $data;
    }

    //obtener los registros de la pagina actual
    public function getDataCurrentPage($offset, $no_of_records_per_page, $from_where_sentence = ""){

        $sql = "
                SELECT * "
            . $from_where_sentence . " 
                LIMIT $offset,$no_of_records_per_page
                ";

        $this->db->query($sql);
        return $this->db->registers();
    }

        //función auxiliar para filtrar por un rango de fechas específicado por el usuario
    public function getFechaCondition(){
        $cad_fechas = "";
        if (isset($_SESSION['userdata']->rango_inicio_gc) && isset($_SESSION['userdata']->rango_fin_gc)) { //si no ingresa una fecha se seleciona el día de hoy como máximo
            $rango_inicio = $_SESSION['userdata']->rango_inicio_gc;
            $rango_fin = $_SESSION['userdata']->rango_fin_gc;
            $cad_fechas = " AND 
                            FECHAHORA >= '" . $rango_inicio . " 00:00:00'  AND 
                            FECHAHORA <= '" . $rango_fin . " 23:59:59' 
                            ";
        }

        return $cad_fechas;
    }
    public function obtenerTodo(){
        
        try {
            $this->db->beginTransaction();
            $sql = "SELECT ID_BANDA from atlas_grupos";
            $this->db->query($sql);
            $todos = $this->db->registers();
            $grupos=[];
            for($i=0;$i<count($todos);$i++){
                $sql = "SELECT * FROM atlas_grupos WHERE ID_BANDA =" . $todos[$i]->ID_BANDA;
                $this->db->query($sql);
                $data['grupo'] = $this->db->register();
                $sql = "SELECT * FROM atlas_personas WHERE ID_BANDA =" . $todos[$i]->ID_BANDA;
                $this->db->query($sql);
                $data['integrantes'] = $this->db->registers();
                array_push($grupos,$data);
            }
            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $grupos;
            
    }
    public function getRemisionDByCadena($cadena, $filtro = '1')
    {
        //CONSULTA COINCIDENCIAS DE CADENA PARA EVENTOS DELICTIVOS
        if (!is_numeric($filtro) || !($filtro >= MIN_FILTRO_GC) || !($filtro <= MAX_FILTRO_GC))
            $filtro = 1;

        //sentencia from_where para hacer la busqueda por la cadena ingresada
        $from_where_sentence = $this->generateFromWhereSentence($cadena, $filtro);
        $numPage = 1;
        $no_of_records_per_page = NUM_MAX_REG_PAGE; //total de registros por pagination
        $offset = ($numPage - 1) * $no_of_records_per_page; // desplazamiento conforme a la pagina

        $results = $this->getTotalPages($no_of_records_per_page, $from_where_sentence);  //total de páginas conforme a la busqueda
        //info de retorno para la creacion de los links conforme a la cadena ingresada
        $data['rows_Rems'] = $this->getDataCurrentPage($offset, $no_of_records_per_page, $from_where_sentence);   //se obtiene la información de la página actual
        $data['numPage'] = $numPage; //numero pag actual para la pagination footer
        $data['total_pages'] = $results['total_pages']; //total pages para la pagination
        $data['total_rows'] = $results['total_rows'];   //total de registro hallados

        return $data;
    }
    public function getAllInfoRemisionDByCadena($from_where_sentence = "")
    {
        $sqlAux = "SELECT *"
            . $from_where_sentence . "
                    ";  //query a la DB
        $this->db->query($sqlAux);          //se prepara el query mediante PDO
        //$registros = $this->db->registers();  
        //$regprint = print_r($registros);
        return $this->db->registers();      //retorna todos los registros devueltos por la consulta
    }
    public function getGrupoIndivicual($no_grupo){
        try {
            $this->db->beginTransaction();
            $grupos=[];
            $sql = "SELECT * FROM atlas_grupos WHERE ID_BANDA =" . $no_grupo;
            $this->db->query($sql);
            $data['grupo'] = $this->db->register();
            $sql = "SELECT * FROM atlas_personas WHERE ID_BANDA =" . $no_grupo;
            $this->db->query($sql);
            $data['integrantes'] = $this->db->registers();
            array_push($grupos,$data);
            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }
        return $grupos;
    }
    public function historial($user, $ip, $movimiento, $descripcion)
    {
        $band = true;
        try {

            $this->db->beginTransaction();

            $sql = " INSERT
                    INTO historial(
                        Id_Usuario,
                        Ip_Acceso,
                        Movimiento,
                        Descripcion
                    )
                    VALUES(
                        trim($user),
                        '" . trim($ip) . "',
                        trim($movimiento),
                        '" . trim($descripcion) . "'
                    )
            ";
            $this->db->query($sql);
            $this->db->execute();

            $this->db->commit();
        } catch (Exception $e) {
            echo "Sucedio un error " . $e;
            $band = false;
            $this->db->rollBack();
        }

        return $band;
    }
    
}