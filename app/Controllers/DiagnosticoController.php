<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Repositories/DiagnosticoRepository.php';
require_once __DIR__ . '/../Repositories/EquipoRepository.php';
require_once __DIR__ . '/../Repositories/MovimientoRepository.php';
require_once __DIR__ . '/../Strategies/ClasificacionStrategy.php';

class DiagnosticoController {
    private DiagnosticoRepository $diagRepo;
    private EquipoRepository $eqRepo;
    private MovimientoRepository $movRepo;

    public function __construct() {
        $this->diagRepo = new DiagnosticoRepository();
        $this->eqRepo   = new EquipoRepository();
        $this->movRepo  = new MovimientoRepository();
    }

    public function index(): void {
        // Sección privada: solo Responsable Ambiental y Administrador consultan diagnósticos y costos
        AuthMiddleware::exigirRoles([Usuario::ROL_AMBIENTAL, Usuario::ROL_ADMIN]);
        AuthMiddleware::responderJson($this->diagRepo->obtenerTodos());
    }

    public function store(array $input): void {
        $usuario = AuthMiddleware::exigirRoles([Usuario::ROL_AMBIENTAL, Usuario::ROL_ADMIN]);
        AuthMiddleware::validarCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        $equipoId = (int)($input['equipo_id'] ?? 0);
        if ($equipoId <= 0 || empty($input['falla_detectada']) || empty($input['accion_reparacion'])) {
            AuthMiddleware::responderJson(['error' => 'Selecciona un equipo y completa la falla y acción de reparación.'], 400);
        }

        // Patrón Strategy para evaluar el destino sustentable y ahorro estimado
        $context = new EvaluadorSustentableContext();
        $decision = $input['decision_final'] ?? 'reutilizar';
        if ($decision === 'reutilizar') {
            $context->setStrategy(new ReparacionStrategy());
        } elseif ($decision === 'conservar') {
            $context->setStrategy(new ReutilizacionDirectaStrategy());
        } else {
            $context->setStrategy(new GestionEspecializadaStrategy());
        }

        $costo = (float)($input['costo_reparacion'] ?? 0);
        $evaluacion = $context->ejecutarEvaluacion([
            'costo_reparacion' => $costo,
            'valor_reposicion' => (float)($input['valor_reposicion'] ?? 5500)
        ]);

        $ahorro = isset($input['ahorro_estimado']) && $input['ahorro_estimado'] !== ''
            ? (float)$input['ahorro_estimado']
            : (float)$evaluacion['ahorro_estimado'];

        $areaDestino = !empty($input['area_reasignada']) ? $input['area_reasignada'] : 'Taller Ambiental';

        $diagModel = new Diagnostico(
            $equipoId,
            $input['falla_detectada'],
            $input['accion_reparacion'],
            $costo,
            $ahorro,
            $decision,
            $areaDestino,
            (int)$usuario['id']
        );

        $id = $this->diagRepo->crear($diagModel);

        // Actualizamos estado y ubicación del equipo
        $this->eqRepo->actualizarParcial($equipoId, [
            'estado_clasificacion' => $evaluacion['estado_clasificacion'],
            'ubicacion_actual'     => $areaDestino
        ]);

        // Documentamos el movimiento en historial de trazabilidad
        $tipoMov = ($decision === 'gestion_especializada') ? 'diagnostico' : 'reparacion';
        $mov = new Movimiento(
            $equipoId,
            $tipoMov,
            $areaDestino,
            false,
            'Dictamen técnico: ' . $evaluacion['recomendacion'],
            (int)$usuario['id']
        );
        $this->movRepo->crear($mov);

        AuthMiddleware::responderJson([
            'mensaje'    => 'Diagnóstico validado por ' . $usuario['nombre'],
            'id'         => $id,
            'evaluacion' => $evaluacion
        ], 201);
    }
}
