<?php
/*
    VIEWS DEL SISTEMA:
    usuario_permisos para busquedas más eficientes

    ___________________________________________
    permisos: CRUD -- Create-Read-Update-Delete -> posiciones en array:  3-2-1-0
*/
class Usuario
{
    public $db; //variable para instanciar el objeto PDO
    public function __construct(){
        $this->db = new Base(); //se instancia el objeto con los métodos de PDO
    }

    public function getUserById($id_user = null){
        if ($id_user != null) {
            $sql = "
                    SELECT  usuario.*,
                            EXPORT_SET(permisos.Clases,'1','0','',4) AS Clases,
                            permisos.Modo_Admin,
                            AES_DECRYPT(usuario.Password,'".CRYPTO_KEY."') as Pass_Decrypt
                    FROM usuario
                    LEFT JOIN permisos ON permisos.Id_Permisos = usuario.Id_Permisos
                    WHERE usuario.Id_Usuario = $id_user
                    ";
            $this->db->query($sql);
            return $this->db->register();
        }
        else return false;
    }

    public function getUsers(){   //funcion creada para obtener los registros de los usuarios
        $sqlAux = "SELECT usuario.*,permisos.* 
                    FROM usuario
                    LEFT JOIN permisos ON permisos.Id_Permisos = usuario.Id_Permisos";  //query a la DB
        $this->db->query($sqlAux);          //se prepara el query mediante PDO
        return $this->db->registers();      //retorna todos los registros devueltos por la consulta
    }

    public function loginUser($post){
        $sqlAux = " SELECT  usuario.*, 
                            EXPORT_SET(permisos.Clases,'1','0','',4) AS Clases,
                            EXPORT_SET(permisos.Pagos,'1','0','',4) AS Pagos,
                            permisos.Modo_Admin
                    FROM usuario
                    LEFT JOIN permisos ON permisos.Id_Permisos = usuario.Id_Permisos
                    WHERE Estatus=1 AND User_Name='".$post['User_Name']."' AND Password = AES_ENCRYPT('".$post['Password']."','".CRYPTO_KEY."')";

        $this->db->query($sqlAux);
        return $this->db->register();
    }


