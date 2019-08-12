<?php

use Illuminate\Database\Seeder;
use App\Models\General\Keyword;

class MakeKeywordsSeeder extends Seeder
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
            $this->command->info('Comienza el seeder MakeKeywordsSeeder');

            $file = dirname(__FILE__) . '/data/keywords.json';
            $file = file_get_contents($file);
            $keywors = json_decode($file, true);

            foreach ($keywors as $value)
            {
                if (isset($value['name']) && isset($value['display_name']))
                {
                    $item = [];
                    $keyword = Keyword::where("name", $value["name"])->first(); 
                    
                    $item = [
                        "name" 	        => $value["name"],
                        "display_name" 	=> $value["display_name"]
                    ];
                    
                    if (!$keyword)
                    {
                        Keyword::create($item);
                    }
                    /*else
                    {
                        $keyword->update($item);
                    }*/
                }
                else
                {
                    $this->command->info('Elemento omitido por formato invalido: '. json_encode($value));
                }
			}

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info('Ocurrio un error al ejecutar la clase MakeKeywordsSeeder');
        }
    }
}
