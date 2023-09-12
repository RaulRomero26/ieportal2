
<?php

//Clase para conectarse a la base de datos y ejecutar consultas
//PDO
class Base
{

    private $host = DB_HOST;
    private $user = DB_USER;
    private $password = DB_PASSWORD;
    private $databaseName = DB_NAME;

    private $dbh; //Database handler
    private $stmt;
    private $error;

    public function __construct(){

        //Configurar la conexión
        $dsn = 'mysql: host=' . $this->host . ';dbname=' . $this->databaseName;
        $options = array(
            PDO::ATTR_PERSISTENT => true, //Conexión persistente
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        //Crear una instancia de PDO
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->password, $options);
            $this->dbh->exec('set names utf8');
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error. 'Aqui ando: PDO_Exception';
        }
    }

    //Preparamos la consulta
    public function query($sql){
        $this->stmt = $this->dbh->prepare($sql);
    }


    //Vinculamos la consulta con bind
    public function bind($param, $valor, $tipo =  null){
        if (is_null($tipo)) {
            switch (true) {
                case is_int($valor):
                    $tipo = PDO::PARAM_INT;
                    break;

                case is_bool($valor):
                    $tipo = PDO::PARAM_BOOL;
                    break;

                case is_null($valor):
                    $tipo = PDO::PARAM_NULL;
                    break;

                default:
                    $tipo = PDO::PARAM_STR;
                break;
            }
        }

        $this->stmt->bindValue($param, $valor, $tipo);
    }


    //Ejecutamos la consulta
    public function execute(){
        return $this->stmt->execute();
    }

    //Obtener los registros de la consulta
    public function registers(){
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }


    //Obtener registro de la consulta
    public function register(){
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    //Obtener numero de filas o registros
    public function rowCount(){
        return $this->stmt->rowCount();
    }

    public function beginTransaction(){
        $this->dbh->beginTransaction();
    }

    public function commit(){
        $this->dbh->commit();
    }
    public function rollBack(){
        $this->dbh->rollBack();
    }

}
