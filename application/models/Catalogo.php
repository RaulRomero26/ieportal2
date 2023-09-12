<?php
/*
    Catálogos:
    1  - Tatuajes
*/
class Catalogo
{
	
	public $db; //variable para instanciar el objeto PDO
    public function __construct(){
        $this->db = new Base(); //se instancia el objeto con los métodos de PDO
    }

    //Obtener la info del catálogo conforme a la cadena de búsqueda y al catálogo en sí
    public function getCatalogoByCadena($cadena,$catalogo = '1'){
        //CONSULTA COINCIDENCIAS DE CADENA CONFORME AL CATALOGO SELECCIONADO

        if (!is_numeric($catalogo) || !($catalogo>=MIN_CATALOGO) || !($catalogo<=MAX_CATALOGO))
        	$catalogo = 1;
        
        //sentencia from_where para hacer la busqueda por la cadena ingresada
        $from_where_sentence = $this->generateFromWhereSentence($catalogo,$cadena);
        $numPage = 1;
        $no_of_records_per_page = NUM_MAX_REG_PAGE; //total de registros por pagination
        $offset = ($numPage-1) * $no_of_records_per_page; // desplazamiento conforme a la pagina

        $results = $this->getTotalPages($no_of_records_per_page,$from_where_sentence);  //total de páginas conforme a la busqueda
        //info de retorno para la creacion de los links conforme a la cadena ingresada
        $data['cat_rows'] = $this->getDataCurrentPage($offset,$no_of_records_per_page,$from_where_sentence);   //se obtiene la información de la página actual
        $data['numPage'] = $numPage; //numero pag actual para la pagination footer
        $data['total_pages'] = $results['total_pages']; //total pages para la pagination
        $data['total_rows'] = $results['total_rows'];   //total de registro hallados
        
        return $data;
    }
    //esta funcion retorna tanto el número total de paginas para los links como el total de registros contados conforme a la busqueda
    public function getTotalPages($no_of_records_per_page,$from_where_sentence = ""){ 
        $sql_total_pages = "SELECT COUNT(*) as Num_Pages ".$from_where_sentence; //total registros
        $this->db->query($sql_total_pages);      //prepararando query
        $total_rows = $this->db->register()->Num_Pages; //ejecutando query y recuperando el valor obtenido
        $total_pages = ceil($total_rows / $no_of_records_per_page); //calculando el total de paginations

        $data['total_rows'] = $total_rows;
        $data['total_pages'] = $total_pages;
        return $data;
    }

    public function getDataCurrentPage($offset,$no_of_records_per_page,$from_where_sentence = ""){

        $sql = "
                SELECT * "
                .$from_where_sentence." 
                LIMIT $offset,$no_of_records_per_page
                ";

        $this->db->query($sql);
        return $this->db->registers();
    }

    public function generateFromWhereSentence($catalogo,$cadena=""){
        $from_where_sentence = "";
        switch ($catalogo) {
        	case '1': $from_where_sentence.= "FROM alumno WHERE Nombre LIKE '%".$cadena."%' OR Apellido_materno LIKE '%".$cadena."%' OR Edad LIKE '%".$cadena."%' OR Ciudad_origen LIKE '%".$cadena."%' OR Correo LIKE '%".$cadena."%' OR Telefono LIKE '%".$cadena."%' OR Tipo_pago LIKE '%".$cadena."%' OR Nivel LIKE '%".$cadena."%' OR Contador LIKE '%".$cadena."%' OR Apellido_paterno LIKE '%".$cadena."%'"; break;
            case '2': $from_where_sentence.= "FROM maestros WHERE Nombre LIKE '%".$cadena."%' OR Apellido_materno LIKE '%".$cadena."%' OR Nivel LIKE '%".$cadena."%' OR Correo LIKE '%".$cadena."%' OR Telefono LIKE '%".$cadena."%' OR Dias_disponible LIKE '%".$cadena."%' OR Horario_disponible LIKE '%".$cadena."%' OR Apellido_paterno LIKE '%".$cadena."%'"; break;
            case '3': $from_where_sentence.= "FROM tipo_clase WHERE Descripcion LIKE '%".$cadena."%'"; break;
            case '4': $from_where_sentence.= "FROM tipo_pago WHERE Descripcion LIKE '%".$cadena."%'"; break;
            case '5': $from_where_sentence.= "FROM metodo_pago WHERE Descripcion LIKE '%".$cadena."%'"; break;
            case '6': $from_where_sentence.= "FROM pago_hora WHERE Descripcion LIKE '%".$cadena."%'"; break;
        	default:
        		case '1': $from_where_sentence.= "FROM alumno WHERE Nombre LIKE '%".$cadena."%' OR Apellido_materno LIKE '%".$cadena."%'"; break;
        	break;
        }
        return $from_where_sentence;
    }
    public function getAllInfoCatalogoByCadena($from_where_sentence = ""){
    	$sqlAux = "SELECT *"
    				.$from_where_sentence."
                    ";  //query a la DB
        $this->db->query($sqlAux);          //se prepara el query mediante PDO
        return $this->db->registers();      //retorna todos los registros devueltos por la consulta
    }

