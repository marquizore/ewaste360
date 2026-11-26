<?php
if (!defined('EWASTE_APP')) {
    http_response_code(403);
    exit('Acceso directo no permitido.');
}
?>
        <!-- MÓDULO 6: GESTIÓN DE USUARIOS Y ROLES (Exclusivo Admin) -->
        <section id="mod-usuarios" class="modulo">
            <div class="card">
                <h2>Crear Nuevo Usuario del Sistema</h2>
                <form id="form-usuario">
                    <div class="form-grid">
                        <div>
                            <label for="usr_nombre">Nombre completo *</label>
                            <input type="text" id="usr_nombre" placeholder="Ej. Paulina Noriega" required>
                        </div>
                        <div>
                            <label for="usr_username">Nombre de usuario *</label>
                            <input type="text" id="usr_username" placeholder="Ej. paulina" required>
                        </div>
                        <div>
                            <label for="usr_email">Correo institucional *</label>
                            <input type="email" id="usr_email" placeholder="paulina@ewaste360.edu.mx" required>
                        </div>
                        <div>
                            <label for="usr_password">Contraseña inicial *</label>
                            <input type="password" id="usr_password" placeholder="Mínimo 6 caracteres" minlength="6" required>
                        </div>
                        <div>
                            <label for="usr_rol">Rol asignado *</label>
                            <select id="usr_rol">
                                <option value="visitante">Visitante</option>
                                <option value="capturista">Capturista</option>
                                <option value="responsable_ambiental">Responsable Ambiental</option>
                                <option value="administrador">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-primary">Registrar usuario con hash bcrypt</button>
                    <p id="msg-usuario" class="status-msg"></p>
                </form>
            </div>

            <div class="card" style="margin-top:1.5rem;">
                <h2>Control de Usuarios y Permisos en el Servidor</h2>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Usuario / Correo</th>
                                <th>Rol Asignado</th>
                                <th>Estado</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-usuarios">
                            <tr><td colspan="6">Cargando lista de usuarios...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
