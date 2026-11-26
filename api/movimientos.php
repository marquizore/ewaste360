<?php
ob_start();
ini_set('display_errors', '0');
require_once __DIR__ . '/../app/Controllers/MovimientoController.php';

AuthMiddleware::iniciarSesionSegura();
$controller = new MovimientoController();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $equipoId = isset($_GET['equipo_id']) ? (int)$_GET['equipo_id'] : null;
        $controller->index($equipoId);
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $controller->store($input);
    } else {
        AuthMiddleware::responderJson(['error' => 'Método no permitido.'], 405);
    }
} catch (Throwable $e) {
    AuthMiddleware::responderJson(['error' => 'Error interno al registrar el movimiento de trazabilidad.'], 500);
}
