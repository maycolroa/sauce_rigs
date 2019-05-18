<?php

use Illuminate\Database\Seeder;
use App\Models\IndustrialSecure\DangerMatrix\Qualification;
use App\Models\IndustrialSecure\DangerMatrix\QualificationType;

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
                if (isset($item['name']) && isset($item['types']))
                {
                    if (COUNT($item['types']) > 0)
                    {
                        $qualification = Qualification::where('name', $item['name'])->first();

                        if (!$qualification)
                        {
                            $qualification = new Qualification();
                            $qualification->name = $item['name'];
                            $qualification->save();

                            foreach ($item['types'] as $type)
                            {
                                if (isset($type['description']) && isset($type['type_input']))
                                {
                                    if ($type['type_input'] == 'select')
                                    {
                                        if (COUNT($type['values']) > 0)
                                        {
                                            $qualificationType = $qualification->types()->updateOrCreate([
                                                    'description' => $type['description']
                                                ], [
                                                    'description' => $type['description'],
                                                    'type_input' => $type['type_input'],
                                                    'readonly' => $type['readonly']
                                                ]);

                                            foreach ($type['values'] as $value)
                                            {
                                                if (isset($value['description']) && isset($value['value']))
                                                {
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
                                        }
                                        else 
                                        {
                                            $this->command->info('Tipo de calificación omitida por no tener valores de calificaciones asociadas: '. json_encode($type));
                                        }
                                    }
                                    else if ($type['type_input'] == 'text')
                                    {
                                        $qualificationType = $qualification->types()->updateOrCreate([
                                            'description' => $type['description']
                                        ], [
                                            'description' => $type['description'],
                                            'type_input' => $type['type_input'],
                                            'readonly' => $type['readonly']
                                        ]);
                                    }
                                    else
                                    {
                                        $this->command->info('Tipo de calificación omitida por tipo de input invalido: '. json_encode($type));
                                    }
                                }
                                else
                                {
                                    $this->command->info('Tipo de calificación omitida por formato invalido: '. json_encode($type));
                                }
                            }
                        }
                        else 
                        {
                            $this->command->info('Elemento omitido por existir en base de datos: '. $item['name']);
                        }
                    }
                    else 
                    {
                        $this->command->info('Elemento omitido por no tener tipos de calificaciones asociados: '. $item['name']);
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
            $this->command->info($e->getMessage());
            //$this->command->info('Ocurrio un error al ejecutar la clase DmQualificationsSeeder');
        }
    }
}
