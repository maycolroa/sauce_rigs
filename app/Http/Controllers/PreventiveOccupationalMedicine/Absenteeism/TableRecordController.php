<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Table;
use App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\TableRequest;
use App\Exports\PreventiveOccupationalMedicine\Absenteeism\TableRecordImportTemplate;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\PreventiveOccupationalMedicine\Absenteeism\TableRecord\TableRecordExportJob;
use App\Jobs\PreventiveOccupationalMedicine\Absenteeism\TableRecord\TableRecordImportJob;
USE Carbon\Carbon;
use App\Models\Administrative\Users\User;
use App\Models\General\Company;
use App\Facades\Mail\Facades\NotificationMail;
use DB;

class TableRecordController extends Controller
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
        $table = Table::findOrFail($request->tableId);

        $records = DB::table($table->table_name)
                ->selectRaw("{$table->id} AS table_id, id, ".implode(", ", $table->columns->get('columns')));

        return Vuetable::of($records)
                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Table $table, $id)
    {
        try
        {
            $record = DB::table($table->table_name)->where('id', $id)->first();

            return $this->respondHttp200([
                'data' => $record
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
    public function store(Request $request)
    {
        DB::beginTransaction();

        try
        {
            $table = Table::findOrFail($request->table_id);

            $data = [];

            foreach ($table->columns->get('columns') as $key => $column)
            {
                if ($request->has($column))
                    $data[$column] = $request->$column;
            }

            $result = DB::table($table->table_name)
            ->updateOrInsert(['id' => $request->id], $data);

            DB::commit();

            $superadmin_notify = (new User(['email'=> 'mroat0@gmail.com']));     
            $company = Company::find($this->company);     
            
            if ($superadmin_notify && $company)
            {
                NotificationMail::
                    subject('Carga de información en tabla ausentismo')
                    ->message("Se han agregado o modificado registros en la tabla {$table->name}, perteneciente a la compañia {$company->name} por el usuario {$this->user->name} - {$this->user->email}")
                    ->recipients($superadmin_notify)
                    ->module('absenteeism')
                    ->company($this->company)
                    ->send();
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se guardo el dato'
        ]);
    }

    public function destroy(Table $table, $id)
    {
        DB::beginTransaction();

        try
        {
            DB::table($table->table_name)->where('id', $id)->delete();

            DB::commit();

        } catch(Exception $e){
            DB::rollback();
            $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se elimino el dato'
        ]);
    }

    public function downloadTemplateImport(Table $table)
    {
        return Excel::download(new TableRecordImportTemplate($table->id, collect([]), $this->company), 'PlantillaImportacionDatos.xlsx');
    }

    public function import(Table $table, Request $request)
    {
        try
        {
            TableRecordImportJob::dispatch($request->file, $table->id, $this->company, $this->user);
        
            return $this->respondHttp200();

        } catch(Exception $e)
        {
            return $this->respondHttp500();
        }
    }

    public function export(Table $table, Request $request)
    {
        try
        {
            TableRecordExportJob::dispatch($table->id, $this->user, $this->company);
          
            return $this->respondHttp200();
        } 
        catch(Exception $e) {
            return $this->respondHttp500();
        }
    }
}
