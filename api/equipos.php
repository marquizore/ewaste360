<?php
ob_start();
ini_set('display_errors', '0');
require_once __DIR__ . '/../app/Controllers/EquipoController.php';

AuthMiddleware::iniciarSesionSegura();
$controller = new EquipoController();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $controller->show((int)$_GET['id']);
        } else {
            $controller->index($_GET['estado'] ?? null, $_GET['q'] ?? null);
        }
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $controller->store($input);
    } elseif ($method === 'PATCH') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $controller->update((int)($_GET['id'] ?? 0), $input);
    } else {
        AuthMiddleware::responderJson(['error' => 'Método no permitido.'], 405);
    }
} catch (Throwable $e) {
    AuthMiddleware::responderJson(['error' => 'Error al procesar el equipo (verifica que el código no esté repetido).'], 500);
}
