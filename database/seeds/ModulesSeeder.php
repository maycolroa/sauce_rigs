<?php

use Illuminate\Database\Seeder;
use App\Models\General\Application;
use App\Models\General\Module;

class ModulesSeeder extends Seeder
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
            $this->command->info('Comienza el seeder de los modulos');

            $file = dirname(__FILE__) . '/data/modules.json';
            $file = file_get_contents($file);
            $modules = json_decode($file, true);

            foreach ($modules as $value)
            {	
                if (isset($value['application']) && isset($value['modules']))
                {
                    $app = Application::where("name", $value["application"])->first();
                    $data = [];

                    if ($app)
                    {
                        foreach ($value["modules"] as $item)
                        {
                            if (isset($item["name"]) && isset($item["display_name"]))
                            {
                                $mod = Module::where("application_id", $app->id)
                                        ->where("name", $item["name"])->first(); 

                                if (!$mod)
                                {
                                    $data = [
                                        "name" 	         => $item["name"],
                                        "display_name" 	 => $item["display_name"],
                                        "logo"           => $item["logo"],
                                        "application_id" => $app->id,
                                        "main"           => $item["main"],
                                        "created_at" 	 => date("Y-m-d H:i:s")
                                    ];
                
                                    Module::create($data);
                                }
                                else 
                                {
                                    //$this->command->info("Elemento ".$item["name"]." duplicado");
                                    $mod->update([
                                        "name" 	         => $item["name"],
                                        "display_name" 	 => $item["display_name"],
                                        "logo"           => $item["logo"],
                                        "main"           => $item["main"]
                                    ]);
                                }
                            }
                            else
                                $this->command->info('Elemento omitido por formato invalido: '. json_encode($item));
                        }
                    }
                    else
                        $this->command->info("Aplicacion ".$value["application"]." no encontrada");
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
            $this->command->info('Ocurrio un error al ejecutar la clase ModulesSeeder');
        }
    }
}
