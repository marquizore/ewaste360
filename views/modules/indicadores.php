<?php
if (!defined('EWASTE_APP')) {
    http_response_code(403);
    exit('Acceso directo no permitido.');
}
?>
        <!-- MÓDULO 5: INDICADORES Y REPORTES (Resp. Ambiental y Admin) -->
        <section id="mod-indicadores" class="modulo">
            <div class="section-header-flex" style="margin-bottom:1rem;">
                <h2>Panel Ejecutivo de Indicadores Sustentables</h2>
                <button type="button" class="btn-primary" style="margin-top:0;" onclick="window.print()">Imprimir / Exportar Reporte PDF</button>
            </div>

            <div class="grid-kpis">
                <div class="card">
                    <h3>Equipos registrados</h3>
                    <div class="valor" id="kpi-total-equipos">0</div>
                </div>
                <div class="card">
                    <h3>Reparados / Reutilizados</h3>
                    <div class="valor" id="kpi-reutilizados">0</div>
                </div>
                <div class="card">
                    <h3>Entregados a gestor verificado</h3>
                    <div class="valor" id="kpi-gestor">0</div>
                </div>
                <div class="card">
                    <h3>Participaciones en campañas</h3>
                    <div class="valor" id="kpi-participaciones">0</div>
                </div>
                <div class="card">
                    <h3>Inversión en reparación</h3>
                    <div class="valor" id="kpi-costo">$0.00</div>
                </div>
                <div class="card">
                    <h3>Ahorro institucional estimado</h3>
                    <div class="valor" id="kpi-ahorro">$0.00</div>
                </div>
            </div>

            <div class="grid-two-cols">
                <div class="card">
                    <h2>Distribución de Equipos por Clasificación</h2>
                    <div class="chart-box">
                        <canvas id="chart-estados"></canvas>
                    </div>
                </div>
                <div class="card">
                    <h2>Impacto Económico y Acopio por Campaña</h2>
                    <div class="chart-box">
                        <canvas id="chart-campanas"></canvas>
                    </div>
                </div>
            </div>
        </section>
