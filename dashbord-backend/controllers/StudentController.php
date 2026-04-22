<?php
require_once __DIR__ . '/../config/database.php';

class StudentController {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function createStudent() {
        $body = json_decode(file_get_contents("php://input"), true);

        $nom           = $body['nom']            ?? null;
        $prenom        = $body['prenom']         ?? null;
        $email         = $body['email']          ?? null;
        $studentNumber = $body['student_number'] ?? null;
        $department    = $body['department']     ?? null;
        $yearOfStudy   = $body['year_of_study']  ?? null;

        // Vérifier si email existe déjà
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo json_encode(["message" => "Cet email est déjà utilisé."]);
            return;
        }

        // Mot de passe temporaire
        $tempPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
        $passwordHash = password_hash($tempPassword, PASSWORD_BCRYPT);

        // Insérer dans users
        $stmt = $this->conn->prepare(
            "INSERT INTO users (nom, prenom, email, password, role, password_hash, is_active)
             VALUES (?, ?, ?, ?, 'etudiant', ?, 1)"
        );
        $stmt->execute([$nom, $prenom, $email, $passwordHash, $passwordHash]);
        //récupérer l'id du user créé
        $userId = $this->conn->lastInsertId();

        // Insérer dans students
        $stmt = $this->conn->prepare(
            "INSERT INTO students (user_id, first_name, last_name, student_number, department, year_of_study)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $prenom, $nom, $studentNumber, $department, $yearOfStudy]);

        http_response_code(201);
        echo json_encode([
            "message"        => "Compte étudiant créé avec succès.",
            "student_number" => $studentNumber,
            "tempPassword"   => $tempPassword
        ]);
    }
}