    public function getModalidadDetencion($post)
    {
        $modalidad = $post['modalidad'];
        $sql = "SELECT * FROM catalogo_forma_detencion WHERE Forma_Detencion = '".$modalidad."'";
        $this->db->query($sql);
        return $this->db->registers();
    }

    public function InsertOrUpdateCatalogo($post){
        $catalogo = $post['catalogo'];
        $action   = $post['action'];
        $response = "Error";

        //switch de catalogo
        try{
            $this->db->beginTransaction(); //inicio de transaction
                switch ($catalogo) {
                    case '1':
                        switch ($action) { //switch de action 1-insertar  2-actualizar
                            case '1':
                                $sql = "INSERT INTO alumno (Id_clase,Nombre,Apellido_materno,Apellido_paterno,Activo,Edad,Ciudad_origen,Correo,Telefono,Tipo_pago,Nivel,Contador) 
                                        VALUES ('".$post['id_clase']."','".$post['nombre']."','".$post['apellido_m']."','".$post['apellido_p']."','".$post['activo']."','".$post['edad']."','".$post['ciudad']."','".$post['correo']."','".$post['telefono']."','".$post['tipo_pago']."','".$post['nivel']."','".$post['contador']."')";
                            break;
                            case '2':
                                $sql = "UPDATE alumno 
                                        SET Nombre    = '".$post['nombre']."',
                                            Apellido_materno     = '".$post['apellido_m']."',
                                            Apellido_paterno     = '".$post['apellido_p']."',
                                            Activo     = '".$post['activo']."',
                                            Edad     = '".$post['edad']."',
                                            Ciudad_origen     = '".$post['ciudad']."',
                                            Correo     = '".$post['correo']."',
                                            Telefono     = '".$post['telefono']."',
                                            Tipo_pago     = '".$post['tipo_pago']."',
                                            Nivel     = '".$post['nivel']."'
                                        WHERE Id_alumno = ".$post['id_alumno']."
                                       ";
                            break;
                        }
                    break;
                    case '2':
                        switch ($action) { //switch de action 1-insertar  2-actualizar
                            case '1':
                                $sql = "INSERT INTO maestros (Nombre,Apellido_materno,Apellido_paterno,Activo,Horario_disponible,Dias_disponible,Correo,Telefono,Nivel) 
                                        VALUES ('".$post['nombre']."','".$post['apellido_m']."','".$post['apellido_p']."','".$post['activo']."','".$post['horario_disponible']."','".$post['dias_disponible']."','".$post['correo']."','".$post['telefono']."','".$post['nivel']."')";
                            break;
                            case '2':
                                $sql = "UPDATE maestros 
                                        SET Nombre    = '".$post['nombre']."',
                                            Apellido_materno     = '".$post['apellido_m']."',
                                            Apellido_paterno     = '".$post['apellido_p']."',
                                            Activo     = '".$post['activo']."',
                                            Correo     = '".$post['correo']."',
                                            Horario_disponible     = '".$post['horario_disponible']."',
                                            Dias_disponible     = '".$post['dias_disponible']."',
                                            Telefono     = '".$post['telefono']."',
                                            Nivel     = '".$post['nivel']."'
                                        WHERE Id_maestro = ".$post['id_maestro']."
                                       ";
                            break;
                        }
                    break;
                    case '3':
                        switch ($action) { //switch de action 1-insertar  2-actualizar
                            case '1':
                                $sql = "INSERT INTO tipo_clase(Descripcion) 
                                        VALUES ('".$post['Descripcion']."')";
                            break;
                            case '2':
                                $sql = "UPDATE tipo_clase 
                                        SET 
                                            Descripcion     = '".$post['Descripcion']."' 
                                        WHERE ID_TIPO_CLASE = ".$post['id_tipo_clase']."
                                       ";
                            break;
                        }
                    break;
                    case '4':
                        switch ($action) { //switch de action 1-insertar  2-actualizar
                            case '1':
                                $sql = "INSERT INTO tipo_pago(Descripcion) 
                                        VALUES ('".$post['Descripcion']."')";
                            break;
                            case '2':
                                $sql = "UPDATE tipo_pago
                                        SET 
                                            Descripcion     = '".$post['Descripcion']."' 
                                        WHERE Id_tipo_pago = ".$post['id_tipo_pago']."
                                       ";
                            break;
                        }
                    break;
                    case '5':
                        switch ($action) { //switch de action 1-insertar  2-actualizar
                            case '1':
                                $sql = "INSERT INTO metodo_pago(Descripcion) 
                                        VALUES ('".$post['Descripcion']."')";
                            break;
                            case '2':
                                $sql = "UPDATE metodo_pago 
                                        SET 
                                            Descripcion     = '".$post['Descripcion']."' 
                                        WHERE Id_metodo_pago = ".$post['id_metodo_pago']."
                                       ";
                            break;
                        }
                    break;
                    case '6':
                        switch ($action) { //switch de action 1-insertar  2-actualizar
                            case '1':
                                $sql = "INSERT INTO pago_hora(Descripcion,Cantidad) 
                                        VALUES ('".$post['Descripcion']."','".$post['Cantidad']."')";
                            break;
                            case '2':
                                $sql = "UPDATE pago_hora 
                                        SET 
                                            Descripcion     = '".$post['Descripcion']."' , Cantidad     = '".$post['Cantidad']."' 
                                        WHERE Id_pago_hora = ".$post['id_pago_hora']."
                                       ";
                            break;
                        }
                    break;
                }
            $this->db->query($sql); //se prepara query
            $this->db->execute();   //se ejecuta el query
            $this->db->commit();  //si todo sale bien, la transaction realiza commit de los queries
            $response = "Success";
        }
        catch (Exception $e) {
            $this->db->rollBack();    //si algo falla realiza el rollBack por seguridad
            $response = "Fatal Error: ".$e->getMessage();
        }
            

        return $response;
    }

