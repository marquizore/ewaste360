<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/Equipo.php';

class EquipoRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function obtenerTodos(?string $filtroEstado = null, ?string $busqueda = null): array {
        $sql = "SELECT e.*, u.nombre AS nombre_registrador
                FROM equipos e
                INNER JOIN usuarios u ON e.registrado_por = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($filtroEstado)) {
            $sql .= " AND e.estado_clasificacion = :estado";
            $params[':estado'] = $filtroEstado;
        }
        if (!empty($busqueda)) {
            $sql .= " AND (e.codigo_inventario LIKE :q1 OR e.marca LIKE :q2 OR e.modelo LIKE :q3 OR e.ubicacion_actual LIKE :q4)";
            $like = "%$busqueda%";
            $params[':q1'] = $like;
            $params[':q2'] = $like;
            $params[':q3'] = $like;
            $params[':q4'] = $like;
        }
        $sql .= " ORDER BY e.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obtenerPorId(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM equipos WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function crear(Equipo $equipo): int {
        $sql = "INSERT INTO equipos
                (codigo_inventario, categoria, marca, modelo, fecha_ingreso, condicion_general, estado_clasificacion, ubicacion_actual, registrado_por)
                VALUES (:codigo, :cat, :marca, :modelo, :fecha, :condicion, :estado, :ubicacion, :usuario)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':codigo'    => $equipo->getCodigoInventario(),
            ':cat'       => $equipo->getCategoria(),
            ':marca'     => $equipo->getMarca(),
            ':modelo'    => $equipo->getModelo(),
            ':fecha'     => $equipo->getFechaIngreso(),
            ':condicion' => $equipo->getCondicionGeneral(),
            ':estado'    => $equipo->getEstadoClasificacion(),
            ':ubicacion' => $equipo->getUbicacionActual(),
            ':usuario'   => $equipo->getRegistradoPor()
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizarParcial(int $id, array $campos): bool {
        $permitidos = ['estado_clasificacion', 'condicion_general', 'ubicacion_actual'];
        $sets = [];
        $params = [':id' => $id];

        foreach ($campos as $clave => $valor) {
            if (in_array($clave, $permitidos, true)) {
                $sets[] = "$clave = :$clave";
                $params[":$clave"] = htmlspecialchars(strip_tags(trim((string)$valor)));
            }
        }
        if (empty($sets)) {
            return false;
        }
        $sql = "UPDATE equipos SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function contarPorEstado(): array {
        return $this->db->query("
            SELECT estado_clasificacion, COUNT(*) AS total
            FROM equipos
            GROUP BY estado_clasificacion
        ")->fetchAll();
    }

    public function contarTotal(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM equipos")->fetchColumn();
    }
}
