<?php
ob_start();
ini_set('display_errors', '0');
require_once __DIR__ . '/../app/Controllers/AuthController.php';

AuthMiddleware::iniciarSesionSegura();
$controller = new AuthController();
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'status';

try {
    if ($method === 'GET' && $action === 'status') {
        $controller->status();
    } elseif ($method === 'POST' && $action === 'login') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $controller->login($input);
    } elseif ($method === 'POST' && $action === 'logout') {
        $controller->logout();
    } else {
        AuthMiddleware::responderJson(['error' => 'Acción de autenticación no válida.'], 400);
    }
} catch (Throwable $e) {
    AuthMiddleware::responderJson(['error' => 'Error interno en el servicio de autenticación.'], 500);
}
