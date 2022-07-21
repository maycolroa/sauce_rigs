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

    public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    public function sanitize()
    {
        if ($this->has('files'))
        {
            foreach ($this->input('files') as $key => $value)
            {
                $data['files'][$key] = json_decode($value, true);
                $this->merge($data);
            }

            if ($this->has('files_binary') && COUNT($this->files_binary) > 0)
            {
                $data = $this->all();

                foreach ($this->files_binary as $key => $value)
                {
                    $data['files'][$key]['file'] = $value;
                }

                $this->merge($data);
            }
        }
        
        if ($this->has('participants_investigations'))
        {
            $this->merge([
                'participants_investigations' => json_decode($this->input('participants_investigations'), true)
            ]);
        }

        if ($this->has('persons'))
        {
            $this->merge([
                'persons' => json_decode($this->input('persons'), true)
            ]);
        }

        if ($this->has('actionPlan'))
        {
            $this->merge([
                'actionPlan' => json_decode($this->input('actionPlan'), true)
            ]);
        }

        return $this->all();
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
        $laborLinkingTypes = ['Empleador','Misión','Cooperativa de trabaso asociado', 'Estudiante o Aprendiz', 'Independiente'];
        $idTypes = ['NI','CC','CE','NU','PA'];
        $zones = ['Urbana', 'Rural'];
        $shifts = ['Diurna','Nocturna','Mixto','Turnos'];
        $workingDayTypes = ['Normal','Extra'];
        $accidentTypes = ['Violencia','Tránsito','Deportivo','Recreativo o cultural','Propios del trabajo'];
        $personLinkingTypes =  ['Planta', 'Misión', 'Cooperado', 'Estudiante o Aprendiz', 'Independiente'];
    


        $rules = [
            'tipo_vinculador_laboral' => 'required|string|in:' . implode(',', $laborLinkingTypes),
            //'tipo_vinculacion_persona' => 'nullable|string|in:' . implode(',', $personLinkingTypes),
            'nombre_persona' => 'required_unless:tipo_vinculador_laboral,Empleador|',
            'tipo_identificacion_persona' => 'nullable'/*'required_unless:tipo_vinculador_laboral,Empleador|in:' . implode(',', $idTypes)*/,
            'identificacion_persona' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'fecha_nacimiento_persona' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'sexo_persona' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'direccion_persona' => 'required|string',
            'telefono_persona' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'email_persona' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'fax_persona' => 'nullable',
            'departamento_persona_id' => 'required',
            'ciudad_persona_id' => 'required|string',
            'zona_persona' => 'required|string|in:' . implode(',', $zones),
            'cargo_persona' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'salario_persona' => 'required|integer|min:0',
            'jornada_trabajo_habitual_persona' => 'required|string|in:' . implode(',', $shifts),
            'fecha_ingreso_empresa_persona' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'tiempo_ocupacion_habitual_persona' => 'required',
            /////////////////////////////////////////////////////////////////////////////
            'nombre_actividad_economica_sede_principal' => 'required|string',
            'razon_social' => 'required_unless:tipo_vinculador_laboral,Independiente|string',
            'tipo_identificacion_sede_principal' => 'required_unless:tipo_vinculador_laboral,Independiente|string|in:' . implode(',', $idTypes),
            'identificacion_sede_principal' => 'required_unless:tipo_vinculador_laboral,Independiente|string',
            'direccion_sede_principal' => 'required_unless:tipo_vinculador_laboral,Independiente|string',
            'telefono_sede_principal' => 'required_unless:tipo_vinculador_laboral,Independiente|string',
            'email_sede_principal' => 'required_unless:tipo_vinculador_laboral,Independiente|email',
            'departamento_sede_principal_id' => 'required_unless:tipo_vinculador_laboral,Independiente|string',
            'ciudad_sede_principal_id' => 'required_unless:tipo_vinculador_laboral,Independiente|string',
            'zona_sede_principal' => 'required_unless:tipo_vinculador_laboral,Independiente|string|in:' . implode(',', $zones),

            'info_sede_principal_misma_centro_trabajo' => 'required_unless:tipo_vinculador_laboral,Independiente|string',

            'nombre_actividad_economica_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO',
            'direccion_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO',
            'telefono_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO',
            'email_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO',
            'departamento_centro_trabajo_id' => 'required_if:info_sede_principal_misma_centro_trabajo,NO',
            'ciudad_centro_trabajo_id' => 'required_if:info_sede_principal_misma_centro_trabajo,NO',
            'zona_centro_trabajo' => 'required_if:info_sede_principal_misma_centro_trabajo,NO',
            //////////////////////////////////////////////////////////////
            'nivel_accidente' => 'required|string|in:' . implode(',', $accidentLevels),
            'fecha_envio_arl' => 'required',
            'fecha_envio_empresa' => 'required',
            'coordinador_delegado' => 'required|string',
            'cargo' => 'required|string',
            'employee_eps_id' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'employee_arl_id' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'employee_afp_id' => 'required_unless:tipo_vinculador_laboral,Empleador',
            'tiene_seguro_social' => 'required|string',
            'nombre_seguro_social' => 'required_if:tiene_seguro_social,SI',
            /////////////////////////////////////////////////////////////
            'fecha_accidente' => 'required',
            'jornada_accidente' => 'required|string|in:' . implode(',', $workingDayTypes),
            'estaba_realizando_labor_habitual' => 'required|string',
            'otra_labor_habitual' => 'required_if:estaba_realizando_labor_habitual,NO',
            'total_tiempo_laborado' => 'required',
            'accidente_ocurrio_dentro_empresa' => 'required|string',
            'tipo_accidente' => 'required|string|in:' . implode(',', $accidentTypes),
            'departamento_accidente' => 'required_if:accidente_ocurrio_dentro_empresa,Fuera de la empresa',
            'ciudad_accidente' => 'required_if:accidente_ocurrio_dentro_empresa,Fuera de la empresa',
            'zona_accidente' => 'required_if:accidente_ocurrio_dentro_empresa,Fuera de la empresa',
            'causo_muerte' => 'required|string',
            'fecha_muerte' => 'required_if:causo_muerte,SI',

            'agent_id' => 'required|integer|exists:sau_aw_agents,id',
            'mechanism_id' => 'required|integer|exists:sau_aw_mechanisms,id',
            'site_id' => 'required|integer|exists:sau_aw_sites,id',

            'otro_sitio' => 'nullable',
            'otro_mecanismo' => 'nullable',
            'otra_lesion' => 'nullable',
            'otro_agente' => 'nullable',

            'parts_body_id' => 'required',
            'type_lesion_id' => 'required',
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
