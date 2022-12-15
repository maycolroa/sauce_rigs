<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Inform\PreventiveOccupationalMedicine\Reinstatements\InformManagerCheck;

class CheckInformController extends Controller
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
        $identifications = $this->getValuesForMultiselect($request->identifications);
        $names = $this->getValuesForMultiselect($request->names);
        $regionals = $this->getValuesForMultiselect($request->regionals);
        $businesses = $this->getValuesForMultiselect($request->businesses);
        $diseaseOrigin = $this->getValuesForMultiselect($request->diseaseOrigin);
        $years = $this->getValuesForMultiselect($request->years);
        $nextFollowDays = $request->has('nextFollowDays') ? $this->getValuesForMultiselect($request->nextFollowDays) : null;
        $sveAssociateds = $request->has('sveAssociateds') ? $this->getValuesForMultiselect($request->sveAssociateds) : null;
        $medicalCertificates = $request->has('medicalCertificates') ? $this->getValuesForMultiselect($request->medicalCertificates) : null;
        $relocatedTypes = $request->has('relocatedTypes') ? $this->getValuesForMultiselect($request->relocatedTypes) : null;
        $filtersType = $request->filtersType;

        $dates = [];
        $dates_request = explode('/', $request->dateRange);

        if (COUNT($dates_request) == 2)
        {
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d'));
            array_push($dates, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d'));
        }
        
        $informManager = new InformManagerCheck($identifications, $names, $regionals, $businesses, $diseaseOrigin, $nextFollowDays, $dates, $years, $sveAssociateds, $medicalCertificates, $relocatedTypes, $filtersType, $this->company);
        
        return $this->respondHttp200($informManager->getInformData());
    }
}
