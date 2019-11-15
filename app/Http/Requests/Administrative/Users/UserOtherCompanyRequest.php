<?php

namespace App\Http\Requests\Administrative\Users;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class UserOtherCompanyRequest extends FormRequest
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

        return $this->all();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'users' => 'required|array',
            'users.*.user_id' => 'required',
            'users.*.role_id' => 'required'
        ];
    }
}
