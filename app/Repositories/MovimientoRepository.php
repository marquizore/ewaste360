<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/Movimiento.php';

class MovimientoRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function obtenerTodos(?int $equipoId = null): array {
        $sql = "SELECT m.*, e.codigo_inventario, e.marca, e.modelo, u.nombre AS nombre_responsable
                FROM movimientos m
                INNER JOIN equipos e ON m.equipo_id = e.id
                INNER JOIN usuarios u ON m.responsable_id = u.id";
        $params = [];
        if ($equipoId !== null && $equipoId > 0) {
            $sql .= " WHERE m.equipo_id = :eid";
            $params[':eid'] = $equipoId;
        }
        $sql .= " ORDER BY m.fecha_movimiento DESC, m.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function crear(Movimiento $mov): int {
        $stmt = $this->db->prepare("
            INSERT INTO movimientos
            (equipo_id, tipo_movimiento, destino_documentado, gestor_verificado, observaciones, responsable_id)
            VALUES (:equipo_id, :tipo, :destino, :verificado, :obs, :resp)
        ");
        $stmt->execute([
            ':equipo_id'  => $mov->getEquipoId(),
            ':tipo'       => $mov->getTipoMovimiento(),
            ':destino'    => $mov->getDestinoDocumentado(),
            ':verificado' => $mov->isGestorVerificado() ? 1 : 0,
            ':obs'        => $mov->getObservaciones(),
            ':resp'       => $mov->getResponsableId()
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function contarEntregadosAGestorVerificado(): int {
        return (int)$this->db->query("
            SELECT COUNT(DISTINCT equipo_id)
            FROM movimientos
            WHERE tipo_movimiento = 'entrega_gestor' AND gestor_verificado = 1
        ")->fetchColumn();
    }
}
