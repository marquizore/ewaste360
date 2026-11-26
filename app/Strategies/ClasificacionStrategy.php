<?php
/**
 * PATRÓN DE DISEÑO STRATEGY:
 * Encapsula las estrategias de evaluación sustentable para decidir
 * si un equipo debe repararse, reutilizarse o enviarse a gestión especializada.
 */
interface DestinoStrategyInterface {
    public function evaluarDestino(array $datosTecnicos): array;
}

class ReparacionStrategy implements DestinoStrategyInterface {
    public function evaluarDestino(array $datosTecnicos): array {
        $costo = (float)($datosTecnicos['costo_reparacion'] ?? 0);
        $valorReposicion = (float)($datosTecnicos['valor_reposicion'] ?? 5500);
        $ahorro = max(0, $valorReposicion - $costo);
        return [
            'estado_clasificacion' => 'reparable',
            'decision_sugerida'    => 'reutilizar',
            'ahorro_estimado'      => $ahorro,
            'recomendacion'        => 'Viable para mantenimiento correctivo y reasignación interna.'
        ];
    }
}

class ReutilizacionDirectaStrategy implements DestinoStrategyInterface {
    public function evaluarDestino(array $datosTecnicos): array {
        $valorReposicion = (float)($datosTecnicos['valor_reposicion'] ?? 4200);
        return [
            'estado_clasificacion' => 'reutilizable',
            'decision_sugerida'    => 'reutilizar',
            'ahorro_estimado'      => $valorReposicion,
            'recomendacion'        => 'Equipo funcional listo para reasignarse a laboratorio o departamento.'
        ];
    }
}

class GestionEspecializadaStrategy implements DestinoStrategyInterface {
    public function evaluarDestino(array $datosTecnicos): array {
        return [
            'estado_clasificacion' => 'gestion_especializada',
            'decision_sugerida'    => 'gestion_especializada',
            'ahorro_estimado'      => (float)($datosTecnicos['valor_componentes'] ?? 350),
            'recomendacion'        => 'Fin de vida útil. Recuperar piezas útiles y entregar a gestor RAEE verificado.'
        ];
    }
}

class EvaluadorSustentableContext {
    private DestinoStrategyInterface $strategy;

    public function setStrategy(DestinoStrategyInterface $strategy): void {
        $this->strategy = $strategy;
    }

    public function ejecutarEvaluacion(array $datosTecnicos): array {
        return $this->strategy->evaluarDestino($datosTecnicos);
    }
}
