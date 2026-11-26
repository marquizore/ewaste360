<?php
ob_start();
ini_set('display_errors', '0');
require_once __DIR__ . '/../app/Controllers/CampanaController.php';

AuthMiddleware::iniciarSesionSegura();
$controller = new CampanaController();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    if ($method === 'GET') {
        $controller->index();
    } elseif ($method === 'POST' && $action === 'participar') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $controller->registrarParticipacion($input);
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $controller->storeCampana($input);
    } else {
        AuthMiddleware::responderJson(['error' => 'Método no permitido.'], 405);
    }
} catch (Throwable $e) {
    AuthMiddleware::responderJson(['error' => 'Error interno al procesar la campaña o participación.'], 500);
}
