<?php
// controllers/CompteRenduController.phppp
require_once 'config/database.php';

class CompteRenduController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    //récupérer les comptes rendus d'un étudiant avec tri
    public function getComptesRendus($studentId) {
        $sortBy = $_GET['sortBy'] ?? 'DATE';

        $orderBy = match(strtoupper($sortBy)) {
            'TITLE' => 'title ASC',
            'DATE'  => 'created_at DESC',
            default => 'created_at DESC'
        };

        $stmt = $this->conn->prepare(
            "SELECT * FROM comptes_rendus WHERE student_id = ? ORDER BY $orderBy"
        );
        $stmt->execute([$studentId]);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}