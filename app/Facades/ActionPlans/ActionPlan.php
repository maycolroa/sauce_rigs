<?php

namespace App\Facades\ActionPlans;

use Illuminate\Support\Facades\App;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Traits\UtilsTrait;
use Carbon\Carbon;
use App\Models\Administrative\Users\User;
use App\Models\General\Module;
use App\Models\General\Team;
use App\Models\Administrative\ActionPlans\ActionPlansActivity;
use App\Models\Administrative\ActionPlans\ActionPlansActivityModule;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\Administrative\Headquarters\EmployeeHeadquarter;
use App\Models\Administrative\Areas\EmployeeArea;
use App\Models\Administrative\Processes\EmployeeProcess;
use App\Models\System\Licenses\License;
use App\Facades\Mail\Facades\NotificationMail;
use App\Facades\Configuration;
use Exception;
use Session;


class ActionPlan
{
    use UtilsTrait;
    /**
     * States of activities
     *
     * @var String
     */
    private $states;

    /**
     * Prefix that will be used to create the validation rules
     *
     * @var String
     */
    private $prefixIndex = '';

    /**
     * Model to which the actions executed are associated
     *
     * @var Illuminate\Database\Eloquent\Model
     */
    private $model;

    /**
     * User in session
     *
     * @var App\User
     * @var Illuminate\Support\Facades\Auth
     */
    private $user;

    /**
     * Module that executes the event
     *
     * @var App\Models\Module
     * @var String
     */
    private $module;

    /**
     * Activities that are associated with the model that is established for the instance
     *
     * @var Array
     */
    private $activities;

    /**
     * Activities new that are associated with the model that is established for the instance
     *
     * @var Array
     */
    private $activitiesNew = [];

    /**
     * Activities ready that are associated with the model that is established for the instance
     *
     * @var Array
     */
    private $activitiesReady = [];

    /**
     * Regional main register that contains all the activities
     *
     * @var String
     * @var Integer
     * @var App\Administrative\EmployeeRegional
     */
    private $regional;

    /**
     * Headquarters of the main registry that contains all the activities
     *
     * @var String
     * @var Integer
     */
    private $headquarter;

    /**
     * Main record area that contains all the activities
     *
     * @var String
     * @var Integer
     */
    private $area;

    /**
     * Undocumented variable
     *
     * @var String
     * @var Interger
     */
    private $process;

    /**
     * Date of creation of the main record that contains all the activities
     *
     * @var String
     */
    private $creationDate;

    /**
     * Url of the main record that contains all the activities
     *
     * @var String
     */
    private $url;

    /**
     * Id of the company
     *
     * @var Integer
     */
    private $company;

    /**
     * Id of the company
     *
     * @var Integer
     */
    private $daysAlertExpirationDate;

    /**
     * Undocumented variable
     *
     * @var String
     * @var Interger
     */
    private $details;

    /**
     * True: YYYY-MM-DD / False: D M d Y
     *
     * @var Booleam
     */
    private $dateSimpleFormat;

     /**
     * Detail of the plan
     *
     * @var string
     */
    private $detailProcedence = '';


    public function __construct()
    {
        $this->states = Configuration::getConfiguration('action_plans_states');
    }

    public function restart()
    {
        $this->module = null;
        $this->url = null;
        $this->model = null;
        $this->regional = null;
        $this->headquarter = null;
        $this->area = null;
        $this->process = null;
        $this->activities = null;
        $this->company = null;
        $this->details = null;
        $this->states = null;
        $this->prefixIndex = null;
        $this->user = null;
        $this->activitiesNew = [];
        $this->activitiesReady = [];
        $this->creationDate = null;
        $this->daysAlertExpirationDate = null;
        $this->dateSimpleFormat = false;
        $this->detailProcedence = null;
    }

    /**
     * Edit the mail subject
     *
     * @param string $subject
     * @return $this
     */
    public function detailProcedence($detailProcedence)
    {        
        if (!is_string($detailProcedence) || $detailProcedence == '')
            throw new \Exception('The format of the detailProcedence is incorrect'); 

        $this->detailProcedence = $detailProcedence;

        return $this;
    }

