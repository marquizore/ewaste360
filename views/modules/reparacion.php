<?php
if (!defined('EWASTE_APP')) {
    http_response_code(403);
    exit('Acceso directo no permitido.');
}
?>
        <!-- MÓDULO 3: REPARACIÓN, REUTILIZACIÓN Y TRAZABILIDAD -->
        <section id="mod-reparacion" class="modulo">
            <?php if ($permisos['registrar_diagnostico']): ?>
            <div class="grid-two-cols">
                <div class="card">
                    <h2>Registrar Diagnóstico y Dictamen (Patrón Strategy)</h2>
                    <form id="form-diagnostico">
                        <div class="form-grid-single">
                            <div>
                                <label for="diag_equipo_id">Seleccionar equipo *</label>
                                <select id="diag_equipo_id" required>
                                    <option value="">Cargando equipos...</option>
                                </select>
                            </div>
                            <div>
                                <label for="diag_falla">Falla detectada *</label>
                                <input type="text" id="diag_falla" placeholder="Ej. Disco duro dañado / sin encendido" required>
                            </div>
                            <div>
                                <label for="diag_accion">Acción de reparación o mantenimiento *</label>
                                <input type="text" id="diag_accion" placeholder="Ej. Cambio a SSD 480GB y limpieza térmica" required>
                            </div>
                            <div class="form-row-2">
                                <div>
                                    <label for="diag_costo">Costo de reparación ($ MXN)</label>
                                    <input type="number" step="0.01" min="0" id="diag_costo" value="350.00" required>
                                </div>
                                <div>
                                    <label for="diag_valor_repo">Valor reposición ($ MXN)</label>
                                    <input type="number" step="0.01" min="0" id="diag_valor_repo" value="5500.00" required>
                                </div>
                            </div>
                            <div>
                                <label for="diag_decision">Decisión sustentable *</label>
                                <select id="diag_decision">
                                    <option value="reutilizar">Reparar y Reutilizar</option>
                                    <option value="conservar">Conservar / Reasignación Directa</option>
                                    <option value="gestion_especializada">Enviar a Gestión Especializada</option>
                                </select>
                            </div>
                            <div>
                                <label for="diag_area">Área / Laboratorio o Gestor destino *</label>
                                <input type="text" id="diag_area" placeholder="Ej. Laboratorio de Redes" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Registrar dictamen y calcular ahorro</button>
                        <p id="msg-diagnostico" class="status-msg"></p>
                    </form>
                </div>

                <div class="card">
                    <h2>Registrar Movimiento y Trazabilidad de Destino</h2>
                    <form id="form-movimiento">
                        <div class="form-grid-single">
                            <div>
                                <label for="mov_equipo_id">Seleccionar equipo *</label>
                                <select id="mov_equipo_id" required>
                                    <option value="">Cargando equipos...</option>
                                </select>
                            </div>
                            <div>
                                <label for="mov_tipo">Tipo de movimiento *</label>
                                <select id="mov_tipo">
                                    <option value="reasignacion">Reasignación a Laboratorio / Departamento</option>
                                    <option value="reparacion">Envío a Taller de Reparación</option>
                                    <option value="entrega_gestor">Entrega a Gestor Ambiental Autorizado</option>
                                    <option value="diagnostico">Revisión Técnica / Diagnóstico</option>
                                </select>
                            </div>
                            <div>
                                <label for="mov_destino">Destino documentado (Laboratorio o Gestor + Folio) *</label>
                                <input type="text" id="mov_destino" placeholder="Ej. Recicladora del Bajío (Manifiesto #MAN-2026-99)" required>
                            </div>
                            <div class="checkbox-row">
                                <input type="checkbox" id="mov_verificado">
                                <label for="mov_verificado">Destino / Gestor ambiental verificado con soporte documental</label>
                            </div>
                            <div>
                                <label for="mov_obs">Observaciones de trazabilidad</label>
                                <textarea id="mov_obs" rows="3" placeholder="Detalles de entrega, responsable receptor o piezas recuperadas"></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Documentar movimiento</button>
                        <p id="msg-movimiento" class="status-msg"></p>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="card notice-card">
                <strong>AVISO:</strong> Este rol no cuenta con permisos para autorizar diagnósticos o movimientos de trazabilidad. Solo pueden hacerlo los roles <code>responsable_ambiental</code> y <code>administrador</code>.
            </div>
            <?php endif; ?>

            <div class="card" style="margin-top:1.5rem;">
                <h2>Historial de Diagnósticos, Reparaciones y Ahorro</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Equipo</th>
                                <th>Falla y Acción Realizada</th>
                                <th>Costo Rep.</th>
                                <th>Ahorro Est.</th>
                                <th>Decisión</th>
                                <th>Destino / Área</th>
                                <th>Responsable</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-diagnosticos">
                            <tr><td colspan="7">Cargando diagnósticos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card" style="margin-top:1.5rem;">
                <h2>Bitácora Auditable de Movimientos y Destino Final</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Equipo</th>
                                <th>Movimiento</th>
                                <th>Destino Documentado</th>
                                <th>Verificación</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-movimientos">
                            <tr><td colspan="6">Cargando bitácora de movimientos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
