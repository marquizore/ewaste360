// Módulo 2: Gestión de Inventario RAEE (listar, buscar, filtrar, registrar y editar)
let cacheEquipos = [];

async function cargarEquipos() {
    const tbody = document.getElementById('tabla-equipos');
    if (!tbody) return;

    const q = document.getElementById('buscar-equipo')?.value.trim() || '';
    const estado = document.getElementById('filtro-estado')?.value || '';
    const params = new URLSearchParams();
    if (q) params.set('q', q);
    if (estado) params.set('estado', estado);

    try {
        const res = await fetch('api/equipos.php' + (params.toString() ? '?' + params.toString() : ''));
        const data = await res.json();
        if (!Array.isArray(data)) return;

        if (!q && !estado) {
            cacheEquipos = data;
            poblarSelectsEquipos(data);
        }

        tbody.innerHTML = '';
        if (data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${puedeEditarEquipos ? 8 : 7}">No se encontraron equipos con los criterios actuales.</td></tr>`;
            return;
        }

        data.forEach(eq => {
            const tr = document.createElement('tr');
            const btnEditar = puedeEditarEquipos
                ? `<td><button type="button" class="btn-primary btn-sm" onclick="abrirModalEquipo(${eq.id})">Editar</button></td>`
                : '';

            tr.innerHTML = `
                <td><strong>${eq.codigo_inventario}</strong></td>
                <td>${eq.categoria}</td>
                <td>${eq.marca} ${eq.modelo}</td>
                <td>${eq.fecha_ingreso}</td>
                <td>${eq.condicion_general}</td>
                <td><span class="badge ${eq.estado_clasificacion}">${eq.estado_clasificacion.replace('_', ' ')}</span></td>
                <td>${eq.ubicacion_actual}</td>
                ${btnEditar}
            `;
            tbody.appendChild(tr);
        });
    } catch (err) {
        tbody.innerHTML = '<tr><td colspan="8">Error al cargar el inventario. Verifica la conexión a MySQL.</td></tr>';
    }
}

function poblarSelectsEquipos(equipos) {
    ['diag_equipo_id', 'mov_equipo_id'].forEach(selectId => {
        const sel = document.getElementById(selectId);
        if (!sel) return;
        sel.innerHTML = '<option value="">-- Selecciona un equipo --</option>';
        equipos.forEach(eq => {
            const opt = document.createElement('option');
            opt.value = eq.id;
            opt.textContent = `${eq.codigo_inventario} — ${eq.marca} ${eq.modelo} (${eq.estado_clasificacion})`;
            sel.appendChild(opt);
        });
    });
}

document.getElementById('buscar-equipo')?.addEventListener('input', cargarEquipos);
document.getElementById('filtro-estado')?.addEventListener('change', cargarEquipos);

document.getElementById('form-equipo')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('msg-inventario');
    const payload = {
        codigo_inventario: document.getElementById('codigo_inventario').value.trim(),
        categoria: document.getElementById('categoria').value,
        marca: document.getElementById('marca').value.trim(),
        modelo: document.getElementById('modelo').value.trim(),
        fecha_ingreso: document.getElementById('fecha_ingreso').value,
        estado_clasificacion: document.getElementById('estado_clasificacion').value,
        ubicacion_actual: document.getElementById('ubicacion_actual').value.trim(),
        condicion_general: document.getElementById('condicion_general').value.trim()
    };

    try {
        const res = await fetch('api/equipos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (res.ok) {
            msg.style.color = '#15803d';
            msg.innerText = '✅ ' + result.mensaje;
            e.target.reset();
            await cargarEquipos();
            if (typeof cargarMovimientos === 'function') cargarMovimientos();
            refrescarIndicadoresSiAplica();
        } else {
            msg.style.color = '#b91c1c';
            msg.innerText = '⚠️ ' + (result.error || 'Error al registrar');
        }
    } catch (error) {
        msg.style.color = '#b91c1c';
        msg.innerText = '⚠️ Error de conexión con el servidor.';
    }
});

function abrirModalEquipo(id) {
    const eq = cacheEquipos.find(item => parseInt(item.id) === parseInt(id));
    if (!eq) return;
    document.getElementById('edit_eq_id').value = eq.id;
    document.getElementById('modal-eq-titulo').innerText = `Actualizar ${eq.codigo_inventario} (${eq.marca} ${eq.modelo})`;
    document.getElementById('edit_eq_estado').value = eq.estado_clasificacion;
    document.getElementById('edit_eq_ubicacion').value = eq.ubicacion_actual;
    document.getElementById('edit_eq_condicion').value = eq.condicion_general;
    document.getElementById('msg-edit-equipo').innerText = '';
    document.getElementById('modal-editar-equipo').classList.add('abierto');
}

function cerrarModalEquipo() {
    document.getElementById('modal-editar-equipo')?.classList.remove('abierto');
}

document.getElementById('form-editar-equipo')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('edit_eq_id').value;
    const msg = document.getElementById('msg-edit-equipo');
    const payload = {
        estado_clasificacion: document.getElementById('edit_eq_estado').value,
        ubicacion_actual: document.getElementById('edit_eq_ubicacion').value.trim(),
        condicion_general: document.getElementById('edit_eq_condicion').value.trim()
    };

    try {
        const res = await fetch(`api/equipos.php?id=${id}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok) {
            msg.style.color = '#15803d';
            msg.innerText = '✅ ' + data.mensaje;
            await cargarEquipos();
            refrescarIndicadoresSiAplica();
            setTimeout(cerrarModalEquipo, 600);
        } else {
            msg.style.color = '#b91c1c';
            msg.innerText = '⚠️ ' + (data.error || 'No se pudo actualizar.');
        }
    } catch (err) {
        msg.style.color = '#b91c1c';
        msg.innerText = '⚠️ Error al conectar con el servidor.';
    }
});

cargarEquipos();
