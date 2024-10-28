<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Condition;

class AddInformationConditionsReportsInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $condition = [
            ['Aguas emposadas (pilas, tanques, brechas, pisos, MH, buitrones)', 1],
            ['Aparejos de izaje inadecuados y/o en mal estado', 1],
            ['Ausencia de protecciones de seguridad', 1],
            ['Ausencia en sistemas de acceso (Ferri, burros, escaleras, andamios)', 1],
            ['Ausencia de elementos para atencion de emergencia (extintores, camilla, botiquin)', 1],
            ['Camillas de emergencias obstáculizadas', 1],
            ['Complementos de maquinas y/o equipos en mal estado', 1],
            ['Cuartos, gabinetes, tableros eléctricos y/o tomacorrientes sin señalizar', 1],
            ['Elementos de proteccion personal y/o contra caida en mal estado o inadecuados', 1],
            ['Espacio reducido para el trabajo ', 1],
            ['Escombros o materiales a bordo de losa', 1],
            ['Falta de ventilación', 1],
            ['Malla de seguridad deficiente y/o en mal estado', 1],
            ['Pisos en malas condiciones (con desniveles, irregularidades, lisas y/o resbalosas)', 1],
            ['Presencia animales (plagas, ponzoñosos, entre otros)', 1],
            ['Protección inexistente o insuficiente en área de trabajo ', 1],
            ['Protecciones en mal estado (vacios, fosos de ascensor, buitrones, escalas, bordes de losa).', 1],
            ['Sillas en mal estado', 1],
            ['Sistemas de acceso no autorizados (hechizos, elementos no compatibles o elementos faltantes)', 1],
            ['Techos en mal estado, filtraciones y/o humedades', 1],
            ['Tomacorriente sin protección y/o señalización', 1],
            ['Alterar los dispositvos de seguridad (maquinas colgantes, andamios, plumas, concretadoras, entre otros)', 2],
            ['Cerrar o bloquear las entradas de aire en espacios confinados', 2],
            ['Comportamientos agresivos, riñas, peleas, hurtos, etc', 2],
            ['Diligenciamiento incorrecto o incompleto de permisos de trabajo y/o preoperacionales', 2],
            ['Dejar objetos, elementos y/o materiales a borde de losa', 2],
            ['Dejar caer objetos o elementos de pisos superiores o alturas considerables', 2],
            ['Ejecutar actividades simultaneas sin coordinar', 2],
            ['Falta de orden y aseo en las áreas de trabajo ', 2],
            ['Falta de autocuidado', 2],
            ['Lubricar, limpiar y ajustar equipos y herramientas en movimiento o sin desconectar', 2],
            ['Manipulación de tableros electricos por personal no autorizado', 2],
            ['No desenergizar equipos y/o herramientas electromecanicas cuando no se encuentran en uso', 2],
            ['No instalar señalización y/o demarcación ', 2],
            ['Omitir el uso de equipo de protección personal y/o equipos de altura', 2],
            ['Omitir la instalacion de protecciones de seguridad', 2],
            ['Operar equipos y máquinas sin autorización (preoperacional, carta de acreditacion)', 2],
            ['Persona en estado de alicoramiento y/o sustancias psicoactivas', 2],
            ['Realizar actividades de alto riesgo sin permiso de trabajo (alturas, confinados, caliente, energias peligrosas, izajes de carga)', 2],
            ['Realizar actividades en zonas no seguras', 2],
            ['Retirar protecciones de seguridad sin previa autorizacion', 2],
            ['Retirar guardas de seguridad de los equipos y herramientas', 2],
            ['Sobrecargar multitoma', 2],
            ['Transito de personas por areas prohibidas o peligrosas', 2],
            ['Uso de sistemas de acceso de manera insegura', 2],
            ['Usar sistemas de acceso en mal estado y no autorizados', 2]
        ];

        foreach ($condition as $value) 
        {
            $record = new Condition;
            $record->description = $value[0];
            $record->condition_type_id = $value[1];
            $record->save();
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
