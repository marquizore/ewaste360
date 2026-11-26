<?php
require_once __DIR__ . '/../Middleware/AuthMiddleware.php';
require_once __DIR__ . '/../Repositories/UsuarioRepository.php';

class AuthController {
    private UsuarioRepository $repo;

    public function __construct() {
        $this->repo = new UsuarioRepository();
    }

    public function status(): void {
        $usuarioActual = AuthMiddleware::obtenerUsuarioActual();
        AuthMiddleware::responderJson([
            'autenticado' => !empty($usuarioActual['id']),
            'usuario'     => $usuarioActual,
            'csrf_token'  => AuthMiddleware::obtenerCsrfToken()
        ]);
    }

    public function login(array $input): void {
        $identificador = trim($input['username'] ?? '');
        $password      = $input['password'] ?? '';

        if ($identificador === '' || $password === '') {
            AuthMiddleware::responderJson(['error' => 'Por favor ingresa tu usuario/correo y contraseña.'], 400);
        }

        $usuario = $this->repo->buscarPorUsernameOEmail($identificador);

        if (!$usuario || !$usuario->verificarPassword($password)) {
            AuthMiddleware::responderJson(['error' => 'Credenciales incorrectas. Verifica tu usuario y contraseña.'], 401);
        }

        if (!$usuario->isActivo()) {
            AuthMiddleware::responderJson(['error' => 'Esta cuenta de usuario se encuentra desactivada por el administrador.'], 403);
        }

        AuthMiddleware::establecerSesionUsuario($usuario);

        AuthMiddleware::responderJson([
            'mensaje'     => 'Sesión iniciada correctamente como ' . $usuario->getRol(),
            'autenticado' => true,
            'usuario'     => AuthMiddleware::obtenerUsuarioActual(),
            'csrf_token'  => AuthMiddleware::obtenerCsrfToken()
        ]);
    }

    public function logout(): void {
        AuthMiddleware::cerrarSesion();
        AuthMiddleware::iniciarSesionSegura();
        AuthMiddleware::responderJson([
            'mensaje'     => 'Sesión cerrada correctamente.',
            'autenticado' => false,
            'usuario'     => AuthMiddleware::obtenerUsuarioActual(),
            'csrf_token'  => AuthMiddleware::obtenerCsrfToken()
        ]);
    }
}
