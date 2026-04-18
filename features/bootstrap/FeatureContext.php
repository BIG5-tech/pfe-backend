<?php
use Behat\Behat\Context\Context;
 
class FeatureContext implements Context
{
    private string $baseUrl        = 'http://localhost/backend/dashbord-backend/index.php';
    private ?PDO   $db             = null;
    private array  $requestBody    = [];
    private array  $lastResponse   = [];
    private int    $lastStatusCode = 0;
 
    public function __construct()
    {
        try {
            $this->db = new PDO(
                "mysql:host=localhost;dbname=pfe_manager;charset=utf8mb4",
                "root", ""
            );
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            $this->db = null;
        }
    }
 
    // ==========================================================
    // GIVEN
    // ==========================================================
 
    /** @Given je suis connecte en tant que coordonnateur */
    public function connecteCoordonnateur(): void
    {
        $this->requestBody = [];
    }
 
    /** @Given je suis sur la page :page */
    public function surLaPage(string $page): void {}
 
    /** @Given un etudiant avec l'email :email existe deja */
    public function etudiantEmailExisteDeja(string $email): void
    {
        $this->requestBody = [
            'nom'            => 'Existant',
            'prenom'         => 'User',
            'email'          => $email,
            'student_number' => 'ETU-EXIST-' . rand(100, 999),
            'department'     => 'Info',
            'year_of_study'  => '1'
        ];
        $this->appelAPI('POST', '/api/students');
        $this->requestBody = [];
    }
 
    /** @Given un etudiant avec le matricule :matricule existe deja */
    public function etudiantMatriculeExisteDeja(string $matricule): void
    {
        $this->requestBody = [
            'nom'            => 'Existant',
            'prenom'         => 'User',
            'email'          => 'exist.' . rand(100,999) . '@etudiant.tn',
            'student_number' => $matricule,
            'department'     => 'Info',
            'year_of_study'  => '1'
        ];
        $this->appelAPI('POST', '/api/students');
        $this->requestBody = [];
    }
 
    // ==========================================================
    // WHEN
    // ==========================================================
 
    /** @When je remplis le champ :champ avec :valeur */
    public function remplirChamp(string $champ, string $valeur): void
    {
        $mapping = [
            'Nom'         => 'nom',
            'Prenom'      => 'prenom',
            'Email'       => 'email',
            'Matricule'   => 'student_number',
            'Departement' => 'department',
            'Annee'       => 'year_of_study',
        ];
        $this->requestBody[$mapping[$champ] ?? strtolower($champ)] = $valeur;
    }
 
    /** @When je laisse le champ :champ vide */
    public function laisserVide(string $champ): void
    {
        $this->remplirChamp($champ, '');
    }
 
    /** @When je clique sur le bouton :bouton */
    public function cliqueBouton(string $bouton): void
    {
        if ($bouton === 'Enregistrer') {
            $this->appelAPI('POST', '/api/students');
        }
    }
 
    // ==========================================================
    // THEN
    // ==========================================================
 
    /** @Then je dois voir le message :message */
    public function voirMessage(string $message): void
    {
        $got = $this->lastResponse['message'] ?? '';
        if (strpos($got, $message) === false) {
            throw new \Exception(
                "Message attendu : \"$message\"\n" .
                "Message obtenu  : \"$got\"\n" .
                "HTTP            : {$this->lastStatusCode}\n" .
                "Reponse         : " . json_encode($this->lastResponse, JSON_UNESCAPED_UNICODE)
            );
        }
    }
 
    /** @Then je dois voir le message d'erreur :message */
    public function voirMessageErreur(string $message): void
    {
        $this->voirMessage($message);
    }
 
    /** @Then le code HTTP doit etre :code */
    public function verifierCodeHTTP(int $code): void
    {
        if ($this->lastStatusCode !== $code) {
            throw new \Exception(
                "HTTP attendu : $code\n" .
                "HTTP obtenu  : {$this->lastStatusCode}\n" .
                "Reponse      : " . json_encode($this->lastResponse, JSON_UNESCAPED_UNICODE)
            );
        }
    }
 
    /** @Then l'etudiant :email doit exister dans le systeme */
    public function etudiantExisteDansSysteme(string $email): void
    {
        if ($this->lastStatusCode !== 201) {
            throw new \Exception(
                "Etudiant non cree. HTTP: {$this->lastStatusCode}\n" .
                "Reponse: " . json_encode($this->lastResponse, JSON_UNESCAPED_UNICODE)
            );
        }
        if ($this->db) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if (!$stmt->fetch()) {
                throw new \Exception("$email introuvable dans la base pfe_manager !");
            }
        }
    }
 
    /** @Then aucun nouveau compte ne doit etre cree */
    public function aucunCompteCreee(): void
    {
        if ($this->lastStatusCode === 201) {
            throw new \Exception(
                "Un compte a ete cree alors qu'il ne devrait pas !\n" .
                "HTTP: {$this->lastStatusCode}"
            );
        }
    }
 
    /** @Then la reponse doit contenir un mot de passe temporaire */
    public function reponseContientMotDePasseTemporaire(): void
    {
        if (empty($this->lastResponse['tempPassword'])) {
            throw new \Exception(
                "Le mot de passe temporaire est absent de la reponse !\n" .
                "Reponse: " . json_encode($this->lastResponse, JSON_UNESCAPED_UNICODE)
            );
        }
        echo "\n→ Mot de passe temporaire genere : " . $this->lastResponse['tempPassword'] . "\n";
    }
 
    /** @Then la reponse doit contenir le matricule :matricule */
    public function reponseContientMatricule(string $matricule): void
    {
        $got = $this->lastResponse['student_number'] ?? '';
        if ($got !== $matricule) {
            throw new \Exception(
                "Matricule attendu : \"$matricule\"\n" .
                "Matricule obtenu  : \"$got\"\n" .
                "Reponse           : " . json_encode($this->lastResponse, JSON_UNESCAPED_UNICODE)
            );
        }
    }
 
    // ==========================================================
    // METHODE PRIVEE — Appel HTTP reel vers ton API PHP
    // ==========================================================
 
    private function appelAPI(string $methode, string $endpoint): void
    {
        $url = $this->baseUrl . $endpoint;
 
        echo "\n→ APPEL : $methode $url\n";
        echo "→ BODY  : " . json_encode($this->requestBody, JSON_UNESCAPED_UNICODE) . "\n";
 
        $ctx = stream_context_create([
            'http' => [
                'method'        => $methode,
                'header'        => "Content-Type: application/json\r\nAccept: application/json\r\n",
                'content'       => json_encode($this->requestBody),
                'ignore_errors' => true,
            ]
        ]);
 
        $response = @file_get_contents($url, false, $ctx);
 
        $this->lastStatusCode = 500;
        foreach ($http_response_header ?? [] as $h) {
            if (preg_match('/HTTP\/[\d.]+ (\d+)/', $h, $m)) {
                $this->lastStatusCode = (int) $m[1];
                break;
            }
        }
 
        $this->lastResponse = json_decode($response ?: '{}', true) ?? [];
 
        echo "→ HTTP  : {$this->lastStatusCode}\n";
        echo "→ RESP  : " . json_encode($this->lastResponse, JSON_UNESCAPED_UNICODE) . "\n";
    }
}