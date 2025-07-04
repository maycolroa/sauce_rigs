<?php

namespace App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class TableRequest extends FormRequest
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
            $this->sanitize(), $this->container->call([$this, 'rules']), $this->messages(), $this->attributes()
        );
    }

    public function sanitize()
    {
        if ($this->has('columnas') && $this->input('columnas'))
        {
            foreach ($this->input('columnas') as $key => $value)
            {
                $data['columnas'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('column_deleted'))
        {
            $this->merge([
                'column_deleted' => json_decode($this->input('column_deleted'), true)
            ]);
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
            'name' => 'required|max:35|unique:sau_absen_tables,name,'.$id.',id,company_id,'.Session::get('company_id'),
            'columnas' => 'required|array'
        ];
    }
}
