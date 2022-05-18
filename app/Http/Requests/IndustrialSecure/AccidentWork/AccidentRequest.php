<?php

namespace App\Http\Requests\IndustrialSecure\AccidentWork;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class AccidentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->input('id');

        $accidentLevels = ['Accidente', 'Accidente grave', 'Accidente mortal', 'Accidente leve', 'Incidente' ];
        $laborLinkingTypes = ['Empleador','Contratante','Cooperativa de trabaso asociado'];
        $idTypes = ['NI','CC','CE','NU','PA'];
        $zones = ['Urbana', 'Rural'];
        $genders = ['M','F'];
        $shifts = ['Diurna','Nocturna','Mixto','Turnos'];
        $workingDayTypes = ['Normal','Extra'];
        $accidentTypes = ['Violencia','Tránsito','Deportivo','Recreativo o cultural','Propios del trabajo'];
    


        $rules = [
            'tipo_vinculador_laboral' => 'required|string|in:' . implode(',', $laborLinkingTypes),
            'tipo_vinculacion_persona' => 'required|string|in:' . implode(',', $laborLinkingTypes),
            'nombre_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|string|',
            'tipo_identificacion_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|string|in:' . implode(',', $idTypes),
            'identificacion_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|string|max:60',
            'fecha_nacimiento_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|date_format:Y-m-d',
            'sexo_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|string|in:' . implode(',', $genders),
            'direccion_persona' => 'required|string|max:100',
            'telefono_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|string|max:60',
            'email_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|email|max:60',
            'fax_persona' => 'nullable|string|max:60',
            'departamento_persona_id' => 'required|string|max:60',
            'ciudad_persona_id' => 'required|string|max:60',
            'zona_persona' => 'required|string|in:' . implode(',', $zones),
            'cargo_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|string|max:60',
            'salario_persona' => 'required|integer|min:0',
            'jornada_trabajo_habitual_persona' => 'required|string|in:' . implode(',', $shifts),
            'fecha_ingreso_empresa_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|date_format:Y-m-d',
            'tiempo_ocupacion_habitual_persona_hora' => 'required|integer|min:0',
            'tiempo_ocupacion_habitual_persona_minuto' => 'required|integer|min:0|max:60',
            /////////////////////////////////////////////////////////////////////////////
            'nombre_actividad_economica_sede_principal' => 'required|string|max:100',
            'razon_social' => 'required|string|max:100',
            'tipo_identificacion_sede_principal' => 'required|string|in:' . implode(',', $idTypes),
            'identificacion_sede_principal' => 'required|string|max:60',
            'direccion_sede_principal' => 'required|string|max:100',
            'telefono_sede_principal' => 'required|string|max:60',
            'fax_sede_principal' => 'required|string|max:60',
            'email_sede_principal' => 'required|email|max:60',
            'departamento_sede_principal' => 'required|string|max:60',
            'ciudad_sede_principal' => 'required|string|max:60',
            'zona_sede_principal' => 'required|string|in:' . implode(',', $zones),

            'info_sede_principal_misma_centro_trabajo' => 'required|string',

            'nombre_actividad_economica_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO|string|max:100',
            'direccion_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO|string|max:100',
            'telefono_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO|string|max:60',
            'fax_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO|string|max:60',
            'email_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO|email|max:60',
            'departamento_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO|string|max:60',
            'ciudad_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO|string|max:60',
            'zona_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO|string|in:' . implode(',', $zones),
            //////////////////////////////////////////////////////////////
            'nivel_accidente' => 'required|string|in:' . implode(',', $accidentLevels),
            'fecha_envio_arl' => 'required|date_format:Y-m-d',
            'fecha_envio_empresa' => 'required|date_format:Y-m-d',
            'coordinador_delegado' => 'required|string|max:100',
            'cargo' => 'required|string|max:100',
            'employee_eps_id' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'employee_arl_id' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'employee_afp_id' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'tiene_seguro_social' => 'required|string',
            'nombre_seguro_social' => 'required_if:tiene_seguro_social,SI|string|max:100',
            /////////////////////////////////////////////////////////////
            'fecha_accidente' => 'required',
            'jornada_accidente' => 'required|string|in:' . implode(',', $workingDayTypes),
            'estaba_realizando_labor_habitual' => 'required|string',
            'otra_labor_habitual' => 'required_if:estaba_realizando_labor_habitual,NO|string|max:45',
            'total_tiempo_laborado' => 'required',
            'accidente_ocurrio_dentro_empresa' => 'required|string',
            'tipo_accidente' => 'required|string|in:' . implode(',', $accidentTypes),
            'departamento_accidente' => 'required',
            'ciudad_accidente' => 'required',
            'zona_accidente' => 'required',
            'causo_muerte' => 'required|string',
            'fecha_muerte' => 'required_if:causo_muerte,SI',

            'agente_id' => 'required|integer|exists:sau_aw_agents,id',
            'mecanismo_id' => 'required|integer|exists:sau_aw_mechanisms,id',
            'sitio_id' => 'required|integer|exists:sau_aw_sites,id',

            'otro_sitio' => 'string',
            'otro_mecanismo' => 'string',
            'otra_lesion' => 'string',

            'parts_body' => 'required|array',
            'lesions_id' => 'required|array',
            //////////////////////////////////////////////////////////////////
            'descripcion_accidente' => 'required|string',
            'personas_presenciaron_accidente' => 'required|string',
            'nombres_apellidos_responsable_informe' => 'required|string',
            'cargo_responsable_informe' => 'required|string',
            'tipo_identificacion_responsable_informe' => 'required|string|in:'. implode(',', $idTypes),
            'identificacion_responsable_informe' => 'required|string',
            'persons' => 'array',
            'fecha_diligenciamiento_informe' => 'required',
            ///////////////////////////////////////////////////////////////////////

            'observaciones_empresa' => 'required|string',
            'files' => 'nullable',
            'participants_investigations' => 'required|array',
        ];

        return $rules;
    }
}
