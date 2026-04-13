<?php
// controllers/DashboardController.php
require_once 'config/database.php';

class DashboardController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getDashboard($studentId) {
        $data = [];

        // Projets
        $stmt = $this->conn->prepare("SELECT * FROM projects WHERE student_id = ?");
        $stmt->execute([$studentId]);
        $data['projects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Comptes rendus
        $stmt = $this->conn->prepare("SELECT * FROM comptes_rendus WHERE student_id = ? ORDER BY created_at DESC");
        $stmt->execute([$studentId]);
        $data['comptesRendus'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Notifications non lues
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM notifications WHERE student_id = ? AND is_read = 0");
        $stmt->execute([$studentId]);
        $data['unreadCount'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Préférences
        $stmt = $this->conn->prepare("SELECT * FROM dashboard_preferences WHERE student_id = ?");
        $stmt->execute([$studentId]);
        $data['preference'] = $stmt->fetch(PDO::FETCH_ASSOC) ?: new stdClass();

        echo json_encode($data);
    }
}