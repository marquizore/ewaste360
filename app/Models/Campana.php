<?php
class Campana {
    private ?int $id;
    private string $nombre;
    private string $descripcion;
    private string $fechaInicio;
    private string $fechaFin;
    private string $ubicacion;
    private string $equiposAceptados;
    private string $estado;
    private int $creadoPor;

    public function __construct(
        string $nombre,
        string $descripcion,
        string $fechaInicio,
        string $fechaFin,
        string $ubicacion,
        string $equiposAceptados,
        string $estado,
        int $creadoPor,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->nombre = htmlspecialchars(strip_tags(trim($nombre)));
        $this->descripcion = htmlspecialchars(strip_tags(trim($descripcion)));
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->ubicacion = htmlspecialchars(strip_tags(trim($ubicacion)));
        $this->equiposAceptados = htmlspecialchars(strip_tags(trim($equiposAceptados)));
        $this->estado = $estado;
        $this->creadoPor = $creadoPor;
    }

    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getDescripcion(): string { return $this->descripcion; }
    public function getFechaInicio(): string { return $this->fechaInicio; }
    public function getFechaFin(): string { return $this->fechaFin; }
    public function getUbicacion(): string { return $this->ubicacion; }
    public function getEquiposAceptados(): string { return $this->equiposAceptados; }
    public function getEstado(): string { return $this->estado; }
    public function getCreadoPor(): int { return $this->creadoPor; }
}
