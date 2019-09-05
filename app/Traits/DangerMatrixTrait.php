<?php

namespace App\Traits;

trait DangerMatrixTrait
{
    /**
     * returns the grade matrix
     *
     * @param String $type
     * @return Array
     */
    protected function getMatrixCalification($type)
    {
        $matriz = [];

        if ($type == 'Tipo 1')
        {
            $matriz['No ha ocurrido en el sector Hospitalario']['8'] = ['label' => 'Medio-8', 'color' => 'orange', 'count' => 0];
            $matriz['No ha ocurrido en el sector Hospitalario']['4'] = ['label' => 'Bajo-4', 'color' => 'warning', 'count' => 0];
            $matriz['No ha ocurrido en el sector Hospitalario']['3'] = ['label' => 'Trivial-3', 'color' => 'success', 'count' => 0];
            $matriz['No ha ocurrido en el sector Hospitalario']['2'] = ['label' => 'Trivial-2', 'color' => 'success', 'count' => 0];
            $matriz['No ha ocurrido en el sector Hospitalario']['1'] = ['label' => 'Trivial-1', 'color' => 'success', 'count' => 0];

            $matriz['Ha ocurrido en el sector Hospitalario']['10'] = ['label' => 'Medio-10', 'color' => 'orange', 'count' => 0];
            $matriz['Ha ocurrido en el sector Hospitalario']['8'] = ['label' => 'Medio-8', 'color' => 'orange', 'count' => 0];
            $matriz['Ha ocurrido en el sector Hospitalario']['6'] = ['label' => 'Bajo-6', 'color' => 'warning', 'count' => 0];
            $matriz['Ha ocurrido en el sector Hospitalario']['3'] = ['label' => 'Trivial-3', 'color' => 'success', 'count' => 0];
            $matriz['Ha ocurrido en el sector Hospitalario']['2'] = ['label' => 'Trivial-2', 'color' => 'success', 'count' => 0];

            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['15'] = ['label' => 'Alto-15', 'color' => 'primary', 'count' => 0];
            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['12'] = ['label' => 'Medio-12', 'color' => 'orange', 'count' => 0];
            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['9'] = ['label' => 'Medio-9', 'color' => 'orange', 'count' => 0];
            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['6'] = ['label' => 'Bajo-6', 'color' => 'warning', 'count' => 0];
            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['3'] = ['label' => 'Trivial-3', 'color' => 'success', 'count' => 0];

            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['20'] = ['label' => 'Alto-20', 'color' => 'primary', 'count' => 0];
            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['16'] = ['label' => 'Alto-16', 'color' => 'primary', 'count' => 0];
            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['12'] = ['label' => 'Medio-12', 'color' => 'orange', 'count' => 0];
            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['7'] = ['label' => 'Bajo-7', 'color' => 'warning', 'count' => 0];
            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['4'] = ['label' => 'Bajo-4', 'color' => 'warning', 'count' => 0];

            $matriz['Sucede varias veces en el último año en el mismo proceso']['25'] = ['label' => 'Muy Alto-25', 'color' => 'purple', 'count' => 0];
            $matriz['Sucede varias veces en el último año en el mismo proceso']['20'] = ['label' => 'Alto-20', 'color' => 'primary', 'count' => 0];
            $matriz['Sucede varias veces en el último año en el mismo proceso']['15'] = ['label' => 'Alto-15', 'color' => 'primary', 'count' => 0];
            $matriz['Sucede varias veces en el último año en el mismo proceso']['10'] = ['label' => 'Medio-10', 'color' => 'orange', 'count' => 0];
            $matriz['Sucede varias veces en el último año en el mismo proceso']['5'] = ['label' => 'Bajo-5', 'color' => 'warning', 'count' => 0];
        }

        return $matriz;
    }
}