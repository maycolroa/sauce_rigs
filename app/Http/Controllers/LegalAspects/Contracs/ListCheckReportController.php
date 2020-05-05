<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\LegalAspects\Contract\ListCheck\InformManagerListCheck;

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
}
