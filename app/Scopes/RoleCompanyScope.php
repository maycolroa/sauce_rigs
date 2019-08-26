<?php 

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;

class RoleCompanyScope implements Scope {
      public function apply(Builder $builder, Model $model) {

        $company_id = Session::get('company_id') == null ? (isset($builder->company_scope) ? $builder->company_scope : null) : Session::get('company_id');

        $role = DB::table('sau_roles')->select(
            'sau_roles.company_id as company_id',
            'sau_roles.type_role as type_role'
        )
        ->join('sau_role_user', 'sau_role_user.role_id', 'sau_roles.id')
        ->where('sau_role_user.user_id', Auth::user()->id)
        ->whereRaw('(sau_roles.company_id = '.$company_id.' OR sau_roles.company_id IS NULL)')
        ->orderBy('sau_roles.company_id', 'DESC')
        ->first();

        if ($role)
        {
            if ($role->type_role == 'No Definido' && $role->company_id)
                $builder->where($model->getTable().'.company_id', $company_id);
            else 
                $builder->whereNull($model->getTable().'.company_id');
        }
        else
            $builder->where($model->getTable().'.company_id', $company_id);
      }

      public function remove(Builder $builder, Model $model) {

           $query = $builder->getQuery();

           // here you remove the where close to allow developer load   
           // without your global scope condition

           foreach((array)$query->wheres as $key => $where) {

                if($where['column'] == 'company_id') {

                    unset($query->wheres[$key]);

                    $query->wheres = array_values($query->wheres);

                }
          }

      }
}