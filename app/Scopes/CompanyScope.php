<?php 

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class CompanyScope implements Scope {
      public function apply(Builder $builder, Model $model) {
        $where = $model->scope_table_for_company == null ? 'company_id' : $model->scope_table_for_company.'.company_id';
        $builder->where($where, '1');
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