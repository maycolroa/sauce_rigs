<?php

use Illuminate\Database\Seeder;
use App\Models\LegalAspects\Contracts\ActionPlanDefault;

class CtActionPlanDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'Realizar contratación de profesional con perfil requerido de acuerdo a la Resolución 312 de 2019',
            'Documentar carta de asignación de responsable',
            'Definir las responsabilidades frente al SG-SST para todos los cargos de la empresa',
            'Definir los recursos técnicos, humanos y financieros para',
            'Afiliar a todo el personal a la seguridad social incluyendo a los trabajadores independientes, estudiantes en practica, trabajadores dependientes',
            'Identificar trabajadores de al riesgo de acuerdo al Decreto 2090 de 2003',
            'Realizar aportes a la seguridad social de acuerdo a lo establecido para trabajadores de alto riesgo',
            'Realizar convocatoria para la conformación del COPASST',
            'Realizar elección y conformación. Documentar acta de conformación',
            'Documentar acta de reunión mensual',
            'Capacitar al COPASST para garantizar la ejecución de sus funciones',
            'Realizar elección y conformación. Documentar acta de conformación',
            'Documentar acta de reunión trimestral',
            'Documentar el plan de capacitación de acuerdo a los peligros de la identificación de peligros, evaluación y valoración de riesgos',
            'Ejecutar el plan de capacitación de acuerdo a lo definido en este',
            'Documentar contenido de la inducción',
            'Realizar inducción para nuevos ingresos',
            'Realizar curso de 50 horas (responsable del SG-SST)',
            'Documentar la política del SG-SST garantizando que de cumplimiento a los requisitos',
            'Firmar y fechar la política del SG-SST',
            'Evaluar la política del SG-SST',
            'Divulgar la política del SG-SST al COPASST',
            'Definir los objetivos de acuerdo a las prioridades de la empresa',
            'Firmar los objetivos del SG-SST',
            'Divulgar los objetivos del SG-SST',
            'Realizar evaluación inicial del SG-SST',
            'Diseñar el plan de trabajo anual',
            'Ejecutar el plan de trabajo anual',
            'Documentar acciones correctivas por los no cumplimientos al plan de trabajo',
            'Documentar un procedimiento de archivo y retención documental',
            'Definir los mecánismos para la rendición de cuentas',
            'Rendir cuentas frente al cumplimiento de las responsabilidades del SG-SST',
            'Documentar la matriz de requisitos legales en SST',
            'Actualizar la matriz de requisitos legales',
            'Definir mecánismos de comunicación',
            'Documentar un procedimiento para la identificación y evaluación de las especificaciones en SST de las compras o adquisiciones',
            'Documentar un manual para la administración de contratistas y proveedores',
            'Documentar procedimiento de gestión del cambio',
            'Documentar o actualizar la descripción sociodemográfica',
            'Definir las estrategias de medicina del trabajo y medicina preventiva de acuerdo al diagnóstico de condiciones de salud',
            'Comunicar los perfiles de cargo y demás información necesaria para realizar los exámenes medicos a la IPS que los realiza',
            'Documentar el profesiograma o perfil ocupacional de los cargos de la empresa especificando la frecuencia',
            'Comunicar a los trabajadores el resultado de las evaluaciones médicas ocupacionales',
            'Solicitar certificado de custodía de historias clinicas a proveedor o IPS que realiza los exámenes médicos ocupacionales',
            'Documentar'
        ];

        DB::beginTransaction();

        try
        {   
            $this->command->info('Comienza el seeder de los planes de accion por defecto del módulo de contratistas');

            foreach ($data as $key => $value)
            {
                ActionPlanDefault::updateOrCreate(['description' => $value], ['description' => $value]);
            }

            DB::commit();
            $this->command->info('Proceso terminado exitosamente');

        } catch (\Exception $e) {
            DB::rollback();
            $this->command->info('Ocurrio un error al ejecutar la clase CtActionPlanDefaultSeeder');
        }
    }
}
