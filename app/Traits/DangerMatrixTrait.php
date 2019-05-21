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
            $matriz['1']['No ha ocurrido en el sector Hospitalario'] = ['label' => 'T-1', 'color' => 'success', 'count' => 0];
            $matriz['1']['Ha ocurrido en el sector Hospitalario'] = ['label' => 'T-2', 'color' => 'success', 'count' => 0];
            $matriz['1']['Alguna vez ha ocurrido en el Hospital en el último año'] = ['label' => 'T-3', 'color' => 'success', 'count' => 0];
            $matriz['1']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = ['label' => 'B-4', 'color' => 'warning', 'count' => 0];
            $matriz['1']['Sucede varias veces en el último año en el mismo proceso'] = ['label' => 'B-5', 'color' => 'warning', 'count' => 0];

            $matriz['2']['No ha ocurrido en el sector Hospitalario'] = ['label' => 'T-2', 'color' => 'success', 'count' => 0];
            $matriz['2']['Ha ocurrido en el sector Hospitalario'] = ['label' => 'T-3', 'color' => 'success', 'count' => 0];
            $matriz['2']['Alguna vez ha ocurrido en el Hospital en el último año'] = ['label' => 'B-6', 'color' => 'warning', 'count' => 0];
            $matriz['2']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = ['label' => 'B-7', 'color' => 'warning', 'count' => 0];
            $matriz['2']['Sucede varias veces en el último año en el mismo proceso'] = ['label' => 'M-10', 'color' => 'orange', 'count' => 0];

            $matriz['3']['No ha ocurrido en el sector Hospitalario'] = ['label' => 'T-3', 'color' => 'success', 'count' => 0];
            $matriz['3']['Ha ocurrido en el sector Hospitalario'] = ['label' => 'B-6', 'color' => 'warning', 'count' => 0];
            $matriz['3']['Alguna vez ha ocurrido en el Hospital en el último año'] = ['label' => 'M-9', 'color' => 'orange', 'count' => 0];
            $matriz['3']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = ['label' => 'M-12', 'color' => 'orange', 'count' => 0];
            $matriz['3']['Sucede varias veces en el último año en el mismo proceso'] = ['label' => 'A-15', 'color' => 'primary', 'count' => 0];

            $matriz['4']['No ha ocurrido en el sector Hospitalario'] = ['label' => 'B-4', 'color' => 'warning', 'count' => 0];
            $matriz['4']['Ha ocurrido en el sector Hospitalario'] = ['label' => 'M-8', 'color' => 'orange', 'count' => 0];
            $matriz['4']['Alguna vez ha ocurrido en el Hospital en el último año'] = ['label' => 'M-12', 'color' => 'orange', 'count' => 0];
            $matriz['4']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = ['label' => 'A-16', 'color' => 'primary', 'count' => 0];
            $matriz['4']['Sucede varias veces en el último año en el mismo proceso'] = ['label' => 'A-20', 'color' => 'primary', 'count' => 0];

            $matriz['5']['No ha ocurrido en el sector Hospitalario'] = ['label' => 'M-8', 'color' => 'orange', 'count' => 0];
            $matriz['5']['Ha ocurrido en el sector Hospitalario'] = ['label' => 'M-10', 'color' => 'orange', 'count' => 0];
            $matriz['5']['Alguna vez ha ocurrido en el Hospital en el último año'] = ['label' => 'A-15', 'color' => 'primary', 'count' => 0];
            $matriz['5']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = ['label' => 'A-20', 'color' => 'primary', 'count' => 0];
            $matriz['5']['Sucede varias veces en el último año en el mismo proceso'] = ['label' => 'MA-25', 'color' => 'purple', 'count' => 0];
        }

        return $matriz;
    }
}