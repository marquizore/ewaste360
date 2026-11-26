<?php
ob_start();
ini_set('display_errors', '0');
require_once __DIR__ . '/../app/Controllers/UsuarioController.php';

AuthMiddleware::iniciarSesionSegura();
$controller = new UsuarioController();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $controller->index();
    } elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $controller->store($input);
    } elseif ($method === 'PATCH') {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $controller->update($input);
    } else {
        AuthMiddleware::responderJson(['error' => 'Método no permitido.'], 405);
    }
} catch (Throwable $e) {
    AuthMiddleware::responderJson(['error' => 'Error al gestionar usuarios (verifica que el username/correo no esté duplicado).'], 500);
}
