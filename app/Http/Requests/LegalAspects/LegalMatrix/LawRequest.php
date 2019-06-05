<?php

namespace App\Http\Requests\LegalAspects\LegalMatrix;

use Illuminate\Foundation\Http\FormRequest;

class LawRequest extends FormRequest
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

        return [
            'name' => 'required|string|unique:sau_lm_laws,name,'.$id.',id',
            'law_number' => 'required|integer|min:1',
            'apply_system' => 'required',
            'law_year' => 'required|integer',
            'law_type_id' => 'required|exists:sau_lm_laws_types,id',
            'description' => 'required',
            'observations' => 'required',
            'risk_aspect_id' => 'required|exists:sau_lm_risks_aspects,id',
            'entity_id' => 'required|exists:sau_lm_entities,id',
            'sst_risk_id' => 'required|exists:sau_lm_sst_risks,id',
            'repealed' => 'required',
            'file' => 'nullable|max:20480',
        ];
    }
}
