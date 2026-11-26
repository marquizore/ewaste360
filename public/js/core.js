// Configuración global, navegación y utilidades compartidas
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
const puedeEditarEquipos = document.body.dataset.puedeEditar === '1';
const puedeVerReportes = document.body.dataset.verReportes === '1';
const puedeGestionarUsuarios = document.body.dataset.gestionarUsuarios === '1';

function refrescarIndicadoresSiAplica() {
    if (puedeVerReportes && typeof window.cargarIndicadores === 'function') {
        window.cargarIndicadores();
    }
}

function irAModulo(targetId) {
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.toggle('active', b.dataset.target === targetId);
    });
    document.querySelectorAll('.modulo').forEach(m => {
        m.classList.toggle('activo', m.id === targetId);
    });
    document.getElementById('nav-links')?.classList.remove('abierto');

    if (targetId === 'mod-indicadores') {
        refrescarIndicadoresSiAplica();
    }
}

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => irAModulo(btn.dataset.target));
});

document.getElementById('menu-toggle')?.addEventListener('click', () => {
    document.getElementById('nav-links')?.classList.toggle('abierto');
});

document.getElementById('btn-logout')?.addEventListener('click', async () => {
    await fetch('api/auth.php?action=logout', { method: 'POST' });
    window.location.href = 'login.php';
});
