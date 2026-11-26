<?php
require_once __DIR__ . '/Equipo.php';

/**
 * PATRÓN DE DISEÑO FACTORY:
 * Encapsula la creación de instancias de Equipo asignando ubicación inicial
 * y clasificación sugerida según la categoría del dispositivo RAEE.
 */
class EquipoFactory {
    public static function crearDesdeSolicitud(array $input, int $usuarioId): Equipo {
        $categoria = $input['categoria'] ?? 'Computadora';

        $ubicacionPorDefecto = match ($categoria) {
            'Bateria_Cable' => 'Contenedor de Residuos Especiales',
            'Impresora'     => 'Almacén Temporal Periféricos',
            'Computadora', 'Laptop', 'Monitor' => 'Taller de Evaluación de Cómputo',
            default         => 'Almacén Temporal RAEE'
        };

        $estadoPorDefecto = ($categoria === 'Bateria_Cable') ? 'gestion_especializada' : 'reparable';

        return new Equipo(
            $input['codigo_inventario'] ?? '',
            $categoria,
            $input['marca'] ?? '',
            $input['modelo'] ?? '',
            !empty($input['fecha_ingreso']) ? $input['fecha_ingreso'] : date('Y-m-d'),
            !empty($input['condicion_general']) ? $input['condicion_general'] : 'En revisión inicial',
            !empty($input['estado_clasificacion']) ? $input['estado_clasificacion'] : $estadoPorDefecto,
            !empty($input['ubicacion_actual']) ? $input['ubicacion_actual'] : $ubicacionPorDefecto,
            $usuarioId
        );
    }
}
