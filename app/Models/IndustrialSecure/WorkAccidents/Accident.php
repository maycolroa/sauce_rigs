<?php

namespace App\Models\IndustrialSecure\WorkAccidents;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use App\Models\General\Departament;
use App\Models\General\Municipality;

class Accident extends Model
{
    use CompanyTrait;

    protected $table = 'sau_aw_form_accidents';

    protected $fillable = [
        'company_id',
        'employee_id',
        'tipo_vinculacion_persona',
        'nombre_persona',
        'tipo_identificacion_persona',
        'identificacion_persona',
        'fecha_nacimiento_persona',
        'sexo_persona',
        'direccion_persona',
        'telefono_persona',
        'email_persona',
        'departamento_persona_id',
        'ciudad_persona_id',
        'zona_persona',
        'cargo_persona',
        'employee_position_id',
        'tiempo_ocupacion_habitual_persona',
        'fecha_ingreso_empresa_persona',
        'salario_persona',
        'jornada_trabajo_habitual_persona',
        'tipo_vinculador_laboral',
        'razon_social',
        'nombre_actividad_economica_sede_principal',
        'tipo_identificacion_sede_principal',
        'identificacion_sede_principal',
        'direccion_sede_principal',
        'telefono_sede_principal',
        'email_sede_principal',
        'departamento_sede_principal_id',
        'ciudad_sede_principal_id',
        'zona_sede_principal',
        'info_sede_principal_misma_centro_trabajo',
        'nombre_actividad_economica_centro_trabajo',
        'direccion_centro_trabajo',
        'telefono_centro_trabajo',
        'email_centro_trabajo',
        'departamento_centro_trabajo_id',
        'ciudad_centro_trabajo_id',
        'zona_centro_trabajo',
        'nivel_accidente',
        'fecha_envio_arl',
        'fecha_envio_empresa',
        'coordinador_delegado',
        'cargo',
        'employee_eps_id',
        'employee_arl_id',
        'employee_afp_id',
        'tiene_seguro_social',
        'nombre_seguro_social',
        'fecha_accidente',
        'jornada_accidente',
        'estaba_realizando_labor_habitual',
        'otra_labor_habitual',
        'total_tiempo_laborado',
        'tipo_accidente',
        'departamento_accidente',
        'ciudad_accidente',
        'zona_accidente',
        'accidente_ocurrio_dentro_empresa',
        'causo_muerte',
        'fecha_muerte',
        'otro_sitio',
        'otro_mecanismo',
        'otra_lesion',
        'descripcion_accidente',
        'personas_presenciaron_accidente',
        'nombres_apellidos_responsable_informe',
        'cargo_responsable_informe',
        'tipo_identificacion_responsable_informe',
        'identificacion_responsable_informe',
        'fecha_diligenciamiento_informe',
        'observaciones_empresa',
        'consolidado',
        'user_id',
        'site_id',
        'agent_id',
        'mechanism_id',
    ];

    public function agentAccident()
    {
        return $this->belongsTo(Agent::class, 'agent_id');
    }
    public function mechanismAccident()
    {
        return $this->belongsTo(Mechanism::class, 'mechanism_id');
    }
    public function siteAccident()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function partsBody()
    {
        return $this->belongsToMany(PartBody::class, 'sau_aw_form_accidents_parts_body', 'form_accident_id', 'part_body_id');
    }

    public function lesionTypes()
    {
        return $this->belongsToMany(TypeLesion::class, 'sau_aw_form_accidents_types_lesion', 'form_accident_id', 'type_lesion_id');
    }

    public function departamentPerson()
    {
        return $this->belongsTo(Departament::class, 'departamento_persona_id');
    }

    public function departamentSede()
    {
        return $this->belongsTo(Departament::class, 'departamento_sede_principal_id');
    }

    public function departamentCentro()
    {
        return $this->belongsTo(Departament::class, 'departamento_centro_trabajo_id');
    }

    public function departamentAccident()
    {
        return $this->belongsTo(Departament::class, 'departamento_accidente');
    }

    public function ciudadPerson()
    {
        return $this->belongsTo(Municipality::class, 'ciudad_persona_id');
    }

    public function ciudadSede()
    {
        return $this->belongsTo(Municipality::class, 'ciudad_sede_principal_id');
    }

    public function ciudadCentro()
    {
        return $this->belongsTo(Municipality::class, 'ciudad_centro_trabajo_id');
    }

    public function ciudadAccident()
    {
        return $this->belongsTo(Municipality::class, 'ciudad_accidente');
    }

    public function eps()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\EmployeeEPS', 'employee_eps_id');
    }

    public function afp()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\EmployeeAFP', 'employee_afp_id');
    }

    public function arl()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\EmployeeARL', 'employee_arl_id');
    }

    public function personas()
    {
        return $this->hasMany(Person::class, 'form_accident_id');
    }

    /**
     * filters checks through the given deal
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $deal
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInIdentification($query, $identificacion_persona, $typeSearch = 'IN')
    {
        if (COUNT($identificacion_persona) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.identificacion_persona', $identificacion_persona);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.identificacion_persona', $identificacion_persona);
        }

        return $query;
    }

    public function scopeInName($query, $name, $typeSearch = 'IN')
    {
        if (COUNT($name) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.nombre_persona', $name);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.nombre_persona', $name);
        }

        return $query;
    }

    public function scopeInSexs($query, $sexs, $typeSearch = 'IN')
    {
        if (COUNT($sexs) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.sexo_persona', $sexs);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.sexo_persona', $sexs);
        }

        return $query;
    }

    public function scopeInSocialReason($query, $razonSocial, $typeSearch = 'IN')
    {
        if (COUNT($razonSocial) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.razon_social', $razonSocial);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.razon_social', $razonSocial);
        }

        return $query;
    }

    public function scopeInActivityEconomic($query, $activityEconomic, $typeSearch = 'IN')
    {
        if (COUNT($activityEconomic) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.nombre_actividad_economica_sede_principal', $activityEconomic);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.nombre_actividad_economica_sede_principal', $activityEconomic);
        }

        return $query;
    }

    public function scopeInCargo($query, $cargos, $typeSearch = 'IN')
    {
        if (COUNT($cargos) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.cargo_persona', $cargos);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.cargo_persona', $cargos);
        }

        return $query;
    }

    public function scopeInAgents($query, $agents, $typeSearch = 'IN')
    {
        if (COUNT($agents) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.agent_id', $agents);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.agent_id', $agents);
        }

        return $query;
    }

    public function scopeInMechanisms($query, $mechanisms, $typeSearch = 'IN')
    {
        if (COUNT($mechanisms) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.mechanism_id', $mechanisms);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.mechanism_id', $mechanisms);
        }

        return $query;
    }

    public function scopeInDepartamentAccident($query, $departaments, $typeSearch = 'IN')
    {
        if (COUNT($departaments) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.departamento_accidente', $departaments);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.departamento_accidente', $departaments);
        }

        return $query;
    }

    public function scopeInCityAccident($query, $citys, $typeSearch = 'IN')
    {
        if (COUNT($citys) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_aw_form_accidents.ciudad_accidente', $citys);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_aw_form_accidents.ciudad_accidente', $citys);
        }

        return $query;
    }
}
