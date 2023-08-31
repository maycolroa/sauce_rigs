<?php

namespace App\Http\Requests\System\Companies;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class CompanyRequest extends FormRequest
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
        if ($this->has('users'))
        {
            foreach ($this->input('users') as $key => $value)
            {
                $data['users'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('work_centers'))
        {
            foreach ($this->input('work_centers') as $key => $value)
            {
                $data['work_centers'][$key] = json_decode($value, true);
                $this->merge($data);
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

        return [
            'name' => 'required|string|unique:sau_companies,name,'.$id.',id',
            'users' => 'nullable|array',
            'users.*.user_id' => 'required',
            'users.*.role_id' => 'required',
            'work_centers' => 'nullable|array',
            'work_centers.*.activity_economic' => 'required',
            'work_centers.*.direction' => 'required',
            'work_centers.*.departament_id' => 'required',
            'work_centers.*.city_id' => 'required',
            'work_centers.*.zona' => 'required'
        ];
    }
}
