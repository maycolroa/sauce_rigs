<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Foundation\Http\FormRequest;
use Session;
use App\Traits\ContractTrait;
use Illuminate\Support\Facades\Auth;

class ContractInformStateChangeRequest extends FormRequest
{
    use ContractTrait;

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
        $this->merge([
            'inform' => json_decode($this->input('inform'), true)
        ]);

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
                $allKeys = explode("_", $key);
                $keyObj = $allKeys[0];
                $keyItem = $allKeys[1];
                $keyFile = $allKeys[2];

                $data['inform']['themes'][$keyObj]['items'][$keyItem]['files'][$keyFile]['file'] = $value;
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
        return [
            'state' => 'required',
            'motive_rejected' => 'required_if:state,Rechazado'
        ];
    }

    public function attributes()
    {
        return [
            'state' => 'Estado',
            'motive_rejected' => 'Motivo de rechazo',
        ];
    }
}
