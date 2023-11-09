<?php

class Database {
    private $host;
    private $db;
    private $charset;
    private $username;
    private $password;

    public function __construct($host, $db, $charset, $username, $password) {
        $this->host = $host;
        $this->db = $db;
        $this->charset = $charset;
        $this->username = $username;
        $this->password = $password;
    }

    public function connect() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        return new PDO($dsn, $this->username, $this->password);
    }
}

$db = new Database('localhost', 'eazy-pick', 'utf8mb4', 'root', '');
$pdo = $db->connect(); 
?>