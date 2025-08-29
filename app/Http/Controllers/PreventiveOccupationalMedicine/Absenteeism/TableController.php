<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Table;
use App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\TableRequest;
USE Carbon\Carbon;
use DB;

class TableController extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:absen_tables_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:absen_tables_r, {$this->team}");
        $this->middleware("permission:absen_tables_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:absen_tables_d, {$this->team}", ['only' => 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $tables = Table::select('*');

        return Vuetable::of($tables)
                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $table = Table::findOrFail($id);
            $table->delete = [];

            $columnas = [];

            foreach ($table->columns['columns'] as $column)
            {
              array_push($columnas, [
                "key" => Carbon::now()->timestamp + rand(1,10000),
                "value" => $column,
                "old" => $column
              ]);
            }

            $table->columnas = $columnas;

            return $this->respondHttp200([
                'data' => $table
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\TableRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TableRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $table = new Table($request->all());
            $table->company_id = $this->company;
            $table->table_name = "sau_absen_{$this->company}_{$request->name}";

            $columns = collect($request->columnas)->pluck('value')->unique()->toArray();
            $table->columns = collect(['columns' => $columns]);

            if (!$table->save())
                return $this->respondHttp500();

            Schema::create($table->table_name, function ($table2) use ($columns) {
                $table2->increments('id');

                foreach ($columns as $key => $column) {
                    $table2->string($column, 255)->nullable();
                }
            });

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se creo la tabla'
        ]);
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\TableRequest  $request
     * @param  Table  $table
     * @return \Illuminate\Http\Response
     */
    public function update(TableRequest $request, Table $table)
    {
        DB::beginTransaction();

        try {

            $table->fill($request->all());

            $columns = collect($request->columnas)->pluck('value')->unique()->toArray();
            $table->columns = collect(['columns' => $columns]);
            
            if (!$table->update())
                return $this->respondHttp500();

            $column_deleted = $request->delete;
        
            $rename = collect($request->columnas)->filter(function ($item, $key) {
                return $item['old'] && $item['value'] != $item['old'];
            })
            ->toArray();

            $add = collect($request->columnas)->filter(function ($item, $key) {
                return !$item['old'];
            })
            ->pluck('value')->unique()->toArray();

            Schema::table($table->table_name, function ($table2) use ($add) {

                foreach ($add as $column)
                {
                    $table2->string($column, 255)->nullable();
                }
            });

            if($column_deleted && COUNT($column_deleted) > 0)
            {
                Schema::table($table->table_name, function ($table2) use ($column_deleted) {

                    foreach ($column_deleted as $column)
                    {
                        $table2->dropColumn($column);
                    }
                });
            }

            Schema::table($table->table_name, function ($table2) use ($rename) {

                foreach ($rename as $column)
                {
                    $table2->renameColumn($column['old'], $column['value']);
                }
            });

            DB::commit();

        }
        catch(\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la tabla'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Table  $table
     * @return \Illuminate\Http\Response
     */
    public function destroy(Table $table)
    {
        DB::beginTransaction();

        try {

            Schema::dropIfExists($table->table_name);

            $table->delete();

            DB::commit();
        }
        catch(\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se elimino la tabla'
        ]);
    }

    public function cleanData(Table $table)
    {
        DB::beginTransaction();

        try
        {
            DB::table($table->table_name)->truncate();

            DB::commit();
        }
        catch (Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se eliminarón los datos de la tabla'
        ]);
    }

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tables = Table::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tables)
            ]);
        }
        else
        {
            $tables = Table::selectRaw("
                sau_absen_tables.id as id,
                sau_absen_tables.name as name
            ")
            ->orderBy('name')
            ->pluck('id', 'name');

            return $this->multiSelectFormat($tables);
        }
    }

    public function multiselectColumns(Request $request)
    {
        $records = collect([]);

        if (is_numeric($request->table))
        {
            $table = Table::find($request->table);

            foreach ($table->columns['columns'] as $key => $value) 
            {
                $records->push(['name' => $value, 'id' => $value]);
            }            

            $records = $records->pluck('id', 'name');
        }
        
        return $this->multiSelectFormat($records);
    }
}
