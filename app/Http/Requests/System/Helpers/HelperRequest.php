<?php

namespace App\Http\Requests\System\Helpers;

use Illuminate\Foundation\Http\FormRequest;
use Session;

class HelperRequest extends FormRequest
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
        if ($this->has('files'))
        {
            foreach ($this->input('files') as $key => $value)
            {
                $data['files'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('delete'))
        {
            $this->merge([
                'delete' => json_decode($this->input('delete'), true)
            ]);
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
            'title' => 'required',
            'description' => 'nullable',
            'module_id' => 'required',
            'files' => 'required|array',
            'files.*.name' => 'required'
        ];
    }
}
