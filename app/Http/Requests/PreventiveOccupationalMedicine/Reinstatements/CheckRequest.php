<?php

namespace App\Http\Requests\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ReinstatementsTrait;
use Session;

class CheckRequest extends FormRequest
{
    use ReinstatementsTrait;

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
        if ($this->has('delete'))
        {
            $this->merge([
                'delete' => json_decode($this->input('delete'), true)
            ]);
        }

        if ($this->has('files'))
        {
            foreach ($this->input('files') as $key => $value)
            {
                $data['files'][$key] = json_decode($value, true);
                $this->merge($data);
            }

            if ($this->has('files_binary') && COUNT($this->files_binary) > 0)
            {
                $data = $this->all();

                foreach ($this->files_binary as $key => $value)
                {
                    $data['files'][$key]['file'] = $value;
                }

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
        $params = [
            'id' => $this->input('id'),
            'company_id' => Session::get('company_id')
        ];

        return $this->getRules($params);
    }

    public function messages()
    {
        return [
            //'employee_regional_id.required' => 'El campo '.$this->keywordCheck('regional').' es obligatorio.'
            'disease_origin.required' => 'El campo es obligatorio.',
            'created_at.required' => 'El campo Fecha de inicio es obligatorio.',
            'next_date_tracking.after' => 'fecha próximo seguimiento debe ser una fecha posterior a hoy.'
        ];
    }
}
