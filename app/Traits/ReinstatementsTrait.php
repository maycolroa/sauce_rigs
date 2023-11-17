<?php

namespace App\Traits;

use App\Traits\ConfigurableFormTrait;
use Exception;

trait ReinstatementsTrait
{
    use ConfigurableFormTrait;
    
    public function getRules($params = [])
    {
        $formModel = $this->getFormModel('form_check');

        //Default
        $rules = [
            'employee_id' => 'required|exists:sau_employees,id',
            'has_recommendations' => "required",
            'start_recommendations' => 'nullable|date',
            'indefinite_recommendations' => "nullable",
            'end_recommendations' => 'nullable|date|after_or_equal:start_recommendations',
            'relocated' => "nullable",
            'monitoring_recommendations' => 'nullable|date',
            'origin_recommendations' => "nullable",
            'detail' => 'nullable|string',
            'has_restrictions' => "required",
            'restriction_id' => 'nullable|exists:sau_reinc_restrictions,id',
            'medical_monitorings' => 'array',
            'labor_monitorings' => 'array',
            'in_process_origin' => "required",
            'process_origin_done' => "nullable",
            'process_origin_done_date' => 'nullable|date',
            'emitter_origin' => "nullable",
            'date_controversy_origin_1'=> 'nullable|date',
            'date_controversy_origin_2'=> 'nullable|date',
            'emitter_controversy_origin_1' => "nullable",
            'emitter_controversy_origin_2' => "nullable",
            'in_process_pcl' => "required",
            'process_pcl_done' => "nullable",
            'process_pcl_done_date' => 'nullable|date',
            'pcl' => 'nullable|numeric|min:0|max:100',
            'entity_rating_pcl' => 'nullable|string|max:191',
            'process_pcl_file' => 'max:10000',
            'date_controversy_pcl_1'=> 'nullable|date',
            'date_controversy_pcl_2'=> 'nullable|date',
            'punctuation_controversy_plc_1' => "nullable|numeric|min:0|max:100",
            'punctuation_controversy_plc_2' => 'nullable|numeric|min:0|max:100',
        ];
        
        if ($formModel == 'vivaAir')
        {
            $rules = array_merge($rules, [
                'disease_origin' => "required",
                'cie10_code_id' => 'required|exists:sau_reinc_cie10_codes,id',
                'laterality' => "nullable",
                'sve_associated' => 'required',
                'medical_certificate_ueac' => 'required',
            ]);
        }
        else if ($formModel == 'misionEmpresarial')
        {
            $rules = array_merge($rules, [
                'disease_origin' => "required",
                'cie10_code_id' => 'required|exists:sau_reinc_cie10_codes,id',
                'laterality' => "nullable",
                'eps_favorability_concept' => 'required',
                'created_at' => 'required',
                'has_incapacitated' => "required",
                'incapacitated_days' => 'nullable|numeric|min:1',
                'incapacitated_last_extension' => 'nullable|date',
                'next_date_tracking' => 'nullable|date|after:today',
                'case_classification' => "required",
            ]);
        }
        else if ($formModel == 'hptu')
        {
            $rules = array_merge($rules, [
                'disease_origin' => "required",
                'cie10_code_id' => 'required|exists:sau_reinc_cie10_codes,id',
                'laterality' => "nullable",
                'type_controversy_origin_1' => 'nullable',
                'type_controversy_origin_2' => 'nullable'
            ]);
        }
        else if ($formModel == 'chia')
        {
            $rules = array_merge($rules, [
                'dxs' => 'required|array',
                'dxs.*.disease_origin' => 'required',
                'dxs.*.cie10_code_id.*.description' => 'required|exists:sau_reinc_cie10_codes,id'
                
            ]);
        }
        else
        {
            $rules = array_merge($rules, [
                'disease_origin' => "required",
                'cie10_code_id' => 'required|exists:sau_reinc_cie10_codes,id',
                'laterality' => "nullable"
            ]);
        }

        return $rules;
    }
}