// Módulo 3: Diagnósticos Técnicos y Bitácora de Movimientos / Trazabilidad
async function cargarDiagnosticos() {
    const tbody = document.getElementById('tabla-diagnosticos');
    if (!tbody) return;
    try {
        const res = await fetch('api/diagnosticos.php');
        const lista = await res.json();
        tbody.innerHTML = '';
        if (!Array.isArray(lista) || lista.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7">Aún no hay diagnósticos registrados.</td></tr>';
            return;
        }
        lista.forEach(d => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>${d.codigo_inventario}</strong><br><small>${d.marca} ${d.modelo}</small></td>
                <td><strong>Falla:</strong> ${d.falla_detectada}<br><small><strong>Acción:</strong> ${d.accion_reparacion}</small></td>
                <td>$${parseFloat(d.costo_reparacion).toFixed(2)}</td>
                <td style="color:#15803d;font-weight:700;">$${parseFloat(d.ahorro_estimado).toFixed(2)}</td>
                <td><span class="badge funcional">${d.decision_final}</span></td>
                <td>${d.area_reasignada || '-'}</td>
                <td><small>${d.nombre_responsable}</small></td>
            `;
            tbody.appendChild(tr);
        });
    } catch (e) {}
}

document.getElementById('form-diagnostico')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('msg-diagnostico');
    const payload = {
        equipo_id: document.getElementById('diag_equipo_id').value,
        falla_detectada: document.getElementById('diag_falla').value.trim(),
        accion_reparacion: document.getElementById('diag_accion').value.trim(),
        costo_reparacion: document.getElementById('diag_costo').value,
        valor_reposicion: document.getElementById('diag_valor_repo').value,
        decision_final: document.getElementById('diag_decision').value,
        area_reasignada: document.getElementById('diag_area').value.trim()
    };

    try {
        const res = await fetch('api/diagnosticos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (res.ok) {
            msg.style.color = '#15803d';
            msg.innerText = `✅ ${result.mensaje} | Ahorro estimado: $${result.evaluacion.ahorro_estimado}`;
            e.target.reset();
            if (typeof cargarEquipos === 'function') cargarEquipos();
            cargarDiagnosticos();
            cargarMovimientos();
            refrescarIndicadoresSiAplica();
        } else {
            msg.style.color = '#b91c1c';
            msg.innerText = '⚠️ ' + (result.error || 'No autorizado');
        }
    } catch (err) {
        msg.style.color = '#b91c1c';
        msg.innerText = '⚠️ Error al conectar con el servidor.';
    }
});

async function cargarMovimientos() {
    const tbody = document.getElementById('tabla-movimientos');
    if (!tbody) return;
    try {
        const res = await fetch('api/movimientos.php');
        const lista = await res.json();
        tbody.innerHTML = '';
        if (!Array.isArray(lista) || lista.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6">Sin movimientos registrados.</td></tr>';
            return;
        }
        lista.forEach(m => {
            const verif = parseInt(m.gestor_verificado) === 1
                ? '<span class="badge verificado">✓ Verificado</span>'
                : '<span class="badge pendiente">En proceso</span>';
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><small>${m.fecha_movimiento}</small></td>
                <td><strong>${m.codigo_inventario}</strong><br><small>${m.marca} ${m.modelo}</small></td>
                <td><span class="badge reutilizable">${m.tipo_movimiento.replace('_', ' ')}</span></td>
                <td>${m.destino_documentado}</td>
                <td>${verif}</td>
                <td><small>${m.observaciones || '-'}</small></td>
            `;
            tbody.appendChild(tr);
        });
    } catch (e) {}
}

document.getElementById('form-movimiento')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('msg-movimiento');
    const payload = {
        equipo_id: document.getElementById('mov_equipo_id').value,
        tipo_movimiento: document.getElementById('mov_tipo').value,
        destino_documentado: document.getElementById('mov_destino').value.trim(),
        gestor_verificado: document.getElementById('mov_verificado').checked,
        observaciones: document.getElementById('mov_obs').value.trim()
    };

    try {
        const res = await fetch('api/movimientos.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (res.ok) {
            msg.style.color = '#15803d';
            msg.innerText = '✅ ' + result.mensaje;
            e.target.reset();
            if (typeof cargarEquipos === 'function') cargarEquipos();
            cargarMovimientos();
            refrescarIndicadoresSiAplica();
        } else {
            msg.style.color = '#b91c1c';
            msg.innerText = '⚠️ ' + (result.error || 'Error al documentar movimiento.');
        }
    } catch (err) {
        msg.style.color = '#b91c1c';
        msg.innerText = '⚠️ Error al conectar con el servidor.';
    }
});

cargarDiagnosticos();
cargarMovimientos();
