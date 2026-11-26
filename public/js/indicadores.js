// Módulo 5: Indicadores Sustentables y Gráficas Chart.js (Solo Resp. Ambiental y Admin)
let chartEstadosInstance = null;
let chartCampanasInstance = null;

window.cargarIndicadores = async function () {
    if (!puedeVerReportes) return;
    try {
        const res = await fetch('api/indicadores.php');
        if (!res.ok) return;
        const json = await res.json();
        if (json.status !== 'ok') return;

        const d = json.data;
        const m = d.metricas_economicas;

        document.getElementById('kpi-total-equipos').innerText = d.total_equipos;
        document.getElementById('kpi-reutilizados').innerText = m.total_reutilizados || 0;
        document.getElementById('kpi-gestor').innerText = d.entregados_gestor_verif;
        document.getElementById('kpi-participaciones').innerText = d.total_participaciones;
        document.getElementById('kpi-costo').innerText = '$' + parseFloat(m.costo_total).toFixed(2);
        document.getElementById('kpi-ahorro').innerText = '$' + parseFloat(m.ahorro_total).toFixed(2);

        renderizarGraficas(d);
    } catch (e) {}
};

function renderizarGraficas(data) {
    if (typeof Chart === 'undefined') return;

    const ctxEstados = document.getElementById('chart-estados');
    if (ctxEstados) {
        if (chartEstadosInstance) chartEstadosInstance.destroy();
        const labels = data.equipos_por_estado.map(x => x.estado_clasificacion.replace('_', ' ').toUpperCase());
        const valores = data.equipos_por_estado.map(x => parseInt(x.total));

        chartEstadosInstance = new Chart(ctxEstados, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: valores,
                    backgroundColor: ['#15803d', '#eab308', '#2563eb', '#dc2626']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    }

    const ctxCampanas = document.getElementById('chart-campanas');
    if (ctxCampanas) {
        if (chartCampanasInstance) chartCampanasInstance.destroy();
        const labelsC = data.resumen_campanas.map(c => c.nombre.substring(0, 26));
        const unidadesC = data.resumen_campanas.map(c => parseInt(c.unidades_acopiadas));
        const partC = data.resumen_campanas.map(c => parseInt(c.participantes));

        chartCampanasInstance = new Chart(ctxCampanas, {
            type: 'bar',
            data: {
                labels: labelsC,
                datasets: [
                    { label: 'Unidades acopiadas', data: unidadesC, backgroundColor: '#15803d' },
                    { label: 'Participantes', data: partC, backgroundColor: '#38bdf8' }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }
}

window.cargarIndicadores();
