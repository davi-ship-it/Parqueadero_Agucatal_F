<?php
class Conexion {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "db_parqueadero_el_aguacate"; 
    private $conect;

    public function __construct() {
        try {
            $connectionString = "mysql:host=" . $this->host . ";dbname=" . $this->db . ";charset=utf8";
            $this->conect = new PDO($connectionString, $this->user, $this->pass);
            $this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $this->conect = "Error de conexion";
            echo "Error: " . $e->getMessage();
        }
    }

    public function conect() {
        return $this->conect;
    }
}
