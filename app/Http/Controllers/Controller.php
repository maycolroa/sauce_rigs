<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Traits\UtilsTrait;
use App\Traits\ResponseTrait;
use App\Traits\PermissionTrait;
use App\Traits\LocationFormTrait;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\General\Team;
use App\Models\General\Company;
use App\Models\General\LogDelete;
use App\Models\General\LogUserActivitySystem;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, UtilsTrait, ResponseTrait, PermissionTrait, LocationFormTrait;

    protected $team;
    protected $company;
    protected $user;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $team = Team::where('name', Session::get('company_id'))->first();

        $this->team = $team ? $team->id : null;
        $this->company = Session::get('company_id');
        $this->user = Auth::user();
        $this->proyectContract = $this->getProyectContract();
    }

    public function saveLogDelete($module, $description)
    {
        $record = new LogDelete;
        $record->user_id = $this->user->id;
        $record->company_id = $this->company;
        $record->module = $module;
        $record->description = $description;
        $record->save();
    }

    public function saveLogActivitySystem($module, $description)
    {
        $record = new LogUserActivitySystem;
        $record->user_id = $this->user->id;
        $record->company_id = $this->company;
        $record->module = $module;
        $record->description = $description;
        $record->save();
    }

    public function inforCompanyComplete()
    {
        $infor_company = Company::find($this->company);

        if (isset($infor_company->nombre_actividad_economica_sede_principal) && $infor_company->nombre_actividad_economica_sede_principal)
            return true;
        else
            return false;

    }

    public function getProyectContract()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'contracts_use_proyect')->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getLegalMatrixRiskOpportunity()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'legal_matrix_risk_opportunity')->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }
}
