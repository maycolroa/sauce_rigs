<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use App\Administrative\Configurations\LocationLevelForm;
use App\Models\Module;
use App\Models\Application;
use Exception;

trait LocationFormTrait
{
    /**
     * returns the configuration for a specific module
     * @param  String $application
     * @param  String $module
     *
     * @return App\Administrative\Configurations\LocationLevelForm
     */
    protected function getLocationFormConfModule($application, $module)
    {
        $locationLevelForm = LocationLevelForm::select(
            'sau_conf_location_level_forms.regional as regional',
            'sau_conf_location_level_forms.headquarter as headquarter',
            'sau_conf_location_level_forms.area as area',
            'sau_conf_location_level_forms.process as process'
          )
          ->join('sau_modules', 'sau_modules.id', 'sau_conf_location_level_forms.module_id')
          ->join('sau_applications', 'sau_applications.id', 'sau_modules.application_id')
          ->where('sau_applications.name', $application)
          ->where('sau_modules.name', $module)
          ->first();
    
        return $locationLevelForm;
    }

    /**
     * returns the configuration for a specific module
     *
     * @param  String $application
     * @param  String $module
     * @return Array
     */
    protected function getLocationFormRules($application, $module)
    {
        $confLocation = $this->getLocationFormConfModule($application, $module);

        $rules = [];

        if ($confLocation)
        {
            if ($confLocation->regional == 'SI')
                $rules['locations.employee_regional_id'] = 'required';
            if ($confLocation->headquarter == 'SI')
                $rules['locations.employee_headquarter_id'] = 'required';
            if ($confLocation->area == 'SI')
                $rules['locations.employee_area_id'] = 'required';
            if ($confLocation->process == 'SI')
                $rules['locations.employee_process_id'] = 'required';
        }

        return $rules;
    }

    /**
     * update the model that contains the location columns
     *
     * @param String $application
     * @param String $module
     * @param Illuminate\Database\Eloquent\Model $model
     * @param Array $data
     * @return void
     */
    protected function updateModelLocationForm($application, $module, $model, $data)
    {
        $confLocation = $this->getLocationFormConfModule($application, $module);

        if ($confLocation)
        {
            $model->employee_regional_id = null;
            $model->employee_headquarter_id = null;
            $model->employee_area_id = null;
            $model->employee_process_id = null;

            if ($confLocation->regional == 'SI')
                $model->employee_regional_id = $data['employee_regional_id'];
            if ($confLocation->headquarter == 'SI')
                $model->employee_headquarter_id = $data['employee_headquarter_id'];
            if ($confLocation->area == 'SI')
                $model->employee_area_id = $data['employee_area_id'];
            if ($confLocation->process == 'SI')
                $model->employee_process_id = $data['employee_process_id'];

            $model->save();
        }
    }

    /**
     * returns the data in the correct format for the component
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @return Array
     */
    protected function prepareDataLocationForm($model)
    {
        $data = [];

        $data['employee_regional_id'] = $model->employee_regional_id;
        $data['employee_headquarter_id'] = $model->employee_headquarter_id;
        $data['employee_area_id'] = $model->employee_area_id;
        $data['employee_process_id'] = $model->employee_process_id;

        $data['multiselect_regional'] = $model->regional ? $model->regional->multiselect() : null;
        $data['multiselect_headquarter'] = $model->headquarter ? $model->headquarter->multiselect() : null;
        $data['multiselect_area'] = $model->area ? $model->area->multiselect() : null;
        $data['multiselect_process'] = $model->process ? $model->process->multiselect() : null;

        return $data;
    }
}