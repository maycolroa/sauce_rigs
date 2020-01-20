<?php

use Illuminate\Database\Seeder;
use App\Models\LegalAspects\Contracts\SectionCategoryItems;
use App\Models\LegalAspects\Contracts\ActionPlanDefault;

class ctItemActivitiesSeeder extends Seeder
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
            $this->command->info('Comienza el seeder ctItemActivitiesSeeder');

            $file = dirname(__FILE__) . '/data/ctItemActivities.json';
            $file = file_get_contents($file);
            $items = json_decode($file, true);

            foreach ($items as $key => $item)
            {
                if (isset($item['item_name']) && isset($item['activities']))
                {
                    if (COUNT($item['activities']) > 0)
                    {
                        $item_new = SectionCategoryItems::where('item_name', $item['item_name'])->first();

                        $ids = ActionPlanDefault::whereIn('description', $item['activities'])->pluck('id');

                        if ($ids)
                        {
                            $item_new->activities()->sync($ids->toArray());
                        }
                    }
                    else 
                    {
                        $this->command->info('Elemento omitido por no tener actividafes asociadas: '. $item['item_name']);
                    }
                }
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            //$this->command->info($e->getMessage());
            $this->command->info('Ocurrio un error al ejecutar la clase ctItemActivitiesSeeder');
        }
    }
}
