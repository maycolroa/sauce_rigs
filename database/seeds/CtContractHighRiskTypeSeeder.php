<?php

use Illuminate\Database\Seeder;
use App\Models\LegalAspects\Contracts\HighRiskType;

class CtContractHighRiskTypeSeeder extends Seeder
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
                'name' => 'Trabajo en alturas',
            ],
            [
                'name' => 'Energias peligrosas'
            ],
            [
                'name' => 'Trabajos en caliente'
            ],
            [
                'name' => 'Espacios confinados'
            ],
        ];

        DB::beginTransaction();

        try
        {   
            $this->command->info('Comienza el seeder de los tipos de alto riesgo del módulo de contratistas');

            foreach ($data as $key => $value)
            {
                HighRiskType::updateOrCreate(['name' => $value['name']], $value);
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info('Ocurrio un error al ejecutar la clase CtContractHighRiskTypeSeeder');
        }
    }
}
