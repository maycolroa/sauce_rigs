<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RepositoryMake extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'repository:make {singular} {plural} {migracion?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando para crear los archivos del repositorio, pero hay que modificar el contenido';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $singular = ucwords($this->argument('singular'));
        $plural = ucwords($this->argument('plural'));
        $migracion = $this->argument('migracion');
        $name1 = strtolower(preg_replace('/(?<=\w)([A-Z])/', '-$1', $singular));
        $name2 = strtolower(preg_replace('/(?<=\w)([A-Z])/', '_$1', $plural));
        $name3 = preg_replace_callback('/^(\p{Lu})/', function($matches) {
            return strtolower($matches[0]);
        }, $plural);

        $path = base_path();

        ///////////////////////////////////////////////////////////////////////////////////////////////

        $new = "{$path}/app/Http/Controllers/LegalAspects/Contracs/{$singular}Controller.php";

        copy("{$path}/app/Http/Controllers/Administrative/Regionals/EmployeeRegionalController.php", $new);

        $fileContent = file_get_contents($new);

        if ($fileContent !== false)
        {
            $newContent = str_ireplace("EmployeeRegional", $singular, $fileContent);
            $result = file_put_contents($new, $newContent);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////

        $new = "{$path}/app/Http/Requests/LegalAspects/Contracs/{$singular}Request.php";

        copy("{$path}/app/Http/Requests/Administrative/Regionals/PositionRequest.php", $new);

        $fileContent = file_get_contents($new);

        if ($fileContent !== false)
        {
            $newContent = str_ireplace("EmployeeRegionals", $name2, $fileContent);
            $newContent = str_ireplace("EmployeeRegional", $singular, $newContent);            
            $result = file_put_contents($new, $newContent);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////

        $new = "{$path}/app/Models/LegalAspects/Contracs{$singular}.php";

        copy("{$path}/app/Models/Administrative/Regionals/EmployeeRegional.php", $new);

        $fileContent = file_get_contents($new);

        if ($fileContent !== false)
        {
            $newContent = str_ireplace("Position", $singular, $fileContent);
            $result = file_put_contents($new, $newContent);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////

        $new = "{$path}/resources/js/components/{$name2}";

        $this->copyDirectory("{$path}/resources/js/components/positions", $new);

        $new = "{$new}/form.vue";
        $fileContent = file_get_contents($new);

        if ($fileContent !== false)
        {
            $newContent = str_ireplace("@/api/positions", "@/api/{$name2}", $fileContent);
            $newContent = str_ireplace("positions", $name3, $newContent);
            $result = file_put_contents($new, $newContent);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////

        $new = "{$path}/resources/js/pages/{$name2}";

        $this->copyDirectory("{$path}/resources/js/pages/positions", $new);

        $fileContent = file_get_contents("{$new}/create.vue");

        if ($fileContent !== false)
        {
            $newContent = str_ireplace("positions", $name2, $fileContent);
            $result = file_put_contents("{$new}/create.vue", $newContent);
        }

        $fileContent = file_get_contents("{$new}/edit.vue");

        if ($fileContent !== false)
        {
            $newContent = str_ireplace("positions", $name2, $fileContent);
            $result = file_put_contents("{$new}/edit.vue", $newContent);
        }

        $fileContent = file_get_contents("{$new}/index.vue");

        if ($fileContent !== false)
        {
            $newContent = str_ireplace("positions", $name2, $fileContent);
            $result = file_put_contents("{$new}/index.vue", $newContent);
        }

        ///////////////////////////////////////////////////////////////////////////////////////////////

        if ($migracion)
            \Artisan::call("make:migration create_{$name2}_table");

        ///////////////////////////////////////////////////////////////////////////////////////////////

        echo "\n
            SOLO QUEDA PENDIENTE
            \n
            1. Cambiar los campos del request\n
            2. Cambiar los campos del Resource\n
            3. Cambiar las columnas del modelo\n
            4. Cambiar las columnas del archivo json de la tabla ag grid\n
            5. Cambiar los titulos de los archivos blade\n
            6. Cambiar los campos de los archivos create.vue y edit.vue\n
            7. Cambiar el formulario de vue y sus campos\n
            8. Completar la migracion del modelo en caso de haberla creado\n
            9. Crear la migracion de la opcion del menu (Ej: 2024_03_21_005029_add_option_menu_rendicion_tipo_gasto)\n
        \n";
    }

    public function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
    
        $directory = dir($source);
        while (($file = $directory->read()) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
    
            $sourcePath = $source . '/' . $file;
            $destinationPath = $destination . '/' . $file;
    
            if (is_dir($sourcePath)) {
                copyDirectory($sourcePath, $destinationPath);
            } else {
                copy($sourcePath, $destinationPath);
            }
        }
    
        $directory->close();
    }    
}
