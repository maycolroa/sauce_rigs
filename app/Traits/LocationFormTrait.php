<?php

namespace App\Traits;

use App\Facades\ConfigurationsCompany\Facades\ConfigurationsCompany;
use Exception;

trait LocationFormTrait
{
    /**
     * returns the configuration
     *
     * @return Array
     */
    protected function getLocationFormConfModule($company_id = NULL)
    {   
        try
        {   
            $data = [];

            if ($company_id)
                $locationLevelForm = ConfigurationsCompany::company($company_id)->findByKey('location_level_form');
            else
                $locationLevelForm = ConfigurationsCompany::findByKey('location_level_form');

            if ($locationLevelForm)
            {
                if ($locationLevelForm == 'Regional')
                {
                    $data["regional"] = "SI";
                    $data["headquarter"] = "NO";
                    $data["process"] = "NO";
                    $data["area"] = "NO";
                }
                else if ($locationLevelForm == 'Sede')
                {
                    $data["regional"] = "SI";
                    $data["headquarter"] = "SI";
                    $data["process"] = "NO";
                    $data["area"] = "NO";
                }
                else if ($locationLevelForm == 'Proceso')
                {
                    $data["regional"] = "SI";
                    $data["headquarter"] = "SI";
                    $data["process"] = "SI";
                    $data["area"] = "NO";
                }
                else if ($locationLevelForm == 'Área')
                {
                    $data["regional"] = "SI";
                    $data["headquarter"] = "SI";
                    $data["process"] = "SI";
                    $data["area"] = "SI";
                }
            }

        } catch (Exception $e) {
            $data["regional"] = "SI";
            $data["headquarter"] = "SI";
            $data["process"] = "SI";
            $data["area"] = "SI";
        }
    
        return $data;
    }

    /**
     * returns the configuration 
     * 
     * @return Array
     */
    protected function getLocationFormRules()
    {
        $confLocation = $this->getLocationFormConfModule();

        $rules = [];

        if ($confLocation)
        {
            if ($confLocation['regional'] == 'SI')
                $rules['locations.employee_regional_id'] = 'required';
            if ($confLocation['headquarter'] == 'SI')
                $rules['locations.employee_headquarter_id'] = 'required';
            if ($confLocation['process'] == 'SI')
                $rules['locations.employee_process_id'] = 'required';
            if ($confLocation['area'] == 'SI')
                $rules['locations.employee_area_id'] = 'required';
        }

        return $rules;
    }

    /**
     * update the model that contains the location columns
     * 
     * @param Illuminate\Database\Eloquent\Model $model
     * @param Array $data
     * @return void
     */
    protected function updateModelLocationForm($model, $data)
    {
        $confLocation = $this->getLocationFormConfModule();

        if ($confLocation)
        {
            $model->employee_regional_id = null;
            $model->employee_headquarter_id = null;
            $model->employee_process_id = null;
            $model->employee_area_id = null;

            if ($confLocation['regional'] == 'SI')
                $model->employee_regional_id = $data['employee_regional_id'];
            if ($confLocation['headquarter'] == 'SI')
                $model->employee_headquarter_id = $data['employee_headquarter_id'];
            if ($confLocation['process'] == 'SI')
                $model->employee_process_id = $data['employee_process_id'];
            if ($confLocation['area'] == 'SI')
                $model->employee_area_id = $data['employee_area_id'];
            

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
        $data['employee_process_id'] = $model->employee_process_id;
        $data['employee_area_id'] = $model->employee_area_id;

        $data['multiselect_regional'] = $model->regional ? $model->regional->multiselect() : null;
        $data['multiselect_headquarter'] = $model->headquarter ? $model->headquarter->multiselect() : null;
        $data['multiselect_process'] = $model->process ? $model->process->multiselect() : null;
        $data['multiselect_area'] = $model->area ? $model->area->multiselect() : null;

        return $data;
    }
}