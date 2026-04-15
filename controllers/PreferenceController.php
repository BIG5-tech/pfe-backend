<?php
// controllers/PreferenceController.php
require_once 'config/database.php';

class PreferenceController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function savePreference($studentId) {
        $body = json_decode(file_get_contents("php://input"), true);

        // Vérifier si une préférence existe déjà
        $stmt = $this->conn->prepare(
            "SELECT id FROM dashboard_preferences WHERE student_id = ?"
        );
        $stmt->execute([$studentId]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            // Mise à jour
            $stmt = $this->conn->prepare(
                "UPDATE dashboard_preferences
                 SET project_filter = ?, cr_sort_field = ?,
                     show_notifications = ?, layout_mode = ?
                 WHERE student_id = ?"
            );
            $stmt->execute([
                $body['projectFilter'],
                $body['crSortField'],
                $body['showNotifications'] ? 1 : 0,
                $body['layoutMode'],
                $studentId
            ]);
        } else {
            // Insertion
            $stmt = $this->conn->prepare(
                "INSERT INTO dashboard_preferences
                 (student_id, project_filter, cr_sort_field, show_notifications, layout_mode)
                 VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $studentId,
                $body['projectFilter'],
                $body['crSortField'],
                $body['showNotifications'] ? 1 : 0,
                $body['layoutMode']
            ]);
        }

        $stmt = $this->conn->prepare(
            "SELECT * FROM dashboard_preferences WHERE student_id = ?"
        );
        $stmt->execute([$studentId]);
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    }
}
