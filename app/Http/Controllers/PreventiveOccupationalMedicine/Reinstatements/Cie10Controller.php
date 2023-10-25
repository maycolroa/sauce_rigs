<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Cie10Code;

class Cie10Controller extends Controller
{ 
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $code = Cie10Code::findOrFail($id);

            return $this->respondHttp200([
                'data' => $code,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Returns an array for a select type arl
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $cie10 = Cie10Code::selectRaw("
                    sau_reinc_cie10_codes.id as id,
                    CONCAT(sau_reinc_cie10_codes.code, ' - ', sau_reinc_cie10_codes.description) as description
                ")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('description', 'like', $keyword);
                    $query->orWhere('code', 'like', $keyword);
                })
                ->orderBy('description')
                ->take(50)->pluck('id', 'description');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($cie10)
            ]);
        }
        else
        {
            $cie10 = Cie10Code::selectRaw("
                sau_reinc_cie10_codes.id as id,
                CONCAT(sau_reinc_cie10_codes.code, ' - ', sau_reinc_cie10_codes.description) as description
            ")
            ->orderBy('description')
            ->pluck('id', 'description');
        
            return $this->multiSelectFormat($cie10);
        }
    }
}
