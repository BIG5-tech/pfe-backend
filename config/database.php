<?php
// config/database.php
class Database {
    private $host     = "localhost";
    private $db_name  = "pfe_manager";
    private $username = "root";
    private $password = "";        // vide par défaut sur XAMPP/MariaDB
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode(["message" => "Erreur connexion : " . $e->getMessage()]);
            exit();
        }
        return $this->conn;
    }
}