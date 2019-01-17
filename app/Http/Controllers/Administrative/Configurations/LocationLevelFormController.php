<?php

namespace App\Http\Controllers\Administrative\Configurations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Administrative\Configurations\LocationLevelForm;
use App\Http\Requests\Administrative\Configurations\LocationLevelFormRequest;
use App\Models\Module;
use App\Models\Application;
use Session;

class LocationLevelFormController extends Controller
{
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
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $locationLevelForms = LocationLevelForm::selectRaw(
            'sau_conf_location_level_forms.*,
            CONCAT(sau_applications.display_name, "/", sau_modules.display_name) as module'
        )
        ->join('sau_modules', 'sau_modules.id', 'sau_conf_location_level_forms.module_id')
        ->join('sau_applications', 'sau_applications.id', 'sau_modules.application_id');

        return Vuetable::of($locationLevelForms)
                ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Configurations\LocationLevelFormRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(LocationLevelFormRequest $request)
    {
      $locationLevelForm = new LocationLevelForm($request->all());
      $locationLevelForm->company_id = Session::get('company_id');

      //regional - headquarter - area - process
      $values = ["SI", "SI", "SI", "SI"];

      for ($i=$request->get('location_level_form'); $i < COUNT($values); $i++)
      { 
        $values[$i] = "NO";
      }
      
      $locationLevelForm->regional    = $values[0];
      $locationLevelForm->headquarter = $values[1];
      $locationLevelForm->area        = $values[2];
      $locationLevelForm->process     = $values[3];

      if(!$locationLevelForm->save()){
          return $this->respondHttp500();
      }

      return $this->respondHttp200([
          'message' => 'Se creo la configuración'
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
      try
      {
          $locationLevelForm = LocationLevelForm::findOrFail($id);
          $countLevel = 0;

          if ($locationLevelForm->regional == 'SI')
            $countLevel++;
          if ($locationLevelForm->headquarter == 'SI')
            $countLevel++;
          if ($locationLevelForm->area == 'SI')
            $countLevel++;
          if ($locationLevelForm->process == 'SI')
            $countLevel++;

          $locationLevelForm->location_level_form = $countLevel;

          return $this->respondHttp200([
              'data' => $locationLevelForm,
          ]);
      } catch(Exception $e){
          $this->respondHttp500();
      }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Configurations\LocationLevelFormRequest $request
     * @param  LocationLevelForm $locationLevelForm
     * @return \Illuminate\Http\Response
     */
    public function update(LocationLevelFormRequest $request, LocationLevelForm $locationLevelForm)
    {
      $locationLevelForm->fill($request->all());

      //regional - headquarter - area - process
      $values = ["SI", "SI", "SI", "SI"];

      for ($i=$request->get('location_level_form'); $i < COUNT($values); $i++)
      { 
        $values[$i] = "NO";
      }
      
      $locationLevelForm->regional    = $values[0];
      $locationLevelForm->headquarter = $values[1];
      $locationLevelForm->area        = $values[2];
      $locationLevelForm->process     = $values[3];
        
      if(!$locationLevelForm->update()){
        return $this->respondHttp500();
      }
      
      return $this->respondHttp200([
          'message' => 'Se actualizo la configuración'
      ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  LocationLevelForm $locationLevelForm
     * @return \Illuminate\Http\Response
     */
    public function destroy(LocationLevelForm $locationLevelForm)
    {
      if(!$locationLevelForm->delete())
      {
        return $this->respondHttp500();
      }
      
      return $this->respondHttp200([
        'message' => 'Se elimino la configuración'
      ]);
    }

    public function multiselectModules()
    {
      $modules = Module::selectRaw(
        'sau_modules.id as id,
        CONCAT(sau_applications.display_name, "/", sau_modules.display_name) as name'
      )
      ->join('sau_applications', 'sau_applications.id', 'sau_modules.application_id')
      ->whereIn('sau_modules.name', ['dangerMatrix'])
      ->pluck('id', 'name');

      return $this->multiSelectFormat($modules);
    }

    /**
     * Returns an arrangement with the Location Levels
     *
     * @return Array
     */
    public function radioLocationLevels()
    {
      $options = ["Regional"=>"1", "Sede"=>"2", "Área"=>"3", "Proceso"=>"4"];
      return $this->radioFormat(collect($options));
    }

    /**
     * returns the configuration for a specific module
     *
     * @return Array
     */
    public function getConfModule(Request $request)
    {
      $data = [];

      if ($request->has('application') && $request->has('module'))
      {
        $data = $this->getLocationFormConfModule($request->get('application'), $request->get('module'));
      }

      return $data;
    }
}
