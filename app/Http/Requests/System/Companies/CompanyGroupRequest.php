<?php

namespace App\Http\Requests\System\Companies;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class CompanyGroupRequest extends FormRequest
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

    /*public function validator($factory)
    {
        return $factory->make(
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages()
        );
    }

    public function sanitize()
    {
        if ($this->has('emails'))
        {
            foreach ($this->input('emails') as $key => $value)
            {
                $data['emails'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        return $this->all();
    }*/

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->input('id');

        return [
            'name' => 'required|string|unique:sau_company_groups,name,'.$id.',id',
            'emails' => 'nullable|array',
            'receive_report' => 'required'
        ];
    }
}
