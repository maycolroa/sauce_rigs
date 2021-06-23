<?php

namespace App\Traits;

trait DangerMatrixTrait
{
    protected function getAllMatrixCalification()
    {
        $matriz = [];

        $matriz['Automático']['Si']['Total']['Documentado']['Si'] = ['Altamente Efectivo'];
        $matriz['Automático']['Si']['Total']['Parcialmente Documentado']['Si'] = ['Altamente Efectivo'];
        $matriz['Automático']['Si']['Total']['No Documentado']['Si'] = ['Altamente Efectivo'];
        $matriz['Automático']['Si']['Total']['Documentado']['No'] = ['Altamente Efectivo'];
        $matriz['Automático']['Si']['Total']['Parcialmente Documentado']['No'] = ['Altamente Efectivo'];
        $matriz['Automático']['Si']['Total']['No Documentado']['No'] = ['Altamente Efectivo'];
        $matriz['Automático']['No']['Total']['Documentado']['Si'] = ['Altamente Efectivo'];
        $matriz['Automático']['No']['Total']['Parcialmente Documentado']['Si'] = ['Efectivo'];
        $matriz['Mixto']['Si']['Total']['Documentado']['Si'] = ['Efectivo'];
        $matriz['Automático']['No']['Total']['No Documentado']['Si'] = ['Efectivo'];
        $matriz['Mixto']['No']['Total']['Documentado']['Si'] = ['Efectivo'];
        $matriz['Automático']['Si']['Parcial']['Documentado']['Si'] = ['Efectivo'];
        $matriz['Mixto']['Si']['Total']['Parcialmente Documentado']['Si'] = ['Efectivo'];
        $matriz['Automático']['Si']['Parcial']['Parcialmente Documentado']['Si'] = ['Efectivo'];
        $matriz['Automático']['Si']['Parcial']['No Documentado']['Si'] = ['Efectivo'];
        $matriz['Mixto']['Si']['Total']['No Documentado']['Si'] = ['Efectivo'];
        $matriz['Automático']['No']['Parcial']['Documentado']['Si'] = ['Efectivo'];
        $matriz['Mixto']['Si']['Parcial']['Documentado']['Si'] = ['Efectivo'];
        $matriz['Automático']['No']['Total']['No Documentado']['No'] = ['Efectivo'];
        $matriz['Automático']['Si']['Parcial']['Documentado']['No'] = ['Efectivo'];
        $matriz['Mixto']['No']['Total']['Parcialmente Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Automático']['No']['Parcial']['Parcialmente Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['Si']['Parcial']['Parcialmente Documentado']['Si'] = ['Efectivo'];
        $matriz['Mixto']['No']['Total']['No Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['No']['Parcial']['Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['No']['Parcial']['Parcialmente Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Manual']['Si']['Total']['Documentado']['Si'] = ['Altamente Efectivo'];
        $matriz['Automático']['No']['Parcial']['No Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Manual']['Si']['Parcial']['Documentado']['Si'] = ['Efectivo'];
        $matriz['Automático']['No']['Total']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Automático']['No']['Total']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['Si']['Total']['Documentado']['No'] = ['Efectivo'];
        $matriz['Mixto']['Si']['Total']['Parcialmente Documentado']['No'] = ['Efectivo'];
        $matriz['Mixto']['Si']['Total']['No Documentado']['No'] = ['Efectivo'];
        $matriz['Mixto']['No']['Total']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['No']['Total']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Automático']['No']['Parcial']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Automático']['No']['Parcial']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Automático']['Si']['Parcial']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Automático']['Si']['Parcial']['No Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['Si']['Parcial']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['Si']['Parcial']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['Si']['Parcial']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['No']['Total']['No Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['No']['Parcial']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['No']['Parcial']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['No']['Parcial']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Manual']['Si']['Parcial']['No Documentado']['Si'] = ['Efectivo'];
        $matriz['Manual']['Si']['Total']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Manual']['No']['Total']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['Si']['Parcial']['No Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Mixto']['Si']['Parcial']['No Documentado']['Si'] = ['Efectivo'];
        $matriz['Manual']['No']['Parcial']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Manual']['Si']['Total']['No Documentado']['Si'] = ['Efectivo'];
        $matriz['Manual']['No']['Total']['Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Manual']['No']['Total']['Parcialmente Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Automático']['Si']['Inmaterial']['Documentado']['Si'] = ['Inefectivo'];
        $matriz['Automático']['Si']['Inmaterial']['Parcialmente Documentado']['Si'] = ['Inefectivo'];
        $matriz['Automático']['Si']['Inmaterial']['No Documentado']['Si'] = ['Inefectivo'];
        $matriz['Automático']['No']['Inmaterial']['Documentado']['Si'] = ['Inefectivo'];
        $matriz['Automático']['No']['Inmaterial']['Parcialmente Documentado']['Si'] = ['Inefectivo'];
        $matriz['Automático']['No']['Inmaterial']['No Documentado']['Si'] = ['Inefectivo'];
        $matriz['Mixto']['Si']['Inmaterial']['Documentado']['Si'] = ['Inefectivo'];
        $matriz['Mixto']['Si']['Inmaterial']['Parcialmente Documentado']['Si'] = ['Inefectivo'];
        $matriz['Mixto']['Si']['Inmaterial']['No Documentado']['Si'] = ['Inefectivo'];
        $matriz['Mixto']['No']['Parcial']['No Documentado']['Si'] = ['Inefectivo'];
        $matriz['Mixto']['No']['Inmaterial']['Parcialmente Documentado']['Si'] = ['Inefectivo'];
        $matriz['Mixto']['No']['Inmaterial']['No Documentado']['Si'] = ['Inefectivo'];
        $matriz['Manual']['No']['Total']['No Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Manual']['No']['Parcial']['Documentado']['Si'] = ['Inefectivo'];
        $matriz['Manual']['No']['Parcial']['Parcialmente Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Manual']['No']['Parcial']['No Documentado']['Si'] = ['Inefectivo'];
        $matriz['Manual']['No']['Inmaterial']['Documentado']['Si'] = ['Inefectivo'];
        $matriz['Manual']['No']['Inmaterial']['Parcialmente Documentado']['Si'] = ['Inefectivo'];
        $matriz['Manual']['Si']['Inmaterial']['Documentado']['Si'] = ['Inefectivo'];
        $matriz['Manual']['Si']['Inmaterial']['Parcialmente Documentado']['Si'] = ['Inefectivo'];
        $matriz['Mixto']['No']['Inmaterial']['Documentado']['Si'] = ['Inefectivo'];
        $matriz['Manual']['Si']['Total']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Manual']['Si']['Total']['Parcialmente Documentado']['Si'] = ['Medianamente Efectivo'];
        $matriz['Manual']['Si']['Total']['No Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Manual']['No']['Total']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Automático']['No']['Parcial']['No Documentado']['No'] = ['Inefectivo'];
        $matriz['Manual']['Si']['Parcial']['Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Manual']['Si']['Parcial']['Parcialmente Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Automático']['Si']['Inmaterial']['Documentado']['No'] = ['Inefectivo'];
        $matriz['Automático']['Si']['Inmaterial']['Parcialmente Documentado']['No'] = ['Inefectivo'];
        $matriz['Automático']['Si']['Inmaterial']['No Documentado']['No'] = ['Inefectivo'];
        $matriz['Automático']['No']['Inmaterial']['Documentado']['No'] = ['Inefectivo'];
        $matriz['Automático']['No']['Inmaterial']['Parcialmente Documentado']['No'] = ['Inefectivo'];
        $matriz['Automático']['No']['Inmaterial']['No Documentado']['No'] = ['Inefectivo'];
        $matriz['Mixto']['Si']['Inmaterial']['Documentado']['No'] = ['Inefectivo'];
        $matriz['Manual']['Si']['Parcial']['Parcialmente Documentado']['Si'] = ['Efectivo'];
        $matriz['Mixto']['No']['Parcial']['No Documentado']['No'] = ['Inefectivo'];
        $matriz['Manual']['Si']['Parcial']['No Documentado']['No'] = ['Medianamente Efectivo'];
        $matriz['Manual']['No']['Total']['No Documentado']['No'] = ['Inefectivo'];
        $matriz['Manual']['No']['Parcial']['Parcialmente Documentado']['No'] = ['Inefectivo'];
        $matriz['Manual']['No']['Parcial']['No Documentado']['No'] = ['Inefectivo'];
        $matriz['Manual']['Si']['Inmaterial']['No Documentado']['Si'] = ['Inefectivo'];
        $matriz['Manual']['No']['Inmaterial']['No Documentado']['Si'] = ['Sin Mitigación'];
        $matriz['Mixto']['Si']['Inmaterial']['Parcialmente Documentado']['No'] = ['Sin Mitigación'];
        $matriz['Mixto']['Si']['Inmaterial']['No Documentado']['No'] = ['Sin Mitigación'];
        $matriz['Mixto']['No']['Inmaterial']['Parcialmente Documentado']['No'] = ['Sin Mitigación'];
        $matriz['Mixto']['No']['Inmaterial']['No Documentado']['No'] = ['Sin Mitigación'];
        $matriz['Manual']['No']['Inmaterial']['Documentado']['No'] = ['Sin Mitigación'];
        $matriz['Manual']['No']['Inmaterial']['Parcialmente Documentado']['No'] = ['Sin Mitigación'];
        $matriz['Manual']['Si']['Inmaterial']['No Documentado']['No'] = ['Sin Mitigación'];
        $matriz['Manual']['No']['Inmaterial']['No Documentado']['No'] = ['Sin Mitigación'];
        $matriz['Manual']['Si']['Inmaterial']['Documentado']['No'] = ['Inefectivo'];
        $matriz['Manual']['Si']['Inmaterial']['Parcialmente Documentado']['No'] = ['Sin Mitigación'];
        $matriz['Mixto']['No']['Inmaterial']['Documentado']['No'] = ['Sin Mitigación'];

        return $matriz;
    }

    protected function getRulesDmImport($type, $ref = null)
    {
        $rules = [];

        if ($type == 'Tipo 1')
        {
            $rules = [
                'Nivel de Probabilidad' => [
                    'No ha ocurrido en el sector hospitalario' => 'No ha ocurrido en el sector hospitalario',
                    'Ha ocurrido en el sector hospitalario' => 'Ha ocurrido en el sector hospitalario',
                    'Alguna vez ha ocurrido en el hospital en el último año' => 'Alguna vez ha ocurrido en el hospital en el último año',
                    'Sucede varias veces en el último año y en diferentes procesos (en el hospital)' => 'Sucede varias veces en el último año y en diferentes procesos (en el hospital)',
                    'Sucede varias veces en el último año en el mismo proceso' => 'Sucede varias veces en el último año en el mismo proceso'
                ],
                'NR Personas' => [
                    'No ha ocurrido en el sector hospitalario' => [1,2,3,4,8],
                    'Ha ocurrido en el sector hospitalario' => [2,3,6,8,10],
                    'Alguna vez ha ocurrido en el hospital en el último año' => [3,6,9,12,15],
                    'Sucede varias veces en el último año y en diferentes procesos (en el hospital)' => [4,7,12,16,20],
                    'Sucede varias veces en el último año en el mismo proceso' => [5,10,15,20,25]
                ],
                'NR Económico' => [
                    'No ha ocurrido en el sector hospitalario' => [1,2,3,4,8],
                    'Ha ocurrido en el sector hospitalario' => [2,3,6,8,10],
                    'Alguna vez ha ocurrido en el hospital en el último año' => [3,6,9,12,15],
                    'Sucede varias veces en el último año y en diferentes procesos (en el hospital)' => [4,7,12,16,20],
                    'Sucede varias veces en el último año en el mismo proceso' => [5,10,15,20,25]
                ],
                'NR Imagen' => [
                    'No ha ocurrido en el sector hospitalario' => [1,2,3,4,8],
                    'Ha ocurrido en el sector hospitalario' => [2,3,6,8,10],
                    'Alguna vez ha ocurrido en el hospital en el último año' => [3,6,9,12,15],
                    'Sucede varias veces en el último año y en diferentes procesos (en el hospital)' => [4,7,12,16,20],
                    'Sucede varias veces en el último año en el mismo proceso' => [5,10,15,20,25]
                ],
            ];

            if ($ref)
            {
                return ïsset($rules[$ref]) ? $rules[$ref] : [];
            }
        }
        else if ($type == 'Tipo 2')
        {
            $rules = [
                'Frecuencia' => [
                    'RECURRENTE' => 'RECURRENTE',
                    'FRECUENTE' => 'FRECUENTE',
                    'POSIBLE' => 'POSIBLE',
                    'REMOTO' => 'REMOTO'
                ],
                'Severidad' => [
                    'MENOR' => 'MENOR',
                    'LEVE' => 'LEVE',
                    'GRAVE' => 'GRAVE',
                    'CATASTRÓFICA' => 'CATASTRóFICA'
                ]
            ];

            if ($ref)
            {
                return ïsset($rules[$ref]) ? $rules[$ref] : [];
            }
        }

        return $rules;
    }
}