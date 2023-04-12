<?php

namespace App\Http\Requests\System\NewsletterSend;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\FileFormat;

class NewsletterSendRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'subject' => 'required',
            //'image' => [new FileFormat(['png'])],
        ];

        return $rules;
    }
}
