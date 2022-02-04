<?php

namespace App\Http\Requests\IndustrialSecure\RiskMatrix;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Traits\LocationFormTrait;
use Session;


class RiskMatrixRequest extends FormRequest
{
    use LocationFormTrait;

    protected $messages = [];

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
        if ($this->has('locations'))
        {
            $data3['locations'] = json_decode($this->get('locations'), true);
            $this->merge($data3);
        }

        if ($this->has('participants') && $this->input('participants'))
        {
            foreach ($this->input('participants') as $key => $value)
            {
                $data['participants'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('subprocesses') && $this->input('subprocesses'))
        {
            foreach ($this->input('subprocesses') as $key => $value)
            {
                $data2['subprocesses'][$key] = json_decode($value, true);
                $this->merge($data2);
            }
        }

        if ($this->has('subprocessesRemoved') && $this->input('subprocessesRemoved'))
        {
            foreach ($this->input('subprocessesRemoved') as $key => $value)
            {
                $data3['subprocessesRemoved'][$key] = json_decode($value, true);
                $this->merge($data3);
            }
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

        $rules = [
            'name' => 'nullable|string|unique:sau_rm_risks_matrix,name,'.$id.',id,company_id,'.$this->company,
            'approved' => 'nullable',
            'participants' => 'nullable|array',
            'subprocesses' => 'required|array',
            'subprocesses.*.sub_process_id' => 'required|exists:sau_rm_sub_processes,id',
            'subprocesses.*.risks' => 'required|array',
            'subprocesses.*.risks.*.risk_id' => 'required|exists:sau_rm_risk,id',
            'subprocesses.*.risks.*.economic' => 'required|integer|min:0',
            'subprocesses.*.risks.*.quality_care_patient_safety' => 'required|integer|min:0',
            'subprocesses.*.risks.*.reputational' => 'required|integer|min:0',
            'subprocesses.*.risks.*.legal_regulatory' => 'required|integer|min:0',
            'subprocesses.*.risks.*.environmental' => 'required|integer|min:0',
            'subprocesses.*.risks.*.max_inherent_impact' => 'required|integer|min:0',
            'subprocesses.*.risks.*.description_inherent_impact' => 'nullable',
            'subprocesses.*.risks.*.max_inherent_frequency' => 'nullable|integer|min:0',
            'subprocesses.*.risks.*.description_inherent_frequency' => 'nullable',
            'subprocesses.*.risks.*.inherent_exposition' => 'nullable|integer|min:0',
            'subprocesses.*.risks.*.controls_decrease' => 'required',
            'subprocesses.*.risks.*.nature' => 'required',
            'subprocesses.*.risks.*.evidence' => 'required|in:SI,NO',
            'subprocesses.*.risks.*.coverage' => 'required',
            'subprocesses.*.risks.*.documentation' => 'required',
            'subprocesses.*.risks.*.segregation' => 'required|in:SI,NO',
            'subprocesses.*.risks.*.control_evaluation' => 'nullable',
            'subprocesses.*.risks.*.percentege_mitigation' => 'nullable',
            'subprocesses.*.risks.*.max_residual_impact' => 'nullable|integer|min:0',
            'subprocesses.*.risks.*.description_residual_impact' => 'nullable',
            'subprocesses.*.risks.*.max_residual_frequency' => 'nullable|integer|min:0',
            'subprocesses.*.risks.*.description_residual_frequency' => 'nullable',
            'subprocesses.*.risks.*.residual_exposition' => 'nullable|integer|min:0',
            'subprocesses.*.risks.*.max_impact_event_risk' => 'nullable'
        ];

        $confLocation = $this->getLocationFormRules();
        $rulesActionPlan = ActionPlan::prefixIndex('subprocesses.*.risks.*.')->getRules($this->all());

        $rules = array_merge($rules, $confLocation);
        $rules = array_merge($rules, $rulesActionPlan['rules']);

        return $rules;
    }
}
