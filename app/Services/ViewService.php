<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Facades\General\PermissionService;
use App\Facades\Administrative\KeywordManager;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use Session;
use DB;

class ViewService
{
    /**
     * Devuelve una coleccion con los roles definidos
     * indicando si el usuario lo tiene o no
     *
     * @return collect
     */
    public function getHasRole()
    {
        return PermissionService::getHasRole(Auth::user(), Session::get('company_id'));
    }

    /**
     * Devuelve una coleccion con los roles definidos
     * indicando si el usuario lo tiene o no
     *
     * @return collect
     */
    public function getCan()
    {
        return PermissionService::getCan(Auth::user(), Session::get('company_id'));
    }

    /**
     * Devuelve una coleccion con los permisos
     * indicando si el usuario lo tiene o no
     * 
     * @return collect
     */
    public function getKeywords()
    {
        return KeywordManager::getKeywords(Session::get('company_id'));
    }

    public function getTerms()
    {
        return Auth::user()->terms_conditions;
    }

    public function getInventaryEpp()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'inventory_management');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getFilterInspection()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'filter_inspections');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getProyectContract()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'contracts_use_proyect');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getIntegrationContract()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'company_there_integration_contract');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getDangerMatrixBlock()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'danger_matrix_block_old_year');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getLegalMatrixRiskOpport()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'legal_matrix_risk_opportunity');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function getInspectionsCriticality()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'criticality_level_inspections');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'Calificacion';
        else
            return $configuration->value;
    }

    public function getDeleteFilesAproverContracts()
    {
        $configuration = ConfigurationCompany::select('value')->where('key', 'contracts_delete_file_upload');
        $configuration->company_scope = Session::get('company_id');
        $configuration = $configuration->first();

        if (!$configuration)
            return 'NO';
        else
            return $configuration->value;
    }

    public function isContratante()
    {
        $user = Auth::user();

        if ($user->hasRole('Arrendatario', Session::get('company_id')) || $user->hasRole('Contratista', Session::get('company_id')))
        {
            $rolesOld = DB::table('sau_role_user_multilogin')->where('user_id', $user->id)->count();

            return $rolesOld > 0;
        }

        return true;
    }

    public function allAuthData()
    {
        return [
            'can' => $this->getCan(), 
            'hasRole' => $this->getHasRole(), 
            'inventaryEpp' => $this->getInventaryEpp(), 
            'proyectContract' => $this->getProyectContract(), 
            'integrationContract' => $this->getIntegrationContract(), 
            'dangerMatrixBlock' => $this->getDangerMatrixBlock(), 
            'legalMatrixRisk' => $this->getLegalMatrixRiskOpport(),
            'inspectionCriticality' => $this->getInspectionsCriticality(),
            'deleteFilesAproverContracts' => $this->getDeleteFilesAproverContracts(),
            'terms' => $this->getTerms(),
            'user_auth' => Auth::user(),
            'company_id' => Session::get('company_id'),
            'filter_inspections' => $this->getFilterInspection(),
            'isContratante' => $this->isContratante()
        ];
    }
}