<?php
if (!defined('EWASTE_APP')) {
    http_response_code(403);
    exit('Acceso directo no permitido.');
}
?>
    <header class="navbar">
        <div class="brand-box">
            <h1>♻️ E-Waste 360°</h1>
            <small>Plataforma Integral para la Gestión Sustentable de RAEE</small>
        </div>

        <button type="button" id="menu-toggle" class="menu-toggle" aria-label="Abrir menú">☰ Menú</button>

        <nav class="nav-links" id="nav-links">
            <button class="tab-btn active" data-target="mod-inicio">Concientización</button>
            <button class="tab-btn" data-target="mod-campanas">Campañas</button>
            <?php if ($permisos['ver_inventario']): ?>
                <button class="tab-btn" data-target="mod-inventario">Inventario RAEE</button>
            <?php endif; ?>
            <?php if ($permisos['ver_reparacion']): ?>
                <button class="tab-btn" data-target="mod-reparacion">Reparación y Trazabilidad</button>
            <?php endif; ?>
            <?php if ($permisos['ver_reportes']): ?>
                <button class="tab-btn" data-target="mod-indicadores">Indicadores</button>
            <?php endif; ?>
            <?php if ($permisos['gestionar_usuarios']): ?>
                <button class="tab-btn" data-target="mod-usuarios">Usuarios y Roles</button>
            <?php endif; ?>
        </nav>

        <div class="user-panel">
            <span class="role-badge rol-<?php echo htmlspecialchars($usuario['rol']); ?>" title="<?php echo htmlspecialchars($usuario['nombre']); ?>">
                👤 <?php echo htmlspecialchars($usuario['nombre']); ?>
            </span>
            <?php if ($autenticado): ?>
                <button type="button" id="btn-logout" class="btn-logout">Cerrar Sesión</button>
            <?php else: ?>
                <a href="login.php" class="btn-login-link">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </header>
