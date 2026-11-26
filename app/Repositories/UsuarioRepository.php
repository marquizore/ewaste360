<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/Usuario.php';

class UsuarioRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function buscarPorUsernameOEmail(string $identificador): ?Usuario {
        $stmt = $this->db->prepare("
            SELECT * FROM usuarios
            WHERE (username = :id1 OR email = :id2)
            LIMIT 1
        ");
        $stmt->execute([
            ':id1' => trim($identificador),
            ':id2' => trim($identificador)
        ]);
        $row = $stmt->fetch();
        if (!$row) {
            return null;
        }
        return new Usuario(
            $row['nombre'],
            $row['username'],
            $row['email'],
            $row['password_hash'],
            $row['rol'],
            (bool)$row['activo'],
            (int)$row['id']
        );
    }

    public function listarTodos(): array {
        return $this->db->query("
            SELECT id, nombre, username, email, rol, activo, creado_en
            FROM usuarios
            ORDER BY id ASC
        ")->fetchAll();
    }

    public function crearUsuario(string $nombre, string $username, string $email, string $passwordPlano, string $rol): int {
        $hash = password_hash($passwordPlano, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (nombre, username, email, password_hash, rol, activo)
            VALUES (:nombre, :username, :email, :hash, :rol, 1)
        ");
        $stmt->execute([
            ':nombre'   => htmlspecialchars(strip_tags(trim($nombre))),
            ':username' => htmlspecialchars(strip_tags(trim($username))),
            ':email'    => filter_var(trim($email), FILTER_SANITIZE_EMAIL),
            ':hash'     => $hash,
            ':rol'      => $rol
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizarRolYEstado(int $id, string $rol, int $activo): bool {
        $rolesValidos = ['visitante', 'capturista', 'responsable_ambiental', 'administrador'];
        if (!in_array($rol, $rolesValidos, true)) {
            return false;
        }
        $stmt = $this->db->prepare("
            UPDATE usuarios
            SET rol = :rol, activo = :activo
            WHERE id = :id
        ");
        return $stmt->execute([
            ':rol'    => $rol,
            ':activo' => $activo ? 1 : 0,
            ':id'     => $id
        ]);
    }
}
