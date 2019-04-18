<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;

class ListCheckItemsContractRequest extends FormRequest
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
        $rules = [
            'items.*' => [
                // 'required',
                function ($attribute, $value, $fail) {
                    if (isset(json_decode($value)->qualification)) {
                        if(json_decode($value)->qualification == 1) {
                            foreach (json_decode($value)->files as $file) {
                                if ($file->name == "" && $file->file == "") {
                                    $fail('Los campos nombre y archivo son necesarios');
                                }
                            }
                        }
                        
                        if(json_decode($value)->qualification == 2) {
                            if (!isset(json_decode($value)->actionPlan)) {
                                $fail('Es requerido por lo menos un plan de acción');
                            } else {
                                foreach (json_decode($value)->actionPlan->activities as $action) {
                                    //$action->responsible_id == "" tener en cuenta los responsables
                                    if ($action->execution_date == "" && $action->expiration_date == "" && $action->state == "" ) {
                                        $fail('Faltan campos por completar de los planes de acción');
                                    }
                                }
                            }
                        }
                    }
                }
            ]
        ];
        
        return $rules;
    }
}
