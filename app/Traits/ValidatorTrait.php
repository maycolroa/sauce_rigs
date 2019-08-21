<?php

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatorTrait
{

    /**
     * custom validator that could be used for special
     * rules
     * @var Validator
     */
    protected $validator;

    /**
     * builds a validator with the file extension rules
     * specified in the parameters
     * @param  UploadFile $file
     * @param  array $extensionsAllowed
     * @return void
     */
    protected function validateFileExtension($file, $extensionsAllowed)
    {
        $strHelper = 'extension';

        $extensionsAllowed = implode(',', $extensionsAllowed);
        $validatorHelper = [
            $strHelper => "required|in:$extensionsAllowed"
        ];

        $this->validator = Validator::make([
            $strHelper => $file->getClientOriginalExtension()
        ], $validatorHelper);
    }

    /**
     * builds a validator with the file mimetypes rules
     * specified in the parameters
     * @param  UploadFile $file
     * @param  array $mimeTypesAllowed
     * @return void
     */
    protected function validateFileMimeType($file, $mimeTypesAllowed)
    {
        $strHelper = 'mimetype';

        $mimeTypesAllowed = implode(',', $mimeTypesAllowed);
        $validatorHelper = [
            $strHelper => "required|in:$mimeTypesAllowed"
        ];

        $this->validator = Validator::make([
            $strHelper => $file->getClientMimeType()
        ], $validatorHelper);
    }

}