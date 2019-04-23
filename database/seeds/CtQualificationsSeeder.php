<?php

use Illuminate\Database\Seeder;
use App\Models\LegalAspects\Contracts\Qualifications;

class CtQualificationsSeeder extends Seeder
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
                'name' => 'C',
                'description' => 'Cumple',
                'fulfillment' => 1
            ],
            [
                'name' => 'NC',
                'description' => 'No Cumple',
                'fulfillment' => 0
            ],
            [
                'name' => 'NA',
                'description' => 'No Aplica',
                'fulfillment' => 1
            ],
        ];

        DB::beginTransaction();

        try
        {   
            $this->command->info('Comienza el seeder de las calificaciones del módulo de contratistas');

            foreach ($data as $key => $value)
            {
                Qualifications::updateOrCreate(['name' => $value['name']], $value);
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info('Ocurrio un error al ejecutar la clase CtQualificationsSeeder');
        }
    }
}
