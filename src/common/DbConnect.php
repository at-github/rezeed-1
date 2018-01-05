<?php

namespace Common;

use Exception,
    PDO;

class DbConnect
{
    private static $db;
    private $pdo;

    private function __construct(){
        if (!file_exists('env.php'))
            throw new Exception('env.php is missing, create it and fill it with database info');

        require_once 'env.php';

        $dbName = getenv('DBNAME');
        if (!$dbName)
            throw new Exception('DBNAME config is missing in ENV');

        $dbUser = getenv('DBUSER');
        if (!$dbUser)
            throw new Exception('DBUSER config is missing in ENV');

        $dbPass = getenv('DBPASS');
        if (!$dbPass)
            throw new Exception('DBPASS config is missing in ENV');

        $dbHost = getenv('DBHOST');
        if (!$dbHost)
            throw new Exception('DBHOST config is missing in ENV');

        // Let model handle error
        $this->pdo = new PDO(
            "mysql:host=$dbHost;dbname=$dbName",
            $dbUser,
            $dbPass
        );
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    private function __clone(){}

    public static function getInstance(){
        if (null === self::$db){
            self::$db = new self();
        }

        return self::$db;
    }

    public function getPdo()
    {
        return $this->pdo;
    }
}
