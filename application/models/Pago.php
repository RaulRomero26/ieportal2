<?php

class Pago
{

    public $db; //variable para instanciar el objeto PDO
    public function __construct()
    {
        $this->db = new Base(); //se instancia el objeto con los métodos de PDO
    }
    public function getMaestro($id_maestro){
        
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM maestros WHERE Id_maestro =" . $id_maestro;
            $this->db->query($sql);
            $data['maestro'] = $this->db->register();

            $sql = "SELECT * FROM pagos_maestros WHERE Id_maestro =" . $id_maestro;
            $this->db->query($sql);
            $data['pagos'] = $this->db->registers();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $data;
            
    }

    public function getAlumno($id_alumno){
        
        try {
            $this->db->beginTransaction();

            $sql = "SELECT * FROM alumno WHERE Id_alumno =" . $id_alumno;
            $this->db->query($sql);
            $data['alumno'] = $this->db->register();

            $sql = "SELECT * FROM pagos_alumno WHERE Id_alumno =" . $id_alumno;
            $this->db->query($sql);
            $data['pagos'] = $this->db->registers();

            $this->db->commit();
        } catch (Exception $e) {
            echo $e;
            $this->db->rollBack();
        }

        return $data;
            
    }

    public function getRowsFromTable($tableName, $field, $no_ficha)
    {
        $sql = "SELECT * FROM $tableName WHERE $field = $no_ficha";
        $this->db->query($sql);
        $this->db->registers();
        return $this->db->rowCount();
    }


    /* * * * * * * * * * * * * * *   Funciones para insertar accine del historial de Remisiones   * * * * * * * * * * * * * * */

    //Funcionn que inserta los movimientos de los usuarios en la tabla historial
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

/*--------------------------FIN DE UPDATE--------------------------*/

