<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Administrative\Users\User;
use Session;

class HeadquartersScope implements Scope
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
          $company_id = isset($builder->company_scope) && $builder->company_scope ? $builder->company_scope : Session::get('company_id');

          $headquarters = User::find($id)
          ->headquarters()          
          ->select('sau_employees_headquarters.*')
          ->join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_employees_headquarters.employee_regional_id')
          ->where('sau_employees_regionals.company_id', $company_id)
          ->pluck('id');

          if (count($headquarters) > 0)
            $builder->whereIn('sau_employees.employee_headquarter_id', $headquarters);
      }
    }
}