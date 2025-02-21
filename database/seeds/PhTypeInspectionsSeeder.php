<?php

use Illuminate\Database\Seeder;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\TypeInspections;

class PhTypeInspectionsSeeder extends Seeder
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
                'type' => 'Tipo 1',
                'description' => 'Inspecciones en la cuales los items pertenecientes a un tema tienen todos el mismo valor'
            ],
            [
                'type' => 'Tipo 2',
                'description' => 'Inspecciones en la cuales a los items pertenecientes a un tema se les puede asignar una valor especifico dentro de su tema'
            ],
        ];

        DB::beginTransaction();

        try
        {   
            $this->command->info('Comienza el seeder de los tipos de inspecciones del módulo de Condiciones Peligrosas');

            foreach ($data as $key => $value)
            {
                TypeInspections::updateOrCreate(['type' => $value['type']], $value);
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            $this->command->info('Ocurrio un error al ejecutar la clase PhTypeInspectionsSeeder');
        }
    }
}
