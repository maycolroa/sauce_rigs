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
            'Documentar procedimiento para la gestión y seguimiento de recomendaciones médicas y restricciones laborales',
            'Divulgar la política a todo el personal',
            'Documentar un programa de estilos de vida saludable',
            'Diseñar campañas para la prevención del consumo de alcohol, tabaco y drogas',
            'Garantizar el suministro de agua potable, servicios sanitarios, mecanismos para disponer excretas y basuras',
            'Disponer de los elementos necesarios para la disposición de residuos sólidos, líquidos y gaseosos',
            'Reportar los accidentes de trabajo y enfermedades laborales a la ARL',
            'Realizar reporte de los accidentes de trabajo a la EPS a la que se encuentre afiliado el trabajador',
            'Realizar reporte de los accidentes graves y mortales a la Dirección Territorial del Ministerio de Trabajo',
            'Investigar los incidentes de trabajo con la participación del COPASST',
            'Investigar los accidentes de trabajo con la participación del COPASST',
            'Investigar los accidentes graves y mortales con participación del COPASST y de profesional con licencia en SST con alcance de investigación de accidentes de trabajo',
            'Registrar estádisticamente los accidentes de trabajo y enfermedades laborales',
            'Realizar análisis del registro estadistico de accidentes de trabajo y enfermedades laborales',
            'Realizar cálculo de indicador de frecuencia de accidentalidad',
            'Realizar medición de indicadores de severidad de la accidentalidad',
            'Realizar medición de la mortalidad de los accidentes de trabajo',
            'Realizar medición de la prevalencia de la enfermedad laboral',
            'Realizar medición de la incidencia de la enfermedad laboral',
            'Medir el ausentismo global por incapacidades de origen laboral y común',
            'Documentar el procedimiento para la identificación de peligros, evaluación y valoración de riesgos',
            'Alinear la identificación de peligros, evaluación y valoración de riesgos de acuerdo a lo definido en la metodología descrita',
            'Registrar la participación de los trabajadores en la identificación de peligros, evaluación y valoración de riesgos',
            'Actualizar la identificación de peligros, evaluación y valoración de riesgos de acuerdo a los accidentes mortales y catastroficos',
            'Realizar actualización anual de la identificación de peligros, evaluación y valoración de riesgos',
            'Identificar las sustancias químicas cancerigenas utilizadas en el proceso',
            'Priorizar en la identificación de peligros, evaluación y valoración de riesgos de los peligros químicos por la exposición a sustancias cancerigenas',
            'Programar las mediciones ambientales necesarias en el plan de trabajo del SG-SST',
            'Registrar los informes y resultados de las mediciones de higiene',
            'Compartir con el COPASST los resultados de las evaluaciones ambientales',
            'Implementar las acciones de los peligros prioritariarios de acuerdo a la jerarquiización',
            'Incluir las medidas de intervención en el plan de trabajo',
            'Evidenciar el cumplimiento de las responsabilidades de los trabajadores (seguimiento comportamientos seguros, participación en capacitaciones, uso de EPP, etc)',
            'Documentar fichas, instructivos, estándares, procedimientos de trabajo seguro de acuerdo a priorización',
            'Definir formatos para realizar inspecciones a las instalaciones, equipos, herramientas y equipos de emergencias',
            'Realizar inspecciones de seguridad con la participación del COPASST',
            'Definir un programa de mantenimiento que incluya la periodicidad de los mismos a las instalaciones, equipos y herramientas',
            'Realizar mantenimientos de acuerdo a lo definido en el programa de mantenimiento',
            'Documentar la matriz de elementos de protección personal',
            'Documentar la entrega y reposición de elementos de protección personal al personal dependiente',
            'Solicitar los soportes que evidencien la entrega de elementos de protección personal al personal contatrista, cooperado y en misión',
            'Capacitar a todo el personal en uso de los elementos de protección personal',
            'Documentar el plan de prevención, preparación y atención de emergencias incluido la identificación de amenazas y de vulnerabilidad',
            'Diseñar planos de evacuación y publicar',
            'Realizar simulacro de evacuación y generar plan de mejoramiento de acuerdo a los hallazgos',
            'Conformar la brigada de emergencias',
            'Capacitar a la brigada de emergencias',
            'Documentar los indicadores de estructura, proceso y resultado',
            'Realizar medición y análisis de los indicadores de estructura, proceso y resultado',
            'Documentar el programa o procedimiento de auditorias internas del SG-SST',
            'Realizar las auditorias internas',
            'Realizar la revisión por la dirección',
            'Planificar las auditorias internas con la participación del COPASST',
            'Documentar las acciones correctivas y de mejora del SG-SST',
            'Realizar seguimiento al cumplimiento de las acciones correctivas y de mejora',
            'Documentar las acciones correctivas y de mejora derivadas de la revisión por la dirección',
            'Realizar seguimiento al cumplimiento de las acciones correctivas y de mejora',
            'Documentar las acciones correctivas y de mejora derivadas de las investigaciones de los accidentes de trabajo y enfermedades laborales',
            'Realizar seguimiento al cumplimiento de las acciones correctivas y de mejora',
            'Realizar seguimiento a las acciones correctivas generadas por las autoridades administrativas y la ARL'
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
