<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Administrative\Users\User;

class SystemApplyScope implements Scope
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
        $systemApply = User::find($id)->systemsApply()->pluck('id');

        if (count($systemApply) > 0)
          $builder->whereIn('sau_lm_laws.system_apply_id', $systemApply);
      }
    }
}