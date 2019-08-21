<?php

namespace App\Rules\Reinstatements;

class ProcessOriginDoneMustBePresent extends ProcessDoneMustBePresent
{
    /**
     * creates an instance
     * and defines the process labels
     */
    function __construct($in_process, $process_done, $validateInProcessAlone = false)
    {
        $this->inProcessLabel = trans('validation.attributes.in_process_origin');
        $this->processDoneLabel = trans('validation.attributes.process_origin_done');
        parent::__construct($in_process, $process_done, $validateInProcessAlone);
    }
}