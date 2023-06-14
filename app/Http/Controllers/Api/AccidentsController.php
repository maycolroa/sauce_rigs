<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Models\General\Company;
use App\Facades\Configuration;
use App\Http\Requests\Api\CompanyRequiredRequest;
use App\Models\Administrative\Users\User;
use App\Models\General\Team;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Models\Administrative\Employees\Employee;
use App\Facades\General\PermissionService;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use App\Facades\Mail\Facades\NotificationMail;
use Auth;
use Carbon\Carbon;
use App\Traits\UtilsTrait;
use App\Models\General\Departament;
use App\Models\General\Municipality;
use App\Models\IndustrialSecure\WorkAccidents\Agent;
use App\Models\IndustrialSecure\WorkAccidents\Mechanism;
use App\Models\IndustrialSecure\WorkAccidents\PartBody;
use App\Models\IndustrialSecure\WorkAccidents\Site;
use App\Models\IndustrialSecure\WorkAccidents\TypeLesion;
use Hash;

class AccidentsController extends ApiController
{
    use UtilsTrait; 

    public function getEmployees(Request $request)
    {
      $location_level = $this->getLocationFormConfModule($request->company_id);

      $employees = Employee::select("*");
      $employees->company_scope = $request->company_id;
      $employees = $employees->get();

      $employees = $employees->map(function ($item, $keyCompany) use ($request) {
            $item->multiselect = $item->multiselect();

            return $item;
      });

      return $this->respondHttp200([
        'data' => $employees->values()
      ]);  
    }

    public function getPositions(Request $request)
    {
      $positions = EmployeePosition::select("*");
      $positions->company_scope = $request->company_id;
      $positions = $positions->get();

      $positions = $positions->map(function ($item, $keyCompany) use ($request) {
            $item->multiselect = $item->multiselect();

            return $item;
      });

      return $this->respondHttp200([
        'data' => $positions->values()
      ]);  
    }

    public function getDepartaments(Request $request)
    {
        $result = collect([]);

        $departaments = Departament::select('id', 'name');
        $departaments = $departaments->orderBy('name')->get();

        $departaments->transform(function($departament, $keyR) 
        {
            $municipalities = Municipality::select('id', 'name')
                ->where('departament_id', $departament->id)
                ->orderBy('name')
                ->get();

            $departament->municipalities = $municipalities;

            return $departament;
        });

        $result->put('departaments', $departaments);

        return $this->respondHttp200($result->toArray());
    }

    public function dataAccidents()
    {
        $result = collect([]);
        $result->put('agents', $this->agents());
        $result->put('sites', $this->sites());
        $result->put('mechanisms', $this->mechanisms());
        $result->put('lesiontypes', $this->lesiontypes());
        $result->put('partsbody', $this->partsbody());

        return $this->respondHttp200($result->toArray());
    }

    public function agents()
    {
        $result = collect([]);

        $agents = Agent::selectRaw("
            sau_aw_agents.id as id,
            sau_aw_agents.name as name
        ")
        ->orderBy('name')
        ->get();

        $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }

    public function sites()
    {
        $result = collect([]);

        $agents = Site::selectRaw("
            sau_aw_sites.id as id,
            sau_aw_sites.name as name
        ")
        ->orderBy('name')
        ->get();

       $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }

    public function mechanisms()
    {
        $result = collect([]);

        $agents = Mechanism::selectRaw("
            sau_aw_mechanisms.id as id,
            sau_aw_mechanisms.name as name
        ")
        ->orderBy('name')
        ->get();

       $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }

    public function lesiontypes()
    {
        $result = collect([]);

        $agents = TypeLesion::selectRaw("
            sau_aw_types_lesion.id as id,
            sau_aw_types_lesion.name as name
        ")
        ->orderBy('name')
        ->get();

       $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }

    public function partsbody()
    {
        $result = collect([]);

        $agents = PartBody::selectRaw("
            sau_aw_parts_body.id as id,
            sau_aw_parts_body.name as name
        ")
        ->orderBy('name')
        ->get();

       $agents->transform(function($agent, $keyR) 
        {
            return $agent;
        });

        //$result->put('agents', $agents);
        return $agents;

        //return $this->respondHttp200($result->toArray());
    }
}
