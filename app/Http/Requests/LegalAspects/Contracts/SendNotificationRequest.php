<?php

namespace App\Http\Requests\LegalAspects\Contracts;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\FileFormat;

class SendNotificationRequest extends FormRequest
{
    protected $message = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    public function sanitize()
    {
        if ($this->has('activity_id') && $this->input('activity_id'))
        {
            foreach ($this->input('activity_id') as $key => $value)
            {
                $data['activity_id'][$key] = json_decode($value, true);
                $this->merge($data);
            }
        }

        if ($this->has('contract_id') && $this->input('contract_id'))
        {
            foreach ($this->input('contract_id') as $key => $value)
            {
                $data['contract_id'][$key] = json_decode($value, true);
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

        $rules = [
            'subject' => 'required',
            'body' => 'required',
        ];

        return $rules;
    }
}
