<?php

use Illuminate\Database\Seeder;
use App\IndustrialSecure\Qualification;
use App\IndustrialSecure\QualificationType;

class DmQualificationsSeeder extends Seeder
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
            $this->command->info('Comienza el seeder DmQualificationsSeeder');

            $file = dirname(__FILE__) . '/data/dmQualifications.json';
            $file = file_get_contents($file);
            $configurations = json_decode($file, true);

            foreach ($configurations as $key => $item)
            {
                $new = false;

                if (isset($item['name']) && isset($item['types']))
                {
                    if (COUNT($item['types']) > 0)
                    {
                        $qualification = Qualification::where('name', $item['name'])->first();

                        if (!$qualification)
                        {
                            $new = true;
                            $qualification = new Qualification();
                            $qualification->name = $item['name'];
                            $qualification->save();
                        }

                        $types = [];

                        foreach ($item['types'] as $type)
                        {
                            if (isset($type['description']) && isset($type['values']))
                            {
                                if (COUNT($type['values']) > 0)
                                {
                                    array_push($types, $type['description']);

                                    $qualificationType = $qualification->types()->updateOrCreate([
                                            'description' => $type['description']
                                        ], [
                                            'description' => $type['description']
                                        ]);

                                    $values = [];

                                    foreach ($type['values'] as $value)
                                    {
                                        if (isset($value['description']) && isset($value['value']))
                                        {
                                            array_push($values, $value['value']);

                                            $qualificationType->values()->updateOrCreate([
                                                    'value' => $value['value']
                                                ], [
                                                    'value' => $value['value'],
                                                    'description' => $value['description']
                                                ]);
                                        }
                                        else
                                        {
                                            $this->command->info('Valor de calificación omitido por formato invalido: '. json_encode($value));
                                        }
                                    }
                                    
                                    //Esta linea debe ser comentada cuando se haga el modulo para el frontend ya que despues eliminara cualquier valor que haya sido agregado desde la web porque no existira en el JSON
                                    $qualificationType->values()->whereNotIn('sau_dm_qualification_values.value', $values)->delete();
                                }
                                else 
                                {
                                    $this->command->info('Tipo de calificación omitida por no tener valores de calificaciones asociadas: '. json_encode($type));
                                }
                            }
                            else
                            {
                                $this->command->info('Tipo de calificación omitida por formato invalido: '. json_encode($type));
                            }
                        }
                        
                        //Esta linea debe ser comentada cuando se haga el modulo para el frontend ya que despues eliminara cualquier valor que haya sido agregado desde la web porque no existira en el JSON
                        $qualification->types()->whereNotIn('sau_dm_qualification_types.description', $types)->delete();
                    }
                    else 
                    {
                        $this->command->info('Elemento omitido por no tener tipos de calificaciones asociados: '. json_encode($item));
                    }
                }
                else
                {
                    $this->command->info('Elemento omitido por formato invalido: '. json_encode($item));
                }
			}

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            //$this->command->info($e->getMessage());
            $this->command->info('Ocurrio un error al ejecutar la clase DmQualificationsSeeder');
        }
    }
}
