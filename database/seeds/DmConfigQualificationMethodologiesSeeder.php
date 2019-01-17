<?php

use Illuminate\Database\Seeder;
use App\IndustrialSecure\ConfigQualificationMethodologie;

class DmConfigQualificationMethodologiesSeeder extends Seeder
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
            $this->command->info('Comienza el seeder ConfigQualificationMethodologiesSeeder');

            $file = dirname(__FILE__) . '/data/dmConfigQualificationMethodologies.json';
            $file = file_get_contents($file);
            $configurations = json_decode($file, true);

            foreach ($configurations as $key => $value)
            {
                if (isset($value['company_id']) && isset($value['types']) && isset($value['qualifications']))
                {
                    ConfigQualificationMethodologie::withoutGlobalScopes()->updateOrCreate([
                        'company_id' => $value['company_id']
                    ], [
                        'company_id' => $value['company_id'],
                        "types" => json_encode($value["types"]),
                        "qualifications" => json_encode($value["qualifications"])
                       ]
                    );
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
            $this->command->info('Ocurrio un error al ejecutar la clase ConfigQualificationMethodologiesSeeder');
        }
    }
}
