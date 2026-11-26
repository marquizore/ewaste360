<?php
class Movimiento {
    private ?int $id;
    private int $equipoId;
    private string $tipoMovimiento;
    private string $destinoDocumentado;
    private bool $gestorVerificado;
    private string $observaciones;
    private int $responsableId;

    public function __construct(
        int $equipoId,
        string $tipoMovimiento,
        string $destinoDocumentado,
        bool $gestorVerificado,
        string $observaciones,
        int $responsableId,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->equipoId = $equipoId;
        $this->tipoMovimiento = $tipoMovimiento;
        $this->destinoDocumentado = htmlspecialchars(strip_tags(trim($destinoDocumentado)));
        $this->gestorVerificado = $gestorVerificado;
        $this->observaciones = htmlspecialchars(strip_tags(trim($observaciones)));
        $this->responsableId = $responsableId;
    }

    public function getId(): ?int { return $this->id; }
    public function getEquipoId(): int { return $this->equipoId; }
    public function getTipoMovimiento(): string { return $this->tipoMovimiento; }
    public function getDestinoDocumentado(): string { return $this->destinoDocumentado; }
    public function isGestorVerificado(): bool { return $this->gestorVerificado; }
    public function getObservaciones(): string { return $this->observaciones; }
    public function getResponsableId(): int { return $this->responsableId; }
}
