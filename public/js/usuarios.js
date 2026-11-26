// Módulo 6: Administración de Usuarios y Roles (Exclusivo Administrador)
async function cargarUsuarios() {
    if (!puedeGestionarUsuarios) return;
    const tbody = document.getElementById('tabla-usuarios');
    if (!tbody) return;
    try {
        const res = await fetch('api/usuarios.php');
        if (!res.ok) return;
        const usuarios = await res.json();
        tbody.innerHTML = '';
        usuarios.forEach(u => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${u.id}</td>
                <td><strong>${u.nombre}</strong></td>
                <td><code>${u.username}</code><br><small>${u.email}</small></td>
                <td>
                    <select id="rol-usr-${u.id}">
                        <option value="visitante" ${u.rol === 'visitante' ? 'selected' : ''}>Visitante</option>
                        <option value="capturista" ${u.rol === 'capturista' ? 'selected' : ''}>Capturista</option>
                        <option value="responsable_ambiental" ${u.rol === 'responsable_ambiental' ? 'selected' : ''}>Responsable Ambiental</option>
                        <option value="administrador" ${u.rol === 'administrador' ? 'selected' : ''}>Administrador</option>
                    </select>
                </td>
                <td>
                    <select id="act-usr-${u.id}">
                        <option value="1" ${parseInt(u.activo) === 1 ? 'selected' : ''}>Activo</option>
                        <option value="0" ${parseInt(u.activo) === 0 ? 'selected' : ''}>Inactivo</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn-primary btn-sm" onclick="actualizarUsuario(${u.id})">Guardar</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    } catch (e) {}
}

async function actualizarUsuario(id) {
    const rol = document.getElementById(`rol-usr-${id}`).value;
    const activo = document.getElementById(`act-usr-${id}`).value;
    const res = await fetch('api/usuarios.php', {
        method: 'PATCH',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
        body: JSON.stringify({ id, rol, activo })
    });
    const data = await res.json();
    alert(data.mensaje || data.error);
    cargarUsuarios();
}

document.getElementById('form-usuario')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('msg-usuario');
    const payload = {
        nombre: document.getElementById('usr_nombre').value.trim(),
        username: document.getElementById('usr_username').value.trim(),
        email: document.getElementById('usr_email').value.trim(),
        password: document.getElementById('usr_password').value,
        rol: document.getElementById('usr_rol').value
    };
    const res = await fetch('api/usuarios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
        body: JSON.stringify(payload)
    });
    const data = await res.json();
    if (res.ok) {
        msg.style.color = '#15803d';
        msg.innerText = '✅ ' + data.mensaje;
        e.target.reset();
        cargarUsuarios();
    } else {
        msg.style.color = '#b91c1c';
        msg.innerText = '⚠️ ' + (data.error || 'Error al crear usuario');
    }
});

cargarUsuarios();