    /**
     * Process of the main register that contains all the activities
     *
     * @return Array
     */
    public function getStates()
    {
        return $this->states;
    }

    /**
     * Assigns the prefix of the validation rules
     * 
     * @param String $prefixIndex
     * @return $this
     */
    public function prefixIndex($prefixIndex)
    {
        if (!is_string($prefixIndex))
            throw new \Exception('The format of the prefixIndex is incorrect'); 

        $this->prefixIndex = $prefixIndex;

        return $this;
    }

    /**
     * Assigns the model to which the executed actions are associated
     *
     * @param Illuminate\Database\Eloquent\Model $model
     * @return $this
     */
    public function model($model)
    {
        if (!isset($model->id) || !$model->getTable())
            throw new Exception('the model is invalid');

        $this->model = $model;

        return $this;
    }

    /**
     * Set the user in session
     *
     * @param App\User $user
     * @param Illuminate\Support\Facades\Auth $user
     * @return void
     */
    public function user($user)
    {
        if (!$user instanceof User && !$user instanceof Auth)
            throw new Exception('the user is invalid');

        $this->user = $user;

        return $this;
    }

    /**
     * Assigns the module to which the executed actions are associated
     *
     * @param App\Models\Module $module
     * @param String $module
     * @return $this
     */
    public function module($module)
    {
        if (is_string($module))
        {
            $module = Module::where('name', $module)->first();

            if (!$module)
                throw new \Exception('Module not found');   
        }
        else if (!$module instanceof Module)
            throw new \Exception('Invalid module format');
        
        $this->module = $module;

        return $this;
    }

    /**
     * Assign the activities that are associated with the model that was 
     * established for the instance
     *
     * @param Array $activities
     * @return $this
     */
    public function activities($activities)
    {        
        if (empty($activities) || !$this->is_assoc($activities) || !isset($activities['activities']) || !isset($activities['activitiesRemoved']))
            throw new \Exception('The format of the activities is incorrect');
        
        $this->activities = $activities;

        return $this;
    }

    /**
     * Establishes the regional
     *
     * @param Integer $regional
     * @param String $regional
     * @param App\Administrative\EmployeeRegional $regional
     * @return $this
     */
    public function regional($regional)
    {
        if (is_numeric($regional))
        {
            $regional = EmployeeRegional::find($regional);

            if (!$regional)
                throw new \Exception('Regional not found');

            $this->regional = $regional->name;
        }
        else if (is_string($regional))
        {
            $this->regional = $regional;  
        }
        else if ($regional instanceof EmployeeRegional)
        {
            $this->regional = $regional->name;
        }

        return $this;
    }

    /**
     * Establishes the regional
     *
     * @param Integer $headquarter
     * @param String $headquarter
     * @param App\Administrative\EmployeeHeadquarter $headquarter
     * @return $this
     */
    public function headquarter($headquarter)
    {
        if (is_numeric($headquarter))
        {
            $headquarter = EmployeeHeadquarter::find($headquarter);

            if (!$headquarter)
                throw new \Exception('Headquarter not found');

            $this->headquarter = $headquarter->name;
        }
        else if (is_string($headquarter))
        {
            $this->headquarter = $headquarter;  
        }
        else if ($headquarter instanceof EmployeeHeadquarter)
        {
            $this->headquarter = $headquarter->name;
        }

        return $this;
    }

    /**
     * Establishes the area
     *
     * @param Integer $area
     * @param String $area
     * @param App\Administrative\EmployeeArea $area
     * @return $this
     */
    public function area($area)
    {
        if (is_numeric($area))
        {
            $area = EmployeeArea::find($area);

            if (!$area)
                throw new \Exception('Area not found');

            $this->area = $area->name;
        }
        else if (is_string($area))
        {
            $this->area = $area;  
        }
        else if ($area instanceof EmployeeArea)
        {
            $this->area = $area->name;
        }

        return $this;
    }

    /**
     * Establishes the process
     *
     * @param Integer $process
     * @param String $process
     * @param App\Administrative\EmployeeProcess $process
     * @return $this
     */
    public function process($process)
    {
        if (is_numeric($process))
        {
            $process = EmployeeProcess::find($process);

            if (!$process)
                throw new \Exception('process not found');

            $this->process = $process->name;
        }
        else if (is_string($process))
        {
            $this->process = $process;  
        }
        else if ($process instanceof EmployeeProcess)
        {
            $this->process = $process->name;
        }

        return $this;
    }

