<?php
if (!defined('EWASTE_APP')) {
    http_response_code(403);
    exit('Acceso directo no permitido.');
}
?>
        <!-- MÓDULO 2: INVENTARIO DE EQUIPOS ELECTRÓNICOS -->
        <section id="mod-inventario" class="modulo">
            <?php if ($permisos['registrar_equipos']): ?>
            <div class="card">
                <h2>Registrar Dispositivo en Inventario</h2>
                <form id="form-equipo">
                    <div class="form-grid">
                        <div>
                            <label for="codigo_inventario">Código único *</label>
                            <input type="text" id="codigo_inventario" placeholder="Ej. RAEE-2026-006" required>
                        </div>
                        <div>
                            <label for="categoria">Categoría *</label>
                            <select id="categoria">
                                <option value="Computadora">Computadora</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Impresora">Impresora</option>
                                <option value="Periferico">Periférico</option>
                                <option value="Telefonia">Telefonía / Celular</option>
                                <option value="Bateria_Cable">Batería / Cable</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>
                        <div>
                            <label for="marca">Marca *</label>
                            <input type="text" id="marca" placeholder="Ej. Dell / HP / Lenovo" required>
                        </div>
                        <div>
                            <label for="modelo">Modelo *</label>
                            <input type="text" id="modelo" placeholder="Ej. OptiPlex 7050" required>
                        </div>
                        <div>
                            <label for="fecha_ingreso">Fecha de ingreso *</label>
                            <input type="date" id="fecha_ingreso" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div>
                            <label for="estado_clasificacion">Clasificación inicial *</label>
                            <select id="estado_clasificacion">
                                <option value="funcional">Funcional</option>
                                <option value="reparable">Reparable</option>
                                <option value="reutilizable">Reutilizable</option>
                                <option value="gestion_especializada">Gestión Especializada</option>
                            </select>
                        </div>
                        <div>
                            <label for="ubicacion_actual">Ubicación actual</label>
                            <input type="text" id="ubicacion_actual" placeholder="Ej. Taller de Evaluación">
                        </div>
                        <div>
                            <label for="condicion_general">Condición / Observación general *</label>
                            <input type="text" id="condicion_general" placeholder="Describe el estado físico y operativo" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Guardar equipo en base de datos</button>
                    <p id="msg-inventario" class="status-msg"></p>
                </form>
            </div>
            <?php else: ?>
            <div class="card notice-card">
                <strong>AVISO:</strong> Te encuentras en modo de solo lectura. El registro de nuevos equipos está reservado para los roles <code>capturista</code> y <code>administrador</code>.
            </div>
            <?php endif; ?>

            <div class="card" style="margin-top:1.5rem;">
                <div class="section-header-flex">
                    <h2>Inventario Institucional Registrado</h2>
                    <div class="filter-bar">
                        <input type="search" id="buscar-equipo" placeholder="Buscar código, marca, modelo o área...">
                        <select id="filtro-estado">
                            <option value="">Todos los estados</option>
                            <option value="funcional">Funcional</option>
                            <option value="reparable">Reparable</option>
                            <option value="reutilizable">Reutilizable</option>
                            <option value="gestion_especializada">Gestión Especializada</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Categoría</th>
                                <th>Marca / Modelo</th>
                                <th>Ingreso</th>
                                <th>Condición</th>
                                <th>Estado</th>
                                <th>Ubicación</th>
                                <?php if ($permisos['actualizar_equipos']): ?>
                                    <th>Acciones</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="tabla-equipos">
                            <tr><td colspan="8">Cargando inventario...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
