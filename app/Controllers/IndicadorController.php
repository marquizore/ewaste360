<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Repositories/EquipoRepository.php';
require_once __DIR__ . '/../Repositories/DiagnosticoRepository.php';
require_once __DIR__ . '/../Repositories/MovimientoRepository.php';
require_once __DIR__ . '/../Repositories/CampanaRepository.php';

class IndicadorController {
    private EquipoRepository $eqRepo;
    private DiagnosticoRepository $diagRepo;
    private MovimientoRepository $movRepo;
    private CampanaRepository $campRepo;

    public function __construct() {
        $this->eqRepo   = new EquipoRepository();
        $this->diagRepo = new DiagnosticoRepository();
        $this->movRepo  = new MovimientoRepository();
        $this->campRepo = new CampanaRepository();
    }

    public function resumen(?string $subruta = null): void {
        // VALIDACIÓN ESTRICTA EN SERVIDOR: Solo Responsable Ambiental y Administrador acceden a reportes ejecutivos
        AuthMiddleware::exigirRoles([Usuario::ROL_AMBIENTAL, Usuario::ROL_ADMIN]);

        if ($subruta === 'campanas') {
            AuthMiddleware::responderJson([
                'status' => 'ok',
                'data'   => $this->campRepo->obtenerTodasConResultados()
            ]);
        }

        if ($subruta === 'reutilizacion') {
            AuthMiddleware::responderJson([
                'status' => 'ok',
                'data'   => $this->diagRepo->obtenerMetricasEconomicas()
            ]);
        }

        AuthMiddleware::responderJson([
            'status'    => 'ok',
            'timestamp' => date('c'),
            'data'      => [
                'total_equipos'             => $this->eqRepo->contarTotal(),
                'equipos_por_estado'        => $this->eqRepo->contarPorEstado(),
                'entregados_gestor_verif'   => $this->movRepo->contarEntregadosAGestorVerificado(),
                'total_participaciones'     => $this->campRepo->contarTotalParticipaciones(),
                'metricas_economicas'       => $this->diagRepo->obtenerMetricasEconomicas(),
                'resumen_campanas'          => $this->campRepo->obtenerTodasConResultados()
            ]
        ]);
    }
}
