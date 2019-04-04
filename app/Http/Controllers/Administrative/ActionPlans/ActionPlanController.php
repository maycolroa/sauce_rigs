<?php

namespace App\Http\Controllers\Administrative\ActionPlans;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\ActionPlansActivity;
use Illuminate\Support\Facades\Auth;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Http\Requests\Administrative\ActionPlans\ActionPlanRequest;
use Session;
use DB;

class ActionPlanController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        //$this->middleware('permission:actionPlans_c', ['only' => 'store']);
        $this->middleware('permission:actionPlans_r');
        $this->middleware('permission:actionPlans_u', ['only' => 'update']);
        //$this->middleware('permission:actionPlans_d', ['only' => 'destroy']);
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
        $activities = ActionPlansActivity::select(
                'sau_action_plans_activities.*',
                'sau_action_plans_activities.state as state_activity',
                'sau_users.name as responsible',
                'sau_modules.display_name')
            ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
            ->join('sau_modules', 'sau_modules.id', 'sau_action_plans_activity_module.module_id')
            ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id');
            
        if (!Auth::user()->hasRole('Superadmin'))
        {
            $activities->where(function ($subquery) {
                $subquery->whereIn('sau_action_plans_activity_module.module_id', $this->getIdsModulePermissions());

                $subquery->orWhere('sau_action_plans_activities.responsible_id', Auth::user()->id);
            });
        }

        return Vuetable::of($activities)
                    ->make();
    }

     /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            return $this->respondHttp200([
                'data' => ActionPlan::prepareActivityDataComponent($id)
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Http\Requests\Administrative\ActionPlans\ActionPlanRequest $request
     * @param ActionPlansActivity $actionplan
     * @return \Illuminate\Http\Response
     */
    public function update(ActionPlanRequest $request, ActionPlansActivity $actionplan)
    {
        try
        {
            DB::beginTransaction();

            ActionPlan::
                    user(Auth::user())
                ->module('actionPlans')
                ->url(url('/'))
                ->model($actionplan)
                ->activities($request->get('actionPlan'))
                ->save()
                ->sendMail();

            DB::commit();

        } catch(Exception $e){
            DB::rollback();
            $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la actividad'
        ]);
    }
}
