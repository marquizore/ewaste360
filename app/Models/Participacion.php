<?php
class Participacion {
    private ?int $id;
    private int $campanaId;
    private ?int $usuarioId;
    private string $nombreParticipante;
    private string $tipoResiduoEntregado;
    private int $cantidadUnidades;

    public function __construct(
        int $campanaId,
        string $nombreParticipante,
        string $tipoResiduoEntregado,
        int $cantidadUnidades = 1,
        ?int $usuarioId = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->campanaId = $campanaId;
        $this->usuarioId = $usuarioId;
        $this->nombreParticipante = htmlspecialchars(strip_tags(trim($nombreParticipante)));
        $this->tipoResiduoEntregado = htmlspecialchars(strip_tags(trim($tipoResiduoEntregado)));
        $this->cantidadUnidades = max(1, $cantidadUnidades);
    }

    public function getId(): ?int { return $this->id; }
    public function getCampanaId(): int { return $this->campanaId; }
    public function getUsuarioId(): ?int { return $this->usuarioId; }
    public function getNombreParticipante(): string { return $this->nombreParticipante; }
    public function getTipoResiduoEntregado(): string { return $this->tipoResiduoEntregado; }
    public function getCantidadUnidades(): int { return $this->cantidadUnidades; }
}
