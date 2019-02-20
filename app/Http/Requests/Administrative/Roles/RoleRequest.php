<?php

namespace App\Http\Requests\Administrative\Roles;

use Illuminate\Foundation\Http\FormRequest;
use App\Facades\Configuration;
use App\Rules\Permission;
use App\Rules\RoleUnique;

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
            'name' => ['required','string',new RoleUnique($id, $this->input('type_role'))],
            'permissions_asignates' => ['required', new Permission()],
            'module_id' => 'required_if:type_role,true'
        ];
    }
}
