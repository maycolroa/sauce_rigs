<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Vuetable\Facades\Vuetable;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Talend;
use App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\TalendRequest;
use App\Jobs\PreventiveOccupationalMedicine\Absenteeism\Talends\ExtractTalendZipFileJob;
use Validator;

class TalendController extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:absen_uploadTalend_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:absen_uploadTalend_r, {$this->team}", ['except' => ['multiselect', 'talendExist']]);
        $this->middleware("permission:absen_uploadTalend_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:absen_uploadTalend_d, {$this->team}", ['only' => 'toggleState']);
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
        $talends = Talend::select('*');

        return Vuetable::of($talends)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\TalendRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TalendRequest $request)
    {
        Validator::make($request->all(), [
            "file" => [
                function ($attribute, $value, $fail)
                {
                  if ($value && !is_string($value))
                  {
                      $ext = strtolower($value->getClientOriginalExtension());
                      
                      if ($ext != 'zip')
                          $fail('Archivo debe ser un zip');
                  }
                },
            ]
        ])->validate();

        try
        {
            $talend = new Talend($request->except('file'));
            $talend->company_id = $this->company;
            $talend->path = $talend->path_base();
            $this->makeDirectory($talend->path_client());

            $file_tmp = $request->file;
            $file_tmp->storeAs($talend->path_client(false), $file_tmp->getClientOriginalName());

            $talend->file = $file_tmp->getClientOriginalName();
            $talend->file_original_name = pathinfo($file_tmp->getClientOriginalName(), PATHINFO_FILENAME);
            
            if (!$talend->save())
                return $this->respondHttp500();

            ExtractTalendZipFileJob::dispatch($talend);
        }
        catch(\Exception $e) {
          \Log::info($e->getMessage());
          return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se cargo el talend'
        ]);
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
            $talendUpload = Talend::findOrFail($id);
            $talendUpload->old_file = $talendUpload->file;

            return $this->respondHttp200([
                'data' => $talendUpload,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\PreventiveOccupationalMedicine\Absenteeism\TalendRequest  $request
     * @param  Talend  $talendUpload
     * @return \Illuminate\Http\Response
     */
    public function update(TalendRequest $request, Talend $talendUpload)
    {
        Validator::make($request->all(), [
            "file" => [
                function ($attribute, $value, $fail)
                {
                  if ($value && !is_string($value))
                  {
                    $ext = strtolower($value->getClientOriginalExtension());
                    
                    if ($ext != 'zip')
                        $fail('Archivo debe ser un zip');
                  }
                },
            ]
        ])->validate();

        $talendUpload->fill($request->except('file'));

        if ($request->file != $talendUpload->file)
        {
            $talendUpload->path = $talendUpload->path_base();
            $this->makeDirectory($talendUpload->path_client());

            $file_tmp = $request->file;
            $file_tmp->storeAs($talendUpload->path_client(false), $file_tmp->getClientOriginalName());

            $talendUpload->file = $file_tmp->getClientOriginalName();
            $talendUpload->file_original_name = pathinfo($file_tmp->getClientOriginalName(), PATHINFO_FILENAME);
            $talendUpload->state = 'NO';

            ExtractTalendZipFileJob::dispatch($talendUpload);
        }
        
        if (!$talendUpload->update())
          return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el talend'
        ]);
    }

    /**
     * toggles the check state between open and close
     * @param  Talend $Talend
     * @return \Illuminate\Http\Response
     */
    public function toggleState(Talend $talendUpload)
    {
        $newState = $talendUpload->isActive() ? "NO" : "SI";
        $data = ['state' => $newState];

        if (!$talendUpload->update($data)) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado del talend'
        ]);
    }

    public function download(Talend $talendUpload)
    {
        return Storage::disk('local')->download($talendUpload->path_donwload());
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $talends = Talend::active()->select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($talends)
            ]);
        }
        else
        {
            $talends = Talend::active()->select(
                'sau_absen_talends.id as id',
                'sau_absen_talends.name as name'
            )
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($talends);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function talendExist(Request $request)
    {
        try
        {
            $talend = Talend::active()->first();

            return $this->respondHttp200([
                'data' => $talend ? true : false
            ]);

        } catch(Exception $e){
            $this->respondHttp500();
        }
    }
}
