<?php

namespace App\Http\Controllers\IndustrialSecure\AccidentsWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryItems;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryItemsCompany;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategory;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryCompany;
use DB;

class CausesItemsController extends Controller
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
        $items = SectionCategoryItemsCompany::select(
            'sau_aw_causes_section_category_items_company.*',
            'sau_aw_causes_section_category_company.category_name',
            'sau_aw_causes_sections.section_name',
            DB::raw("IF(sau_aw_causes_sections.id <= 2, 'Causas Inmediatas', 'Causas Básicas/Raíz') AS causes_name")
        )
        ->join('sau_aw_causes_section_category_company', 'sau_aw_causes_section_category_company.id', 'sau_aw_causes_section_category_items_company.category_id')
        ->join('sau_aw_causes_sections', 'sau_aw_causes_sections.id', 'sau_aw_causes_section_category_company.section_id')
        ->where('sau_aw_causes_section_category_company.company_id', $this->company)
        ->where('sau_aw_causes_section_category_items_company.company_id', $this->company)
        ->orderBy('sau_aw_causes_section_category_items_company.created_at', 'DESC');

        return Vuetable::of($items)
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
            $item = new SectionCategoryItemsCompany($request->all());
            $item->company_id = $this->company;
            $item->category_default = false;
            
            if(!$item->save()){
                \Log::info('entro error');
                return $this->respondHttp500();
            }

            $this->saveLogActivitySystem('Accidentes e incidentes - Causas Items', 'Se creo el item '.$item->item_name.' ');

            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se creo el item'
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
            $item = SectionCategoryItemsCompany::findOrFail($id);
            $item->multiselect_category = $item->category->multiselect();

            \Log::info($item);

            return $this->respondHttp200([
                'data' => $item,
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
    public function update(Request $request, SectionCategoryItemsCompany $item)
    {
        DB::beginTransaction();

        try 
        {
            $item->fill($request->all());
        
            if(!$item->update()){
                return $this->respondHttp500();
            }

            $this->saveLogActivitySystem('Accidentes e incidentes - Causas Items', 'Se edito el item '.$item->item_name.' ');

            DB::commit();

            return $this->respondHttp200([
                'message' => 'Se edito el item'
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
    public function destroy(SectionCategoryItemsCompany $item)
    {
        $this->saveLogActivitySystem('Accidentes e incidentes - Causas Items', 'Se elimino el item '.$item->item_name.' ');

        if(!$item->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el item'
        ]);
    }

    public function multiselectSectionCategory(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $categories = SectionCategoryCompany::select("id", "category_name as name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('category_name', 'like', $keyword);
                })
                ->where('company_id', $this->company)
                ->orderBy('category_name')
                ->take(30)
                ->get();
                
            $categories = $categories->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($categories)
            ]);
        }
        else
        {
            $categories = SectionCategoryCompany::selectRaw("            
                sau_aw_causes_section_category_company.id as id,
                sau_aw_causes_section_category_company.category_name as name
            ")
            ->where('company_id', $this->company)
            ->orderBy('category_name')
            ->get()
            ->pluck('id', 'name');
        
            return $this->multiSelectFormat($categories);
        }        
    }
}
