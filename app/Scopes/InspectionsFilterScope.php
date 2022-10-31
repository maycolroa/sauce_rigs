<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Administrative\Users\User;

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
      $id = Auth::user() ? Auth::user()->id : (isset($builder->user) ? $builder->user : null);

      if ($id)
      {
        $regionals = User::find($id)->regionals()->pluck('id');
        $headquarters = User::find($id)->headquartersFilter()->pluck('id');
        $processes = User::find($id)->processes()->pluck('id');
        $areas = User::find($id)->areas()->pluck('id');

        if (count($regionals) > 0)
          $builder->whereIn('sau_ph_inspection_regional.employee_regional_id', $regionals);
        if (count($headquarters) > 0)
          $builder->whereIn('sau_ph_inspection_headquarter.employee_headquarter_id', $headquarters);
        if (count($processes) > 0)
          $builder->whereIn('sau_ph_inspection_process.employee_process_id', $processes);
        if (count($areas) > 0)
          $builder->whereIn('sau_ph_inspection_area.employee_area_id', $areas);
      }
    }
}