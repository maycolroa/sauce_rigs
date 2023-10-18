<?php

namespace App\Http\Controllers\IndustrialSecure\AccidentsWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategory;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryCompany;
use DB;

class CausesCategoriesController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:dangers_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:dangers_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:dangers_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:dangers_d, {$this->team}", ['only' => 'destroy']);*/
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
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $categories = SectionCategoryCompany::select(
            'sau_aw_causes_section_category_company.*',
            'sau_aw_causes_sections.section_name',
            DB::raw("IF(sau_aw_causes_sections.id <= 2, 'Causas Inmediatas', 'Causas Básicas/Raíz') AS causes_name")
        )
        ->join('sau_aw_causes_sections', 'sau_aw_causes_sections.id', 'sau_aw_causes_section_category_company.section_id')
        ->where('company_id', $this->company);

        return Vuetable::of($categories)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Dangers\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try 
        {
            $category = new SectionCategoryCompany($request->all());
            $category->company_id = $this->company;
            
            if(!$category->save()){
                \Log::info('entro error');
                return $this->respondHttp500();
            }

            $this->saveLogActivitySystem('Accidentes e incidentes - Causas Categorias', 'Se creo la categoria '.$category->name.' ');

            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se creo la categoria'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
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
            $category = SectionCategoryCompany::findOrFail($id);
            $category->multiselect_section = $category->section->multiselect();


            return $this->respondHttp200([
                'data' => $category,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Dangers\Request  $request
     * @param  Danger  $danger
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SectionCategoryCompany $category)
    {
        DB::beginTransaction();

        try 
        {
            $category->fill($request->all());
        
            if(!$category->update()){
            return $this->respondHttp500();
            }

            $this->saveLogActivitySystem('Accidentes e incidentes - Causas Categorias', 'Se edito la categoria '.$category->name.' ');

            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se edito la categoria'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            return $this->respondHttp500();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Danger  $danger
     * @return \Illuminate\Http\Response
     */
    public function destroy(SectionCategoryCompany $category)
    {
        $this->saveLogActivitySystem('Accidentes e incidentes - Causas Categorias', 'Se elimino la categoria '.$category->name.' ');

        if(!$category->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la categoria'
        ]);
    }
}
