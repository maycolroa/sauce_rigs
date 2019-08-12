<?php

namespace App\Rules\Reinstatements;

use Exception;
use Illuminate\Contracts\Validation\Rule;

abstract class ProcessDoneMustBePresent implements Rule
{

    /**
     * reference value that defines the in process attribute
     * (in_process_origin, in_process_pcl) 
     * @var string
     */
    protected $in_process;

    /**
     * reference value that defines the process done attribute
     * (process_origin_done, process_pcl_done) 
     * @var string
     */
    protected $process_done;

    /**
     * defines if the rule must validate the in_process attribute alone
     * @var boolean
     */
    protected $validateInProcessAlone;

    /**
     * used to define which validations fails and the define the error message
     * @var integer
     */
    protected $ruleNumberRef;

    /**
     * label related to the in process attribute
     * @var string
     */
    protected $inProcessLabel;

    /**
     * label related to the process done attribute
     * @var string
     */
    protected $processDoneLabel;

    /**
     * Create a new rule instance.
     *
     * @param string $in_process
     * @param string $process_done
     */
    public function __construct($in_process, $process_done, $validateInProcessAlone = false)
    {
        $this->in_process = $in_process;
        $this->process_done = $process_done;
        $this->validateInProcessAlone = $validateInProcessAlone;

        if (!$this->inProcessLabel || !$this->processDoneLabel) {
            throw new Exception('inProcessLabel or processDoneLabel undefined');
        }
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
        $valueExists = $value || is_numeric($value) ? true : false;

        if ($this->in_process == 'SI' && $this->validateInProcessAlone) 
        {
            $this->ruleNumberRef = 0;
            return $valueExists;
        }

        if ($this->in_process == 'NO' && $this->process_done == 'SI')
        {
            $this->ruleNumberRef = 1;
            return $valueExists;
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
        switch ($this->ruleNumberRef)
        {
            case 0:
                $message = "El campo :attribute es obligatorio cuando {$this->inProcessLabel} es SI.";
                break;

            case 1:
                $message = "El campo :attribute es obligatorio cuando {$this->inProcessLabel} es NO y {$this->processDoneLabel} es SI.";
                break;
            
            default:
                $message = trans('validation.required');
                break;
        }

        return $message;


    }
}
