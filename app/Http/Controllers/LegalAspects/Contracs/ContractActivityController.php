<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Http\Requests\LegalAspects\Contracts\ActivityContractRequest;
use App\Models\LegalAspects\Contracts\ActivityDocument;
use App\Models\LegalAspects\Contracts\ContractDocument;
use Carbon\Carbon;
use DB;

class ContractActivityController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_activities_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_activities_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:contracts_activities_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_activities_d, {$this->team}", ['only' => 'destroy']);
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
        $activities = ActivityContract::select('*');

        return Vuetable::of($activities)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityContractRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ActivityContractRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $activity = new ActivityContract;
            $activity->company_id = $this->company;
            $activity->name = $request->name;

            if (!$activity->save())
                return $this->respondHttp500();

            if ($request->has('documents'))
                $this->saveDocuments($request->documents, $activity);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la actividad'
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
            $activity = ActivityContract::findOrFail($id);

            foreach ($activity->documents as $document)
            {
                $document->key = Carbon::now()->timestamp + rand(1,10000);
            }

            $activity->delete = [
                'documents' => []
            ];

            return $this->respondHttp200([
                'data' => $activity,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\ActivityContractRequest  $request
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(ActivityContractRequest $request, ActivityContract $activityContract)
    {
        DB::beginTransaction();

        try
        {
            $activityContract->fill($request->all());

            if(!$activityContract->update()){
                return $this->respondHttp500();
            }

            if ($request->has('documents'))
                $this->saveDocuments($request->documents, $activityContract);

            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la actividad'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(ActivityContract $activityContract)
    {
        if (!$activityContract->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la actividad'
        ]);
    }

    private function saveDocuments($documents, $activity)
    {
        foreach ($documents as $document)
        {
            $id = isset($document['id']) ? $document['id'] : NULL;
            $doc_act = $activity->documents()->updateOrCreate(['id'=>$id], $document);

            if ($document['type'] == 'Contratista')
            {
                ContractDocument::updateOrCreate(['document_id'=> $doc_act->id], 
                ['name' => $document['name'],
                'company_id' => $activity->company_id,
                'document_id' => $doc_act->id]);
            }
            else if ($document['type'] == 'Empleado')
            {
                $doc_del = ContractDocument::where('document_id', $doc_act->id)->first();

                if ($doc_del)
                    $doc_del->delete();

            }
        }
    }

    private function deleteData($data)
    {    
        if (COUNT($data['documents']) > 0)
            ActivityDocument::destroy($data['documents']);
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
            $activities = ActivityContract::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
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
            ")->pluck('id', 'name');
        
            return $this->multiSelectFormat($activities);
        }
    }
}
