<?php

namespace App\Traits;

use Exception;
use Carbon\Carbon;
use App\User;
use App\Models\Module;
use App\Models\ActionPlansActivity;
use App\Models\ActionPlansActivityModule;
use Illuminate\Support\Facades\Auth;

trait ActionPlanTrait
{
    /**
     * returns the possible states of activities
     *
     * @return Array
     */
    protected function getActionPlanStates()
    {
        $states = ['Pendiente', 'Ejecutada', 'No aplica'];
        return $states;
    }

    /**
     * returns validation rules for action plans
     *
     * @param  String $application
     * @return Array
     */
    protected function getActionPlanRules($prefixIndex = '')
    {
        $rules = [];

        $ACTION_PLAN_STATES = implode(",", $this->getActionPlanStates());

        $rules['rules'][$prefixIndex.'actionPlan'] = 'array';
        $rules['rules'][$prefixIndex.'actionPlan.activities'] = 'array';
        $rules['rules'][$prefixIndex.'actionPlan.activities.*.description'] = 'required';
        $rules['rules'][$prefixIndex.'actionPlan.activities.*.employee_id'] = 'required|exists:sau_employees,id';
        $rules['rules'][$prefixIndex.'actionPlan.activities.*.execution_date'] = 'required|date';
        $rules['rules'][$prefixIndex.'actionPlan.activities.*.expiration_date'] = 'required|date';
        $rules['rules'][$prefixIndex.'actionPlan.activities.*.state'] = "required|in:$ACTION_PLAN_STATES";

        $rules['messages'][$prefixIndex.'actionPlan.array'] = 'El campo Planes de acción debe ser un array';
        $rules['messages'][$prefixIndex.'actionPlan.activities.array'] = 'El campo Actividades debe ser un array';
        $rules['messages'][$prefixIndex.'actionPlan.activities.*.description.required'] = 'El campo Descripción es obligatorio.';
        $rules['messages'][$prefixIndex.'actionPlan.activities.*.employee_id.required'] = 'El campo Responsable es obligatorio.';
        $rules['messages'][$prefixIndex.'actionPlan.activities.*.employee_id.exists'] = 'El campo Responsable debe existir en la base de datos.';
        $rules['messages'][$prefixIndex.'actionPlan.activities.*.execution_date.required'] = 'El campo Fecha de ejecución es obligatorio.';
        $rules['messages'][$prefixIndex.'actionPlan.activities.*.execution_date.date'] = 'El campo Fecha de ejecución debe ser una fecha valida';
        $rules['messages'][$prefixIndex.'actionPlan.activities.*.expiration_date.required'] = 'El campo Fecha de vencimiento es obligatorio.';
        $rules['messages'][$prefixIndex.'actionPlan.activities.*.expiration_date.date'] = 'El campo Fecha de vencimiento debe ser una fecha valida';
        $rules['messages'][$prefixIndex.'actionPlan.activities.*.state.required'] = 'El campo Estado es obligatorio.';
        $rules['messages'][$prefixIndex.'actionPlan.activities.*.state.*.in'] = 'El campo Estado es inválido.';

        return $rules;
    }

    /**
     * stores the activities of the action plan
     *
     * @param Array $data
     * @param String $moduleName
     * @param Illuminate\Database\Eloquent\Model $model
     * @param App\User $user OR Illuminate\Support\Facades\Auth $user
     * @return void
     */
    protected function saveActionPlan($data, $moduleName, $model, $user)
    {
        if (COUNT($data) > 0)
        {
            $module = Module::select('id')->where('name', $moduleName)->first();

            if (!$module)
                throw new Exception('the name of the module does not exist');

            if (!isset($model->id))
                throw new Exception('the model is invalid');

            if (!isset($data['activities']) || !isset($data['activitiesRemoved']))
                throw new Exception('invalid data');
            
            if (!$user instanceof User && !$user instanceof Auth)
                throw new \Exception('invalid user instance');

            $activitiesReady = [];
            $activitiesNew = [];

            foreach ($data['activities'] as $itemA)
            {   
                if ($itemA['id'] == '')
                {   
                    $activity = new ActionPlansActivity();
                    $activity->user_id = $user->id;
                }
                else
                    $activity = ActionPlansActivity::find($itemA['id']);

                $activity->description = $itemA['description'];
                $activity->employee_id = $itemA['employee_id'];
                $activity->execution_date = (Carbon::createFromFormat('D M d Y', $itemA['execution_date']))->format('Ymd');
                $activity->expiration_date = (Carbon::createFromFormat('D M d Y', $itemA['expiration_date']))->format('Ymd');
                $activity->state = $itemA['state'];
                $activity->save();
                
                if(isset($itemA['oldState']) && ($itemA['oldState'] != $itemA['state']))
                    array_push($activitiesReady, $itemA);

                if($itemA['id'] == "")
                {
                    array_push($activitiesNew, $itemA);

                    $activity->activityModule()->create([
                        'module_id' => $module->id,
                        'item_id' => $model->id,
                        'item_table_name' => $model->getTable()
                    ]);
                }
            }
            
            foreach ($data['activitiesRemoved'] as $itemA)
            {
                $activity = ActionPlansActivity::find($itemA['id']);

                if ($activity)
                    $activity->delete();
            }
        }
    }

    /**
     * returns the information of the activities of the action plan 
     * in the format necessary for the Vue component
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @return Array
     */
    protected function prepareDataActionPlanComponent($model)
    {
        if (!isset($model->id))
            throw new Exception('the model is invalid');
                
        $data['activities'] = [];
        $data['activitiesRemoved'] = [];

        $activities = ActionPlansActivityModule::
              where('sau_action_plans_activity_module.item_id', $model->id)
            ->where('sau_action_plans_activity_module.item_table_name', $model->getTable())
            ->get();

        foreach ($activities as $value)
        {
            $tmp = [];
            $tmp['key'] = Carbon::now()->timestamp + rand(1,10000);
            $tmp['id'] = $value->activity->id;
            $tmp['description'] = $value->activity->description;
            $tmp['employee_id'] = $value->activity->employee_id;
            $tmp['multiselect_employee'] = $value->activity->employee->multiselect();
            $tmp['execution_date'] = (Carbon::createFromFormat('Y-m-d', $value->activity->execution_date))->format('D M d Y');
            $tmp['expiration_date'] = (Carbon::createFromFormat('Y-m-d', $value->activity->expiration_date))->format('D M d Y');
            $tmp['state'] = $value->activity->state;
            $tmp['oldState'] = $value->activity->state;
            $tmp['editable'] = $value->activity->editable;

            array_push($data['activities'], $tmp);
        }

        return $data;
    }

    /**
     * Eliminates all the activities of the action plan in case of 
     * aliminar the item with which this is associated
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    protected function modelDeleteAllActionPlan($model)
    {
        if (!isset($model->id))
            throw new Exception('the model is invalid');

        $activities = ActionPlansActivityModule::
            selectRaw('GROUP_CONCAT(sau_action_plans_activity_module.activity_id) as ids')
          ->where('sau_action_plans_activity_module.item_id', $model->id)
          ->where('sau_action_plans_activity_module.item_table_name', $model->getTable())
          ->first();

        if ($activities)
            ActionPlansActivity::whereIn('id', explode(",", $activities->ids))->delete();
    }
}