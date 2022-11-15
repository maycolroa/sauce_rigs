<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Administrative\Users\User;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use Session;

class InspectionsFilterScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $builder
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
      try
      {
        $configLevel = ConfigurationsCompany::company(Session::get('company_id'))->findByKey('filter_inspections');
          
      } catch (\Exception $e) {
        $configLevel = 'NO';
      }

      if ($configLevel == 'SI')
      {
        $locationLevelForm = ConfigurationsCompany::company(Session::get('company_id'))->findByKey('location_level_form_user_inspection_filter');

        if ($locationLevelForm)
        {

          $id = Auth::user() ? Auth::user()->id : (isset($builder->user) ? $builder->user : null);

          if ($id)
          {
            $regionals = User::find($id)->regionals()->pluck('id');
            $headquarters = User::find($id)->headquartersFilter()->pluck('id');
            $processes = User::find($id)->processes()->pluck('id');
            $areas = User::find($id)->areas()->pluck('id');

            if ($locationLevelForm == 'Regional')
            {
                if (count($regionals) > 0)
                  $builder->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionals);
            }
            else if ($locationLevelForm == 'Sede')
            {
              if (count($regionals) > 0 && count($headquarters) > 0)
                $builder->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionals)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $headquarters);
            }
            else if ($locationLevelForm == 'Proceso')
            {
              if (count($regionals) > 0 && count($headquarters) > 0 && count($processes) > 0)
                $builder->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionals)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $headquarters)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $processes);
            }
            else if ($locationLevelForm == 'Área')
            {                        
              if (count($regionals) > 0 && count($headquarters) > 0 && count($processes) > 0 && count($areas) > 0)
                $builder->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionals)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $headquarters)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $processes)->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $areas);
            }
          }
        }


      

        /*if (count($regionals) > 0)
          $builder->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionals);
        if (count($headquarters) > 0)
          $builder->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $headquarters);
        if (count($processes) > 0)
          $builder->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $processes);
        if (count($areas) > 0)
          $builder->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $areas);*/
      }
    }
}