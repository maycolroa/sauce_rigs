<?php

use Illuminate\Database\Seeder;
use App\Models\LegalAspects\Contracts\StandardClassification;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;

class CtStandardClassificationSeeder extends Seeder
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
            $this->command->info('Comienza el seeder CtStandardClassificationSeeder');

            $file = dirname(__FILE__) . '/data/ctStandardClassification.json';
            $file = file_get_contents($file);
            $standards = json_decode($file, true);

            foreach ($standards as $key => $standard)
            {
                if (isset($standard['standard_name']) && isset($standard['items']))
                {
                    if (COUNT($standard['items']) > 0)
                    {
                        $standard_new = StandardClassification::firstOrCreate(
                            ['standard_name' => $standard['standard_name']], 
                            ['standard_name' => $standard['standard_name']]);

                        $ids = SectionCategoryItems::whereIn('item_name', $standard['items'])->pluck('id');

                        if ($ids)
                        {
                            $standard_new->items()->sync($ids->toArray());
                        }
                    }
                    else 
                    {
                        $this->command->info('Elemento omitido por no tener items asociados: '. $standard['standard_name']);
                    }
                }
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info($e->getMessage());
            $this->command->info('Ocurrio un error al ejecutar la clase CtStandardClassificationSeeder');
        }
    }
}
