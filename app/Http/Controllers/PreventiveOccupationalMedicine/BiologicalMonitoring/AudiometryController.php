<?php

namespace App\Http\Controllers\PreventiveOccupationalMedicine\BiologicalMonitoring;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryRequest;
use App\PreventiveOccupationalMedicine\BiologicalMonitoring\Audiometry;
use Carbon\Carbon;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryExportJob;
use App\Jobs\PreventiveOccupationalMedicine\BiologicalMonitoring\AudiometryImportJob;

class AudiometryController extends Controller
{
    /**
     * Display index.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
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

       return Vuetable::of($audiometry)
                ->make();
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
            'message' => 'Se creo la audiometria'
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
      $audiometry = Audiometry::findOrFail($id);

      try{
        $audiometry->date = (Carbon::createFromFormat('Y-m-d',$audiometry->date))->format('D M d Y');
        $audiometry->multiselect_employee = $audiometry->employee->multiselect(); 
        return $this->respondHttp200([
            'data' => $audiometry,
        ]);
      }catch(Exception $e){
        $this->respondHttp500();
      }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AudiometryRequest $request, Audiometry $audiometry)
    {
 
      $audiometry->fill($request->all());
      $audiometry->date = (Carbon::createFromFormat('D M d Y',$audiometry->date))->format('Ymd');
      
      if(!$audiometry->update()){
        return $this->respondHttp500();
      }
      return $this->respondHttp200([
          'message' => 'Se actualizo la audiometria'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Audiometry $audiometry)
    {
        if(!$audiometry->delete()){
          return $this->respondHttp500();
        }
        return $this->respondHttp200([
            'message' => 'Se elimino la audiometria'
        ]);
    }

    /**
     * Export resources from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
      try{
        AudiometryExportJob::dispatch();
      
        return $this->respondHttp200();
      }catch(Exception $e){
        return $this->respondHttp500();
      }

      

      
    }

    /**
     * import.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
      try{
       AudiometryImportJob::dispatch($request->file);
      
       return $this->respondHttp200();
      }catch(Exception $e){
        return $this->respondHttp500();
      }
    }
}