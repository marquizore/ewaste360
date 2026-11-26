<?php
ob_start();
ini_set('display_errors', '0');
require_once __DIR__ . '/../app/Controllers/DiagnosticoController.php';

AuthMiddleware::iniciarSesionSegura();
$controller = new DiagnosticoController();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $controller->index();
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $controller->store($input);
    } else {
        AuthMiddleware::responderJson(['error' => 'Método no permitido.'], 405);
    }
} catch (Throwable $e) {
    AuthMiddleware::responderJson(['error' => 'Error interno al procesar el diagnóstico técnico.'], 500);
}
