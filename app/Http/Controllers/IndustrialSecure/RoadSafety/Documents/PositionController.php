<?php

namespace App\Http\Controllers\IndustrialSecure\RoadSafety\Documents;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RoadSafety\Position;
use App\Http\Requests\IndustrialSecure\RoadSafety\Documents\PositionRequest;
use App\Models\IndustrialSecure\RoadSafety\PositionDocument;
use App\Models\Administrative\Positions\EmployeePosition;
use Carbon\Carbon;
use DB;

class PositionController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:roadsafety_documents_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:roadsafety_documents_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:roadsafety_documents_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:roadsafety_documents_d, {$this->team}", ['only' => 'destroy']);
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
        $positions = Position::select('*')->orderBy('id', 'DESC');

        return Vuetable::of($positions)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\PositionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PositionRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $position = new Position;
            $position->company_id = $this->company;
            $position->name = EmployeePosition::find($request->employee_position_id)->name;
            $position->employee_position_id = $request->employee_position_id;

            if (!$position->save())
                return $this->respondHttp500();

            if ($request->has('documents'))
                $this->saveDocuments($request->documents, $position);

            DB::commit();

        $this->saveLogActivitySystem('Seguridad vial - Documentos', 'Se creo el documento '.$position->name);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el documento'
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
            $position = Position::findOrFail($id);

            foreach ($position->documents as $document)
            {
                $document->key = Carbon::now()->timestamp + rand(1,10000);
            }

            $position->multiselect_cargo = $position->position->multiselect();

            $position->delete = [
                'documents' => []
            ];

            return $this->respondHttp200([
                'data' => $position,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\PositionRequest  $request
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function update(PositionRequest $request, Position $document)
    {
        DB::beginTransaction();

        try
        {
            $document->name = EmployeePosition::find($request->employee_position_id)->name;
            $document->employee_position_id = $request->employee_position_id;

            if(!$document->update()){
                return $this->respondHttp500();
            }

            if ($request->has('documents'))
                $this->saveDocuments($request->documents, $document);

            $this->deleteData($request->get('delete'));

            DB::commit();

            $this->saveLogActivitySystem('Seguridad vial - Documentos', 'Se edito el documento '.$document->name);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el documento'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy(Position $document)
    {
        $this->saveLogActivitySystem('Seguridad vial - Documentos', 'Se elimino el documento '.$document->name);

        if (!$document->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el documento'
        ]);
    }

    private function saveDocuments($documents, $position)
    {
        foreach ($documents as $document)
        {
            $id = isset($document['id']) ? $document['id'] : NULL;
            $doc_act = $position->documents()->updateOrCreate(['id'=>$id], $document);
        }
    }

    private function deleteData($data)
    {    
        if (COUNT($data['documents']) > 0)
            Position::destroy($data['documents']);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

   /*public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $activities = ActivityContract::select("id", "name")
                ->where('company_id', $this->company)
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($activities)
            ]);
        }
        else
        {
            $activities = ActivityContract::selectRaw("
                sau_ct_activities.id as id,
                sau_ct_activities.name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('name')
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($activities);
        }
    }*/
}
