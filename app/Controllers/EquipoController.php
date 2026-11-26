<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Repositories/EquipoRepository.php';
require_once __DIR__ . '/../Repositories/MovimientoRepository.php';
require_once __DIR__ . '/../Models/EquipoFactory.php';

class EquipoController {
    private EquipoRepository $repo;
    private MovimientoRepository $movRepo;

    public function __construct() {
        $this->repo = new EquipoRepository();
        $this->movRepo = new MovimientoRepository();
    }

    public function index(?string $estado, ?string $q): void {
        // Sección privada: solo roles operativos autenticados consultan el inventario interno
        AuthMiddleware::exigirRoles([Usuario::ROL_CAPTURISTA, Usuario::ROL_AMBIENTAL, Usuario::ROL_ADMIN]);
        AuthMiddleware::responderJson($this->repo->obtenerTodos($estado, $q));
    }

    public function show(int $id): void {
        AuthMiddleware::exigirRoles([Usuario::ROL_CAPTURISTA, Usuario::ROL_AMBIENTAL, Usuario::ROL_ADMIN]);
        $equipo = $this->repo->obtenerPorId($id);
        if (!$equipo) {
            AuthMiddleware::responderJson(['error' => 'Equipo no encontrado'], 404);
        }
        AuthMiddleware::responderJson($equipo);
    }

    public function store(array $input): void {
        $usuario = AuthMiddleware::exigirRoles([Usuario::ROL_CAPTURISTA, Usuario::ROL_ADMIN]);
        AuthMiddleware::validarCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        if (empty($input['codigo_inventario']) || empty($input['marca']) || empty($input['modelo'])) {
            AuthMiddleware::responderJson(['error' => 'Faltan campos obligatorios (código, marca y modelo).'], 400);
        }

        // Uso del Patrón Factory para instanciar el equipo con valores sustentables según categoría
        $equipo = EquipoFactory::crearDesdeSolicitud($input, (int)$usuario['id']);
        $id = $this->repo->crear($equipo);

        // Registrar movimiento inicial de ingreso para trazabilidad automática
        $movInicial = new Movimiento(
            $id,
            'ingreso',
            $equipo->getUbicacionActual(),
            false,
            'Alta inicial en inventario institucional: ' . $equipo->getCondicionGeneral(),
            (int)$usuario['id']
        );
        $this->movRepo->crear($movInicial);

        AuthMiddleware::responderJson([
            'mensaje' => 'Equipo registrado con trazabilidad inicial por ' . $usuario['nombre'],
            'id'      => $id
        ], 201);
    }

    public function update(int $id, array $input): void {
        $usuario = AuthMiddleware::exigirRoles([Usuario::ROL_CAPTURISTA, Usuario::ROL_AMBIENTAL, Usuario::ROL_ADMIN]);
        AuthMiddleware::validarCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        if ($id <= 0 || empty($input)) {
            AuthMiddleware::responderJson(['error' => 'ID inválido o sin campos para actualizar.'], 400);
        }

        $ok = $this->repo->actualizarParcial($id, $input);
        if (!$ok) {
            AuthMiddleware::responderJson(['error' => 'No se realizaron cambios en el registro.'], 400);
        }

        AuthMiddleware::responderJson([
            'actualizado' => true,
            'mensaje'     => 'Registro actualizado correctamente por ' . $usuario['nombre']
        ]);
    }
}
