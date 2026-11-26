<?php
class Diagnostico {
    private ?int $id;
    private int $equipoId;
    private string $fallaDetectada;
    private string $accionReparacion;
    private float $costoReparacion;
    private float $ahorroEstimado;
    private string $decisionFinal;
    private string $areaReasignada;
    private string $fechaDiagnostico;
    private int $responsableId;

    public function __construct(
        int $equipoId,
        string $fallaDetectada,
        string $accionReparacion,
        float $costoReparacion,
        float $ahorroEstimado,
        string $decisionFinal,
        string $areaReasignada,
        int $responsableId,
        ?string $fechaDiagnostico = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->equipoId = $equipoId;
        $this->fallaDetectada = htmlspecialchars(strip_tags(trim($fallaDetectada)));
        $this->accionReparacion = htmlspecialchars(strip_tags(trim($accionReparacion)));
        $this->costoReparacion = max(0, $costoReparacion);
        $this->ahorroEstimado = max(0, $ahorroEstimado);
        $this->decisionFinal = $decisionFinal;
        $this->areaReasignada = htmlspecialchars(strip_tags(trim($areaReasignada)));
        $this->responsableId = $responsableId;
        $this->fechaDiagnostico = $fechaDiagnostico ?: date('Y-m-d');
    }

    public function getId(): ?int { return $this->id; }
    public function getEquipoId(): int { return $this->equipoId; }
    public function getFallaDetectada(): string { return $this->fallaDetectada; }
    public function getAccionReparacion(): string { return $this->accionReparacion; }
    public function getCostoReparacion(): float { return $this->costoReparacion; }
    public function getAhorroEstimado(): float { return $this->ahorroEstimado; }
    public function getDecisionFinal(): string { return $this->decisionFinal; }
    public function getAreaReasignada(): string { return $this->areaReasignada; }
    public function getFechaDiagnostico(): string { return $this->fechaDiagnostico; }
    public function getResponsableId(): int { return $this->responsableId; }
}
