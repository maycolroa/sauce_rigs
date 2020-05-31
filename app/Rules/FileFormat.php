<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FileFormat implements Rule
{
    protected $mime_types;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($mime_types)
    {
        $this->mime_types = collect($mime_types)
            ->map(function($item, $key) {
                return trim(strtolower($item));
            })
            ->toArray();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if ($value && !is_string($value))
        {
            $ext = strtolower($value->getClientOriginalExtension());

            if (!in_array($ext, $this->mime_types))
                return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "Archivo debe ser: ".implode(", ", $this->mime_types);
    }
}
