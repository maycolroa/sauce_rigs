<?php

use Illuminate\Database\Seeder;
use App\Models\LegalAspects\LegalMatrix\FulfillmentValues;

class LmFulfillmentValuesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'Sin calificar',
            'Cumple',
            'No cumple',
            'En estudio',
            'Parcial',
            'No aplica',
            'Informativo'             
        ];

        foreach ($data as $value)
        {
            FulfillmentValues::updateOrCreate(['name' => $value], ['name' => $value]);
        }
    }
}
