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
        \Log::info($this);
        $id = $this->input('id');

        $accidentLevels = ['Accidente', 'Accidente grave', 'Accidente mortal', 'Accidente leve', 'Incidente' ];
        $laborLinkingTypes = ['Empleado','Misión','Cooperativa de trabaso asociado', 'Estudiante o Aprendiz', 'Independiente'];
        $idTypes = ['NI','CC','CE','NU','PA'];
        $zones = ['Urbana', 'Rural'];
        $shifts = ['Diurna','Nocturna','Mixto','Turnos'];
        $workingDayTypes = ['Normal','Extra'];
        $accidentTypes = ['Violencia','Tránsito','Deportivo','Recreativo o cultural','Propios del trabajo'];
        $personLinkingTypes =  ['Planta', 'Misión', 'Cooperado', 'Estudiante o Aprendiz', 'Independiente'];
    


        $rules = [
            'tipo_vinculador_laboral' => 'required|string|in:' . implode(',', $laborLinkingTypes),
            //'tipo_vinculacion_persona' => 'nullable|string|in:' . implode(',', $personLinkingTypes),
            'nombre_persona' => 'nullable',
            'tipo_identificacion_persona' => 'nullable'/*'nullable|in:' . implode(',', $idTypes)*/,
            'identificacion_persona' => 'nullable',
            'fecha_nacimiento_persona' => 'nullable',
            'sexo_persona' => 'nullable',
            'direccion_persona' => 'required|string',
            'telefono_persona' => 'nullable',
            'email_persona' => 'nullable',
            'fax_persona' => 'nullable',
            'departamento_persona_id' => 'required',
            'ciudad_persona_id' => 'required|string',
            'zona_persona' => 'required|string|in:' . implode(',', $zones),
            'cargo_persona' => 'nullable',
            'salario_persona' => 'required|integer|min:0',
            'jornada_trabajo_habitual_persona' => 'required|string|in:' . implode(',', $shifts),
            'fecha_ingreso_empresa_persona' => 'nullable',
            'tiempo_ocupacion_habitual_persona' => 'required',
            /////////////////////////////////////////////////////////////////////////////

            'info_sede_principal_misma_centro_trabajo' => 'required_unless:tipo_vinculador_laboral,Independiente|string',
            'centro_trabajo_secundary_id' => 'required_if:info_sede_principal_misma_centro_trabajo,NO',

            //////////////////////////////////////////////////////////////
            'nivel_accidente' => 'required|string|in:' . implode(',', $accidentLevels),
            'investigation_arl' => 'required',
            'fecha_envio_arl' => 'required_unless:investigation_arl,SI',
            'employee_eps_id' => 'nullable',
            'employee_arl_id' => 'nullable',
            'employee_afp_id' => 'nullable',
            'tiene_seguro_social' => 'required|string',
            'nombre_seguro_social' => 'required_if:tiene_seguro_social,SI',
            /////////////////////////////////////////////////////////////
            'fecha_accidente' => 'required',
            'jornada_accidente' => 'nullable|string|in:' . implode(',', $workingDayTypes),
            'estaba_realizando_labor_habitual' => 'nullable|string',
            'otra_labor_habitual' => 'nullable:estaba_realizando_labor_habitual,NO',
            'total_tiempo_laborado' => 'nullable',
            'accidente_ocurrio_dentro_empresa' => 'required|string',
            'tipo_accidente' => 'nullable|string|in:' . implode(',', $accidentTypes),
            'departamento_accidente' => 'required_if:accidente_ocurrio_dentro_empresa,Fuera de la empresa',
            'ciudad_accidente' => 'required_if:accidente_ocurrio_dentro_empresa,Fuera de la empresa',
            'zona_accidente' => 'required_if:accidente_ocurrio_dentro_empresa,Fuera de la empresa',
            'causo_muerte' => 'nullable|string',
            'fecha_muerte' => 'nullable:causo_muerte,SI',

            'agent_id' => 'nullable|integer|exists:sau_aw_agents,id',
            'mechanism_id' => 'nullable|integer|exists:sau_aw_mechanisms,id',
            'site_id' => 'nullable|integer|exists:sau_aw_sites,id',

            'otro_sitio' => 'nullable',
            'otro_mecanismo' => 'nullable',
            'otra_lesion' => 'nullable',
            'otro_agente' => 'nullable',

            'parts_body_id' => 'nullable',
            'type_lesion_id' => 'nullable',
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
