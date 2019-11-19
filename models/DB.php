<?php


namespace models;


use PDO;
use PDOException;

class DB
{
    //use countProducts;

    private static $PDOInstance = null;
    private $connection;

    /**
     * DB constructor.
     */
    private function __construct()
    {
        $this->connection = self::setDb();
    }


    /**
     * @return DB|null
     */
    static public function getInstance()
    {
        if (is_null(self::$PDOInstance)) {
            self::$PDOInstance = new self();
        }
        return
            self::$PDOInstance;
    }

    /**
     * @return bool|PDO|null
     */
    private function setDb()
    {
        try {
            // соединяемся с базой данных
            $connect_str = DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ";charset" . DB_CHARSET;
            self::$PDOInstance = new PDO($connect_str, DB_USER, DB_PASS, DB_OPTIONS);
            // в случае ошибки SQL выражения выведем сообщене об ошибке
            $error_array = self::$PDOInstance->errorInfo();


            if (self::$PDOInstance->errorCode() != 00000) {
                echo 'Error DB in setDB : ' . $error_array[2] . '<br><br>';
                return false;
            }
            return self::$PDOInstance;

        } catch (PDOException $e) {
            echo 'Error DB in Query : ', $e->getMessage(), "\n";
            die("Error (PDO CONNECTION ): " . $e->getMessage());
        }
    }

    protected function toExecute($query, $params = array())
    {
        try {
            $stmt = $this->connection->prepare($query);
            if ($stmt) {
                $stmt->execute($params);//$params
                return $stmt;
            }
        } catch (PDOException $e) {
            echo 'Error DB in Query : ', $e->getMessage(), "\n";
            //die("Error DB in Query : " . $e->getMessage());
        }
        return false;
    }


    /**
     * does prepared require
     * @param $query
     * @param array $params
     * @return array|bool
     */
    public function querySelect($query, $params = array())
    {
        $result = $this->toExecute($query, $params);
        if ($result) return $result->fetchAll();
    }

    /**
     * does prepared require
     * @param $query
     * @param array $params
     * @return bool
     */
    public function querySave($query, $params = array())
    {
        return $this->toExecute($query, $params);
    }


    private function __clone()
    {
    }

    private function __sleep()
    {
    }

    private function __wakeup()
    {
    }

}