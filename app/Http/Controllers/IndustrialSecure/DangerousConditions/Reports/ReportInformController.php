<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\IndustrialSecure\DangerousConditions\Reports\InformManagerReport;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use App\Traits\Filtertrait;

class ReportInformController extends Controller
{
    use Filtertrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:ph_reports_informs_view, {$this->team}", ['only' => 'data']);
        //$this->middleware('permission:biologicalMonitoring_audiometry_inform_individual_r', ['only' => 'dataIndividual']);
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
        $url = "/industrialsecure/dangerousconditions/inspection/report";
        $init = true;
        $filters = [];

        if ($request->has('filtersType'))
            $init = false;
        else 
            $filters = $this->filterDefaultValues($this->user->id, $url);

        $headquarters = !$init ? $this->getValuesForMultiselect($request->headquarters) : (isset($filters['headquarters']) ? $this->getValuesForMultiselect($filters['headquarters']) : []);
        
        $areas = !$init ? $this->getValuesForMultiselect($request->areas) : (isset($filters['areas']) ? $this->getValuesForMultiselect($filters['areas']) : []);
      
        $regionals = !$init ? $this->getValuesForMultiselect($request->regionals) : (isset($filters['regionals']) ? $this->getValuesForMultiselect($filters['regionals']) : []);

        $processes = !$init ? $this->getValuesForMultiselect($request->processes) : (isset($filters['processes']) ? $this->getValuesForMultiselect($filters['processes']) : []);

        $users = !$init ? $this->getValuesForMultiselect($request->users) : (isset($filters['users']) ? $this->getValuesForMultiselect($filters['users']) : []);

        $conditions = !$init ? $this->getValuesForMultiselect($request->conditions) : (isset($filters['conditions']) ? $this->getValuesForMultiselect($filters['conditions']) : []);

        $rates = !$init ? $this->getValuesForMultiselect($request->rates) : (isset($filters['rates']) ? $this->getValuesForMultiselect($filters['rates']) : []);

        $years = $this->getValuesForMultiselect($request->years);
        $months = $this->getValuesForMultiselect($request->months);

        $filtersType = !$init ? $request->filtersType : (isset($filters['filtersType']) ? $filters['filtersType'] : null);

        $dates = [];

        $datesF = !$init ? $request->dateRange : (isset($filters['dateRange']) ? $filters['dateRange'] : null);

         if (isset($datesF) && $datesF)
        {
              $dates_request = explode('/', $datesF);

              if (COUNT($dates_request) == 2)
              {
                  array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                  array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
              }
         }

        $informManager = new InformManagerReport($this->company, $regionals, $headquarters, $processes, $areas, $conditions, $rates, $users, $years, $months, $dates, $filtersType);
        
        return $this->respondHttp200($informManager->getInformData());
    }

    /*public function locationWithCondition(Request $request)
    {
        $ids = explode(",", $request->id);
        
        $informManager = new InformManagerReport();
        
        return $this->respondHttp200($informManager->locationWithCondition($ids));
    }*/

    public function multiselectBar()
    {
        $keywords = $this->user->getKeywords();

        $confLocation = $this->getLocationFormConfModule();

        $select = [
            'Severidad' => "rate", 
            'Condición' => "condition",
            'Usuario' => "user"
        ];

        if ($confLocation['regional'] == 'SI')
            $select[$keywords['regionals']] = 'regional';
        if ($confLocation['headquarter'] == 'SI')
            $select[$keywords['headquarters']] = 'headquarter';
        if ($confLocation['process'] == 'SI')
            $select[$keywords['processes']] = 'process';
        if ($confLocation['area'] == 'SI')
            $select[$keywords['areas']] = 'area';
  
      return $this->multiSelectFormat(collect($select));
    }
    
    public function multiselectYears()
    {
        $years = Report::selectRaw(
            'DISTINCT YEAR(sau_ph_reports.created_at) AS year'
        )
        ->withoutGlobalScopes()
        ->where('company_id', $this->company)
        ->orderBy('year')
        ->pluck('year', 'year');

      return $this->multiSelectFormat($years);
    }

    public function multiselectMounts()
    {
        $months = Report::selectRaw(
            'DISTINCT month(sau_ph_reports.created_at) AS month'
        )
        ->withoutGlobalScopes()
        ->where('company_id', $this->company)
        ->orderBy('month')
        ->get();

        $months = $months->map(function($item, $key){
            return [
                "label" => trans("months.$item->month"),
                "month" => $item->month
            ];
        });

        return $this->multiSelectFormat($months->pluck('month', 'label'));
    }
}
