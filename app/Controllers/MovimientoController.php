<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Repositories/MovimientoRepository.php';
require_once __DIR__ . '/../Repositories/EquipoRepository.php';

class MovimientoController {
    private MovimientoRepository $movRepo;
    private EquipoRepository $eqRepo;

    public function __construct() {
        $this->movRepo = new MovimientoRepository();
        $this->eqRepo  = new EquipoRepository();
    }

    public function index(?int $equipoId = null): void {
        // Sección privada: solo Responsable Ambiental y Administrador consultan la bitácora de movimientos
        AuthMiddleware::exigirRoles([Usuario::ROL_AMBIENTAL, Usuario::ROL_ADMIN]);
        AuthMiddleware::responderJson($this->movRepo->obtenerTodos($equipoId));
    }

    public function store(array $input): void {
        $usuario = AuthMiddleware::exigirRoles([Usuario::ROL_AMBIENTAL, Usuario::ROL_ADMIN]);
        AuthMiddleware::validarCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        $equipoId = (int)($input['equipo_id'] ?? 0);
        $tipo     = $input['tipo_movimiento'] ?? '';
        $destino  = trim($input['destino_documentado'] ?? '');
        $verif    = !empty($input['gestor_verificado']);
        $obs      = $input['observaciones'] ?? '';

        $tiposValidos = ['ingreso', 'diagnostico', 'reparacion', 'reasignacion', 'entrega_gestor'];
        if ($equipoId <= 0 || !in_array($tipo, $tiposValidos, true) || $destino === '') {
            AuthMiddleware::responderJson(['error' => 'Completa el equipo, el tipo de movimiento y el destino documentado.'], 400);
        }

        // Regla de Desarrollo Sustentable: exigir verificación explícita antes de documentar entrega a gestor
        if ($tipo === 'entrega_gestor' && !$verif) {
            AuthMiddleware::responderJson([
                'error' => 'Por política ambiental, no se puede marcar una entrega a gestor sin verificar previamente su autorización/manifiesto.'
            ], 400);
        }

        $mov = new Movimiento($equipoId, $tipo, $destino, $verif, $obs, (int)$usuario['id']);
        $id = $this->movRepo->crear($mov);

        // Actualizar ubicación del equipo al nuevo destino documentado
        $camposEq = ['ubicacion_actual' => $destino];
        if ($tipo === 'entrega_gestor') {
            $camposEq['estado_clasificacion'] = 'gestion_especializada';
        } elseif ($tipo === 'reasignacion') {
            $camposEq['estado_clasificacion'] = 'reutilizable';
        }
        $this->eqRepo->actualizarParcial($equipoId, $camposEq);

        AuthMiddleware::responderJson([
            'mensaje' => 'Movimiento documentado con trazabilidad por ' . $usuario['nombre'],
            'id'      => $id
        ], 201);
    }
}
