<?php

namespace App\Http\Controllers\IndustrialSecure\DangerousConditions\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\IndustrialSecure\DangerousConditions\Reports\InformManagerReport;

class ReportInformController extends Controller
{
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
        $informManager = new InformManagerReport($this->company);
        
        return $this->respondHttp200($informManager->getInformData());
    }

    public function locationWithCondition(Request $request)
    {
        $ids = explode(",", $request->id);
        
        $informManager = new InformManagerReport();
        
        return $this->respondHttp200($informManager->locationWithCondition($ids));
    }

    public function multiselectBar()
    {
        $keywords = $this->user->getKeywords();

        $confLocation = $this->getLocationFormConfModule();

        $select = [
            'Severidad' => "rate", 
            'Condición' => "condition"
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
}
