<?php

namespace App\Traits;

trait RiskMatrixTrait
{
    protected function getControlEvaluation()
    {
        $matriz = [];

        $matriz['Automático']['SI']['Total']['Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['Parcialmente Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['No Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['Documentado']['NO'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['Parcialmente Documentado']['NO'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['No Documentado']['NO'] = 'Altamente Efectivo';
        $matriz['Automático']['NO']['Total']['Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['NO']['Total']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['NO']['Total']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['NO']['Total']['Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['SI']['Parcial']['Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['SI']['Parcial']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['SI']['Parcial']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['NO']['Parcial']['Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['NO']['Total']['No Documentado']['NO'] = 'Efectivo';
        $matriz['Automático']['SI']['Parcial']['Documentado']['NO'] = 'Efectivo';
        $matriz['Mixto']['NO']['Total']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['NO']['Total']['No Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Total']['Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['No Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Parcial']['Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['NO']['Total']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Total']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Total']['Documentado']['NO'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['Parcialmente Documentado']['NO'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['No Documentado']['NO'] = 'Efectivo';
        $matriz['Mixto']['NO']['Total']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Total']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['SI']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['SI']['Parcial']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Total']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Parcial']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Manual']['SI']['Total']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Total']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Manual']['NO']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Total']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Manual']['NO']['Total']['Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Total']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Automático']['SI']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['SI']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['SI']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['SI']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['SI']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['SI']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['NO']['Parcial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['NO']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['NO']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Total']['No Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Parcial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Parcial']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Parcial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['SI']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['SI']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['NO']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['SI']['Total']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Total']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Total']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Total']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['SI']['Inmaterial']['Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['SI']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['SI']['Inmaterial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Mixto']['SI']['Inmaterial']['Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Parcial']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['NO']['Parcial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Parcial']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Total']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['NO']['Parcial']['Parcialmente Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['NO']['Parcial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Inmaterial']['No Documentado']['SI'] = 'Sin Mitigación';
        $matriz['Mixto']['SI']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Mixto']['SI']['Inmaterial']['No Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Mixto']['NO']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Mixto']['NO']['Inmaterial']['No Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['NO']['Inmaterial']['Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['NO']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['SI']['Inmaterial']['No Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['NO']['Inmaterial']['No Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['SI']['Inmaterial']['Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Mixto']['NO']['Inmaterial']['Documentado']['NO'] = 'Sin Mitigación';

        return $matriz;
    }

    public function getDescriptionImpactsFrequency()
    {
        $data = [];

        $data['impact'][1] = 'No Significativo';
        $data['impact'][2] = 'Leve';
        $data['impact'][3] = 'Moderado';
        $data['impact'][4] = 'Grave';
        $data['impact'][5] = 'Extremo';
        $data['frecuency'][1] = 'Muy Bajo';
        $data['frecuency'][2] = 'Bajo';
        $data['frecuency'][3] = 'Moderada';
        $data['frecuency'][4] = 'Alta';
        $data['frecuency'][5] = 'Muy Alta';
        
        return $data;
    }

    public function percentageMitigation()
    {
        $data = [];

        $data['Altamente Efectivo'] = 80;
        $data['Efectivo'] = 60;
        $data['Medianamente Efectivo'] = 45;
        $data['Inefectivo'] = 20;
        $data['Sin Mitigación'] = 0;
        
        return $data;
    }
}