    public function details($details)
    {
        $this->details = $details;
        return $this;
    }

    public function dateSimpleFormat($dateSimpleFormat)
    {
        $this->dateSimpleFormat = $dateSimpleFormat;
        return $this;
    }

    /**
     * Assign the creation date of the main record that contains all the activities
     * 
     * @param String $creationDate
     * @return $this
     */
    public function creationDate($creationDate)
    {
        if (!$this->validateDate($creationDate))
            throw new \Exception('The format of the creation date is incorrect'); 

        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Assign the URL of the main record that contains all the activities
     * 
     * @param String $url
     * @return $this
     */
    public function url($url)
    {
        if (!is_string($url))
            throw new \Exception('The format of the url is incorrect'); 

        $this->url = $url;

        return $this;
    }

    /**
     * Set the id of the company
     *
     * @param Integer $company
     * @return $this
     */
    public function company($company)
    {
        if (!is_numeric($company))
            throw new \Exception('Invalid company format');

        $this->company = $company;

        return $this;
    }

    /**
     * Establishes the days on which workers must be altered by close expiration date
     *
     * @param Integer $daysAlertExpirationDate
     * @return $this
     */
    public function daysAlertExpirationDate($daysAlertExpirationDate)
    {
        if (!is_numeric($daysAlertExpirationDate))
            throw new \Exception('Invalid daysAlertExpirationDate format');

        $this->daysAlertExpirationDate = $daysAlertExpirationDate;

        return $this;
    }

    /**
     * Returns an array with the validation rules for action plans.
     *
     * @return Array
     */
    public function getRules()
    {
        $rules = [];

        $ACTION_PLAN_STATES = implode(",", $this->states);

        $rules['rules'][$this->prefixIndex.'actionPlan'] = 'array';
        $rules['rules'][$this->prefixIndex.'actionPlan.activities'] = 'array';
        $rules['rules'][$this->prefixIndex.'actionPlan.activities.*.description'] = 'required';
        $rules['rules'][$this->prefixIndex.'actionPlan.activities.*.responsible_id'] = 'required|exists:sau_users,id';
        $rules['rules'][$this->prefixIndex.'actionPlan.activities.*.execution_date'] = 'date';
        $rules['rules'][$this->prefixIndex.'actionPlan.activities.*.expiration_date'] = 'required|date';
        $rules['rules'][$this->prefixIndex.'actionPlan.activities.*.state'] = "required|in:$ACTION_PLAN_STATES";

        $rules['messages'][$this->prefixIndex.'actionPlan.array'] = 'El campo Planes de acción debe ser un array';
        $rules['messages'][$this->prefixIndex.'actionPlan.activities.array'] = 'El campo Actividades debe ser un array';
        $rules['messages'][$this->prefixIndex.'actionPlan.activities.*.description.required'] = 'El campo Descripción es obligatorio.';
        $rules['messages'][$this->prefixIndex.'actionPlan.activities.*.responsible_id.required'] = 'El campo Responsable es obligatorio.';
        $rules['messages'][$this->prefixIndex.'actionPlan.activities.*.responsible_id.exists'] = 'El campo Responsable debe existir en la base de datos.';
        //$rules['messages'][$this->prefixIndex.'actionPlan.activities.*.execution_date.required'] = 'El campo Fecha de ejecución es obligatorio.';
        $rules['messages'][$this->prefixIndex.'actionPlan.activities.*.execution_date.date'] = 'El campo Fecha de ejecución debe ser una fecha valida';
        $rules['messages'][$this->prefixIndex.'actionPlan.activities.*.expiration_date.required'] = 'El campo Fecha de vencimiento es obligatorio.';
        $rules['messages'][$this->prefixIndex.'actionPlan.activities.*.expiration_date.date'] = 'El campo Fecha de vencimiento debe ser una fecha valida';
        $rules['messages'][$this->prefixIndex.'actionPlan.activities.*.state.required'] = 'El campo Estado es obligatorio.';
        $rules['messages'][$this->prefixIndex.'actionPlan.activities.*.state.*.in'] = 'El campo Estado es inválido.';

        return $rules;
    }

    /**
     * returns the information of the activities of the action plan 
     * in the format necessary for the Vue component
     *
     * @return Array
     */
    public function prepareDataComponent()
    {
        if (empty($this->model))
            throw new \Exception('A valid model was not entered');
                
        $data['activities'] = [];
        $data['activitiesRemoved'] = [];

        $activities = ActionPlansActivityModule::
              where('sau_action_plans_activity_module.item_id', $this->model->id)
            ->where('sau_action_plans_activity_module.item_table_name', $this->model->getTable())
            ->get();

        foreach ($activities as $value)
        {
            $tmp = [];
            $tmp['key'] = Carbon::now()->timestamp + rand(1,10000);
            $tmp['id'] = $value->activity->id;
            $tmp['description'] = $value->activity->description;
            $tmp['oldDescription'] = $value->activity->description;
            $tmp['responsible_id'] = $value->activity->responsible_id;
            $tmp['oldResponsible_id'] = $value->activity->responsible_id;
            $tmp['multiselect_responsible'] = $value->activity->responsible->multiselectActionPlan();
            $tmp['user_id'] = $value->activity->user_id;
            $tmp['execution_date'] = ($value->activity->execution_date) ? (Carbon::createFromFormat('Y-m-d', $value->activity->execution_date))->format('D M d Y') : '';
            $tmp['oldExecution_date'] = ($value->activity->execution_date) ? (Carbon::createFromFormat('Y-m-d', $value->activity->execution_date))->format('D M d Y') : '';
            $tmp['expiration_date'] = (Carbon::createFromFormat('Y-m-d', $value->activity->expiration_date))->format('D M d Y');
            $tmp['oldExpiration_date'] = (Carbon::createFromFormat('Y-m-d', $value->activity->expiration_date))->format('D M d Y');
            $tmp['state'] = $value->activity->state;
            $tmp['oldState'] = $value->activity->state;
            $tmp['observation'] = $value->activity->observation;
            $tmp['oldObservation'] = $value->activity->observation;
            $tmp['editable'] = $value->activity->editable;
            $tmp['company_id'] = $value->activity->company_id;
            $tmp['edit_all'] = $this->checkEditAll($value->activity);
            $tmp['detail_procedence'] = $value->activity->detail_procedence;

            array_push($data['activities'], $tmp);
        }

        return $data;
    }

    /**
     * returns the information of the activity of the action plan 
     * in the format necessary for the Vue component
     *
     * @return Array
     */
    public function prepareActivityDataComponent($activity)
    {
        if (is_numeric($activity))
        {
            $activity = ActionPlansActivity::find($activity);

            if (!$activity)
                throw new \Exception('activity not found');
        }
        else if (!$activity instanceof ActionPlansActivity)
        {
            throw new \Exception('activity not found');
        }

        $data['actionPlan'] = [
            'activities' => [],
            'activitiesRemoved' => []
        ];

        if ($activity)
        {
            $tmp = [];
            $tmp['key'] = Carbon::now()->timestamp + rand(1,10000);
            $tmp['id'] = $activity->id;
            $tmp['description'] = $activity->description;
            $tmp['oldDescription'] = $activity->description;
            $tmp['responsible_id'] = $activity->responsible_id;
            $tmp['oldResponsible_id'] = $activity->responsible_id;
            $tmp['multiselect_responsible'] = $activity->responsible->multiselectActionPlan();
            $tmp['user_id'] = $activity->user_id;
            $tmp['user_creator_name'] = $activity->user->name;
            $tmp['execution_date'] = ($activity->execution_date) ? (Carbon::createFromFormat('Y-m-d', $activity->execution_date))->format('D M d Y') : '';
            $tmp['oldExecution_date'] = ($activity->execution_date) ? (Carbon::createFromFormat('Y-m-d', $activity->execution_date))->format('D M d Y') : '';
            $tmp['expiration_date'] = (Carbon::createFromFormat('Y-m-d', $activity->expiration_date))->format('D M d Y');
            $tmp['oldExpiration_date'] = (Carbon::createFromFormat('Y-m-d', $activity->expiration_date))->format('D M d Y');
            $tmp['state'] = $activity->state;
            $tmp['oldState'] = $activity->state;
            $tmp['observation'] = $activity->observation;
            $tmp['oldObservation'] = $activity->observation;
            $tmp['editable'] = $activity->editable;
            $tmp['company_id'] = $activity->company_id;
            $tmp['edit_all'] = $this->checkEditAll($activity);
            $tmp['detail_procedence'] = $activity->detail_procedence;

            array_push($data['actionPlan']['activities'], $tmp);
        }

        return $data;
    }

    /**
     * Verify if the activity was created by the user in session or can see it by assignment
     *
     * @param App\Models\ActionPlansActivity $activity
     * @return booleam
     */
    private function checkEditAll($activity)
    {
        $team = Team::where('name', Session::get('company_id'))->first();

        if (Auth::user()->hasRole('Superadmin', $team))
            return true;

        else if ($activity->user_id == Auth::user()->id)
            return true;

        else 
            return false;
    }

    /**
     * Eliminates all the activities of the action plan in case of 
     * aliminar the item with which this is associated
     *
     * @return Void
     */
    public function modelDeleteAll()
    {
        if (empty($this->model))
            throw new \Exception('A valid model was not entered');

        $activities = ActionPlansActivityModule::
            selectRaw('GROUP_CONCAT(sau_action_plans_activity_module.activity_id) as ids')
          ->where('sau_action_plans_activity_module.item_id', $this->model->id)
          ->where('sau_action_plans_activity_module.item_table_name', $this->model->getTable())
          ->first();

        if ($activities)
        {
            if (!empty($this->company))
            {
                $data = ActionPlansActivity::whereIn('id', explode(",", $activities->ids));
                $data->company_scope = $this->company;
                $data->delete();
            }
            else 
                ActionPlansActivity::whereIn('id', explode(",", $activities->ids))->delete();
        }
    }

    /**
     * Edit the activity of the one action plan
     * 
     * @return $this
     */
    /*public function updateActivity()
    {
        if (empty($this->activities))
            throw new \Exception('No valid activities have been entered');

        if (empty($this->module))
            throw new \Exception('The id of the module that performed the action was not entered');

        if (empty($this->model))
            throw new \Exception('A valid model was not entered');

        if (empty($this->user))
            throw new \Exception('A valid user has not been entered.');

        if (!empty($this->company))
            $company_id = $this->company;
        else if (Session::get('company_id'))
            $company_id = Session::get('company_id');
        else
            throw new \Exception('A valid company has not been entered.');

        /**********************************************************************/

        /*foreach ($this->activities['activities'] as $itemA)
        {   
            $model->description = $itemA['description'];
            $model->responsible_id = $itemA['responsible_id'];
            $model->execution_date = ($itemA['execution_date']) ? (Carbon::createFromFormat('D M d Y', $itemA['execution_date']))->format('Ymd') : null;
            $model->expiration_date = (Carbon::createFromFormat('D M d Y', $itemA['expiration_date']))->format('Ymd');
            $model->state = $itemA['state'];
            $model->company_id = $company_id;
            $model->save();
            
            if(isset($itemA['oldState']) && ($itemA['oldState'] != $itemA['state']))
                array_push($this->activitiesReady, $itemA);
        }

        return $this;
    }*/

    /**
     * Stores the activities of the action plan
     * 
     * @return $this
     */
    public function save()
    {
        if (empty($this->activities))
            throw new \Exception('No valid activities have been entered');

        if (empty($this->module))
            throw new \Exception('The id of the module that performed the action was not entered');

        if (empty($this->model))
            throw new \Exception('A valid model was not entered');

        if (empty($this->user))
            throw new \Exception('A valid user has not been entered.');

        if (!empty($this->company))
            $company_id = $this->company;
        else if (Session::get('company_id'))
            $company_id = Session::get('company_id');
        else
            throw new \Exception('A valid company has not been entered.');

        /**********************************************************************/

        foreach ($this->activities['activities'] as $keyItem => $itemA)
        {   
            if ($itemA['id'] == '')
            {   
                $activity = new ActionPlansActivity();
                $activity->user_id = $this->user->id;
            }
            else
            {
                $activity = ActionPlansActivity::query();
                $activity->company_scope = $company_id;
                $activity = $activity->find($itemA['id']);
            }

            $activity->description = $itemA['description'];
            $activity->responsible_id = $itemA['responsible_id'];

            if ($this->dateSimpleFormat)
            {
                $activity->execution_date = $itemA['execution_date'];
                $activity->expiration_date = $itemA['expiration_date'];
            }
            else
            {
                $activity->execution_date = ($itemA['execution_date']) ? (Carbon::createFromFormat('D M d Y', $itemA['execution_date']))->format('Ymd') : null;
                $activity->expiration_date = (Carbon::createFromFormat('D M d Y', $itemA['expiration_date']))->format('Ymd');
            }
             
            $activity->state = $itemA['state'];
            $activity->editable = (isset($itemA['editable']) && $itemA['editable']) ? $itemA['editable'] : 'SI';
            $activity->company_id = $company_id;
            $activity->observation = $itemA['observation'];
            $activity->detail_procedence = $this->detailProcedence;
            $activity->save();
            
            if(isset($itemA['oldState']))
            {
                if (($itemA['oldDescription'] != $itemA['description']) ||
                    ($itemA['oldExpiration_date'] != $itemA['expiration_date']) ||
                    ($itemA['oldExecution_date'] != $itemA['execution_date']) ||
                    ($itemA['oldState'] != $itemA['state']) ||
                    ($itemA['oldObservation'] != $itemA['observation'])
                   )
                {
                    array_push($this->activitiesReady, $itemA);
                }

                if ($itemA['oldResponsible_id'] != $itemA['responsible_id'])
                    if ($itemA['state'] == 'Pendiente')
                        array_push($this->activitiesNew, $itemA);
            }

            if($itemA['id'] == "")
            {
                if ($itemA['state'] == 'Pendiente')
                    array_push($this->activitiesNew, $itemA);

                $activity->activityModule()->create([
                    'module_id' => $this->module->id,
                    'item_id' => $this->model->id,
                    'item_table_name' => $this->model->getTable()
                ]);
            }
            
            $this->activities['activities'][$keyItem]['id'] = $activity->id;
            $this->activities['activities'][$keyItem]['oldDescription'] = $activity->description;
            $this->activities['activities'][$keyItem]['oldState'] = $activity->state;
            $this->activities['activities'][$keyItem]['oldObservation'] = $activity->observation;
            $this->activities['activities'][$keyItem]['oldResponsible_id'] = $activity->responsible_id;
            $this->activities['activities'][$keyItem]['user_id'] = $activity->user_id;

            if ($this->dateSimpleFormat)
            {
                $this->activities['activities'][$keyItem]['oldExpiration_date'] = $activity->expiration_date;
                $this->activities['activities'][$keyItem]['oldExecution_date'] = $activity->execution_date;
            }
            else
            {
                $this->activities['activities'][$keyItem]['oldExpiration_date'] = $activity->expiration_date ? (Carbon::createFromFormat('Ymd', $activity->expiration_date))->format('D M d Y') : '';
                $this->activities['activities'][$keyItem]['oldExecution_date'] = $activity->execution_date ? (Carbon::createFromFormat('Ymd', $activity->execution_date))->format('D M d Y') : '';
            }
        }
        
        foreach ($this->activities['activitiesRemoved'] as $itemA)
        {
            $activity = ActionPlansActivity::query();
            $activity->company_scope = $company_id;
            $activity = $activity->find($itemA['id']);


            if ($activity)
                $activity->delete();
        }

        $this->activities['activitiesRemoved'] = [];

        return $this;
    }

    /**
     * Returns an array with the activities for action plans.
     *
     * @return Array
     */
    public function getActivities()
    {
        return $this->activities;
    }

    /**
     * It is responsible for sending the activities by mail
     *
     * @return Void
     */
    public function sendMail()
    {
        if (empty($this->user))
            throw new \Exception('A valid user has not been entered.');

        /*if (empty($this->creationDate))
            throw new \Exception('A valid creation date has not been entered.');*/

        if (empty($this->url))
            throw new \Exception('A valid url has not been entered.');

        $this->sendMailNew();

        $this->sendMailReady();
    }

    /**
     * It is responsible for sending new activities by mail
     * 
     * @return Void
     */
    private function sendMailNew()
    {
        if (!empty($this->company))
            $company_id = $this->company;
        else if (Session::get('company_id'))
            $company_id = Session::get('company_id');

        $this->activitiesNew = collect($this->activitiesNew);

        $groupResponsible = $this->activitiesNew->groupBy('responsible_id');

        foreach($groupResponsible as $data => $value)
        {
            $responsible = User::active()->find($data);

            if($responsible && $responsible->email != null)
            {
                if ($responsible->can('actionPlans_receive_notifications', $company_id))
                {
                    NotificationMail::
                        subject('Nuevas Actividades')
                        ->view('actionplan.activities')
                        ->recipients($responsible)
                        ->message('Se han asignado las siguientes actividades para usted.')
                        ->module('actionPlans')
                        ->table($this->prepareDataTable($value->toArray(), $this->module->display_name))
                        ->list($this->prepareListItemMainEmail($this->user->name), 'ul')
                        ->with(['responsible'=>$responsible->name])
                        ->buttons([
                            ['text'=>'Llevarme al sitio', 'url'=>$this->url]
                        ])
                        ->company($company_id)
                        ->send();
                }
            }
        }
    }

    /**
     * It is responsible for sending ready activities by mail
     * 
     * @return Void
     */
    private function sendMailReady()
    {
        if (!empty($this->company))
            $company_id = $this->company;
        else if (Session::get('company_id'))
            $company_id = Session::get('company_id');

        $this->activitiesReady = collect($this->activitiesReady);

        $groupSupervisor = $this->activitiesReady->groupBy('user_id');

        foreach($groupSupervisor as $data => $value)
        {
            $supervisor = User::active()->find($data);

            if($supervisor && $supervisor->email != null)
            {
                if ($supervisor->can('actionPlans_receive_notifications', $company_id))
                {
                    NotificationMail::
                        subject('Actividades Actualizadas')
                        ->view('actionplan.activities')
                        ->recipients($supervisor)
                        ->message('El usuario '.$this->user->name.' ha cambiado la información de las siguientes actividades:')
                        ->module('actionPlans')
                        ->table($this->prepareDataTable($value->toArray(), $this->module->display_name))
                        ->list($this->prepareListItemMainEmail($supervisor->name), 'ul')
                        ->with(['responsible'=>$supervisor->name])
                        ->buttons([
                            ['text'=>'Llevarme al sitio', 'url'=>$this->url]
                        ])
                        ->company($company_id)
                        ->send();
                }
            }
        }
    }

    private function prepareListItemMainEmail($supervisor)
    {
        $list = [];

        if (!empty($this->company))
            $company_id = $this->company;
        else if (Session::get('company_id'))
            $company_id = Session::get('company_id');

        $keywords = $this->getKeywordQueue($company_id);
        
        array_push($list, 'Usuario Supervisor: '.$supervisor);

        if ($this->regional)
            array_push($list, $keywords['regional'].': '.$this->regional);

        if ($this->headquarter)
            array_push($list, $keywords['headquarter'].': '.$this->headquarter);

        if ($this->process)
            array_push($list, $keywords['process'].': '.$this->process);

        if ($this->area)
            array_push($list, $keywords['area'].': '.$this->area);

        if ($this->creationDate)
            array_push($list, 'Fecha de creación: '.$this->creationDate);

        if ($this->details)
            array_push($list, $this->details);

        return $list;
    }

    private function prepareDataTable($data, $module = null, $format = 'D M d Y')
    {
        $result = [];

        foreach ($data as $key => $value) 
        {
            if ($this->dateSimpleFormat)
            {
                $expiration_date = $value['expiration_date'];
                $execution_date = $value['execution_date'];
            }
            else
            {
                $expiration_date = Carbon::createFromFormat($format, $value['expiration_date'])->toFormattedDateString();
                $execution_date = ($value['execution_date']) ? Carbon::createFromFormat($format, $value['execution_date'])->toFormattedDateString() : '';
            }

            array_push($result, [
                'Fecha Vencimiento' => $expiration_date,
                'Fecha Ejecución' => $expiration_date,
                'Estado' => $value['state'],
                'Descripción' => $value['description'],
                'Módulo' => $module ? $module : (isset($value['module_name']) ? $value['module_name'] : '')
            ]);
        }

        return $result;
    }

    public function sendMailAlert()
    {
        if (empty($this->company))
            throw new \Exception('A valid company has not been entered.');

        if (empty($this->daysAlertExpirationDate))
            throw new \Exception('A valid daysAlertExpirationDate has not been entered.');

        $activities = ActionPlansActivity::
                select(
                    'sau_action_plans_activities.*',
                    'sau_action_plans_activity_module.module_id as module_id',
                    'sau_modules.display_name as module_name'/*,
                    'sau_applications.name as application_name'*/
                )
                ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
                ->join('sau_modules', 'sau_modules.id', 'sau_action_plans_activity_module.module_id')
                //->join('sau_applications', 'sau_applications.id', 'sau_modules.application_id')
                ->join('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id')
                //->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id')
                ->where('sau_action_plans_activities.state', 'Pendiente')
                ->whereIn('sau_action_plans_activity_module.module_id', $this->getModules())
                ->whereRaw("CURDATE() = DATE_ADD(sau_action_plans_activities.expiration_date, INTERVAL -$this->daysAlertExpirationDate DAY)");

        $activities->company_scope = $this->company;
        $activities = $activities->get();

        $groupResponsible = $activities->groupBy('responsible_id');

        foreach($groupResponsible as $data => $value)
        {
            $responsible = User::active()->find($data);

            $groupSupervisor = $value->groupBy('user_id');

            foreach($groupSupervisor as $dataS => $valueS)
            {
                $supervisor = User::active()->find($dataS);

                //$url = url(strtolower('/'.$value[0]->application_name.'/'.$value[0]->module_name));

                if($responsible && $supervisor && $supervisor->email != null)
                {
                    //$module = Module::find($dataS);

                    if ($supervisor->can('actionPlans_receive_notifications', $this->company))
                    {
                        NotificationMail::
                            subject('Actividades Próximas a Vencerse')
                            ->view('actionplan.activities')
                            ->recipients($supervisor)
                            ->message('Las siguientes actividades están próximas a vencerse: ')
                            ->module('actionPlans')
                            ->event('Tarea programada: DaysAlertExpirationDateActionPlan')
                            ->table($this->prepareDataTable($valueS->toArray(), null, 'Y-m-d'))
                            //->list($this->prepareListItemMainEmail(), 'ul')
                            ->with(['responsible'=>$responsible->name])
                            /*->buttons([
                                ['text'=>'Llevarme al sitio', 'url'=>$url]
                            ])*/
                            ->company($this->company)
                            ->send();
                    }
                }
            }

            if($responsible && $responsible->email != null)
            {
                if ($responsible->can('actionPlans_receive_notifications', $this->company))
                {
                    NotificationMail::
                        subject('Actividades Próximas a Vencerse')
                        ->view('actionplan.activities')
                        ->recipients($responsible)
                        ->message('Las siguientes actividades están próximas a vencerse: ')
                        ->module('actionPlans')
                        ->event('Tarea programada: DaysAlertExpirationDateActionPlan')
                        ->table($this->prepareDataTable($value->toArray(), null, 'Y-m-d'))
                        //->list($this->prepareListItemMainEmail(), 'ul')
                        ->with(['responsible'=>$responsible->name])
                        /*->buttons([
                            ['text'=>'Llevarme al sitio', 'url'=>$url]
                        ])*/
                        ->company($this->company)
                        ->send();
                }
            }
        }
    }

    /**
     * Returns the IDs of the modules where the company 
     * that sends the mail has an active license
     *
     * @return Booleam
     */
    private function getModules()
    {
        $licenses = License::selectRaw('DISTINCT module_id AS module_id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->orderBy('module_id');

        $licenses->company_scope = $this->company;

        return $licenses->pluck('module_id')->toArray();
    }
}