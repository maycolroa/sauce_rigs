<?php

namespace App\Http\Requests\IndustrialSecure\Epp;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class ElementTransactionsReturnsRequest extends FormRequest
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
        if ($this->has('elements_id'))
        {
            foreach ($this->input('elements_id') as $key => $value)
            {
                $data['elements_id'][$key] = json_decode($value, true);
                $this->merge($data);
            }
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

        if ($this->has('delete'))
        {
            $this->merge([
                'delete' => json_decode($this->input('delete'), true)
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
        //\Log::info($this->all());

        return [
            'employee_id' => 'required|integer',
            'position_employee' => 'required|string',
            'elements_id' => 'required|array',
            'elements_id.*.quantity' => 'required|integer',
            'elements_id.*.waste' => 'required|string',
            'files' => 'nullable|array',
            'firm_employee' => 'nullable',
            'observations' => 'nullable'
        ];
    }
}
