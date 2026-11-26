<?php
define('EWASTE_APP', true);

require_once __DIR__ . '/app/Middleware/AuthMiddleware.php';
AuthMiddleware::iniciarSesionSegura();

$usuario     = AuthMiddleware::obtenerUsuarioActual();
$permisos    = $usuario['permisos'];
$csrfToken   = AuthMiddleware::obtenerCsrfToken();
$autenticado = !empty($usuario['id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo htmlspecialchars($csrfToken); ?>">
    <title>E-Waste 360° | Gestión Sustentable de Residuos Electrónicos</title>
    <link rel="stylesheet" href="public/css/styles.css?v=5">
    <?php if ($permisos['ver_reportes']): ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <?php endif; ?>
</head>
<body data-puede-editar="<?php echo $permisos['actualizar_equipos'] ? '1' : '0'; ?>"
      data-ver-reportes="<?php echo $permisos['ver_reportes'] ? '1' : '0'; ?>"
      data-gestionar-usuarios="<?php echo $permisos['gestionar_usuarios'] ? '1' : '0'; ?>">

    <?php require __DIR__ . '/views/layout/header.php'; ?>

    <main class="container">
        <?php
        // Sección pública (Visitante y todos los roles)
        require __DIR__ . '/views/modules/inicio.php';
        require __DIR__ . '/views/modules/campanas.php';

        // Sección privada (cargada únicamente según permisos del rol autenticado)
        if ($permisos['ver_inventario']) {
            require __DIR__ . '/views/modules/inventario.php';
        }
        if ($permisos['ver_reparacion']) {
            require __DIR__ . '/views/modules/reparacion.php';
        }
        if ($permisos['ver_reportes']) {
            require __DIR__ . '/views/modules/indicadores.php';
        }
        if ($permisos['gestionar_usuarios']) {
            require __DIR__ . '/views/modules/usuarios.php';
        }
        ?>
    </main>

    <?php
    if ($permisos['actualizar_equipos']) {
        require __DIR__ . '/views/partials/modal_equipo.php';
    }
    require __DIR__ . '/views/layout/footer.php';
    ?>

    <!-- Scripts divididos por módulo (solo se cargan los módulos autorizados para el rol actual) -->
    <script src="public/js/core.js?v=5"></script>
    <script src="public/js/campanas.js?v=5"></script>
    <?php if ($permisos['ver_inventario']): ?>
        <script src="public/js/inventario.js?v=5"></script>
    <?php endif; ?>
    <?php if ($permisos['ver_reparacion']): ?>
        <script src="public/js/reparacion.js?v=5"></script>
    <?php endif; ?>
    <?php if ($permisos['ver_reportes']): ?>
        <script src="public/js/indicadores.js?v=5"></script>
    <?php endif; ?>
    <?php if ($permisos['gestionar_usuarios']): ?>
        <script src="public/js/usuarios.js?v=5"></script>
    <?php endif; ?>
</body>
</html>
