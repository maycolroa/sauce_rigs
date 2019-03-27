<?php

namespace App\Http\Requests\LegalApects\Contracts;

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
        // $id = $this->input('id');
        // var_dump($this->items[0]);
        // dd($this->items[0]->name);
        $rules = [
            'items.*' => [
                // 'required',
                function ($attribute, $value, $fail) {
                    if (!isset(json_decode($value)->qualification)) {
                        $fail('La calificación es obligatoria');
                    }
                }
            ]
        ];
        
        return $rules;
    }
}
