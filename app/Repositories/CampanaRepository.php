<?php
require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../Models/Campana.php';
require_once __DIR__ . '/../Models/Participacion.php';

class CampanaRepository {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function obtenerTodasConResultados(): array {
        return $this->db->query("
            SELECT c.id, c.nombre, c.descripcion, c.fecha_inicio, c.fecha_fin,
                   c.ubicacion, c.equipos_aceptados, c.estado,
                   COUNT(p.id) AS participantes,
                   COALESCE(SUM(p.cantidad_unidades), 0) AS unidades_acopiadas
            FROM campanas c
            LEFT JOIN participaciones p ON c.id = p.campana_id
            GROUP BY c.id
            ORDER BY c.fecha_inicio DESC
        ")->fetchAll();
    }

    public function obtenerParticipacionesRecientes(): array {
        return $this->db->query("
            SELECT p.*, c.nombre AS nombre_campana
            FROM participaciones p
            INNER JOIN campanas c ON p.campana_id = c.id
            ORDER BY p.fecha_registro DESC
            LIMIT 25
        ")->fetchAll();
    }

    public function crearCampana(Campana $campana): int {
        $stmt = $this->db->prepare("
            INSERT INTO campanas
            (nombre, descripcion, fecha_inicio, fecha_fin, ubicacion, equipos_aceptados, estado, creado_por)
            VALUES (:nombre, :desc, :inicio, :fin, :ubic, :aceptados, :estado, :creado_por)
        ");
        $stmt->execute([
            ':nombre'     => $campana->getNombre(),
            ':desc'       => $campana->getDescripcion(),
            ':inicio'     => $campana->getFechaInicio(),
            ':fin'        => $campana->getFechaFin(),
            ':ubic'       => $campana->getUbicacion(),
            ':aceptados'  => $campana->getEquiposAceptados(),
            ':estado'     => $campana->getEstado(),
            ':creado_por' => $campana->getCreadoPor()
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function registrarParticipacion(Participacion $part): int {
        $stmt = $this->db->prepare("
            INSERT INTO participaciones
            (campana_id, usuario_id, nombre_participante, tipo_residuo_entregado, cantidad_unidades)
            VALUES (:cid, :uid, :nombre, :residuo, :cant)
        ");
        $stmt->execute([
            ':cid'     => $part->getCampanaId(),
            ':uid'     => $part->getUsuarioId(),
            ':nombre'  => $part->getNombreParticipante(),
            ':residuo' => $part->getTipoResiduoEntregado(),
            ':cant'    => $part->getCantidadUnidades()
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function contarTotalParticipaciones(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM participaciones")->fetchColumn();
    }
}
