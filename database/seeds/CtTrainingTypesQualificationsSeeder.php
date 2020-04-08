<?php

use Illuminate\Database\Seeder;
use App\Models\LegalAspects\Contracts\TrainingTypeQuestion;

class CtTrainingTypesQualificationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => 'selection_simple',
                'description' => 'Selección simple'
            ],
            [
                'name' => 'true_false',
                'description' => 'Verdadero y Falso'
            ],
            [
                'name' => 'selection_multiple',
                'description' => 'Selección múltiple'
            ],
            [
                'name' => 'yes_no',
                'description' => 'Sí y No'
            ],
        ];

        DB::beginTransaction();

        try
        {   
            $this->command->info('Comienza el seeder de los tipos de preguntas del módulo de capacitación de contratistas');

            foreach ($data as $key => $value)
            {
                TrainingTypeQuestion::updateOrCreate(['name' => $value['name']], $value);
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info('Ocurrio un error al ejecutar la clase CtTrainingTypesQualificationsSeeder');
        }
    }
}
