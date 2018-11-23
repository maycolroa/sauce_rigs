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
use Illuminate\Support\Facades\Auth;
use Session;

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
           'sau_bm_audiometries.*',
           'sau_employees.identification as identification',
           'sau_employees.name as name'
        )->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
        ->join('sau_employees_regionals','sau_employees_regionals.id','sau_employees.employee_regional_id');

       return Vuetable::of($audiometry)
                ->addColumn('base_si_no', function ($audiometry) {
                  return $audiometry->base_type == 'Base' ? 'Si' : 'No';
                })
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
        AudiometryExportJob::dispatch(Auth::user());
      
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
       AudiometryImportJob::dispatch($request->file, Session::get('company_id'), Auth::user());
      
       return $this->respondHttp200();
      }catch(Exception $e){
        return $this->respondHttp500();
      }
    }

    public function reportPta(Request $request)
    {
      try
      {
        $data = [
          'air_left_pta' => [],
          'air_left_legend' => [],
          'air_right_pta' => [],
          'air_right_legend' => [],
        ];

        $key_types = ['air'];
        $key_orientation = ['left', 'right'];

        foreach ($key_types as $type)
        {
          foreach ($key_orientation as $orientation)
          {
            $col = 'sau_bm_audiometries.severity_grade_'.$type.'_'.$orientation.'_pta';

            $audiometry = Audiometry::selectRaw(
              'COUNT(IF('.$col.'="Audición normal",1, NULL)) as AN,
               COUNT(IF('.$col.'="Hipoacusia leve",1, NULL)) as HL,
               COUNT(IF('.$col.'="Hipoacusia moderada",1, NULL)) as HM,
               COUNT(IF('.$col.'="Hipoacusia moderada a severa",1, NULL)) as HMS,
               COUNT(IF('.$col.'="Hipoacusia severa",1, NULL)) as HS,
               COUNT(IF('.$col.'="Hipoacusia profunda",1, NULL)) as HP'
            )->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id');
            
            if ($request->get('year') != '')
            {
              $audiometry->whereRaw('YEAR(sau_bm_audiometries.date) IN ('.implode(",", $this->getDataFromMultiselect($request->get('year'))).')');
            }

            if ($request->get('area') != '')
            {
              $audiometry->whereIn('sau_employees.employee_area_id', $this->getDataFromMultiselect($request->get('area')));
            }

            if ($request->get('regional') != '')
            {
              $audiometry->whereIn('sau_employees.employee_regional_id', $this->getDataFromMultiselect($request->get('regional')));
            }
            
            $aux = [];
            $aux_legend = [];

            foreach ($audiometry->get() as $key => $value)
            {
              if ($value->AN > 0)
              {
                $aux['Audición normal'] = $value->AN;
                array_push($aux_legend, 'Audición normal');
              }
              if ($value->HL > 0)
              {
                $aux['Hipoacusia leve'] = $value->HL;
                array_push($aux_legend, 'Hipoacusia leve');
              }
              if ($value->HM > 0)
              {
                $aux['Hipoacusia moderada'] = $value->HM;
                array_push($aux_legend, 'Hipoacusia moderada');
              }
              if ($value->HMS > 0)
              {
                $aux['Hipoacusia moderada a severa'] = $value->HMS;
                array_push($aux_legend, 'Hipoacusia moderada a severa');
              }
              if ($value->HS > 0)
              {
                $aux['Hipoacusia severa'] = $value->HS;
                array_push($aux_legend, 'Hipoacusia severa');
              }
              if ($value->HP > 0)
              {
                $aux['Hipoacusia profunda'] = $value->HP;
                array_push($aux_legend, 'Hipoacusia profunda');
              }
            }

            if (COUNT($aux) > 0)
            {
              $key = $type.'_'.$orientation;
              $data[$key.'_pta'] = $this->multiSelectFormat(collect($aux));
              $data[$key.'_legend'] = $aux_legend;
            }

          }
        }

        return $this->respondHttp200([
            'data' => $data,
        ]);
      }catch(Exception $e){
        $this->respondHttp500();
      }
    }

    /**
     * Returns an arrangement with the last 5 years
     *
     * @return Array
     */
    public function multiselectYears()
    {
      $audiometries = Audiometry::selectRaw(
        'DISTINCT YEAR(sau_bm_audiometries.date) as year'
      )->join('sau_employees','sau_employees.id','sau_bm_audiometries.employee_id')
      ->orderBy('year')
      ->get()
      ->pluck('year', 'year');

      return $this->multiSelectFormat($audiometries);
    }
}