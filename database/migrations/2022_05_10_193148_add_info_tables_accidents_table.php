<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\IndustrialSecure\WorkAccidents\Site;
use App\Models\IndustrialSecure\WorkAccidents\Mechanism;
use App\Models\IndustrialSecure\WorkAccidents\PartBody;
use App\Models\IndustrialSecure\WorkAccidents\TypeLesion;
use App\Models\IndustrialSecure\WorkAccidents\Agent;

class AddInfoTablesAccidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $agents = [
            ['1', '1.00', 'Máquienas y/o equipos'],
            ['2', '2.00', 'Medios de transporte'],
            ['3', '3.00', 'Aparatos'],
            ['4', '3.36', 'Herramientas, implementos o utensilios'],
            ['5', '4.00', 'Materiales o sustancias'],
            ['6', '4.40', 'Radiaciones'],
            ['7', '5.00', 'Ambiente de trabajo (Incluye superficies de tránsito y de trabajo, muebles, tejados, en el exterior, interior o subterráneos)'],
            ['8', '6.00', 'Otros agentes no clasificados'],
            ['9', '6.61', 'Animales (Vivos o productos animales)'],
            ['10', '7.00', 'Agentes no clasificados por falta de datos']
        ];

        foreach ($agents as $key => $value) 
        {
            $agent = new Agent;
            $agent->id = $value[0];
            $agent->code = $value[1];
            $agent->name = $value[2];
            $agent->save();
        }

        $sites = [
            ['1', '1', 'Almacenes o depósitos'],
            ['2', '2', 'Áreas de producción'],
            ['3', '3', 'Áreas recreativas o productivas'],
            ['4', '4', 'Corredores o pasillos'],
            ['5', '5', 'Escaleras'],
            ['6', '6', 'Parqueaderos o áreas de circulación vehicular'],
            ['7', '7', 'Oficinas'],
            ['8', '8', 'Otras áreas comunes'],
            ['9', '9', 'Otro']
        ];

        foreach ($sites as $key => $value) 
        {
            $site = new Site;
            $site->id = $value[0];
            $site->code = $value[1];
            $site->name = $value[2];
            $site->save();
        }

        $parts_body = [
            ['1', '1.00', 'Cabeza'],
            ['2', '1.12', 'Ojo'],
            ['3', '2.00', 'Cuello'],
            ['4', '3.00', 'Tronco (Incluye espalda, columna vertebral, médula espinal, pélvis)'],
            ['5', '3.32', 'Tórax'],
            ['6', '3.33', 'Abdomen'],
            ['7', '4.00', 'Miembros superiores'],
            ['8', '4.46', 'Manos'],
            ['9', '5.00', 'Miembros inferiores'],
            ['10', '5.56', 'Pies'],
            ['11', '6.00', 'Ubicaciones múltiples'],
            ['12', '7.00', 'Lesiones generales u otras']
        ];

        foreach ($parts_body as $key => $value) 
        {
            $part_body = new PartBody;
            $part_body->id = $value[0];
            $part_body->code = $value[1];
            $part_body->name = $value[2];
            $part_body->save();
        }

        $types_lesion = [
            ['1', '10', 'Fractura'],
            ['2', '20', 'Luxación'],
            ['3', '25', 'Torcedura, esguince, desgarro muscular, hernia o laceración de músculo o tendón sin herida'],
            ['4', '30', 'Conmoción o trauma interno'],
            ['5', '40', 'Amputación o enucleación'],
            ['6', '41', 'Herida'],
            ['7', '50', 'Trauma superficial'],
            ['8', '55', 'Golpe, contusión o aplastamiento'],
            ['9', '60', 'Quemadura'],
            ['10', '70', 'Envenenamiento o intoxicación aguda o alergia'],
            ['11', '80', 'Efecto del tiempo, del clima u otro relacionado con el ambiente'],
            ['12', '81', 'Asfixia'],
            ['13', '82', 'Efecto de la electricidad'],
            ['14', '83', 'Efecto nocivo de la radiación'],
            ['15', '90', 'Lesiones múltiples'],
            ['16', '99', 'Otro']            
        ];

        foreach ($types_lesion as $key => $value) 
        {
            $lesion = new TypeLesion;
            $lesion->id = $value[0];
            $lesion->code = $value[1];
            $lesion->name = $value[2];
            $lesion->save();
        }

        $mecanismos = [
            ['1', '1', 'Caída de personas'],
            ['2', '2', 'Caída de objetos'],
            ['3', '3', 'Pisadas, choques o golpes'],
            ['4', '4', 'Atrapamientos'],
            ['5', '5', 'Sobreesfuerzo, esfuerzo excesivo o falso movimiento'],
            ['6', '6', 'Exposición o contacto con temperatura extrema'],
            ['7', '7', 'Exposición o contacto con la electricidad'],
            ['8', '8', 'Exposición o contacto con sustancias nocivas, radiaciones o salpicaduras'],
            ['9', '9', 'Otro']            
        ];

        foreach ($mecanismos as $key => $value) 
        {
            $meca = new Mechanism;
            $meca->id = $value[0];
            $meca->code = $value[1];
            $meca->name = $value[2];
            $meca->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
