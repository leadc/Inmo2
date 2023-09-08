<?php
/*Clase no instanciable, genera una conexión a una base de datos Oracle para hacer consultas
 * Para Conectarse se debe configurar una conexión ODBC en el sistema con el nombre del $dsn
 *Se pueden setear sus atributos estáticos para seleccionar a qué base de datos conectarse
 USANDO PDO
 */
class ConecxionPDO
{

    
    
    public static $dsn = 'odbc:Inmo2'; // Nombre del DSN ODBC
    public static $usuario = 'sa'; // Usuario si es necesario
    public static $clave = 'SQL2005Express'; // Contraseña si es necesario


    // public static $dsn = 'mysql:Server=10.100.10.53;dbname=web_uruguay;charset=utf8'; //Para mySQL
    // public static $usuario = "root"; //Administrador para el srv local - web para srv progreso
    // public static $clave = '';
    private static $ObjetoAccesoDatos;

    /**Ejecuta una consulta en la base de datos conectada y devuelve el resultado como un array asociativo
     * 
     */
    public static function GetResultados($sql, $abrirConexion = true)
    {
        if ($abrirConexion) {
            self::AbrirConexion();
        }
        $consulta = self::$ObjetoAccesoDatos->prepare($sql);
        $consulta->execute();
        return $consulta->fetchall();
    }

    /**Devuelve un recurso para ejecutar una consulta en una base de datos
     * 
     */
    public static function RetornarConsulta($sql, $abrirConexion = true)
    {
        if ($abrirConexion) {
            self::AbrirConexion();
        }
        return self::$ObjetoAccesoDatos->prepare($sql);
    }

    /** Ejecuta una consulta en la base de datos y devuelve el nro de filas afectadas
     * 
     */
    public static function ExecNonQuery($sql, $abrirConexion = true)
    {
        if ($abrirConexion) {
            self::AbrirConexion();
        }
        return self::$ObjetoAccesoDatos->exec($sql);
    }

    /**Devuelve el último ID insertado
     * 
     */
    public static function RetornarUltimoIdInsertado()
    {
        try {
            return self::$ObjetoAccesoDatos->lastInsertId();
        } catch (PDOException $e) {
            echo "Error al obtener el último ID insertado: " . $e->getMessage();
            return false;
        }
    }
    /**Establece una conexión según los parámetros de dsn, usuario y clave
     * 
     */
    public static function AbrirConexion()
    {
        self::$ObjetoAccesoDatos = new PDO(self::$dsn, self::$usuario, self::$clave);
    }

    /**Evita que el objeto se pueda clonar
     * 
     */
    public function __clone()
    {
        trigger_error('La clonación de este objeto no está permitida', E_USER_ERROR);
    }

    public static function CerrarConexion()
    {
        self::$ObjetoAccesoDatos = null;
    }


    public static function ArrayToObject($array)
    {
        $object = new stdClass();
        foreach ($array as $clave => $valor) {
            if (!is_numeric($clave)) {
                $object->$clave = $valor;
            }
        }
        return $object;
    }

    /** ResultToObjectArray
     * 
     */
    public static function ResultToObjectArray($array)
    {
        $obj_array = [];
        for ($i = 0; $i < count($array); $i++) {
            $object = new stdClass();
            foreach ($array[$i] as $clave => $valor) {
                if (!is_numeric($clave)) {
                    $object->$clave = $valor;
                }
            }
            array_push($obj_array, $object);
        }
        return $obj_array;
    }
}