    /*------------------FUNCIONES PARA FILTRADO Y BÚSQUEDA------------------*/
    //obtener el total de páginas y de registros de la consulta
    public function getTotalPages($no_of_records_per_page, $from_where_sentence = "")
    {
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
    public function getDataCurrentPage($offset, $no_of_records_per_page, $from_where_sentence = "")
    {

        $sql = "
                SELECT * "
            . $from_where_sentence . " 
                LIMIT $offset,$no_of_records_per_page
                ";

        $this->db->query($sql);
        return $this->db->registers();
    }

    //genera la consulta where dependiendo del filtro
    public function generateFromWhereSentence($cadena = "", $filtro = '1')
    {

        $from_where_sentence = "";
        switch ($filtro) {
            case '1':   //general
                $from_where_sentence .= "
                                        FROM maestros

                                        WHERE  (    Nombre LIKE '%" . $cadena . "%' OR 
                                                    Apellido_paterno LIKE '%" . $cadena . "%' OR 
                                                    Apellido_materno LIKE '%" . $cadena . "%' OR 
                                                    Nivel LIKE '%" . $cadena . "%' OR 
                                                    Correo LIKE '%" . $cadena . "%' OR 
                                                    Telefono LIKE '%" . $cadena . "%' OR 
                                                    Dias_disponible LIKE '%" . $cadena . "%' OR 
                                                    Horario_disponible LIKE '%" . $cadena . "%') 
                                            ";
                break;
            case '2':   //peticionarios
                $from_where_sentence .= "
                                        FROM alumno

                                        WHERE  (    Nombre LIKE '%" . $cadena . "%' OR 
                                                    Apellido_paterno LIKE '%" . $cadena . "%' OR 
                                                    Apellido_materno LIKE '%" . $cadena . "%' OR 
                                                    Edad LIKE '%" . $cadena . "%' OR 
                                                    Ciudad_origen LIKE '%" . $cadena . "%' OR 
                                                    Correo LIKE '%" . $cadena . "%' OR 
                                                    Telefono LIKE '%" . $cadena . "%' OR 
                                                    Tipo_pago LIKE '%" . $cadena . "%' OR 
                                                    Nivel LIKE '%" . $cadena . "%') 
                                            ";
                break;
                case '3':   //general
                    $from_where_sentence .= "
                                            FROM maestros INNER JOIN pagos_maestros ON maestros.Id_maestro=pagos_maestros.Id_maestro
    
                                            WHERE  (    Nombre LIKE '%" . $cadena . "%' OR 
                                                        Apellido_paterno LIKE '%" . $cadena . "%' OR 
                                                        Apellido_materno LIKE '%" . $cadena . "%' OR 
                                                        Nivel LIKE '%" . $cadena . "%' OR 
                                                        Correo LIKE '%" . $cadena . "%' OR 
                                                        Telefono LIKE '%" . $cadena . "%' OR 
                                                        Dias_disponible LIKE '%" . $cadena . "%' OR 
                                                        Horario_disponible LIKE '%" . $cadena . "%')
                                            AND pagos_maestros.Descripcion='PENDIENTE'
                                                ";
                    break;
                case '4':   //peticionarios
                    $from_where_sentence .= "
                                            FROM alumno INNER JOIN pagos_alumno ON alumno.Id_alumno=pagos_alumno.Id_alumno
    
                                            WHERE  (    Nombre LIKE '%" . $cadena . "%' OR 
                                                        Apellido_paterno LIKE '%" . $cadena . "%' OR 
                                                        Apellido_materno LIKE '%" . $cadena . "%' OR 
                                                        Edad LIKE '%" . $cadena . "%' OR 
                                                        Ciudad_origen LIKE '%" . $cadena . "%' OR 
                                                        Correo LIKE '%" . $cadena . "%' OR 
                                                        Telefono LIKE '%" . $cadena . "%' OR 
                                                        Tipo_pago LIKE '%" . $cadena . "%' OR 
                                                        Nivel LIKE '%" . $cadena . "%') 
                                                        AND pagos_alumno.Descripcion='PENDIENTE'
                                                ";
                    break;
            
            
        }

        //where complemento fechas (si existe)
        $from_where_sentence .= $this->getFechaCondition();
        //order by
        
        return $from_where_sentence;
    }

    public function editarPagos($post){
        $data['status'] = true;
        try{
            //print_r($_FILES);
            $this->db->beginTransaction();  //inicia la transaction
            
           
            $pagos = json_decode($post['pagos_table']);
            foreach ($pagos as $pago) {
                //$integrante->row->categoria_int
                $sql = "UPDATE pagos_maestros 
                SET Fecha      = '" . $pago->row->fecha3 . "',
                    Monto           = '" . $pago->row->monto_pago . "',
                    Comentarios           = '" . $pago->row->comentarios . "',
                    Descripcion           = '" . $pago->row->desc_pago . "'
                    WHERE Id_pagosm = '" . $pago->row->id_pago . "'";
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
    public function editarPagosA($post){
        $data['status'] = true;
        try{
            //print_r($_FILES);
            $this->db->beginTransaction();  //inicia la transaction
            $pagos = json_decode($post['pagos_table']);
            foreach ($pagos as $pago) {
                if ($pago->row->id_pago != ''){
                    //$integrante->row->categoria_int
                    $sql = "UPDATE pagos_alumno 
                    SET Fecha      = '" . $pago->row->fecha3 . "',
                        Monto           = '" . $pago->row->monto_pago . "',
                        Comentarios           = '" . $pago->row->comentarios . "',
                        Descripcion           = '" . $pago->row->desc_pago . "'
                        WHERE Id_pagosa = '" . $pago->row->id_pago . "'";
                    $this->db->query($sql);
                    $this->db->execute();
                }
                else{
                    //$integrante->row->categoria_int
                    $sql = "INSERT INTO pagos_alumno (Id_alumno, Fecha_inicio, Fecha_final, Fecha, Monto, Descripcion,Comentarios) VALUES
                    ('".$pago->row->id_alumno."','".$pago->row->fecha1."','".$pago->row->fecha2."','".$pago->row->fecha3."','".$pago->row->monto_pago."','".$pago->row->desc_pago."','".$pago->row->comentarios."')";
                    $this->db->query($sql);
                    $this->db->execute();
                }
                
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

    public function getRemisionDByCadena($cadena, $filtro = '1')
    {
        //CONSULTA COINCIDENCIAS DE CADENA PARA EVENTOS DELICTIVOS
        if (!is_numeric($filtro) || !($filtro >= MIN_FILTRO_PA) || !($filtro <= MAX_FILTRO_PA))
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

    //obtener todos los registros de un cierto filtro para su exportación
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
    //complementaria de getAll para vista general para exportación EXCEL
    public function getTodosApartados($rows)
    {
        $data['Maestro'] = [];  
        $data['Pagos'] = [];                 
        foreach ($rows as $key => $row) {
            $data['Pagos'][$key] = (object)[];
            $sql = "SELECT * FROM pagos_maestros WHERE Id_maestro = " . $row->Id_maestro;
            $this->db->query($sql);
            $data['Pagos'][$key] = $this->db->registers();

            $data['Maestro'][$key] = (object)[];
            $sql = "SELECT * FROM maestros WHERE Id_maestro = " . $row->Id_maestro;
            $this->db->query($sql);
            $data['Maestro'][$key] = $this->db->register();
        }
        return $data;
    }
    public function getTodosApartadosA($rows)
    {
        $data['Alumno'] = [];  
        $data['Pagos'] = [];                 
        foreach ($rows as $key => $row) {
            $data['Pagos'][$key] = (object)[];
            $sql = "SELECT * FROM pagos_alumno WHERE Id_alumno = " . $row->Id_alumno;
            $this->db->query($sql);
            $data['Pagos'][$key] = $this->db->registers();

            $data['Maestro'][$key] = (object)[];
            $sql = "SELECT * FROM alumno WHERE Id_alumno = " . $row->Id_alumno;
            $this->db->query($sql);
            $data['Maestro'][$key] = $this->db->register();
        }
        return $data;
    }

    //función auxiliar para filtrar por un rango de fechas específicado por el usuario
    public function getFechaCondition()
    {
        $cad_fechas = "";
        if (isset($_SESSION['userdata']->rango_inicio_rem) && isset($_SESSION['userdata']->rango_fin_rem)) { //si no ingresa una fecha se seleciona el día de hoy como máximo
            $rango_inicio = $_SESSION['userdata']->rango_inicio_rem;
            $rango_fin = $_SESSION['userdata']->rango_fin_rem;
            $cad_fechas = " AND 
                            Fecha_Hora >= '" . $rango_inicio . " 00:00:00'  AND 
                            Fecha_Hora <= '" . $rango_fin . " 23:59:59' 
                            ";
        }
        // else{
        //     $hoy = date("Y-m-d");
        //     $_SESSION['userdata']->rango_inicio_rem = $hoy;
        //     $_SESSION['userdata']->rango_fin_rem = $hoy;
        //     $rango_inicio = $_SESSION['userdata']->rango_inicio_rem;
        //     $rango_fin = $_SESSION['userdata']->rango_fin_rem;
        //     $cad_fechas = " AND 
        //                     Fecha_Hora >= '" . $rango_inicio . " 00:00:00'  AND 
        //                     Fecha_Hora <= '" . $rango_fin . " 23:59:59' 
        //                     ";
        // }

        return $cad_fechas;
    }
}
