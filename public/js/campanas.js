// Módulo 4: Campañas de Recolección y Registro de Participaciones
async function cargarCampanas() {
    const cont = document.getElementById('contenedor-campanas');
    const sel = document.getElementById('part_campana_id');
    const tbodyPart = document.getElementById('tabla-participaciones');
    if (!cont) return;

    try {
        const res = await fetch('api/campanas.php');
        const data = await res.json();
        cont.innerHTML = '';
        if (sel) sel.innerHTML = '<option value="">-- Elige una campaña --</option>';

        (data.campanas || []).forEach(c => {
            const div = document.createElement('div');
            div.className = 'card';
            div.innerHTML = `
                <span class="badge ${c.estado === 'activa' ? 'funcional' : 'reparable'}">${c.estado.toUpperCase()}</span>
                <h4 style="margin:0.5rem 0 0.35rem;color:var(--verde-oscuro);font-size:1.1rem;">${c.nombre}</h4>
                <p style="font-size:0.9rem;margin-bottom:0.5rem;">${c.descripcion}</p>
                <p style="font-size:0.85rem;">📅 <strong>Fechas:</strong> ${c.fecha_inicio} al ${c.fecha_fin}</p>
                <p style="font-size:0.85rem;">📍 <strong>Sede:</strong> ${c.ubicacion}</p>
                <p style="font-size:0.85rem;">♻️ <strong>Acepta:</strong> ${c.equipos_aceptados}</p>
                <p style="font-size:0.88rem;margin-top:0.5rem;padding-top:0.5rem;border-top:1px solid #e2e8f0;">
                    👥 Participantes: <strong>${c.participantes}</strong> · 📦 Unidades: <strong>${c.unidades_acopiadas}</strong>
                </p>
            `;
            cont.appendChild(div);

            if (sel && c.estado !== 'finalizada') {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.textContent = `${c.nombre} (${c.ubicacion})`;
                sel.appendChild(opt);
            }
        });

        if (tbodyPart) {
            tbodyPart.innerHTML = '';
            (data.participaciones || []).forEach(p => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${p.nombre_campana}</strong></td>
                    <td>${p.nombre_participante}</td>
                    <td>${p.tipo_residuo_entregado}</td>
                    <td><strong>${p.cantidad_unidades}</strong></td>
                    <td><small>${p.fecha_registro}</small></td>
                `;
                tbodyPart.appendChild(tr);
            });
        }
    } catch (e) {}
}

document.getElementById('form-participacion')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('msg-participacion');
    const payload = {
        campana_id: document.getElementById('part_campana_id').value,
        nombre_participante: document.getElementById('part_nombre').value.trim(),
        tipo_residuo_entregado: document.getElementById('part_residuo').value.trim(),
        cantidad_unidades: document.getElementById('part_cantidad').value
    };
    try {
        const res = await fetch('api/campanas.php?action=participar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok) {
            msg.style.color = '#15803d';
            msg.innerText = '✅ ' + data.mensaje;
            document.getElementById('part_residuo').value = '';
            document.getElementById('part_cantidad').value = '1';
            cargarCampanas();
            refrescarIndicadoresSiAplica();
        } else {
            msg.style.color = '#b91c1c';
            msg.innerText = '⚠️ ' + (data.error || 'Error al registrar participación.');
        }
    } catch (err) {
        msg.style.color = '#b91c1c';
        msg.innerText = '⚠️ Error al conectar con el servidor.';
    }
});

document.getElementById('form-campana')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = document.getElementById('msg-campana');
    const payload = {
        nombre: document.getElementById('camp_nombre').value.trim(),
        fecha_inicio: document.getElementById('camp_inicio').value,
        fecha_fin: document.getElementById('camp_fin').value,
        ubicacion: document.getElementById('camp_ubicacion').value.trim(),
        equipos_aceptados: document.getElementById('camp_equipos').value.trim(),
        estado: document.getElementById('camp_estado').value,
        descripcion: document.getElementById('camp_descripcion').value.trim()
    };
    try {
        const res = await fetch('api/campanas.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (res.ok) {
            msg.style.color = '#15803d';
            msg.innerText = '✅ ' + data.mensaje;
            e.target.reset();
            cargarCampanas();
            refrescarIndicadoresSiAplica();
        } else {
            msg.style.color = '#b91c1c';
            msg.innerText = '⚠️ ' + (data.error || 'No autorizado.');
        }
    } catch (err) {
        msg.style.color = '#b91c1c';
        msg.innerText = '⚠️ Error al conectar con el servidor.';
    }
});

cargarCampanas();
