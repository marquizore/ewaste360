<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Repositories/CampanaRepository.php';

class CampanaController {
    private CampanaRepository $repo;

    public function __construct() {
        $this->repo = new CampanaRepository();
    }

    public function index(): void {
        AuthMiddleware::responderJson([
            'campanas'        => $this->repo->obtenerTodasConResultados(),
            'participaciones' => $this->repo->obtenerParticipacionesRecientes()
        ]);
    }

    public function storeCampana(array $input): void {
        $usuario = AuthMiddleware::exigirRoles([Usuario::ROL_AMBIENTAL, Usuario::ROL_ADMIN]);
        AuthMiddleware::validarCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        if (empty($input['nombre']) || empty($input['fecha_inicio']) || empty($input['fecha_fin']) || empty($input['ubicacion'])) {
            AuthMiddleware::responderJson(['error' => 'Completa nombre, fechas y ubicación de la campaña.'], 400);
        }

        $campana = new Campana(
            $input['nombre'],
            $input['descripcion'] ?? 'Campaña institucional de acopio RAEE.',
            $input['fecha_inicio'],
            $input['fecha_fin'],
            $input['ubicacion'],
            $input['equipos_aceptados'] ?? 'Equipos de cómputo, periféricos y cables',
            $input['estado'] ?? 'activa',
            (int)$usuario['id']
        );

        $id = $this->repo->crearCampana($campana);
        AuthMiddleware::responderJson([
            'mensaje' => 'Campaña publicada correctamente por ' . $usuario['nombre'],
            'id'      => $id
        ], 201);
    }

    public function registrarParticipacion(array $input): void {
        // Permite registrar participación tanto a visitantes públicos como a usuarios autenticados (con CSRF)
        AuthMiddleware::validarCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        $usuarioActual = AuthMiddleware::obtenerUsuarioActual();

        $campanaId = (int)($input['campana_id'] ?? 0);
        $nombre    = trim($input['nombre_participante'] ?? '');
        $residuo   = trim($input['tipo_residuo_entregado'] ?? '');
        $cantidad  = (int)($input['cantidad_unidades'] ?? 1);

        if ($campanaId <= 0 || $nombre === '' || $residuo === '' || $cantidad <= 0) {
            AuthMiddleware::responderJson(['error' => 'Completa campaña, tu nombre, tipo de residuo y cantidad válida.'], 400);
        }

        $part = new Participacion(
            $campanaId,
            $nombre,
            $residuo,
            $cantidad,
            !empty($usuarioActual['id']) ? (int)$usuarioActual['id'] : null
        );
        $id = $this->repo->registrarParticipacion($part);

        AuthMiddleware::responderJson([
            'mensaje' => '¡Gracias! Tu participación quedó registrada en la campaña.',
            'id'      => $id
        ], 201);
    }
}
