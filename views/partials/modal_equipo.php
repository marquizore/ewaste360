<?php
if (!defined('EWASTE_APP')) {
    http_response_code(403);
    exit('Acceso directo no permitido.');
}
?>
    <!-- MODAL PARA ACTUALIZAR ESTADO Y UBICACIÓN DE EQUIPO (PATCH /api/equipos.php) -->
    <div id="modal-editar-equipo" class="modal-overlay" aria-hidden="true">
        <div class="card modal-content">
            <div class="section-header-flex">
                <h2 id="modal-eq-titulo">Actualizar Equipo</h2>
                <button type="button" class="btn-close-modal" onclick="cerrarModalEquipo()">✕</button>
            </div>
            <form id="form-editar-equipo">
                <input type="hidden" id="edit_eq_id">
                <div class="form-grid-single">
                    <div>
                        <label for="edit_eq_estado">Nuevo estado de clasificación</label>
                        <select id="edit_eq_estado">
                            <option value="funcional">Funcional</option>
                            <option value="reparable">Reparable</option>
                            <option value="reutilizable">Reutilizable</option>
                            <option value="gestion_especializada">Gestión Especializada</option>
                        </select>
                    </div>
                    <div>
                        <label for="edit_eq_ubicacion">Nueva ubicación</label>
                        <input type="text" id="edit_eq_ubicacion" required>
                    </div>
                    <div>
                        <label for="edit_eq_condicion">Condición actualizada</label>
                        <input type="text" id="edit_eq_condicion" required>
                    </div>
                </div>
                <div style="display:flex;gap:0.75rem;margin-top:1rem;">
                    <button type="submit" class="btn-primary" style="margin-top:0;">Guardar cambios</button>
                    <button type="button" class="btn-secondary" onclick="cerrarModalEquipo()">Cancelar</button>
                </div>
                <p id="msg-edit-equipo" class="status-msg"></p>
            </form>
        </div>
    </div>
