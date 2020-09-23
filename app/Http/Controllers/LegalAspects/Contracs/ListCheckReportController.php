<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\LegalAspects\Contract\ListCheck\InformManagerListCheck;
use App\Models\LegalAspects\Contracts\ActivityContract;
use App\Models\LegalAspects\Contracts\ContractDocument;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\TrainingEmployeeSend;
use App\Models\LegalAspects\Contracts\TrainingEmployeeQuestionsAnswers;
use App\Traits\Filtertrait;
use App\Vuetable\Facades\Vuetable;
use DB;

class ListCheckReportController extends Controller
{
    use Filtertrait;
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

    public function employeeDocument(Request $request)
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
            });
            
        $url = "/legalaspects/report/contracts";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $documentsEmployee->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $documentsEmployee->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }

        return Vuetable::of($documentsEmployee)
                    ->make();
    }

    public function globalDocument(Request $request)
    {
        $documentsGlobal = ContractLesseeInformation::selectRaw("
            sau_ct_contracts_documents.id as id,
            sau_ct_contracts_documents.name as documento,
            sau_ct_information_contract_lessee.social_reason AS contratista,
            case when sau_ct_file_document_contract.contract_id is not null then 'SI' else 'NO' end as cargado
            ")
            ->join('sau_ct_contracts_documents', 'sau_ct_information_contract_lessee.company_id', 'sau_ct_contracts_documents.company_id')
            ->leftJoin('sau_ct_file_document_contract', function ($join) 
            {
                $join->on("sau_ct_file_document_contract.contract_id", "sau_ct_information_contract_lessee.id");
                $join->on("sau_ct_file_document_contract.document_id", "sau_ct_contracts_documents.id");
            });            

            if($documentsGlobal)
                $exist = true;            
            else
                $exist = false;

        $url = "/legalaspects/report/contracts";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $documentsGlobal->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $documentsGlobal->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }

        return Vuetable::of($documentsGlobal)
                    ->make();
    }

    public function trainingEmployeeDetails (Request $request)
    {
        /*"SELECT employee_id, training_id, cap, trab, social_reason, count_question, COUNT(id) AS attemp, max(max_q), concat(max(max_q), '/', count_question) as puntaje FROM 
        (
        SELECT s.employee_id, s.training_id, t.name AS cap, e.name AS trab, c.social_reason, t.number_questions_show AS count_question,
        a.id,
        (SELECT SUM(IF(correct, 1, 0)) AS total FROM sau_ct_training_employee_questions_answers WHERE attempt_id = a.id) AS max_q
        FROM sauce_2.sau_ct_training_employee_send s
        inner join sau_ct_trainings t ON t.id = s.training_id
        inner join sau_ct_contract_employees e on e.id = s.employee_id
        inner join sau_ct_information_contract_lessee c on c.id = e.contract_id
        left join sau_ct_training_employee_attempts a ON a.training_id = s.training_id AND a.employee_id = s.employee_id
        group by s.training_id, s.employee_id, a.id) AS t
        group by employee_id, training_id"*/

        $trainigsDetails = ContractLesseeInformation::select(
            'sau_ct_training_employee_send.employee_id',
            'sau_ct_training_employee_send.training_id',
            'sau_ct_trainings.name AS training',
            'sau_ct_contract_employees.name AS employee',
            'sau_ct_information_contract_lessee.social_reason AS contract',
            'sau_ct_trainings.number_questions_show AS count_question',
            'sau_ct_training_employee_attempts.id',
            DB::raw('(SELECT SUM(IF(correct, 1, 0)) AS total 
            FROM sau_ct_training_employee_questions_answers 
            WHERE attempt_id = sau_ct_training_employee_attempts.id) AS max_q')
        )
        ->join('sau_ct_contract_employees', 'sau_ct_contract_employees.contract_id', 'sau_ct_information_contract_lessee.id')
        ->join('sau_ct_training_employee_send', 'sau_ct_training_employee_send.employee_id', 'sau_ct_contract_employees.id')
        ->join('sau_ct_trainings', 'sau_ct_trainings.id', 'sau_ct_training_employee_send.training_id')        
        ->leftJoin('sau_ct_training_employee_attempts', function ($join) 
        {
            $join->on("sau_ct_training_employee_attempts.training_id", "sau_ct_training_employee_send.training_id");
            $join->on("sau_ct_training_employee_attempts.employee_id", "sau_ct_training_employee_send.employee_id");
        })
        ->withoutGlobalScopes()        
        ->where('sau_ct_information_contract_lessee.company_id', $this->company)
        ->groupBy('sau_ct_training_employee_send.training_id', 'sau_ct_training_employee_send.employee_id', 'sau_ct_training_employee_attempts.id');

        $url = "/legalaspects/report/contracts";

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $trainigsDetails->inContracts($this->getValuesForMultiselect($filters["contracts"]),$filters['filtersType']['contracts']);
            $trainigsDetails->inClassification($this->getValuesForMultiselect($filters["classification"]),$filters['filtersType']['classification']);
        }

        $report = DB::table(DB::raw("({$trainigsDetails->toSql()}) AS t"))
        ->selectRaw("employee_id, training_id, training, employee, contract, COUNT(id) AS attemp, concat(max(max_q), '/', count_question) as puntaje")
        ->mergeBindings($trainigsDetails->getQuery())
        ->groupBy('employee_id', 'training_id');

        return Vuetable::of($report)
                    ->make();
    }

    public function trainigEmployeeConsolidated (Request $request)
    {
        
    }
}                        