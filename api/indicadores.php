<?php
ob_start();
ini_set('display_errors', '0');
require_once __DIR__ . '/../app/Controllers/IndicadorController.php';

AuthMiddleware::iniciarSesionSegura();
$controller = new IndicadorController();

try {
    $controller->resumen($_GET['tipo'] ?? null);
} catch (Throwable $e) {
    AuthMiddleware::responderJson(['error' => 'No se pudieron calcular los indicadores ambientales.'], 500);
}
