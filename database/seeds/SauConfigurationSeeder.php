<?php

use Illuminate\Database\Seeder;
use App\Models\General\Configuration;

class SauConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'key' => 'biologicalmonitoring_audiometries_select_epp',
                'value' => '["Copa","Moldeable","Inserción","Ninguno","Otro"]',
                'observation' => 'Select para EPP de audiometrias'
            ],
            [
                'key' => 'biologicalmonitoring_audiometries_select_exposition_level',
                'value' => '["85 a 95 dB","No realizada","Menos de 80 dB","80 a 84.9 dB"]',
                'observation' => 'select para Nivel de exposicion de audiometrias'
            ],
            [
                'key' => 'action_plans_states',
                'value' => '["Pendiente","Ejecutada"]',
                'observation' => 'select para los estados de los planes de acción'
            ],
            [
                'key' => 'days_alert_user_suspension',
                'value' => '1000',
                'observation' => 'Cantidad de días para consultar si un usuario no ha iniciado sesión y enviar la notificación de alerta de suspensión'
            ],
            [
                'key' => 'days_user_suspension',
                'value' => '1007',
                'observation' => 'SI el usuario no ha iniciado sesion en esta cantidad de dias sera supendido'
            ],
            [
                'key' => 'days_alert_expired_license',
                'value' => '30',
                'observation' => 'Cantidad de días para consultar si una licencia esta próxima a vencerse y enviar la notificación de alerta'
            ],
            [
                'key' => 'admin_license_notification_email',
                'value' => 'carolina.madrid@rigs.com.co,caladsantiago@thotstrategy.com,asistenterl@rigs.com.co',
                'observation' => 'Administrador al que se notificara sobre las licencias próximas a vencer'
            ],
            [
                'key' => 'reinc_select_disease_origin',
                'value' => '["Enfermedad Laboral","Enfermedad General","Accidente de Trabajo","Maternidad"]',
                'observation' => 'Reincorporaciones - Opciones de Tipo de Evento'
            ],
            [
                'key' => 'reinc_select_lateralities',
                'value' => '["Derecho","Izquierdo","Derecho e izquierdo","NA"]',
                'observation' => 'Reincorporaciones - Opciones de Lateralidad'
            ],
            [
                'key' => 'reinc_select_origin_advisors',
                'value' => '["ARL","EPS","Médico de la empresa"]',
                'observation' => 'Reincorporaciones - Procedencia de las recomendaciones'
            ],
            [
                'key' => 'reinc_select_medical_conclusions',
                'value' => '["Estable","Mejorando","Empeorando","Sin información médica"]',
                'observation' => 'Reincorporaciones - Conclusión Seguimiento Médico'
            ],
            [
                'key' => 'reinc_select_labor_conclusions',
                'value' => '["Leve","Moderado","Severo"]',
                'observation' => 'Reincorporaciones - Conclusión Seguimiento Laboral'
            ],
            [
                'key' => 'reinc_select_emitter_origin',
                'value' => '["ARL","EPS","AFP","JUNTA REGIONAL","JUNTA NACIONAL","Sin Información"]',
                'observation' => 'Reincorporaciones - Entidad que Califica Origen'
            ],
            [
                'key' => 'days_delete_files_temporal',
                'value' => '10',
                'observation' => 'Cantidad de días para borrar archivos temporales'
            ]
        ];

        foreach ($data as $key => $value)
        {
            Configuration::updateOrCreate(['key' => $value['key']], $value);
        }
    }
}
