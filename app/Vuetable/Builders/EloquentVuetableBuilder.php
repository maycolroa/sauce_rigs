<?php

namespace App\Vuetable\Builders;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class EloquentVuetableBuilder
{

    /**
     * The current request.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Query used to make the table data.
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    private $query;

    /**
     * Array of columns that should be added and the new content.
     *
     * @var array
     */
    private $columnsToAdd = [];


    public function __construct(Request $request, $query)
    {
        request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn', 'fields', 'tables']);
        $this->request = $request;
        $this->query = $query;
    }
    
    public function make()
    {
        $query = $this->request->input('query');
        $limit = $this->request->input('limit');
        $page = $this->request->input('page');
        $orderBy = $this->request->input('orderBy');
        $ascending = $this->request->input('ascending');
        $byColumn = $this->request->input('byColumn');
        $fields = $this->request->input('fields');
        $tables = $this->request->input('tables');

        $data = $this->query;

        if (isset($query) && $query) {
            $data = $byColumn == 1 ?
                $this->filterByColumn($data, $query, $tables) :
                $this->filter($data, $query, $fields, $tables);
        }

        //$count = $data->count();
        $count = COUNT($data->get());
        
        $data->take($limit)
            ->skip($limit * ($page - 1));

        if (isset($orderBy)) {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $data->orderBy($orderBy, $direction);
        }

        $results = $data->get();

        $this->applyChangesTo($results);

        return [
            'data' => $results,
            'count' => $count,
        ];
    }

    /**
     * Add filter by column.
     *
     * @param  $data, \Illuminate\Database\Eloquent\Model $query
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function filterByColumn($data, $queries, $tables)
    {
        return $data->where(function ($q) use ($queries, $tables) {
            foreach ($queries as $field => $query) {
                $field = isset($tables[$field]) ? $tables[$field] : $field;
                
                if ($field == 'date_upload')
                {
                    $q->whereRaw("date_format(sau_ct_file_upload_contracts_leesse.created_at, '%Y-%m') LIKE '%{$query}%'");
                }
                else if ($field == 'cargado_documento_driver')
                {
                    $q->whereRaw("case when sau_rs_drivers_documents.driver_id is not null then 'SI' else 'NO' end LIKE '%{$query}%'");
                }
                else if ($field == 'report_vehicle_soat_vencido')
                {
                    $q->whereRaw("case when sau_rs_vehicles.due_date_soat is not null  
                        then 
                            case when sau_rs_vehicles.due_date_soat >= curdate()
                                then 'NO'
                                else 'SI' end
                        else 'NO' end LIKE '%{$query}%'");
                }
                else if ($field == 'report_vehicle_mechanical_vencido')
                {
                    $q->whereRaw("case when sau_rs_vehicles.due_date_mechanical_tech is not null  
                        then 
                            case when sau_rs_vehicles.due_date_mechanical_tech >= curdate()
                                then 'NO'
                                else 'SI' end
                        else 'NO' end LIKE '%{$query}%'");
                }
                else if ($field == 'report_vehicle_policy_vencido')
                {
                    $q->whereRaw("case when sau_rs_vehicles.due_date_policy is not null  
                        then 
                            case when sau_rs_vehicles.due_date_policy >= curdate()
                                then 'NO'
                                else 'SI' end
                        else 'NO' end LIKE '%{$query}%'");
                }
                else if ($field == 'sau_ct_contract_employees.state_employee')
                {
                    $q->whereRaw("case when sau_ct_contract_employees.state_employee is true 
                        then 'Activo' 
                        else 'Inactivo' end LIKE '{$query}%'");
                }
                else
                {
                    if (is_string($query)) {
                        $q->where($field, 'LIKE', "%{$query}%");
                    } else {
                        $start = Carbon::createFromFormat('Y-m-d', $query['start'])->startOfDay();
                        $end = Carbon::createFromFormat('Y-m-d', $query['end'])->endOfDay();

                        $q->whereBetween($field, [$start, $end]);
                    }
                }
            }
        });
    }

    /**
     * Add filter.
     *
     * @param  $data, \Illuminate\Database\Eloquent\Model $query, $fields
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function filter($data, $query, $fields)
    {
        return $data->where(function ($q) use ($query, $fields, $tables) {
            foreach ($fields as $index => $field) {
                $field = isset($tables[$field]) ? $tables[$field] : $field;
                $method = $index ? 'orWhere' : 'where';
                $q->{$method}($field, 'LIKE', "%{$query}%");

            }
        });
    }

    /**
     * Add a new column to the columns to add.
     *
     * @param string $column
     * @param string|Closure $content
     */
    public function addColumn($column, $content)
    {
        $this->columnsToAdd[$column] = $content;

        return $this;
    }

    /**
     * Edit the results inside the pagination object.
     *
     * @param  \Illuminate\Pagination\LengthAwarePaginator $results
     * @param  array $columnsToEdit
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function applyChangesTo($results)
    {
        if (empty($this->columnsToAdd)) {
            return $results;
        }

        if ($results instanceof LengthAwarePaginator) {
            $newData = $results->getCollection();
            return $results->setCollection($this->editData($newData));

        } else if ($results instanceof Collection) {
            return $this->editData($results);

        } else {
            throw new Exception('invalid results object');
        }
    }

    /**
     * edits the $data fetched from the database 
     * @param  collection $data
     * @return void
     */
    public function editData($data)
    {
        return $data->map(function ($model) {
            $model = $this->addModelAttibutes($model);
            return $model;
        });
    }

    /**
     * Add the model attributes acording to the columnsToAdd attribute.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    protected function addModelAttibutes($model)
    {
        foreach ($this->columnsToAdd as $column => $value) {
            if ($model->relationLoaded($column) || $model->getAttributeValue($column) != null) {
                throw new \Exception("Can not add the '{$column}' column, the results already have that column.");
            }

            $model = $this->changeAttribute($model, $column, $value);
        }

        return $model;
    }

    /**
     * Change a model attribe
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  string $attribute
     * @param  string|Closure $value
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function changeAttribute($model, $attribute, $value)
    {
        if ($value instanceof Closure) {
            $model->setAttribute($attribute, $value($model));
        } else {
            $model->setAttribute($attribute, $value);
        }

        if ($model->relationLoaded($attribute)) {
            $model->setRelation($attribute, 'removed');
        }

        return $model;
    }
}