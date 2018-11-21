<?php

use Illuminate\Database\Seeder;
use App\Models\Application;

class ApplicationsSeeder extends Seeder
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
            $this->command->info('Comienza el seeder de las aplicaciones');

            $file = dirname(__FILE__) . '/data/applications.json';
            $file = file_get_contents($file);
            $applications = json_decode($file, true);

            foreach ($applications as $value)
            {
                if (isset($value['name']) && isset($value['display_name']) && isset($value['image']))
                {
                    $item = [];
                    $app = Application::where("name", $value["name"])->first(); 
                    
                    if (!$app)
                    {
                        $item = [
                            "name" 	        => $value["name"],
                            "display_name" 	=> $value["display_name"],
                            "image" 		=> $value["image"],
                            "created_at" 	=> date("Y-m-d H:i:s")
                        ];

                        if (isset($value["id"]))
                            $item["id"] = $value["id"];

                        Application::create($item);
                    }
                    else
                        $this->command->info("Elemento ".$value["name"]." duplicado");
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
            $this->command->info('Ocurrio un error al ejecutar la clase ApplicationsSeeder');
        }
    }
}
