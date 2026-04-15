<?php
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (preg_match('/\/api\/dashboard\/(\d+)$/', $uri, $m) && $method === 'GET') {
    require 'controllers/DashboardController.php';
    $ctrl = new DashboardController();
    $ctrl->getDashboard($m[1]);

} elseif (preg_match('/\/api\/projects\/filter\/(\d+)$/', $uri, $m) && $method === 'POST') {
    require 'controllers/ProjectController.php';
    $ctrl = new ProjectController();
    $ctrl->filterProjects($m[1]);

} elseif (preg_match('/\/api\/comptes-rendus\/(\d+)$/', $uri, $m) && $method === 'GET') {
    require 'controllers/CompteRenduController.php';
    $ctrl = new CompteRenduController();
    $ctrl->getComptesRendus($m[1]);

} elseif (preg_match('/\/api\/notifications\/(\d+)$/', $uri, $m) && $method === 'GET') {
    require 'controllers/NotificationController.php';
    $ctrl = new NotificationController();
    $ctrl->getNotifications($m[1]);

} elseif (preg_match('/\/api\/notifications\/(\d+)\/read$/', $uri, $m) && $method === 'PATCH') {
    require 'controllers/NotificationController.php';
    $ctrl = new NotificationController();
    $ctrl->markAsRead($m[1]);

} elseif (preg_match('/\/api\/preferences\/(\d+)$/', $uri, $m) && $method === 'PUT') {
    require 'controllers/PreferenceController.php';
    $ctrl = new PreferenceController();
    $ctrl->savePreference($m[1]);

} elseif (preg_match('/\/api\/students$/', $uri) && $method === 'POST') {
    require 'controllers/StudentController.php';
    $ctrl = new StudentController();
    $ctrl->createStudent();

} else {
    http_response_code(404);
    echo json_encode(["message" => "Route introuvable"]);
}