<?php
if (!defined('EWASTE_APP')) {
    http_response_code(403);
    exit('Acceso directo no permitido.');
}
?>
        <!-- MÓDULO 1: PÁGINA DE INICIO Y CONCIENTIZACIÓN (Sección Pública) -->
        <section id="mod-inicio" class="modulo activo">
            <article class="card hero-card">
                <div class="hero-text">
                    <span class="pill-tag">Programación Web + Desarrollo Sustentable</span>
                    <h2>Economía Circular y Gestión Responsable de Residuos Electrónicos (RAEE)</h2>
                    <p>
                        Los aparatos eléctricos y electrónicos fuera de uso contienen materiales valiosos recuperables y sustancias
                        peligrosas como <strong>plomo, mercurio, cadmio y retardantes de llama</strong>. E-Waste 360° centraliza el inventario
                        institucional para priorizar la reparación y reutilización antes de la disposición final con gestores verificados.
                    </p>
                    <div class="hero-actions">
                        <button type="button" class="btn-primary" onclick="irAModulo('mod-campanas')">Ver Campañas Activas</button>
                        <?php if ($permisos['ver_inventario']): ?>
                            <button type="button" class="btn-secondary" onclick="irAModulo('mod-inventario')">Consultar Inventario</button>
                        <?php elseif (!$autenticado): ?>
                            <a href="login.php" class="btn-secondary" style="text-decoration:none;display:inline-flex;align-items:center;">Acceso Personal Autorizado</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hero-media">
                    <img src="public/img/hero-ewaste.svg" alt="Ilustración de economía circular para residuos electrónicos">
                </div>
            </article>

            <div class="grid-kpis" style="margin-top:1.5rem;">
                <article class="card feature-card">
                    <img src="public/img/icon-reparar.svg" alt="Icono reparar" width="52" height="52">
                    <h3>1. Reparar y Extender Vida Útil</h3>
                    <p>Diagnosticamos equipos lentos o con fallas menores (cambio a SSD, limpieza térmica, reemplazo de fuente) para evitar compras innecesarias.</p>
                </article>
                <article class="card feature-card">
                    <img src="public/img/icon-reutilizar.svg" alt="Icono reutilizar" width="52" height="52">
                    <h3>2. Reutilizar y Reasignar</h3>
                    <p>Reasignamos computadoras, monitores y periféricos funcionales a laboratorios académicos y áreas administrativas con ahorro documentado.</p>
                </article>
                <article class="card feature-card">
                    <img src="public/img/icon-reciclar.svg" alt="Icono disposición segura" width="52" height="52">
                    <h3>3. Entrega Responsable Verificada</h3>
                    <p>Solo canalizamos a gestión especializada los equipos sin viabilidad técnica, registrando manifiesto y verificación del gestor ambiental.</p>
                </article>
            </div>

            <div class="grid-two-cols">
                <article class="card">
                    <h2>Impactos Ambientales y Sociales de los RAEE</h2>
                    <ul class="eco-list">
                        <li><strong>Contaminación de suelos y agua:</strong> Una sola batería o tarjeta lógica desechada en basura común puede liberar metales pesados al subsuelo.</li>
                        <li><strong>Huella de carbono:</strong> Fabricar una laptop nueva genera cientos de kilogramos de CO₂; prolongar su uso 3 años reduce drásticamente el impacto.</li>
                        <li><strong>Brecha digital:</strong> Reacondicionar equipos escolares permite equipar más estaciones de trabajo para estudiantes.</li>
                    </ul>
                </article>
                <article class="card">
                    <h2>Recomendaciones para Prolongar la Vida Útil y Recursos</h2>
                    <ul class="eco-list">
                        <li>Realizar limpieza interna preventiva y cambio de pasta térmica cada 12 meses.</li>
                        <li>Usar reguladores de voltaje para proteger fuentes y tarjetas madre de variaciones eléctricas.</li>
                        <li>Antes de donar o entregar un equipo, respaldar archivos y realizar borrado seguro de datos.</li>
                    </ul>
                    <div class="resource-links">
                        <a href="https://www.gob.mx/semarnat" target="_blank" rel="noopener noreferrer">Guías SEMARNAT sobre RAEE ↗</a>
                        <a href="https://ewastemonitor.info/" target="_blank" rel="noopener noreferrer">Global E-Waste Monitor (ONU) ↗</a>
                    </div>
                </article>
            </div>
        </section>
