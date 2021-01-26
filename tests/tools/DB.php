<?php namespace tools;


use \PDO;

class DB {
    private ?PDO $pdo;
    
    public function __construct() {
        $dbtype = \Configuration::get("dbtype");
        $dbhost = \Configuration::get("dbhost");
        $dbuser = \Configuration::get("dbuser");
        $dbpassword = \Configuration::get("dbpassword");

        $this->pdo = new \PDO("{$dbtype}:host={$dbhost};charset=utf8", $dbuser, $dbpassword);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function get_pdo() {
        return $this->pdo;
    }
    
    public function init() {
        $dbname = \Configuration::get("dbname");
        $sql = file_get_contents("tests/database/tests.sql");
        $query = $this->pdo->prepare($sql);
        $query->execute();

        $sql = file_get_contents("tests/database/tests_dump.sql");
        $query = $this->pdo->prepare($sql);
        $query->execute();
    }

    public function execute($sql, $params=null) {
        $stmt =$this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
}