<?php

use Illuminate\Database\Seeder;
use App\Models\General\Module;

class ModulesDependenciesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();

        try
        { 
            $this->command->info('Comienza el seeder ModulesDependenciesSeeder');

            $file = dirname(__FILE__) . '/data/modulesDependencies.json';
            $file = file_get_contents($file);
            $items = json_decode($file, true);

            foreach ($items as $key => $item)
            {
                if (isset($item['module']) && isset($item['dependencies']))
                {
                    if (COUNT($item['dependencies']) > 0)
                    {
                        $module = Module::where('name', $item['module'])->first();

                        $ids = Module::whereIn('name', $item['dependencies'])->pluck('id');

                        if ($ids)
                        {
                            $module->dependencies()->sync($ids->toArray());
                        }
                    }
                    else 
                    {
                        $this->command->info('Elemento omitido por no tener dependencias asociadas: '. $item['module']);
                    }
                }
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info($e->getMessage());
            $this->command->info('Ocurrio un error al ejecutar la clase ModulesDependenciesSeeder');
        }
    }
}
