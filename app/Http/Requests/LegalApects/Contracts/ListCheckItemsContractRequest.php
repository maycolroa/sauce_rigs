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
        $id = $this->input('id');

        $rules = [
            'items.*.qualification' => 'required',
        ];
        
        return $rules;
    }
}
