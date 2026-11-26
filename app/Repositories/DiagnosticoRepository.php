<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/Diagnostico.php';

class DiagnosticoRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function obtenerTodos(): array {
        return $this->db->query("
            SELECT d.*, e.codigo_inventario, e.marca, e.modelo, u.nombre AS nombre_responsable
            FROM diagnosticos d
            INNER JOIN equipos e ON d.equipo_id = e.id
            INNER JOIN usuarios u ON d.responsable_id = u.id
            ORDER BY d.id DESC
        ")->fetchAll();
    }

    public function crear(Diagnostico $diag): int {
        $stmt = $this->db->prepare("
            INSERT INTO diagnosticos
            (equipo_id, falla_detectada, accion_reparacion, costo_reparacion, ahorro_estimado, decision_final, area_reasignada, fecha_diagnostico, responsable_id)
            VALUES (:equipo_id, :falla, :accion, :costo, :ahorro, :decision, :area, :fecha, :resp)
        ");
        $stmt->execute([
            ':equipo_id' => $diag->getEquipoId(),
            ':falla'     => $diag->getFallaDetectada(),
            ':accion'    => $diag->getAccionReparacion(),
            ':costo'     => $diag->getCostoReparacion(),
            ':ahorro'    => $diag->getAhorroEstimado(),
            ':decision'  => $diag->getDecisionFinal(),
            ':area'      => $diag->getAreaReasignada(),
            ':fecha'     => $diag->getFechaDiagnostico(),
            ':resp'      => $diag->getResponsableId()
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function obtenerMetricasEconomicas(): array {
        return $this->db->query("
            SELECT
                COUNT(*) AS total_diagnosticos,
                SUM(CASE WHEN decision_final = 'reutilizar' THEN 1 ELSE 0 END) AS total_reutilizados,
                COALESCE(SUM(costo_reparacion), 0) AS costo_total,
                COALESCE(SUM(ahorro_estimado), 0) AS ahorro_total
            FROM diagnosticos
        ")->fetch();
    }
}
