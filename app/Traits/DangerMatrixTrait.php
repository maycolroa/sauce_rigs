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
            $matriz['1']['No ha ocurrido en el sector Hospitalario'] = 'T-1';
            $matriz['1']['Ha ocurrido en el sector Hospitalario'] = 'T-2';
            $matriz['1']['Alguna vez ha ocurrido en el Hospital en el último año'] = 'T-3';
            $matriz['1']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = 'B-4';
            $matriz['1']['Sucede varias veces en el último año en el mismo proceso'] = 'B-5';

            $matriz['2']['No ha ocurrido en el sector Hospitalario'] = 'T-2';
            $matriz['2']['Ha ocurrido en el sector Hospitalario'] = 'T-3';
            $matriz['2']['Alguna vez ha ocurrido en el Hospital en el último año'] = 'B-6';
            $matriz['2']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = 'B-7';
            $matriz['2']['Sucede varias veces en el último año en el mismo proceso'] = 'M-10';

            $matriz['3']['No ha ocurrido en el sector Hospitalario'] = 'T-3';
            $matriz['3']['Ha ocurrido en el sector Hospitalario'] = 'B-6';
            $matriz['3']['Alguna vez ha ocurrido en el Hospital en el último año'] = 'M-9';
            $matriz['3']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = 'M-12';
            $matriz['3']['Sucede varias veces en el último año en el mismo proceso'] = 'A-15';

            $matriz['4']['No ha ocurrido en el sector Hospitalario'] = 'B-4';
            $matriz['4']['Ha ocurrido en el sector Hospitalario'] = 'M-8';
            $matriz['4']['Alguna vez ha ocurrido en el Hospital en el último año'] = 'M-12';
            $matriz['4']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = 'A-16';
            $matriz['4']['Sucede varias veces en el último año en el mismo proceso'] = 'A-20';

            $matriz['5']['No ha ocurrido en el sector Hospitalario'] = 'M-8';
            $matriz['5']['Ha ocurrido en el sector Hospitalario'] = 'M-10';
            $matriz['5']['Alguna vez ha ocurrido en el Hospital en el último año'] = 'A-15';
            $matriz['5']['Sucede varias veces en el último año y en diferentes procesos (en el Hospital)'] = 'A-20';
            $matriz['5']['Sucede varias veces en el último año en el mismo proceso'] = 'MA-25';
        }

        return $matriz;
    }
}