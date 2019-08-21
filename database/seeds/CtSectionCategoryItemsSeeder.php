<?php

use Illuminate\Database\Seeder;
use App\Models\LegalAspects\Contracts\Section;

class CtSectionCategoryItemsSeeder extends Seeder
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
            $this->command->info('Comienza el seeder CtSectionCategoryItemsSeeder');

            $file = dirname(__FILE__) . '/data/ctSectionCategoryItems.json';
            $file = file_get_contents($file);
            $sections = json_decode($file, true);

            foreach ($sections as $key => $section)
            {
                if (isset($section['section_name']) && isset($section['categories']))
                {
                    if (COUNT($section['categories']) > 0)
                    {
                        $section_new = Section::firstOrCreate(
                            ['section_name' => $section['section_name']], 
                            ['section_name' => $section['section_name']]);

                        foreach ($section['categories'] as $category)
                        {
                            if (isset($category['category_name']) && isset($category['items']))
                            {
                                if (COUNT($category['items']) > 0)
                                {
                                    $category_new = $section_new->categories()->updateOrCreate([
                                            'category_name' => $category['category_name']
                                        ], [
                                            'category_name' => $category['category_name']
                                        ]);

                                    foreach ($category['items'] as $item)
                                    {
                                        if (isset($item['item_name']) && isset($item['criterion_description']) && isset($item['verification_mode']) && isset($item['percentage_weight']))
                                        {
                                            $category_new->items()->updateOrCreate([
                                                    'item_name' => $item['item_name']
                                                ], [
                                                    'item_name' => $item['item_name'],
                                                    'criterion_description' => $item['criterion_description'],
                                                    'verification_mode' => $item['verification_mode'],
                                                    'percentage_weight' => $item['percentage_weight']
                                                ]);
                                        }
                                        else
                                        {
                                            $this->command->info('Item omitido por formato invalido: '. json_encode($item));
                                        }
                                    }
                                }
                                else 
                                {
                                    $this->command->info('Categoria omitida por no tener items asociados: '. json_encode($category));
                                }
                            }
                            else
                            {
                                $this->command->info('Categoria omitida por formato invalido: '. json_encode($category));
                            }
                        }
                    }
                    else 
                    {
                        $this->command->info('Elemento omitido por no tener categorias asociadas: '. $section['section_name']);
                    }
                }
                else
                {
                    $this->command->info('Elemento omitido por formato invalido: '. json_encode($section));
                }
			}

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            //$this->command->info($e->getMessage());
            $this->command->info('Ocurrio un error al ejecutar la clase CtSectionCategoryItemsSeeder');
        }
    }
}
