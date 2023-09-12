<?php

class Historial
{
    public $db;
    public function __construct()
    {
        $this->db = new Base();    
    }

    public function generateWhereSentence($cadena="",$filtro='1')
    {
        $where_sentence = "";
        switch($filtro){
            case '1': //Todo
                $where_sentence.= " FROM historial
                    INNER JOIN usuario
                    ON historial.Id_Usuario = usuario.Id_Usuario
                    WHERE (
                        historial.Id_Usuario LIKE '%".$cadena."%' OR
                        usuario.User_Name LIKE '%".$cadena."%' OR
                        historial.Ip_Acceso LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',1) LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',-1) LIKE '%".$cadena."%'
                    )
                ";
            break;
            case '2': //Inicio de Sesion
                $where_sentence.= " FROM historial
                    INNER JOIN usuario
                    ON historial.Id_Usuario = usuario.Id_Usuario
                    WHERE (
                        historial.Id_Usuario LIKE '%".$cadena."%' OR
                        usuario.User_Name LIKE '%".$cadena."%' OR
                        historial.Ip_Acceso LIKE '%".$cadena."%'
                    ) AND historial.Movimiento = 1
                ";
            break;
            case '3': //Crear remisión
                $where_sentence.= " FROM historial
                    INNER JOIN usuario
                    ON historial.Id_Usuario = usuario.Id_Usuario
                    WHERE (
                        historial.Id_Usuario LIKE '%".$cadena."%' OR
                        usuario.User_Name LIKE '%".$cadena."%' OR
                        historial.Ip_Acceso LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',1) LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',-1) LIKE '%".$cadena."%'
                    ) AND historial.Movimiento = 2
                ";
            break;
            case '4': //Editar remisión
                $where_sentence.= " FROM historial
                    INNER JOIN usuario
                    ON historial.Id_Usuario = usuario.Id_Usuario
                    WHERE (
                        historial.Id_Usuario LIKE '%".$cadena."%' OR
                        usuario.User_Name LIKE '%".$cadena."%' OR
                        historial.Ip_Acceso LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',1) LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',-1) LIKE '%".$cadena."%'
                    ) AND historial.Movimiento = 3
                ";
            break;
            case '5': //Validar remisión
                $where_sentence.= " FROM historial
                    INNER JOIN usuario
                    ON historial.Id_Usuario = usuario.Id_Usuario
                    WHERE (
                        historial.Id_Usuario LIKE '%".$cadena."%' OR
                        usuario.User_Name LIKE '%".$cadena."%' OR
                        historial.Ip_Acceso LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',1) LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',-1) LIKE '%".$cadena."%'
                    ) AND historial.Movimiento = 4
                ";
            break;
            case '6': //Ver remisión
                $where_sentence.= " FROM historial
                    INNER JOIN usuario
                    ON historial.Id_Usuario = usuario.Id_Usuario
                    WHERE (
                        historial.Id_Usuario LIKE '%".$cadena."%' OR
                        usuario.User_Name LIKE '%".$cadena."%' OR
                        historial.Ip_Acceso LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',1) LIKE '%".$cadena."%' OR
                        SUBSTRING_INDEX(historial.Descripcion,' ',-1) LIKE '%".$cadena."%'
                    ) AND historial.Movimiento = 5
                ";
            break;
        }

        $where_sentence.= $this->getFechaCondition();
        return $where_sentence;
    }

    public function getFechaCondition()
    {
        $cad_fechas="";
        if(isset($_SESSION['userdata']->rango_inicio_his) && isset($_SESSION['userdata']->rango_fin_his)){
            $rango_inicio = $_SESSION['userdata']->rango_inicio_his;
            $rango_fin = $_SESSION['userdata']->rango_fin_his;

            $cad_fechas = " AND 
                Fecha_Hora >='".$rango_inicio." 00:00:00'
                AND Fecha_Hora <='".$rango_fin." 23:59:59'
            ";
        }

        return $cad_fechas;
    }

    public function getTotalPages($no_of_records_per_page,$where_sentence="")
    {
        $where_sentence = strstr($where_sentence, 'FROM');

        $sql_total_pages = "SELECT COUNT(*) as Num_Pages ".$where_sentence;
        $this->db->query($sql_total_pages);
        $total_rows = $this->db->register()->Num_Pages;
        $total_pages = ceil($total_rows/$no_of_records_per_page);

        $data['total_rows'] = $total_rows;
        $data['total_pages'] = $total_pages;

        return $data;
    }

    public function getDataCurrentPage($offset, $no_of_records_per_page,$where_sentence ="")
    {
        $sql = "
            SELECT * ".$where_sentence."
            LIMIT $offset , $no_of_records_per_page
        ";

        $this->db->query($sql);
        return $this->db->registers();
    }

    public function getHistorialByCadena($cadena,$filtro=1)
    {
        if(!is_numeric($filtro) || !($filtro>=MIN_FILTRO_HIS) || !($filtro<=MAX_FILTRO_HIS)){
            $filtro = 1;
        }

        $from_where_sentence = $this->generateWhereSentence($cadena,$filtro);
        $numPage = 1;
        $no_of_records_per_page = NUM_MAX_REG_PAGE;
        $offset = ($numPage-1)*$no_of_records_per_page;

        $results = $this->getTotalPages($no_of_records_per_page,$from_where_sentence);

        $data['rows_Hisroriales'] = $this->getDataCurrentPage($offset,$no_of_records_per_page,$from_where_sentence);
        $data['numPage'] = $numPage;
        $data['total_pages'] = $results['total_pages'];
        $data['total_rows'] = $results['total_rows'];

        return $data;
    }

    public function getAllInfoHistorialByCadena($from_where_sentence="")
    {
        $sqlAux = "SELECT *"
                    .$from_where_sentence."
                    ";
        
        $this->db->query($sqlAux);
        return $this->db->registers();
    }

    public function insertHistorial($movimiento = null, $descripcion = null){
        if( $movimiento == null || $descripcion == null){
            return false;
        }

        $ip = $this->obtenerIp();
        $sql = "INSERT INTO historial(Id_Usuario,Ip_Acceso,Movimiento,Descripcion) VALUES(".$_SESSION['userdata']->Id_Usuario.",'".$ip."','".$movimiento."','".$descripcion."')";
        $this->db->query($sql);
        
        return $this->db->execute();
    }

    private function obtenerIp()
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

    
}
?>