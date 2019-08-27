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
            $matriz['No ha ocurrido en el sector Hospitalario']['8'] = ['label' => 'M-8', 'color' => 'orange', 'count' => 0];
            $matriz['No ha ocurrido en el sector Hospitalario']['4'] = ['label' => 'B-4', 'color' => 'warning', 'count' => 0];
            $matriz['No ha ocurrido en el sector Hospitalario']['3'] = ['label' => 'T-3', 'color' => 'success', 'count' => 0];
            $matriz['No ha ocurrido en el sector Hospitalario']['2'] = ['label' => 'T-2', 'color' => 'success', 'count' => 0];
            $matriz['No ha ocurrido en el sector Hospitalario']['1'] = ['label' => 'T-1', 'color' => 'success', 'count' => 0];

            $matriz['Ha ocurrido en el sector Hospitalario']['10'] = ['label' => 'M-10', 'color' => 'orange', 'count' => 0];
            $matriz['Ha ocurrido en el sector Hospitalario']['8'] = ['label' => 'M-8', 'color' => 'orange', 'count' => 0];
            $matriz['Ha ocurrido en el sector Hospitalario']['6'] = ['label' => 'B-6', 'color' => 'warning', 'count' => 0];
            $matriz['Ha ocurrido en el sector Hospitalario']['3'] = ['label' => 'T-3', 'color' => 'success', 'count' => 0];
            $matriz['Ha ocurrido en el sector Hospitalario']['2'] = ['label' => 'T-2', 'color' => 'success', 'count' => 0];

            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['15'] = ['label' => 'A-15', 'color' => 'primary', 'count' => 0];
            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['12'] = ['label' => 'M-12', 'color' => 'orange', 'count' => 0];
            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['9'] = ['label' => 'M-9', 'color' => 'orange', 'count' => 0];
            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['6'] = ['label' => 'B-6', 'color' => 'warning', 'count' => 0];
            $matriz['Alguna vez ha ocurrido en el Hospital en el último año']['3'] = ['label' => 'T-3', 'color' => 'success', 'count' => 0];

            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['20'] = ['label' => 'A-20', 'color' => 'primary', 'count' => 0];
            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['16'] = ['label' => 'A-16', 'color' => 'primary', 'count' => 0];
            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['12'] = ['label' => 'M-12', 'color' => 'orange', 'count' => 0];
            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['7'] = ['label' => 'B-7', 'color' => 'warning', 'count' => 0];
            $matriz['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)']['4'] = ['label' => 'B-4', 'color' => 'warning', 'count' => 0];

            $matriz['Sucede varias veces en el último año en el mismo proceso']['25'] = ['label' => 'MA-25', 'color' => 'purple', 'count' => 0];
            $matriz['Sucede varias veces en el último año en el mismo proceso']['20'] = ['label' => 'A-20', 'color' => 'primary', 'count' => 0];
            $matriz['Sucede varias veces en el último año en el mismo proceso']['15'] = ['label' => 'A-15', 'color' => 'primary', 'count' => 0];
            $matriz['Sucede varias veces en el último año en el mismo proceso']['10'] = ['label' => 'M-10', 'color' => 'orange', 'count' => 0];
            $matriz['Sucede varias veces en el último año en el mismo proceso']['5'] = ['label' => 'B-5', 'color' => 'warning', 'count' => 0];
        }

        return $matriz;
    }
}