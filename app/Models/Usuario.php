<?php
class Usuario {
    private ?int $id;
    private string $nombre;
    private string $username;
    private string $email;
    private string $passwordHash;
    private string $rol;
    private bool $activo;

    public const ROL_VISITANTE  = 'visitante';
    public const ROL_CAPTURISTA = 'capturista';
    public const ROL_AMBIENTAL  = 'responsable_ambiental';
    public const ROL_ADMIN      = 'administrador';

    public function __construct(
        string $nombre,
        string $username,
        string $email,
        string $passwordHash,
        string $rol = self::ROL_VISITANTE,
        bool $activo = true,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nombre = htmlspecialchars(strip_tags(trim($nombre)));
        $this->username = htmlspecialchars(strip_tags(trim($username)));
        $this->email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
        $this->passwordHash = $passwordHash;
        $this->rol = $rol;
        $this->activo = $activo;
    }

    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getUsername(): string { return $this->username; }
    public function getEmail(): string { return $this->email; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function getRol(): string { return $this->rol; }
    public function isActivo(): bool { return $this->activo; }

    public function verificarPassword(string $passwordPlano): bool {
        return password_verify($passwordPlano, $this->passwordHash);
    }

    public function toArrayPublico(): array {
        return [
            'id'       => $this->id,
            'nombre'   => $this->nombre,
            'username' => $this->username,
            'email'    => $this->email,
            'rol'      => $this->rol,
            'activo'   => $this->activo,
            'permisos' => self::obtenerPermisosPorRol($this->rol)
        ];
    }

    public static function obtenerPermisosPorRol(string $rol): array {
        switch ($rol) {
            case self::ROL_ADMIN:
                return [
                    'ver_publico'           => true,
                    'ver_inventario'        => true,
                    'ver_reparacion'        => true,
                    'registrar_equipos'     => true,
                    'actualizar_equipos'    => true,
                    'registrar_diagnostico' => true,
                    'registrar_movimientos' => true,
                    'gestionar_campanas'    => true,
                    'ver_reportes'          => true,
                    'gestionar_usuarios'    => true
                ];
            case self::ROL_AMBIENTAL:
                return [
                    'ver_publico'           => true,
                    'ver_inventario'        => true,
                    'ver_reparacion'        => true,
                    'registrar_equipos'     => false,
                    'actualizar_equipos'    => true,
                    'registrar_diagnostico' => true,
                    'registrar_movimientos' => true,
                    'gestionar_campanas'    => true,
                    'ver_reportes'          => true,
                    'gestionar_usuarios'    => false
                ];
            case self::ROL_CAPTURISTA:
                return [
                    'ver_publico'           => true,
                    'ver_inventario'        => true,
                    'ver_reparacion'        => false,
                    'registrar_equipos'     => true,
                    'actualizar_equipos'    => true,
                    'registrar_diagnostico' => false,
                    'registrar_movimientos' => false,
                    'gestionar_campanas'    => false,
                    'ver_reportes'          => false,
                    'gestionar_usuarios'    => false
                ];
            case self::ROL_VISITANTE:
            default:
                return [
                    'ver_publico'           => true,
                    'ver_inventario'        => false,
                    'ver_reparacion'        => false,
                    'registrar_equipos'     => false,
                    'actualizar_equipos'    => false,
                    'registrar_diagnostico' => false,
                    'registrar_movimientos' => false,
                    'gestionar_campanas'    => false,
                    'ver_reportes'          => false,
                    'gestionar_usuarios'    => false
                ];
        }
    }
}
