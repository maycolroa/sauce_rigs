<?php

namespace App\Vuetable\Builders;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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
        request()->only(['query', 'limit', 'page', 'orderBy', 'ascending', 'byColumn', 'fields']);
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

        $data = $this->query;

        if (isset($query) && $query) {
            $data = $byColumn == 1 ?
                $this->filterByColumn($data, $query) :
                $this->filter($data, $query, $fields);
        }

        $count = $data->count();
        
        $data->take($limit)
            ->skip($limit * ($page - 1));

        if (isset($orderBy)) {
            $direction = $ascending == 1 ? 'ASC' : 'DESC';
            $data->orderBy($orderBy, $direction);
        }

        $results = $data->get()->toArray();

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
    protected function filterByColumn($data, $queries)
    {
        return $data->where(function ($q) use ($queries) {
            foreach ($queries as $field => $query) {
                if (is_string($query)) {
                    $q->where($field, 'LIKE', "%{$query}%");
                } else {
                    $start = Carbon::createFromFormat('Y-m-d', $query['start'])->startOfDay();
                    $end = Carbon::createFromFormat('Y-m-d', $query['end'])->endOfDay();

                    $q->whereBetween($field, [$start, $end]);
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
        return $data->where(function ($q) use ($query, $fields) {
            foreach ($fields as $index => $field) {
                $method = $index ? 'orWhere' : 'where';
                $q->{$method}($field, 'LIKE', "%{$query}%");

            }
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
}