<?php 

namespace App\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Session;

class CompanyScope implements Scope {
      public function apply(Builder $builder, Model $model) {
        if($model->scope_table_for_company_table != null){
            if($builder->getQuery()->joins != null){
                foreach($builder->getQuery()->joins as $join){
                    if($join->table == $model->scope_table_for_company_table){
                        $where = $model->scope_table_for_company_table.'.company_id';
                        $builder->where($where, Session::get('company_id'));
                    }
                }
            }
        }else{
            $builder->where('company_id', Session::get('company_id'));
        }
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