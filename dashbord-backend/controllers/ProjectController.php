<?php
// controllers/ProjectController.php
require_once 'config/database.php';

class ProjectController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function filterProjects($studentId) {
        $body   = json_decode(file_get_contents("php://input"), true);
        $status   = $body['status']   ?? null;
        $category = $body['category'] ?? null;

        $sql    = "SELECT * FROM projects WHERE student_id = ?";
        $params = [$studentId];

        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}