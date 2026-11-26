<?php
require_once __DIR__ . '/../Models/Usuario.php';

/**
 * AuthMiddleware:
 * Gestiona sesiones seguras PHP (HttpOnly, SameSite, Secure en HTTPS),
 * tokens CSRF y autorización estricta por roles en el servidor.
 */
class AuthMiddleware {

    public static function iniciarSesionSegura(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $esHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                       || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
                       || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

            session_set_cookie_params([
                'lifetime' => 0,
                'path'     => '/',
                'domain'   => '',
                'secure'   => $esHttps,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_name('__session');
            session_start();
            if (!headers_sent()) {
                header('Cache-Control: private, no-store, no-cache, must-revalidate');
            }
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    public static function establecerSesionUsuario(Usuario $usuario): void {
        self::iniciarSesionSegura();
        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario->toArrayPublico();
        $_SESSION['autenticado_en'] = time();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    public static function cerrarSesion(): void {
        self::iniciarSesionSegura();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    public static function obtenerUsuarioActual(): array {
        self::iniciarSesionSegura();
        if (!empty($_SESSION['usuario'])) {
            return $_SESSION['usuario'];
        }
        return [
            'id'       => null,
            'nombre'   => 'Visitante Público',
            'username' => 'invitado',
            'email'    => '',
            'rol'      => Usuario::ROL_VISITANTE,
            'activo'   => true,
            'permisos' => Usuario::obtenerPermisosPorRol(Usuario::ROL_VISITANTE)
        ];
    }

    public static function obtenerCsrfToken(): string {
        self::iniciarSesionSegura();
        return $_SESSION['csrf_token'];
    }

    public static function validarCsrfToken(?string $tokenRecibido): void {
        self::iniciarSesionSegura();
        $tokenSesion = $_SESSION['csrf_token'] ?? '';
        if (empty($tokenRecibido) || !hash_equals($tokenSesion, $tokenRecibido)) {
            self::responderJson(['error' => 'Token de seguridad CSRF inválido o expirado. Recarga la página.'], 403);
        }
    }

    public static function exigirRoles(array $rolesPermitidos): array {
        self::iniciarSesionSegura();
        if (empty($_SESSION['usuario']) || empty($_SESSION['usuario']['id'])) {
            self::responderJson([
                'error' => 'Acceso no autorizado. Debes iniciar sesión para realizar esta operación.'
            ], 401);
        }

        $usuario = $_SESSION['usuario'];
        if (!in_array($usuario['rol'], $rolesPermitidos, true)) {
            self::responderJson([
                'error' => 'Permisos insuficientes en el servidor. Tu rol (' . $usuario['rol'] . ') no está autorizado para esta acción.'
            ], 403);
        }

        return $usuario;
    }

    public static function responderJson(array $payload, int $statusCode = 200): void {
        if (ob_get_length()) {
            ob_clean();
        }
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
