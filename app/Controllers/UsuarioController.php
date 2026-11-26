<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Repositories/UsuarioRepository.php';

class UsuarioController {
    private UsuarioRepository $repo;

    public function __construct() {
        $this->repo = new UsuarioRepository();
    }

    public function index(): void {
        AuthMiddleware::exigirRoles([Usuario::ROL_ADMIN]);
        AuthMiddleware::responderJson($this->repo->listarTodos());
    }

    public function store(array $input): void {
        AuthMiddleware::exigirRoles([Usuario::ROL_ADMIN]);
        AuthMiddleware::validarCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        if (empty($input['nombre']) || empty($input['username']) || empty($input['email']) || empty($input['password'])) {
            AuthMiddleware::responderJson(['error' => 'Todos los campos son obligatorios para crear un usuario.'], 400);
        }
        if (strlen($input['password']) < 6) {
            AuthMiddleware::responderJson(['error' => 'La contraseña debe tener al menos 6 caracteres.'], 400);
        }

        $id = $this->repo->crearUsuario(
            $input['nombre'],
            $input['username'],
            $input['email'],
            $input['password'],
            $input['rol'] ?? Usuario::ROL_CAPTURISTA
        );
        AuthMiddleware::responderJson(['mensaje' => 'Usuario creado exitosamente con hash bcrypt.', 'id' => $id], 201);
    }

    public function update(array $input): void {
        AuthMiddleware::exigirRoles([Usuario::ROL_ADMIN]);
        AuthMiddleware::validarCsrfToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

        $id = (int)($input['id'] ?? 0);
        if ($id <= 0 || empty($input['rol'])) {
            AuthMiddleware::responderJson(['error' => 'Datos inválidos para actualizar el rol del usuario.'], 400);
        }
        $ok = $this->repo->actualizarRolYEstado($id, $input['rol'], (int)($input['activo'] ?? 1));
        AuthMiddleware::responderJson([
            'actualizado' => $ok,
            'mensaje'     => 'Rol y estado actualizados correctamente en el servidor.'
        ]);
    }
}
