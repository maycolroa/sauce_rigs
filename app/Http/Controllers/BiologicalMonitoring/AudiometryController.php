<?php

namespace App\Http\Controllers\BiologicalMonitoring;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\BiologicalMonitoring\AudiometryRequest;
use App\BiologicalMonitoring\Audiometry;
use Carbon\Carbon;

class AudiometryController extends Controller
{
    /**
     * Display index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('biologicalmonitoring/audiometry/index');
    }
    
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
   public function data(Request $request)
   {
       $audiometry = Audiometry::select(
           'bm_audiometries.*',
           'sau_employees.identification as employee_identification',
           'sau_employees.name as employee_name'
        )->join('sau_employees','sau_employees.id','bm_audiometries.employee_id')
        ->join('sau_employees_regionals','sau_employees_regionals.id','sau_employees.employee_regional_id');

       return Vuetable::of($audiometry)->make();
   }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('biologicalmonitoring/audiometry/create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\AudiometryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AudiometryRequest $request)
    {
        $audiometry = new Audiometry($request->all());
        $audiometry->date = (Carbon::createFromFormat('D M d Y',$audiometry->date))->format('Ymd');
        
        if(!$audiometry->save()){
            return $this->respondHttp500();
        }
        return $this->respondHttp200([
            'url' => route('audiometry.index')
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
