<?php
if (!defined('EWASTE_APP')) {
    http_response_code(403);
    exit('Acceso directo no permitido.');
}
?>
        <!-- MÓDULO 4: CAMPAÑAS DE RECOLECCIÓN -->
        <section id="mod-campanas" class="modulo">
            <div class="card">
                <h2>Campañas Institucionales de Recolección RAEE</h2>
                <p>Consulta fechas, sedes, equipos aceptados y resultados de acopio en tiempo real.</p>
                <div id="contenedor-campanas" class="grid-kpis" style="margin-top:1rem;"></div>
            </div>

            <div class="grid-two-cols" style="margin-top:1.5rem;">
                <div class="card">
                    <h2>Registrar Participación en Campaña</h2>
                    <form id="form-participacion">
                        <div class="form-grid-single">
                            <div>
                                <label for="part_campana_id">Campaña *</label>
                                <select id="part_campana_id" required>
                                    <option value="">Cargando campañas...</option>
                                </select>
                            </div>
                            <div>
                                <label for="part_nombre">Nombre del participante *</label>
                                <input type="text" id="part_nombre" value="<?php echo $autenticado ? htmlspecialchars($usuario['nombre']) : ''; ?>" placeholder="Tu nombre completo" required>
                            </div>
                            <div>
                                <label for="part_residuo">Equipo o residuo a entregar *</label>
                                <input type="text" id="part_residuo" placeholder="Ej. 2 teclados y 1 cargador de laptop" required>
                            </div>
                            <div>
                                <label for="part_cantidad">Cantidad de unidades *</label>
                                <input type="number" id="part_cantidad" min="1" value="1" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Registrar mi entrega</button>
                        <p id="msg-participacion" class="status-msg"></p>
                    </form>
                </div>

                <div class="card">
                    <h2>Guía para Entregar Dispositivos de Forma Segura</h2>
                    <ul class="eco-list">
                        <li><strong>Respaldo y borrado:</strong> Elimina cuentas vinculadas y formatea discos o teléfonos antes de entregarlos.</li>
                        <li><strong>Baterías seguras:</strong> Si una batería está inflada o dañada, colócala por separado en bolsa sellada y cubre sus polos con cinta aislante.</li>
                        <li><strong>No desensamblar monitores:</strong> Entrega pantallas intactas para evitar liberación de polvos o gases tóxicos.</li>
                        <li><strong>Cables y cargadores:</strong> Enróllalos y agrupa los accesorios junto con su dispositivo si aún funcionan.</li>
                    </ul>
                </div>
            </div>

            <?php if ($permisos['gestionar_campanas']): ?>
            <div class="card" style="margin-top:1.5rem;">
                <h2>Publicar Nueva Campaña de Recolección</h2>
                <form id="form-campana">
                    <div class="form-grid">
                        <div>
                            <label for="camp_nombre">Nombre de la campaña *</label>
                            <input type="text" id="camp_nombre" placeholder="Ej. Reciclatón Primavera 2027" required>
                        </div>
                        <div>
                            <label for="camp_inicio">Fecha inicio *</label>
                            <input type="date" id="camp_inicio" required>
                        </div>
                        <div>
                            <label for="camp_fin">Fecha fin *</label>
                            <input type="date" id="camp_fin" required>
                        </div>
                        <div>
                            <label for="camp_ubicacion">Ubicación / Sede *</label>
                            <input type="text" id="camp_ubicacion" placeholder="Ej. Edificio de Sistemas" required>
                        </div>
                        <div>
                            <label for="camp_equipos">Equipos aceptados *</label>
                            <input type="text" id="camp_equipos" placeholder="Laptops, monitores, cables, baterías..." required>
                        </div>
                        <div>
                            <label for="camp_estado">Estado</label>
                            <select id="camp_estado">
                                <option value="activa">Activa</option>
                                <option value="programada">Programada</option>
                                <option value="finalizada">Finalizada</option>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top:1rem;">
                        <label for="camp_descripcion">Descripción de la campaña *</label>
                        <textarea id="camp_descripcion" rows="2" placeholder="Objetivo y horario de recepción" required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Publicar campaña</button>
                    <p id="msg-campana" class="status-msg"></p>
                </form>
            </div>
            <?php endif; ?>

            <div class="card" style="margin-top:1.5rem;">
                <h2>Participaciones Recientes Registradas</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Campaña</th>
                                <th>Participante</th>
                                <th>Residuos Entregados</th>
                                <th>Unidades</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-participaciones">
                            <tr><td colspan="5">Cargando participaciones...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