    public function deleteCatalogoRow($post){
        $catalogo = $post['catalogo'];
        $id_reg   = $post['Id_Reg'];
        $response = "Error";

        //switch de catalogo
        try{
            $this->db->beginTransaction(); //inicio de transaction
                switch ($catalogo) {
                    case '1': $sql = "DELETE FROM alumno WHERE Id_alumno = ".$id_reg; break;
                    case '2': $sql = "DELETE FROM maestros WHERE Id_maestro = ".$id_reg; break;
                    case '3': $sql = "DELETE FROM tipo_clase WHERE Id_tipo_clase = ".$id_reg; break;
                    case '4': $sql = "DELETE FROM tipo_pago WHERE Id_tipo_pago = ".$id_reg; break;
                    case '5': $sql = "DELETE FROM metodo_pago WHERE Id_metodo_pago = ".$id_reg; break;
                    case '6': $sql = "DELETE FROM pago_hora WHERE Id_pago_hora  = ".$id_reg; break;
                }
            $this->db->query($sql); //se prepara query
            $this->db->execute();   //se ejecuta el query
            $this->db->commit();  //si todo sale bien, la transaction realiza commit de los queries
            $response = "Success";
        }
        catch (Exception $e) {
            $this->db->rollBack();    //si algo falla realiza el rollBack por seguridad
            $response = "Fatal Error: ".$e->getMessage();
        }
            

        return $response;
    }


    public function getCatalogforDropdown($post){
        //SELECT Valor_MF FROM catalogo_media_filiacion WHERE Tipo_MF = 'COMPLEXIÓN'
        $sql = "SELECT Valor_MF FROM catalogo_media_filiacion WHERE Tipo_MF ="."'".$post."'"."ORDER BY Id_MF";
        $this->db->query($sql);
        $resultado = $this->db->registers();
        return $resultado;
    }

    public function getSimpleCatalogo($campo, $tabla){
        $sql = "SELECT DISTINCT $campo FROM $tabla";
        $this->db->query($sql);
        $resultado = $this->db->registers();
        return $resultado;

    }

 /* dejo esta funcion como ejemplo */

    public function getColonia( $termino ){
        $sql = "SELECT Tipo_Colonia, Colonia  FROM catalogo_colonia WHERE Colonia LIKE " ."'". $termino."%' OR Colonia LIKE " . "'%" .$termino . "%' OR Colonia LIKE " . "'" . $termino . "%'" ;
        $this->db->query( $sql );
        return $this->db->registers();
    }
    public function getSimpleCatalogoOrder($campo, $tabla,$order){
        $sql = "SELECT DISTINCT $campo FROM $tabla Order By $order";
        $this->db->query($sql);
        $resultado = $this->db->registers();
        return $resultado;
    }
    public function getMunicipiosEstados( $termino,$estado ){
        $sql = "SELECT Municipio  FROM catalogo_estados_municipios WHERE (Municipio LIKE " ."'". $termino."%' OR Municipio LIKE " . "'%" .$termino . "%' OR Municipio LIKE " . "'" . $termino . "%') AND Estado = "."'".$estado."'";
        $this->db->query( $sql );
        return $this->db->registers();
    }
/* ------------ FUNCIONES PARA OBTENER PRFESORES Y ALUMNOS PARA LOS DIFERENTES SELECT -------------- */
    public function getProfesores( ){
        $sql = "SELECT *  FROM maestros WHERE Activo = 1 " ;
        $this->db->query( $sql );
        return $this->db->registers();
    }

    public function getAlumnos( ){
        $sql = "SELECT *  FROM alumno WHERE Activo = 1 " ;
        $this->db->query( $sql );
        return $this->db->registers();
    }

    public function getNiveles( ){
        $sql = "SELECT *  FROM alumno WHERE Activo = 1 " ;
        $this->db->query( $sql );
        return $this->db->registers();
    }

}

?>