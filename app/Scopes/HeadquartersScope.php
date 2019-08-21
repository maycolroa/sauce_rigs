<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Administrative\Users\User;

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
        $headquarters = User::find($id)->headquarters()->pluck('id');

        if (count($headquarters) > 0)
          $builder->whereIn('sau_employees.employee_headquarter_id', $headquarters);
      }
    }
}