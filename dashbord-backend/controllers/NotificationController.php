<?php
// controllers/NotificationController.php
require_once 'config/database.php';

class NotificationController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }
    //récupérer les notification d'un étudiant
    public function getNotifications($studentId) {
        $unreadOnly = $_GET['unreadOnly'] ?? 'false';

        $sql    = "SELECT * FROM notifications WHERE student_id = ?";
        $params = [$studentId];

        if ($unreadOnly === 'true') {
            $sql .= " AND is_read = 0";
        }

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    //marquer une notification comme lue
    public function markAsRead($notifId) {
        $stmt = $this->conn->prepare(
            "UPDATE notifications SET is_read = 1 WHERE id = ?"
        );
        $stmt->execute([$notifId]);

        $stmt = $this->conn->prepare("SELECT * FROM notifications WHERE id = ?");
        $stmt->execute([$notifId]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    }
}