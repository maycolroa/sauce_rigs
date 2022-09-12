<?php

namespace App\Traits;

trait RiskMatrixTrait
{
    protected function getControlEvaluation()
    {
        $matriz = [];

        $matriz['Automático']['SI']['Total']['Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['Parcialmente Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['No Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['Documentado']['NO'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['Parcialmente Documentado']['NO'] = 'Altamente Efectivo';
        $matriz['Automático']['SI']['Total']['No Documentado']['NO'] = 'Altamente Efectivo';
        $matriz['Automático']['NO']['Total']['Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['NO']['Total']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['NO']['Total']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['NO']['Total']['Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['SI']['Parcial']['Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['SI']['Parcial']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['SI']['Parcial']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['NO']['Parcial']['Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['NO']['Total']['No Documentado']['NO'] = 'Efectivo';
        $matriz['Automático']['SI']['Parcial']['Documentado']['NO'] = 'Efectivo';
        $matriz['Mixto']['NO']['Total']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['NO']['Total']['No Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Total']['Documentado']['SI'] = 'Altamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['No Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Parcial']['Documentado']['SI'] = 'Efectivo';
        $matriz['Automático']['NO']['Total']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Total']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Total']['Documentado']['NO'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['Parcialmente Documentado']['NO'] = 'Efectivo';
        $matriz['Mixto']['SI']['Total']['No Documentado']['NO'] = 'Efectivo';
        $matriz['Mixto']['NO']['Total']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Total']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['SI']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['SI']['Parcial']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Total']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['NO']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Parcial']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Manual']['SI']['Total']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Total']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Mixto']['SI']['Parcial']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Manual']['NO']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Total']['No Documentado']['SI'] = 'Efectivo';
        $matriz['Manual']['NO']['Total']['Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Total']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Automático']['SI']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['SI']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['SI']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['SI']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['SI']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['SI']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['NO']['Parcial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['NO']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['NO']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Total']['No Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Parcial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Parcial']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Parcial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['SI']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['SI']['Inmaterial']['Parcialmente Documentado']['SI'] = 'Inefectivo';
        $matriz['Mixto']['NO']['Inmaterial']['Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['SI']['Total']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Total']['Parcialmente Documentado']['SI'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Total']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Total']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['NO']['Parcial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Parcial']['Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['SI']['Parcial']['Parcialmente Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Automático']['SI']['Inmaterial']['Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['SI']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['SI']['Inmaterial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Inefectivo';
        $matriz['Automático']['NO']['Inmaterial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Mixto']['SI']['Inmaterial']['Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Parcial']['Parcialmente Documentado']['SI'] = 'Efectivo';
        $matriz['Mixto']['NO']['Parcial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Parcial']['No Documentado']['NO'] = 'Medianamente Efectivo';
        $matriz['Manual']['NO']['Total']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['NO']['Parcial']['Parcialmente Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['NO']['Parcial']['No Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Inmaterial']['No Documentado']['SI'] = 'Inefectivo';
        $matriz['Manual']['NO']['Inmaterial']['No Documentado']['SI'] = 'Sin Mitigación';
        $matriz['Mixto']['SI']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Mixto']['SI']['Inmaterial']['No Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Mixto']['NO']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Mixto']['NO']['Inmaterial']['No Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['NO']['Inmaterial']['Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['NO']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['SI']['Inmaterial']['No Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['NO']['Inmaterial']['No Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Manual']['SI']['Inmaterial']['Documentado']['NO'] = 'Inefectivo';
        $matriz['Manual']['SI']['Inmaterial']['Parcialmente Documentado']['NO'] = 'Sin Mitigación';
        $matriz['Mixto']['NO']['Inmaterial']['Documentado']['NO'] = 'Sin Mitigación';

        return $matriz;
    }

    public function getDescriptionImpactsFrequency()
    {
        $data = [];

        $data['impact'][1] = 'No Significativo';
        $data['impact'][2] = 'Leve';
        $data['impact'][3] = 'Moderado';
        $data['impact'][4] = 'Grave';
        $data['impact'][5] = 'Extremo';
        $data['frecuency'][1] = 'Muy Baja';
        $data['frecuency'][2] = 'Baja';
        $data['frecuency'][3] = 'Moderada';
        $data['frecuency'][4] = 'Alta';
        $data['frecuency'][5] = 'Muy Alta';
        
        return $data;
    }

    public function percentageMitigation()
    {
        $data = [];

        $data['Altamente Efectivo'] = 80;
        $data['Efectivo'] = 60;
        $data['Medianamente Efectivo'] = 45;
        $data['Inefectivo'] = 20;
        $data['Sin Mitigación'] = 0;
        
        return $data;
    }

    public function textHelp()
    {
        $data = [];

        $data = [
            'economico' => '0 No aplica
            1 No Significativo: Pérdidas Económicas hasta el 0.10% del patrimonio
            2 Leve: Pérdidas Económicas entre 0.11% y 0.20% del patrimonio.
            3 Moderado: Pérdidas Económicas entre el 0.21% y 0.30% del patrimonio.
            4 Grave: Pérdidas Económicas entre el 0.31% y 0.40% del patrimonio.
            5 Extremo: Pérdidas Económicas de más de 0.4% del patrimonio.',
            'calidad' => '0 No aplica
            1 No Significativo: No hay lesión o lesión menor o tratamiento no significativo / No identificado por el paciente.
            2 Leve: Lesión leve que requiere tratamiento  e intervención leve sin cambio en la complejidad de la atención.
            3 Moderado: Lesión o enfermedad que requiere aumento en sus días de estancia o cambio en la complejidad de la atención .
            4 Grave: Lesión de largo plazo o discapacidad permanente menor.
            5 Extremo: Discapacidad permanente mayor o efecto psicológico mayor (en el paciente o su familia) o Muerte.',
            'reputacional' => '0 No aplica
            1 No significativo: El hecho no afecta la confianza y credibilidad en ningún grupo de interés ni tiene despliegue por medios masivos de comunicación. El hecho es conocido al interior de la Institución.
            2 Leve: El hecho afecta la confianza y credibilidad en los grupos de interés cuyo Coeficiente de Cn  esté en el rango (0.1 – 0.3) /  el hecho tiene despliegue por medios masivos de comunicación locales. 
            3 Moderado: El hecho afecta la confianza y credibilidad en los grupos de interés cuyo Coeficiente de Cn esté en el rango (0.31 – 0.6) / el hecho tiene despliegue por medios masivos de comunicación locales y regionales. 
            4 Grave: El hecho afecta la confianza y credibilidad en los grupos de interés cuyo Coeficiente de Cn esté en el rango (0.61 – 0.9) / el hecho tiene despliegue por medios masivos de comunicación locales, regionales y nacionales. 
            5 Extremo: El hecho afecta la confianza y credibilidad en todos los grupos de interés de la organización y en otros públicos afectados / el hecho tiene despliegue por medios masivos de comunicación locales, regionales, nacionales e internacionales.',
            'legal' => '0 No aplica
            1 No Significativo: Queja ante autoridad administrativa 
            2 Leve: Observaciones por parte de los entes de control con plazo para cumplimiento de acciones 
            3 Moderado: Demandas que impliquen indemnizaciones o Multas o sanciones por incumplimiento de la normatividad 
            4 Grave: Intervención total o parcial de la Entidad 
            5 Extremo: Suspensión temporal de actividades o  no habilitación del servicio ',
            'ambiental' => '0 No aplica
            1 No Significativo: Afectación transitoria de un ecosistema sin consecuencias ambientales
            2 Leve: Afectación ambiental recuperable de un ecosistema de corto plazo (de un día a un mes) 
            3 Moderado: Afectación ambiental recuperable de un ecosistema de mediano plazo (de un mes a doce meses) 
            4 Grave: Afectación ambiental recuperable de un ecosistema de largo plazo (mayor a un año) 
            5 Extremo: Afectación ambiental que implica un daño irreversible en un ecosistema ',
            'max_frequence' => '1 Muy Bajo: Podría suceder hasta 1 vez cada 3 años - Se tiene entre 1%  a 10% de seguridad que éste se presente 
            2 Bajo: Podría suceder hasta una vez al año - Se tiene entre 11%  a 30% de seguridad que éste se presente.
            3 Moderado: Podría suceder hasta cuatro veces al año - Se tiene entre 31%  a 50% de seguridad que éste se presente.
            4 Alto: Podría suceder hasta doce veces al año - Se tiene entre 51%  a 70% de seguridad que éste se presente.
            5 Muy Alto: Podría suceder más de doce veces al año - Se tiene entre 71%  a 100% de seguridad que éste se presente',
            'naturaleza' => 'Manual: Ejecutado por colaboradores responsables de la ejecución del proceso
            Mixto: Su ejecución involucra la participación de colaboradores y aplicativos de sistemas.             
            Automático: Control se ejecuta directamente por un aplicativo o un sistema.',
            'evidencia' => 'Si: La ejecución del control deja evidencia de su aplicación. (especificar donde )
            No: La ejecución del control  no deja evidencia de su aplicación.',
            'cobertura' => 'Inmaterial: Pocas veces que se genera las causas de un evento de riesgo, se ejecuta el control y/o La cobertura del control no mitiga el impacto del evento de riesgo significativamente.

            Parcial: Se ejecuta el control con una frecuencia definida, sin importar s ise presentan las causas y/o el control mitiga parcialmente el impacto.
            
            Total: Se ejecuta el control siempre que se presentan las causas y/o el control mitiga totalmente el impacto del evento de riesgo.',
            'documentacion' => 'No Documentado: Procedimiento del control no documentado.

            Parcialmente Documentado: Procedimiento del control parcialmente documentado y/o desactualizado.
            
            Documentado: Procedimiento del control documentado y actualizado.',
            'segregacion' => 'Si: La ejecución o revisión del control la realiza un superior jerárquico al colaborador que ejecuta el proceso asociado a la causa del evento de riesgo.

            No: La ejecución o revisión del control la realiza el mismo colaborador que ejecuta el proceso asociado a la causa del evento de riesgo'
        ];
        
        return $data;
    }

    protected function getMatrixReport()
    {
        $matriz = [];

        //[frecuencia][impacto]
        
        $matriz['Muy Baja']['Extremo'] = ['color' => 'warning', 'count' => 0];
        $matriz['Muy Baja']['Grave'] = ['color' => 'warning', 'count' => 0];
        $matriz['Muy Baja']['Moderado'] = ['color' => 'success', 'count' => 0];
        $matriz['Muy Baja']['Leve'] = ['color' => 'success', 'count' => 0];
        $matriz['Muy Baja']['No Significativo'] = ['color' => 'success', 'count' => 0];

        $matriz['Baja']['Extremo'] = ['color' => 'orange', 'count' => 0];
        $matriz['Baja']['Grave'] = ['color' => 'warning', 'count' => 0];
        $matriz['Baja']['Moderado'] = ['color' => 'warning', 'count' => 0];
        $matriz['Baja']['Leve'] = ['color' => 'success', 'count' => 0];
        $matriz['Baja']['No Significativo'] = ['color' => 'success', 'count' => 0];

        $matriz['Moderada']['Extremo'] = ['color' => 'primary', 'count' => 0];
        $matriz['Moderada']['Grave'] = ['color' => 'orange', 'count' => 0];
        $matriz['Moderada']['Moderado'] = ['color' => 'warning', 'count' => 0];
        $matriz['Moderada']['Leve'] = ['color' => 'warning', 'count' => 0];
        $matriz['Moderada']['No Significativo'] = ['color' => 'success', 'count' => 0];

        $matriz['Alta']['Extremo'] = ['color' => 'primary', 'count' => 0];
        $matriz['Alta']['Grave'] = ['color' => 'primary', 'count' => 0];
        $matriz['Alta']['Moderado'] = ['color' => 'orange', 'count' => 0];
        $matriz['Alta']['Leve'] = ['color' => 'warning', 'count' => 0];
        $matriz['Alta']['No Significativo'] = ['color' => 'warning', 'count' => 0];

        $matriz['Muy Alta']['Extremo'] = ['color' => 'primary', 'count' => 0];
        $matriz['Muy Alta']['Grave'] = ['color' => 'primary', 'count' => 0];
        $matriz['Muy Alta']['Moderado'] = ['color' => 'primary', 'count' => 0];
        $matriz['Muy Alta']['Leve'] = ['color' => 'orange', 'count' => 0];
        $matriz['Muy Alta']['No Significativo'] = ['color' => 'warning', 'count' => 0];

        return $matriz;
    }
}