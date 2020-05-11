<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\LegalAspects\Contract\ListCheck\InformManagerListCheck;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\ContractDocument;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Vuetable\Facades\Vuetable;

class ListCheckReportController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:reinc_checks_informs, {$this->team}", ['only' => 'data']);
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
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $contracts = $this->getValuesForMultiselect($request->contracts);
        $classification = $this->getValuesForMultiselect($request->classification);
        $itemStandar = $this->getValuesForMultiselect($request->itemStandar);
        $filtersType = $request->filtersType;
        
        $informManager = new InformManagerListCheck($this->company, $contracts, $classification, $itemStandar, $filtersType);
        
        return $this->respondHttp200($informManager->getInformData());
    }

    public function employeeDocument()
    {
        $documentsEmployee = ContractLesseeInformation::selectRaw("
                sau_ct_information_contract_lessee.id AS id,
                sau_ct_information_contract_lessee.social_reason AS contract,
                sau_ct_contract_employees.name AS employee,
                sau_ct_activities.name As activity,
                sau_ct_activities_documents.name AS document,
                case when sau_ct_file_document_employee.employee_id is not null then 'SI' else 'NO' end as cargado
            ")
            ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.contract_id', 'sau_ct_information_contract_lessee.id')
            ->join('sau_ct_contract_employee_activities', 'sau_ct_contract_employee_activities.employee_id', 'sau_ct_contract_employees.id')
            ->join('sau_ct_activities', 'sau_ct_activities.id', 'sau_ct_contract_employee_activities.activity_contract_id')
            ->join('sau_ct_activities_documents', 'sau_ct_activities_documents.activity_id', 'sau_ct_activities.id')
            ->leftJoin('sau_ct_file_document_employee', function ($join) 
            {
                $join->on("sau_ct_file_document_employee.employee_id", "sau_ct_contract_employee_activities.employee_id");
                $join->on("sau_ct_file_document_employee.document_id", "sau_ct_activities_documents.id");
            })
            ->orderBy('contract');

        return Vuetable::of($documentsEmployee)
                    ->make();
    }

    public function globalDocument()
    {
        $exist;

        $documentsGlobal = ContractDocument::selectRaw("
            sau_ct_contracts_documents.id as id,
            sau_ct_contracts_documents.name as documento,
            count(sau_ct_information_contract_lessee.id) as contratista,
            sum(case when sau_ct_file_document_contract.contract_id is not null then 1 else 0 end ) as cargado
            ")
            ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.company_id', 'sau_ct_contracts_documents.company_id')
            ->leftJoin('sau_ct_file_document_contract', function ($join) 
            {
                $join->on("sau_ct_file_document_contract.contract_id", "sau_ct_information_contract_lessee.id");
                $join->on("sau_ct_file_document_contract.document_id", "sau_ct_contracts_documents.id");
            })
            ->groupBy('id', 'documento')
            ->orderBy('documento');

            if($documentsGlobal)
                $exist = true;            
            else
                $exist = false;

        return Vuetable::of($documentsGlobal)
                    ->make();
    }
}
