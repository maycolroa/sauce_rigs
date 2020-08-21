<?php

namespace App\Traits;

trait DangerMatrixTrait
{
    protected function getDefaultCalificationDm()
    {
        return 'Tipo 1';
    }
    
    /**
     * returns the grade matrix
     *
     * @param String $type
     * @return Array
     */
    protected function getMatrixCalification($type)
    {
        $matriz = [];
        $all_matriz = $this->getAllMatrixCalification();

        if ($type == 'Tipo 1' && isset($all_matriz['Tipo 1']))
        {
            $matriz = $all_matriz['Tipo 1'];
        }
        else if ($type == 'Tipo 2' && isset($all_matriz['Tipo 2']))
        {
            $matriz = $all_matriz['Tipo 2'];
        }

        return $matriz;
    }

    protected function getAllMatrixCalification()
    {
        $matriz = [];

        //Tipo 1
        
        $matriz['Tipo 1']['No ha ocurrido en el sector hospitalario']['8'] = ['label' => 'Medio-8', 'color' => 'orange', 'count' => 0];
        $matriz['Tipo 1']['No ha ocurrido en el sector hospitalario']['4'] = ['label' => 'Bajo-4', 'color' => 'warning', 'count' => 0];
        $matriz['Tipo 1']['No ha ocurrido en el sector hospitalario']['3'] = ['label' => 'Trivial-3', 'color' => 'success', 'count' => 0];
        $matriz['Tipo 1']['No ha ocurrido en el sector hospitalario']['2'] = ['label' => 'Trivial-2', 'color' => 'success', 'count' => 0];
        $matriz['Tipo 1']['No ha ocurrido en el sector hospitalario']['1'] = ['label' => 'Trivial-1', 'color' => 'success', 'count' => 0];

        $matriz['Tipo 1']['Ha ocurrido en el sector hospitalario']['10'] = ['label' => 'Medio-10', 'color' => 'orange', 'count' => 0];
        $matriz['Tipo 1']['Ha ocurrido en el sector hospitalario']['8'] = ['label' => 'Medio-8', 'color' => 'orange', 'count' => 0];
        $matriz['Tipo 1']['Ha ocurrido en el sector hospitalario']['6'] = ['label' => 'Bajo-6', 'color' => 'warning', 'count' => 0];
        $matriz['Tipo 1']['Ha ocurrido en el sector hospitalario']['3'] = ['label' => 'Trivial-3', 'color' => 'success', 'count' => 0];
        $matriz['Tipo 1']['Ha ocurrido en el sector hospitalario']['2'] = ['label' => 'Trivial-2', 'color' => 'success', 'count' => 0];

        $matriz['Tipo 1']['Alguna vez ha ocurrido en el hospital en el último año']['15'] = ['label' => 'Alto-15', 'color' => 'primary', 'count' => 0];
        $matriz['Tipo 1']['Alguna vez ha ocurrido en el hospital en el último año']['12'] = ['label' => 'Medio-12', 'color' => 'orange', 'count' => 0];
        $matriz['Tipo 1']['Alguna vez ha ocurrido en el hospital en el último año']['9'] = ['label' => 'Medio-9', 'color' => 'orange', 'count' => 0];
        $matriz['Tipo 1']['Alguna vez ha ocurrido en el hospital en el último año']['6'] = ['label' => 'Bajo-6', 'color' => 'warning', 'count' => 0];
        $matriz['Tipo 1']['Alguna vez ha ocurrido en el hospital en el último año']['3'] = ['label' => 'Trivial-3', 'color' => 'success', 'count' => 0];

        $matriz['Tipo 1']['Sucede varias veces en el último año y en diferentes procesos (en el hospital)']['20'] = ['label' => 'Alto-20', 'color' => 'primary', 'count' => 0];
        $matriz['Tipo 1']['Sucede varias veces en el último año y en diferentes procesos (en el hospital)']['16'] = ['label' => 'Alto-16', 'color' => 'primary', 'count' => 0];
        $matriz['Tipo 1']['Sucede varias veces en el último año y en diferentes procesos (en el hospital)']['12'] = ['label' => 'Medio-12', 'color' => 'orange', 'count' => 0];
        $matriz['Tipo 1']['Sucede varias veces en el último año y en diferentes procesos (en el hospital)']['7'] = ['label' => 'Bajo-7', 'color' => 'warning', 'count' => 0];
        $matriz['Tipo 1']['Sucede varias veces en el último año y en diferentes procesos (en el hospital)']['4'] = ['label' => 'Bajo-4', 'color' => 'warning', 'count' => 0];

        $matriz['Tipo 1']['Sucede varias veces en el último año en el mismo proceso']['25'] = ['label' => 'Muy Alto-25', 'color' => 'purple', 'count' => 0];
        $matriz['Tipo 1']['Sucede varias veces en el último año en el mismo proceso']['20'] = ['label' => 'Alto-20', 'color' => 'primary', 'count' => 0];
        $matriz['Tipo 1']['Sucede varias veces en el último año en el mismo proceso']['15'] = ['label' => 'Alto-15', 'color' => 'primary', 'count' => 0];
        $matriz['Tipo 1']['Sucede varias veces en el último año en el mismo proceso']['10'] = ['label' => 'Medio-10', 'color' => 'orange', 'count' => 0];
        $matriz['Tipo 1']['Sucede varias veces en el último año en el mismo proceso']['5'] = ['label' => 'Bajo-5', 'color' => 'warning', 'count' => 0];

        //TIpo 2


        $matriz['Tipo 2']['MENOR']['RECURRENTE'] = ['label' => 'Moderada/Tolerable-4', 'color' => 'warning', 'count' => 0];
        $matriz['Tipo 2']['LEVE']['RECURRENTE'] = ['label' => 'Alta/Inaceptable-8', 'color' => 'orange', 'count' => 0];
        $matriz['Tipo 2']['GRAVE']['RECURRENTE'] = ['label' => 'Extrema/Inadmisible-12', 'color' => 'primary', 'count' => 0];
        $matriz['Tipo 2']['CATASTRÓFICA']['RECURRENTE'] = ['label' => 'Extrema/Inadmisible-16', 'color' => 'primary', 'count' => 0];

         $matriz['Tipo 2']['MENOR']['FRECUENTE'] = ['label' => 'Moderada/Tolerable-3', 'color' => 'warning', 'count' => 0];
         $matriz['Tipo 2']['LEVE']['FRECUENTE'] = ['label' => 'Alta/Inaceptable-6', 'color' => 'orange', 'count' => 0];
         $matriz['Tipo 2']['GRAVE']['FRECUENTE'] = ['label' => 'Alta/Inaceptable-9', 'color' => 'orange', 'count' => 0];
         $matriz['Tipo 2']['CATASTRÓFICA']['FRECUENTE'] = ['label' => 'Extrema/Inadmisible-12', 'color' => 'primary', 'count' => 0];

         $matriz['Tipo 2']['MENOR']['POSIBLE'] = ['label' => 'Baja/Aceptable-2', 'color' => 'info', 'count' => 0];
         $matriz['Tipo 2']['LEVE']['POSIBLE'] = ['label' => 'Moderada/Tolerable-4', 'color' => 'warning', 'count' => 0];
         $matriz['Tipo 2']['GRAVE']['POSIBLE'] = ['label' => 'Alta/Inaceptable-6', 'color' => 'orange', 'count' => 0];
         $matriz['Tipo 2']['CATASTRÓFICA']['POSIBLE'] = ['label' => 'Alta/Inaceptable-8', 'color' => 'orange', 'count' => 0];

         $matriz['Tipo 2']['MENOR']['REMOTO'] = ['label' => 'Baja/Aceptable-1', 'color' => 'info', 'count' => 0];
         $matriz['Tipo 2']['LEVE']['REMOTO'] = ['label' => 'Baja/Aceptable-2', 'color' => 'info', 'count' => 0];
         $matriz['Tipo 2']['GRAVE']['REMOTO'] = ['label' => 'Moderada/Tolerable-3', 'color' => 'warning', 'count' => 0];
         $matriz['Tipo 2']['CATASTRÓFICA']['REMOTO'] = ['label' => 'Moderada/Tolerable-4', 'color' => 'warning', 'count' => 0];

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
        if ($type == 'Tipo 2')
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
                    'CATASTRóFICA' => 'CATASTRóFICA'
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