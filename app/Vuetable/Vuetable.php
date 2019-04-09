<?php

namespace App\Vuetable;

use App\Vuetable\Builders\EloquentVuetableBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class Vuetable
{

    //Usado para vue-table-2 https://www.npmjs.com/package/vue-tables-2
    /**
     * The current request
     * @var Illuminate\Http\Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle automatic builder
     *
     * @param  mixed $query
     * @return mixed
     */
    public static function of($query)
    {
        $request = app('request');

        if (get_class($query) != \Illuminate\Database\Eloquent\Builder::class &&
        get_class($query) != \Illuminate\Database\Query\Builder::class) {
            throw new \Exception('Unsupported builder type');
        }

        return new EloquentVuetableBuilder($request, $query);
    }

    /**
     * Return the Eloquent Vuetable Builder
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @return \App\Vuetable\Builders\EloquentBuilder
     */
    public function eloquent($query)
    {
        return new EloquentVuetableBuilder($this->request, $query);
    }
}
