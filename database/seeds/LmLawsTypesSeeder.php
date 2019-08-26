<?php

use Illuminate\Database\Seeder;
use App\Models\LegalAspects\LegalMatrix\LawType;

class LmLawsTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'Circular',
            'Resolución',
            'Decisión',
            'Decreto',
            'Convenio',
            'Resolución metropolitana',
            'Acuerdo metropolitano',
            'Código sustantivo de trabajo',
            'Concepto',
            'Constitución política',
            'Ley',
            'Acuerdo',
            'NTC',
            'Decreto Ley'            
        ];

        foreach ($data as $value)
        {
            LawType::updateOrCreate(['name' => $value], ['name' => $value]);
        }
    }
}
