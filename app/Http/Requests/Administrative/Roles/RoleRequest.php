<?php

namespace App\Http\Requests\Administrative\Roles;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\Configuration;
use Session;

class RoleRequest extends FormRequest
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
            'name' => 'required|string|unique:roles,name,'.$id.',id,company_id,'.Session::get('company_id'),
            'permissions_multiselect' => 'required'
        ];
    }
}