    public function insertNewUser($post,$file_name = null){
        if ($file_name == null) {
            $file_name = "default.png";
        }
        $dataReturn['success'] = '0';
        $dataReturn['errorForm'] = null;

        $sinErrorForm = true;   //bandera que marca error en el formulario

        
        try {  
            $this->db->beginTransaction(); //inicio de transaction
                //se comprueba si existe un usuario con mismo email
                $this->db->query("SELECT Id_Usuario FROM usuario WHERE Email = '".(trim($post['Email']))."'");
                if ($this->db->register()) {
                    $sinErrorForm = false;
                    $dataReturn['errorForm']['Email'] = '<p class="text-danger">Este email ya ha sido registrado</p>';
                }
            
                //se comprueba si existe un usuario con mismo user_name
                $this->db->query("SELECT Id_Usuario FROM usuario WHERE User_Name = '".(trim($post['User_Name']))."'");
                if ($this->db->register()) {
                    $sinErrorForm = false;
                    $dataReturn['errorForm']['User_Name'] = '<p class="text-danger">Este nombre de usuario ya existe</p>';
                }
                
                //si el username y email son distintos entonces se procede a armar los permisos y asignar un id_permisos o crearlos (si es necesario)
                if ($sinErrorForm){

                    $id_permisos_new_user = 1; //permisos temporal

                    if (isset($post['Modo_Admin'])) { //permisos de Admin
                        $this->db->query("SELECT Id_Permisos FROM permisos WHERE Modo_Admin = 1");
                        $Permisos_Dios = $this->db->register();
                        
                        if ($Permisos_Dios) {
                            $id_permisos_new_user = $Permisos_Dios->Id_Permisos;
                        }
                        else{//permiso dios no existe, entonces se crea un permisos nuevo con modo Dios activado
                            //se crea un nuevo permiso de modo Dios
                            $this->db->query("INSERT INTO permisos (Modo_Admin) VALUES (1)");
                            $this->db->execute();
                            $this->db->query("SELECT LAST_INSERT_ID() as Id_Permisos"); //se recupera el id de permisos creado recientemente
                            $id_permisos_new_user = $this->db->register()->Id_Permisos;
                        }
                    }
                    else{   //permisos conforme a los check dados en la matriz de permisos
                        //nuevos permisos
                        $Clases="";
                            
                        $Clases.= (isset($post['Ju_Create']))?'1':'0'; 
                        $Clases.= (isset($post['Ju_Read']))?'1':'0'; 
                        $Clases.= (isset($post['Ju_Update']))?'1':'0'; 
                        $Clases.= (isset($post['Ju_Delete']))?'1':'0';

                        $this->db->query("  SELECT  Id_Permisos 
                                            FROM    permisos 
                                            WHERE   Clases = b'".$Clases."' AND 
                                                    Modo_Admin = 0");
                        $Permisos = $this->db->register();
                        if ($Permisos) {
                            //se obtine el id de los permisos que coinciden con las marcas dadas
                            $id_permisos_new_user = $Permisos->Id_Permisos;
                        }
                        else{//permiso dios no existe, entonces se crea un permisos nuevo con modo Dios activado
                            //se crea un nuevo permiso de modo Dios
                            $this->db->query("INSERT INTO   permisos 
                                                            (Clases) 
                                                            VALUES (    b'".$Clases."'
                                                                    )");
                            $this->db->execute();
                            $this->db->query("SELECT LAST_INSERT_ID() as Id_Permisos"); //se recupera el id de permisos creado recientemente
                            $id_permisos_new_user = $this->db->register()->Id_Permisos;
                        }
                    }

                    $auxNivelUser = (isset($post['Nivel_User']))?1:0;
                    $sqlAux1 = "
                                INSERT INTO usuario (Nombre,Ap_Paterno,Ap_Materno,Area,Email,User_Name,Password,Estatus,Nivel_User,Id_Permisos,Path_Imagen_User)
                                VALUES  ('".trim($post['Nombre'])."', 
                                        '".trim($post['Ap_Paterno'])."',
                                        '".trim($post['Ap_Materno'])."',
                                        '".trim($post['Area'])."',
                                        '".trim($post['Email'])."',
                                        '".trim($post['User_Name'])."',
                                        '".trim($post['Password'])."',
                                         ".trim($post['Estatus'])." ,
                                         ".$auxNivelUser." ,
                                         ".$id_permisos_new_user.",
                                         '".$file_name."'
                                         )
                                ";
                    $this->db->query($sqlAux1);
                    $this->db->execute();
                    //recuperar el Id del último usuario registrado
                    $this->db->query("SELECT LAST_INSERT_ID() as Id_Usuario"); //se recupera el id de permisos creado recientemente
                    $dataReturn['id_new_user'] = $this->db->register()->Id_Usuario;
                }
                    
            $this->db->commit();  //si todo sale bien, la transaction realiza commit de los queries
          
            //se comprueban las banderas para comprobar los resultados y dar respuesta al controlador
            if (!$sinErrorForm) {   //error en formulario
                //echo "caí en sinErrorForm";
                $dataReturn['success'] = '-1';
            }
            else{   //sin cambios en la información
                //echo "caí en sin cambios";
                $dataReturn['success'] = '1';
            }
        }catch (Exception $e) {
            $this->db->rollBack();    //si algo falla realiza el rollBack por seguridad
            echo "Fallo en DB: " . $e->getMessage();
            $dataReturn['success'] = '-2';
            $dataReturn['errorForm'] = null;
        }
        

        return $dataReturn;
    }

    public function updateUserInfo($post){
        $dataReturn['success'] = '0';
        $dataReturn['errorForm'] = null;

        $sinErrorForm = true;   //bandera que marca error en el formulario
        $cambiosUserInfo = false;   //bandera para comprobar si se esta actualizando algo direfente al anterior o no
        $cambiosPermisos = false;

        $sqlUserInfo = "SELECT  usuario.*,
                                EXPORT_SET(permisos.Clases,'1','0','',4) AS Clases,
                                permisos.Modo_Admin,  
                                AES_DECRYPT(Password,'".CRYPTO_KEY."') as Pass_Decrypt FROM usuario 
                                LEFT JOIN permisos ON permisos.Id_Permisos = usuario.Id_Permisos 
                                WHERE Id_Usuario = ".$post['Id_Usuario']."";
        $this->db->query($sqlUserInfo);
        $dataBefore = $this->db->register();
        //convert stdClass to array
        $dataBefore = json_decode(json_encode($dataBefore), true);
        $post['Nivel_User'] = (isset($post['Nivel_User']))?1:0; //se modifica para ver si hubo cambio en nivel de user (Validaciones remisiones)

        if ($dataBefore) { //existe el usuario con el id del post?
            try {  
                $this->db->beginTransaction(); //inicio de transaction
                    //buscando diferencias en los valores nuevos y viejos de la info de User
                
                    //buscando diferencias en el formulario
                    if (($post['Nombre'] != $dataBefore['Nombre']) || ($post['Ap_Paterno'] != $dataBefore['Ap_Paterno']) || ($post['Ap_Materno'] != $dataBefore['Ap_Materno']) || ($post['Email'] != $dataBefore['Email']) || ($post['Area'] != $dataBefore['Area']) || ($post['User_Name'] != $dataBefore['User_Name']) || ($post['Password'] != $dataBefore['Pass_Decrypt']) || ($post['Estatus'] != $dataBefore['Estatus']) || ($post['Nivel_User'] != $dataBefore['Nivel_User'])) {
                        
                        if ($post['Email'] != $dataBefore['Email']) {
                            //se comprueba si existe un usuario con mismo email
                            $this->db->query("SELECT Id_Usuario FROM usuario WHERE Email = '".(trim($post['Email']))."'");
                            if ($this->db->register()) {
                                $sinErrorForm = false;
                                $dataReturn['errorForm']['Email'] = '<p class="text-danger">Este email ya ha sido registrado</p>';
                            }
                        }
                        
                        if ($post['User_Name'] != $dataBefore['User_Name']) {
                            //se comprueba si existe un usuario con mismo user_name
                            $this->db->query("SELECT Id_Usuario FROM usuario WHERE User_Name = '".(trim($post['User_Name']))."'");
                            if ($this->db->register()) {
                                $sinErrorForm = false;
                                $dataReturn['errorForm']['User_Name'] = '<p class="text-danger">Este nombre de usuario ya existe</p>';
                            }
                        }
                        
                        if ($sinErrorForm) {
                            $cambiosUserInfo = true;
                            $sqlAux1 = "
                                        UPDATE  usuario 
                                        SET     Nombre = '".trim($post['Nombre'])."', 
                                                Ap_Paterno = '".trim($post['Ap_Paterno'])."',
                                                Ap_Materno = '".trim($post['Ap_Materno'])."',
                                                Area = '".trim($post['Area'])."',
                                                Email = '".trim($post['Email'])."',
                                                User_Name = '".trim($post['User_Name'])."',
                                                usuario.Password = '".trim($post['Password'])."',
                                                Estatus = ".trim($post['Estatus']).", 
                                                Nivel_User     =  ".trim($post['Nivel_User'])." 
                                        WHERE   Id_Usuario = ".$post['Id_Usuario']."
                                        ";
                            $this->db->query($sqlAux1);
                            $this->db->execute();
                        }
                        
                    }
                    //comprobacion de cambios en Permisos de usuario
                    //modo Dios
                    if ($sinErrorForm) {
                        $Modo_Admin_Now = (isset($post['Modo_Admin']))?1:0;

                        //cambio a permisos de Dios por lo que solo se busca un permiso que coincida o si no se crea uno (ya no se toman n cuenta los demas permisos)
                        if (($Modo_Admin_Now != $dataBefore['Modo_Admin']) && ($Modo_Admin_Now == 1)) {
                            $cambiosPermisos = true;
                            $this->db->query("SELECT Id_Permisos FROM permisos WHERE Modo_Admin = 1");
                            $Permisos_Dios = $this->db->register();
                            if ($Permisos_Dios) {
                                //actualiza el id de permisos al usuario en cuestion
                                $sqlAux2 = "UPDATE usuario SET Id_Permisos = $Permisos_Dios->Id_Permisos WHERE Id_Usuario = ".$post['Id_Usuario']."";
                                $this->db->query($sqlAux2);
                                $this->db->execute();
                            }
                            else{//permiso dios no existe, entonces se crea un permisos nuevo con modo Dios activado
                                //se crea un nuevo permiso de modo Dios
                                $this->db->query("INSERT INTO permisos (Modo_Admin) VALUES (1)");
                                $this->db->execute();
                                $this->db->query("SELECT LAST_INSERT_ID() as Id_Permisos"); //se recupera el id de permisos creado recientemente
                                $id_new_permisos = $this->db->register()->Id_Permisos;
                                //se actualiza el id permisos al usuario en cuestion
                                $sqlAux2 = "UPDATE usuario SET Id_Permisos = $id_new_permisos WHERE Id_Usuario = ".$post['Id_Usuario']."";
                                $this->db->query($sqlAux2);
                                $this->db->execute();
                            }
                        }
                        elseif (($Modo_Admin_Now != $dataBefore['Modo_Admin']) && ($Modo_Admin_Now == 0)) { //se cambian los otros permisos
                            $cambiosPermisos = true;
                            //nuevos permisos
                            $Clases="";
                            
                            $Clases.= (isset($post['Ju_Create']))?'1':'0'; 
                            $Clases.= (isset($post['Ju_Read']))?'1':'0'; 
                            $Clases.= (isset($post['Ju_Update']))?'1':'0'; 
                            $Clases.= (isset($post['Ju_Delete']))?'1':'0';


                            $this->db->query("  SELECT  Id_Permisos 
                                                FROM    permisos 
                                                WHERE   Clases = b'".$Clases."' AND 
                                                        Modo_Admin = 0");
                            $Permisos = $this->db->register();
                            if ($Permisos) {
                                //actualiza el id de permisos al usuario en cuestion
                                $sqlAux2 = "UPDATE usuario SET Id_Permisos = $Permisos->Id_Permisos WHERE Id_Usuario = ".$post['Id_Usuario']."";
                                $this->db->query($sqlAux2);
                                $this->db->execute();
                            }
                            else{//permiso dios no existe, entonces se crea un permisos nuevo con modo Dios activado
                                //se crea un nuevo permiso de modo Dios
                                $this->db->query("INSERT INTO   permisos 
                                                                (Clases,) 
                                                                VALUES (    b'".$Clases."' 
                                                                        )");
                                $this->db->execute();
                                $this->db->query("SELECT LAST_INSERT_ID() as Id_Permisos"); //se recupera el id de permisos creado recientemente
                                $id_new_permisos = $this->db->register()->Id_Permisos;
                                //se actualiza el id permisos al usuario en cuestion
                                $sqlAux2 = "UPDATE usuario SET Id_Permisos = $id_new_permisos WHERE Id_Usuario = ".$post['Id_Usuario']."";
                                $this->db->query($sqlAux2);
                                $this->db->execute();
                            }
                            
                        }
                        elseif(($Modo_Admin_Now == $dataBefore['Modo_Admin']) && ($Modo_Admin_Now == 0)){ //No cambio el modo Dios pero puede que si los deás permisos
                            //nuevos permisos
                            $Clases="";
                            
                            $Clases.= (isset($post['Ju_Create']))?'1':'0'; 
                            $Clases.= (isset($post['Ju_Read']))?'1':'0'; 
                            $Clases.= (isset($post['Ju_Update']))?'1':'0'; 
                            $Clases.= (isset($post['Ju_Delete']))?'1':'0';

                            if (($Clases != $dataBefore['Clases'])) {

                                $cambiosPermisos = true;

                                $this->db->query("  SELECT  Id_Permisos 
                                                    FROM    permisos 
                                                    WHERE   Clases = b'".$Clases."' AND 
                                                            Modo_Admin = 0");

                                $Permisos = $this->db->register();
                                if ($Permisos) {
                                    //actualiza el id de permisos al usuario en cuestion
                                    $sqlAux2 = "UPDATE usuario SET Id_Permisos = $Permisos->Id_Permisos WHERE Id_Usuario = ".$post['Id_Usuario']."";
                                    $this->db->query($sqlAux2);
                                    $this->db->execute();
                                }
                                else{//permiso dios no existe, entonces se crea un permisos nuevo con modo Dios activado
                                    //se crea un nuevo permiso de modo Dios
                                    $this->db->query("INSERT INTO   permisos 
                                                            (Clases) 
                                                            VALUES (    b'".$Clases."'
                                                                    )");
                                    $this->db->execute();
                                    $this->db->query("SELECT LAST_INSERT_ID() as Id_Permisos"); //se recupera el id de permisos creado recientemente
                                    $id_new_permisos = $this->db->register()->Id_Permisos;
                                    //se actualiza el id permisos al usuario en cuestion
                                    $sqlAux2 = "UPDATE usuario SET Id_Permisos = $id_new_permisos WHERE Id_Usuario = ".$post['Id_Usuario']."";
                                    $this->db->query($sqlAux2);
                                    $this->db->execute();
                                }
                            }
                        }
                    }
                        
                $this->db->commit();  //si todo sale bien, la transaction realiza commit de los queries
              
                //se comprueban las banderas para comprobar los resultados y dar respuesta al controlador
                if (!$sinErrorForm) {   //error en formulario
                    //echo "caí en sinErrorForm";
                    $dataReturn['success'] = '-1';
                }
                elseif ($cambiosUserInfo || $cambiosPermisos) { //actualizacion correcta
                    //echo "caí en correcto";
                    $dataReturn['success'] = '1';
                }
                else{   //sin cambios en la información
                    //echo "caí en sin cambios";
                    $dataReturn['success'] = '0';
                }
            }catch (Exception $e) {
                $this->db->rollBack();    //si algo falla realiza el rollBack por seguridad
                echo "Fallo en DB: " . $e->getMessage();
                $dataReturn['success'] = '-2';
                $dataReturn['errorForm'] = null;
            }
        }

        return $dataReturn;
    }

    public function updateImgNameUser($foto_name,$id_user){
        //actualizar el nombre de la imágen del usuario
        $sqlAux1 = "
                    UPDATE usuario 
                    SET Path_Imagen_User = '".$foto_name."'
                    WHERE Id_Usuario = ".$id_user."
                    ";
        $this->db->query($sqlAux1);
        $this->db->execute();
    }

    public function generateWhereSentence($cadena){
        $where_sentence = "";
        if ($cadena != "") {
            $where_sentence = "
                        WHERE   User_Name LIKE '%".$cadena."%' OR
                                Nombre_Completo LIKE '%".$cadena."%' OR
                                Email LIKE '%".$cadena."%' OR
                                Area LIKE '%".$cadena."%'  OR
                                Refe1 LIKE '%".$cadena."%'  OR
                                Refe_Temp LIKE '%".$cadena."%'  
                        ";
        }
        
        return $where_sentence;
    }

    public function decToBin($numDec){ //decimal a binario
            $dataReturn = decbin($numDec);

            while (strlen($dataReturn) < 4) {   //se agregan ceros a la izquierda para siempre tener un string de 4 bits
                $dataReturn = "0".$dataReturn;
            }
            return $dataReturn;
    }

    //función para actualizar la contraseña del usuario en módulo de Mi Cuenta
    public function updateUserPassword($post){

        $dataReturn['success'] = '0';
        $dataReturn['errorForm'] = null;

        $sinErrorForm = true;   //bandera que marca error en el formulario
        $cambiosUserInfo = false;   //bandera para comprobar si se esta actualizando algo direfente al anterior o no
        

        $sqlUserInfo = "SELECT usuario.*, AES_DECRYPT(Password,'".CRYPTO_KEY."') as Pass_Decrypt FROM usuario LEFT JOIN permisos ON permisos.Id_Permisos = usuario.Id_Permisos WHERE Id_Usuario = ".$_SESSION['userdata']->Id_Usuario."";
        $this->db->query($sqlUserInfo);
        $dataBefore = $this->db->register();
        //convert stdClass to array
        $dataBefore = json_decode(json_encode($dataBefore), true);

        if ($dataBefore) { //existe el usuario con el id del post?
            try {  
                $this->db->beginTransaction(); //inicio de transaction                
                    //buscando diferencias en el formulario
                    if ($post['Password'] != $dataBefore['Pass_Decrypt']) {
                        $cambiosUserInfo = true;
                        $sqlAux1 = "
                                    UPDATE  usuario 
                                    SET     usuario.Password = '".trim($post['Password'])."'
                                    WHERE   Id_Usuario = ".$_SESSION['userdata']->Id_Usuario."
                                    ";
                        $this->db->query($sqlAux1);
                        $this->db->execute();
                    }
                    //comprobacion de cambios en Permisos de usuario
                    //modo Dios
                      
                $this->db->commit();  //si todo sale bien, la transaction realiza commit de los queries
              
                //se comprueban las banderas para comprobar los resultados y dar respuesta al controlador
                if (!$sinErrorForm) {   //error en formulario
                    //echo "caí en sinErrorForm";
                    $dataReturn['success'] = '-1';
                }
                elseif ($cambiosUserInfo) { //actualizacion correcta
                    //echo "caí en correcto";
                    $dataReturn['success'] = '1';
                    

                }
                else{   //sin cambios en la información
                    //echo "caí en sin cambios";
                    $dataReturn['success'] = '0';
                }
            }catch (Exception $e) {
                $this->db->rollBack();    //si algo falla realiza el rollBack por seguridad
                echo "Fallo en DB: " . $e->getMessage();
                $dataReturn['success'] = '-2';
                $dataReturn['errorForm'] = null;
            }
        }

        return $dataReturn;
    }


    /*-----FUNCIONES PARA FILTRADO Y BUSQUEDA ACTUALIZADOS (WHERE SENTENCE)-----*/
    //obtener el total de páginas y de registros de la consulta
    public function getTotalPages($no_of_records_per_page,$from_where_sentence = ""){
        //quitamos todo aquello que este fuera de los parámetros para solo obtener el substring desde FROM
        $from_where_sentence = strstr($from_where_sentence, 'FROM');

        $sql_total_pages = "SELECT COUNT(*) as Num_Pages ".$from_where_sentence; //total registros
        $this->db->query($sql_total_pages);      //prepararando query
        $total_rows = $this->db->register()->Num_Pages; //ejecutando query y recuperando el valor obtenido
        $total_pages = ceil($total_rows / $no_of_records_per_page); //calculando el total de paginations

        $data['total_rows'] = $total_rows;
        $data['total_pages'] = $total_pages;
        return $data;
    }

    //obtener los registros de la pagina actual
    public function getDataCurrentPage($offset,$no_of_records_per_page,$from_where_sentence = ""){

        $sql = "
                SELECT * "
                .$from_where_sentence." 
                LIMIT $offset,$no_of_records_per_page
                ";

        $this->db->query($sql);
        return $this->db->registers();
    }

    //genera la consulta where dependiendo del filtro
    public function generateFromWhereSentence($cadena="",$filtro='1'){

        $from_where_sentence = "";
        switch ($filtro) {
            case '1':   //todos
                
                $from_where_sentence.= " FROM usuario_permisos 
                                         WHERE      (User_Name LIKE '%".$cadena."%' OR
                                                    Nombre_Completo LIKE '%".$cadena."%' OR
                                                    Email LIKE '%".$cadena."%' OR
                                                    Area LIKE '%".$cadena."%'  OR
                                                    Refe1 LIKE '%".$cadena."%'  OR
                                                    Refe_Temp LIKE '%".$cadena."%')  
                                            ";
                
            break;
            case '2':   //administradores
                $from_where_sentence.= " FROM usuario_permisos 
                                         WHERE      (User_Name LIKE '%".$cadena."%' OR
                                                    Nombre_Completo LIKE '%".$cadena."%' OR
                                                    Email LIKE '%".$cadena."%' OR
                                                    Area LIKE '%".$cadena."%'  OR
                                                    Refe1 LIKE '%".$cadena."%'  OR
                                                    Refe_Temp LIKE '%".$cadena."%') 
                                                    AND Modo_Admin = 1 
                                            ";
                
            break;
            case '3':   //otros
                $from_where_sentence.= " FROM usuario_permisos 
                                         WHERE      (User_Name LIKE '%".$cadena."%' OR
                                                    Nombre_Completo LIKE '%".$cadena."%' OR
                                                    Email LIKE '%".$cadena."%' OR
                                                    Area LIKE '%".$cadena."%'  OR
                                                    Refe1 LIKE '%".$cadena."%'  OR
                                                    Refe_Temp LIKE '%".$cadena."%')
                                                    AND Modo_Admin = 0 
                                            ";
                
            break;

                /*
                    SELECT * FROM evento_delictivo_view WHERE Fecha >= '2020-01-01' AND Fecha <= '2020-03-30'
                */
        }

        //where complemento fechas (si existe)
        $from_where_sentence.= $this->getFechaCondition();
        //order by
        $from_where_sentence.= " ORDER BY Id_Usuario";   
        return $from_where_sentence;
    }

    public function getUsersByCadena($cadena,$filtro='1'){
        //CONSULTA COINCIDENCIAS DE CADENA PARA EVENTOS DELICTIVOS
        if (!is_numeric($filtro) || !($filtro>=MIN_FILTRO_USER) || !($filtro<=MAX_FILTRO_USER))
            $filtro = 1;
        
        //sentencia from_where para hacer la busqueda por la cadena ingresada
        $from_where_sentence = $this->generateFromWhereSentence($cadena,$filtro);
        $numPage = 1;
        $no_of_records_per_page = NUM_MAX_REG_PAGE; //total de registros por pagination
        $offset = ($numPage-1) * $no_of_records_per_page; // desplazamiento conforme a la pagina

        $results = $this->getTotalPages($no_of_records_per_page,$from_where_sentence);  //total de páginas conforme a la busqueda
        //info de retorno para la creacion de los links conforme a la cadena ingresada
        $data['rows_Users'] = $this->getDataCurrentPage($offset,$no_of_records_per_page,$from_where_sentence);   //se obtiene la información de la página actual
        $data['numPage'] = $numPage; //numero pag actual para la pagination footer
        $data['total_pages'] = $results['total_pages']; //total pages para la pagination
        $data['total_rows'] = $results['total_rows'];   //total de registro hallados
        
        return $data;
    }
    
    //obtener todos los registros de un cierto filtro para su exportación
    public function getAllInfoUsersByCadena($from_where_sentence = ""){
        $sqlAux = "SELECT *"
                    .$from_where_sentence."
                    ";  //query a la DB
        $this->db->query($sqlAux);          //se prepara el query mediante PDO
        return $this->db->registers();      //retorna todos los registros devueltos por la consulta
    }

    //Se obtiene la IP del usuario para insertarla en la tabla historial
    public function obtenerIp()
    {
        $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        $hosts = gethostbynamel($hostname);
        if (is_array($hosts)) 
        {
            //echo "Host ".$hostname." ip:<br><br>";
            foreach ($hosts as $ip) 
            {
                //echo "IP: ".$ip."<br>";
                return $ip;
            }
        }

        else 
        {
            return $ip="No se encontró IP";
        }

    }

    //Funcionn que inserta los movimientos de los usuarios en la tabla historial
    public function historical($idusuario,$descripcion)
    {
        $ip = $this->obtenerIp();
        //print_r($_SESSION['userdata']);
        $sql="INSERT INTO historial (Id_Usuario,Fecha_Hora,Ip_Acceso,Descripcion) VALUES('$idusuario',CURRENT_TIMESTAMP(),'$ip','$descripcion')";
        $this->db->query($sql);
        $this->db->execute();
    }

    //función auxiliar para filtrar por un rango de fechas específicado por el usuario
    public function getFechaCondition(){
        $cad_fechas = "";
        if (isset($_SESSION['userdata']->rango_inicio_user) && isset($_SESSION['userdata']->rango_fin_user)) { //si no ingresa una fecha se seleciona el día de hoy como máximo
            $rango_inicio = $_SESSION['userdata']->rango_inicio_user;
            $rango_fin = $_SESSION['userdata']->rango_fin_user;
            $cad_fechas = " AND 
                            Fecha_Registro_Usuario >= '".$rango_inicio." 00:00:00'  AND 
                            Fecha_Registro_Usuario <= '".$rango_fin." 23:59:59' 
                            ";
        }

        return $cad_fechas; 
    }
}