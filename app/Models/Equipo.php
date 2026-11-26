<?php
class Equipo {
    private ?int $id;
    private string $codigoInventario;
    private string $categoria;
    private string $marca;
    private string $modelo;
    private string $fechaIngreso;
    private string $condicionGeneral;
    private string $estadoClasificacion;
    private string $ubicacionActual;
    private int $registradoPor;

    public function __construct(
        string $codigoInventario,
        string $categoria,
        string $marca,
        string $modelo,
        string $fechaIngreso,
        string $condicionGeneral,
        string $estadoClasificacion,
        string $ubicacionActual = 'Almacén Temporal RAEE',
        int $registradoPor = 1,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->codigoInventario = htmlspecialchars(strip_tags(trim($codigoInventario)));
        $this->categoria = htmlspecialchars(strip_tags(trim($categoria)));
        $this->marca = htmlspecialchars(strip_tags(trim($marca)));
        $this->modelo = htmlspecialchars(strip_tags(trim($modelo)));
        $this->fechaIngreso = $fechaIngreso;
        $this->condicionGeneral = htmlspecialchars(strip_tags(trim($condicionGeneral)));
        $this->estadoClasificacion = $estadoClasificacion;
        $this->ubicacionActual = htmlspecialchars(strip_tags(trim($ubicacionActual)));
        $this->registradoPor = $registradoPor;
    }

    public function getId(): ?int { return $this->id; }
    public function getCodigoInventario(): string { return $this->codigoInventario; }
    public function getCategoria(): string { return $this->categoria; }
    public function getMarca(): string { return $this->marca; }
    public function getModelo(): string { return $this->modelo; }
    public function getFechaIngreso(): string { return $this->fechaIngreso; }
    public function getCondicionGeneral(): string { return $this->condicionGeneral; }
    public function getEstadoClasificacion(): string { return $this->estadoClasificacion; }
    public function getUbicacionActual(): string { return $this->ubicacionActual; }
    public function getRegistradoPor(): int { return $this->registradoPor; }
}
