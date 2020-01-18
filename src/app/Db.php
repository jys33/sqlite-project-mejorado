<?php

class Db {

    private $conn = null;
    private static $_instance = null;

    private function __construct() {
        try {
            // $config = Config::getConfig('mysql');
            $dsn = 'sqlite:C:\Users\neo\Desktop\app\db\db.db'; // ruta absoluta
            // $dsn = 'mysql:host=localhost;dbname=db;charset=utf8mb4';
            $this->conn = new PDO($dsn, '', '', array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false
            ));
        } catch (PDOException $e) {
            trigger_error('Error:' . $e->getMessage(), E_USER_ERROR);
            exit;
        }
    }

    // Devolvemos la conexión
    private function getConnection() {
        return $this->conn;
    }

    // Magic method clone is empty to prevent duplication of connection
    private function __clone() {}
    
    // close db connection
    public function __destruct() {
        $this->conn = null;
    }

    // Instanciamos la clase
    public static function getInstance() {
        //si no esta inicializada y no es distinta de NULL
        if(!isset(self::$_instance)) {
            self::$_instance = new Db();
        }
        // Despues de instanciar la clase, llamamos al método que devuelve la conexión.
        return self::$_instance->getConnection();
    }

    /**
     * func_get_args ( void ) : array, Obtiene un array de la lista de argumentos de una función.
     * array_slice — Extraer una parte de un array
     */
    public static function query(/* $sql [, ... ] */)
    {
        // SQL statement
        $sql = func_get_arg(0);

        // parameters, if any
        $parameters = array_slice(func_get_args(), 1);

        $stmt = self::getInstance()->prepare($sql);

        if ($stmt === false) return false;

        $result = $stmt->execute($parameters);

        if($result === false) return false;

        if ($stmt->columnCount() > 0) {
		    // return result set's rows
		    return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		// if query was DELETE, INSERT, or UPDATE
		else {
		    // return number of rows affected
		    return ($stmt->rowCount() == 1); // true o false
		}
    }
}