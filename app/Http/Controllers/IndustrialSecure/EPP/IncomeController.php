<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\EppIncome;
use App\Models\IndustrialSecure\Epp\EppIncomeDetail;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\TagReason;
use App\Http\Requests\IndustrialSecure\Epp\ElementIncomeRequest;
use Validator;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
use Hash;

class IncomeController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:elements_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:elements_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:elements_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:elements_d, {$this->team}", ['only' => 'destroy']);*/
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
        $income = EppIncome::selectRaw(
            "sau_epp_incomen.*,
            GROUP_CONCAT(DISTINCT sau_epp_elements.name ORDER BY sau_epp_elements.name ASC) AS elements,            
            sau_epp_locations.name as name_location"
        )
        ->join('sau_epp_incomen_details', 'sau_epp_incomen_details.income_id', 'sau_epp_incomen.id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_incomen_details.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_incomen_details.location_id')
        ->groupBy('sau_epp_incomen.id', 'sau_epp_locations.name');

        return Vuetable::of($income)
        ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Epp\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ElementIncomeRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $income = new EppIncome;
            $income->company_id = $this->company;
            $income->user_id = $this->user->id;
            $income->location_id = $request->location_id;
            
            if(!$income->save())
                return $this->respondHttp500();

            if (COUNT($request->elements_id) > 0)
            {
                foreach ($request->elements_id as $key => $value) 
                {
                    $element = Element::find($value['id_ele']);

                    if ($element)
                    {
                        $balance = ElementBalanceLocation::where('element_id', $element->id)
                        ->where('location_id', $request->location_id)->first();

                        $reason = $this->tagsPrepare($value['reason']);
                        $this->tagsSave($reason, TagReason::class);

                        $elements_sync = [];

                        $detail = new EppIncomeDetail;
                        $detail->income_id = $income->id;
                        $detail->company_id = $this->company;
                        $detail->element_id = $element->id;
                        $detail->location_id = $request->location_id;
                        $detail->reason = $reason->implode(',');

                        if ($element->identify_each_element)
                        {
                            $detail->quantity = COUNT($value['codes']);

                            if(!$detail->save())
                                return $this->respondHttp500();

                            if ($balance)
                            {
                                $balance->quantity = $balance->quantity + $detail->quantity;
                                $balance->quantity_available = $balance->quantity_available + $detail->quantity;
                                $balance->save();
                            }
                            else
                            {
                                $balance = new ElementBalanceLocation();
                                $balance->element_id = $element->id;
                                $balance->location_id = $detail->location_id;
                                $balance->quantity = $detail->quantity;
                                $balance->quantity_available = $detail->quantity;
                                $balance->quantity_allocated = 0;
                                $balance->save();
                            }

                            foreach ($value['codes'] as $key => $code) 
                            {
                                $product = new ElementBalanceSpecific;
                                $product->hash = $code['value'];
                                $product->code = $code['value'];
                                $product->element_balance_id = $balance->id;
                                $product->location_id = $balance->location_id;
                                $product->expiration_date = $value['expiration_date'];
                                $product->save();

                                array_push($elements_sync, $product->id);
                            }
                        }
                        else
                        {
                            $detail->quantity = $value['quantity'];

                            if(!$detail->save())
                                return $this->respondHttp500();

                            if ($balance)
                            {
                                $balance->quantity = $balance->quantity + $detail->quantity;
                                $balance->quantity_available = $balance->quantity_available + $detail->quantity;
                                $balance->save();
                            }
                            else
                            {
                                $balance = new ElementBalanceLocation();
                                $balance->element_id = $element->id;
                                $balance->location_id = $detail->location_id;
                                $balance->quantity = $detail->quantity;
                                $balance->quantity_available = $detail->quantity;
                                $balance->quantity_allocated = 0;
                                $balance->save();
                            }

                            for ($i=1; $i <= $value['quantity']; $i++) { 
                                $hash = Hash::make($element->id . str_random(30));
                                $product = new ElementBalanceSpecific;
                                $product->hash = $hash;
                                $product->code = $hash;
                                $product->element_balance_id = $balance->id;
                                $product->location_id = $balance->location_id;
                                $product->save();

                                array_push($elements_sync, $product->id);
                            }
                        }
                        
                        $detail->specifics()->sync($elements_sync);
                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            //DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el ingreso'
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
            $income = EppIncome::findOrFail($id);
            $income->multiselect_location = $income->location->multiselect();

            $elements_id = [];
            $id_saltar = [];
            $codes = [];

            foreach ($income->detail as $key => $detail) 
            {
                foreach ($detail->specifics as $key => $product) 
                {                    
                    if ($product->element->element->identify_each_element)
                    {                        
                        array_push($codes, $product->hash);                        
                    }
                }

                foreach ($detail->specifics as $key => $product) 
                {                    
                    if ($product->element->element->identify_each_element)
                    {                        
                        if (!in_array($product->element_balance_id, $id_saltar))
                        {
                            $content = [
                                'id' => $detail->id,
                                'id_ele' => $product->element->element->id,
                                'quantity' => '',
                                'type' => 'Identificable',
                                'codes' => implode(',', $codes),
                                'multiselect_element' => $product->element->element->multiselect(),
                                'reason' => $detail->reason
                            ];

                            array_push($elements_id, $content);
                            array_push($id_saltar, $product->element_balance_id);
                        }                        
                    }
                    else
                    {
                        if (!in_array($product->element_balance_id, $id_saltar))
                        {
                            $content = [
                                'id' => $detail->id,
                                'id_ele' => $product->element->element->id,
                                'quantity' => $detail->quantity,
                                'type' => 'No Identificable',
                                'codes' => '',
                                'multiselect_element' => $product->element->element->multiselect(),
                                'reason' => $detail->reason
                            ];

                            array_push($elements_id, $content);
                            array_push($id_saltar, $product->element_balance_id);
                        }
                    }
                }
            }

            $income->elements_id = $elements_id;

            $income->delete = [
                'files' => [],
                'elements' => []
            ];

            return $this->respondHttp200([
                'data' => $income,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\elements\Request  $request
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function update(ElementIncomeRequest $request, EppIncome $income)
    {
        DB::beginTransaction();

        try
        {
            $income->user_id = $this->user->id;
            $income->location_id = $request->location_id;

            if(!$income->save())
                return $this->respondHttp500();

            foreach ($request->elements_id as $key => $value) 
            {
                if (isset($value['id']))
                {
                    $reason = $this->tagsPrepare($value['reason']);
                    $this->tagsSave($reason, TagReason::class);

                    $detail = EppIncomeDetail::find($value['id']);
                    $detail->location_id = $request->location_id;
                    $detail->reason = $reason->implode(',');

                    $element = Element::find($detail->element_id);
                    $ids = [];

                    if ($element->identify_each_element)
                    {
                        $codes_new = [];

                        foreach ($value['codes'] as $key => $code) 
                        {
                            $product = ElementBalanceSpecific::where('hash', $code['value'])->first();

                            if ($product)
                                array_push($ids, $product->id);
                            else
                                array_push($codes_new, $code['value']);
                            
                        }

                        if ($detail)
                        {
                            $balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                            foreach ($detail->specifics as $key => $product) 
                            {
                                if (!in_array($product->id, $ids))
                                {
                                    if ($product->state == 'Disponible')
                                    {
                                        $product->delete();

                                        $detail->quantity = $detail->quantity - 1;
                                        $detail->save();

                                        $balance->quantity = $balance->quantity - 1;
                                        $balance->quantity_available = $balance->quantity_available - 1;
                                        $balance->save();
                                    }
                                    else
                                    {
                                        return $this->respondWithError('El elemento ' . $element->name . ' con código '. $product->hash .' no se puede eliminar en la ubicación seleccionada ya que se encuentra en estado '. $product->state);
                                    }                                
                                }
                            }

                            foreach ($codes_new as $key => $new) 
                            {
                                $product = new ElementBalanceSpecific;
                                $product->hash = $new;
                                $product->code = $new;
                                $product->element_balance_id = $balance->id;
                                $product->location_id = $balance->location_id;
                                $product->save();

                                $detail->quantity = $detail->quantity + 1;

                                $balance->quantity = $balance->quantity + 1;
                                $balance->quantity_available = $balance->quantity_available + 1;
                                $balance->save();

                                array_push($ids, $product->id);
                            }
                        }

                        $detail->save();

                        $detail->specifics()->sync($ids);
                    }
                    else
                    {
                        $balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                        if ($detail->quantity > $value['quantity'])
                        {
                            $delete = $detail->quantity - $value['quantity'];
                            $hashs = [];

                            foreach ($detail->specifics as $key => $product) 
                            {
                                array_push($hashs, $product->hash);
                            }

                            $products = ElementBalanceSpecific::whereIn('hash', $hashs)->where('state', 'Disponible')->limit($delete)->get();

                            foreach ($products as $key => $value) 
                            {
                               $value->delete();

                               $detail->quantity = $detail->quantity - 1;
                               $detail->save();

                                $balance->quantity = $balance->quantity - 1;
                                $balance->quantity_available = $balance->quantity_available - 1;
                                $balance->save();
                            }       
                            
                            $products_restantes = ElementBalanceSpecific::whereIn('hash', $hashs)->get();

                            foreach ($products_restantes as $key => $product2) 
                            {
                                array_push($ids, $product2->id);
                            }
                        }
                        else if ($detail->quantity < $value['quantity'])
                        {
                            $news = $value['quantity'] - $detail->quantity;

                            foreach ($detail->specifics as $key => $product) 
                            {
                                array_push($hashs, $product->hash);
                            }

                            $products = ElementBalanceSpecific::whereIn('hash', $hashs)->where('state', 'Disponible')->get();

                            foreach ($products as $key => $product2) 
                            {
                                array_push($ids, $product2->id);
                            }

                            $balance = ElementBalanceLocation::where('element_id', $element->id)
                            ->where('location_id', $request->location_id)->first(); 

                            for ($i=1; $i <= $news; $i++) { 
                                
                                $hash = Hash::make($element->id . str_random(30));
                                $product = new ElementBalanceSpecific;
                                $product->hash = $hash;
                                $product->code = $hash;
                                $product->element_balance_id = $balance->id;
                                $product->location_id = $balance->location_id;
                                $product->save();

                                $detail->quantity = $detail->quantity + 1;

                                $balance->quantity = $balance->quantity + 1;
                                $balance->quantity_available = $balance->quantity_available + 1;
                                $balance->save();

                                array_push($ids, $product->id);
                            }
                        }

                        $detail->save();

                        $detail->specifics()->sync($ids);
                    }
                }
                else
                {
                    $element = Element::find($value['id_ele']);

                    if ($element)
                    {
                        $balance = ElementBalanceLocation::where('element_id', $element->id)
                        ->where('location_id', $request->location_id)->first();

                        $reason = $this->tagsPrepare($value['reason']);
                        $this->tagsSave($reason, TagReason::class);

                        $elements_sync = [];

                        $detail = new EppIncomeDetail;
                        $detail->income_id = $income->id;
                        $detail->company_id = $this->company;
                        $detail->element_id = $element->id;
                        $detail->location_id = $request->location_id;
                        $detail->reason = $reason->implode(',');

                        if ($element->identify_each_element)
                        {
                            $detail->quantity = COUNT($value['codes']);

                            if(!$detail->save())
                                return $this->respondHttp500();

                            if ($balance)
                            {
                                $balance->quantity = $balance->quantity + $detail->quantity;
                                $balance->quantity_available = $balance->quantity_available + $detail->quantity;
                                $balance->save();
                            }
                            else
                            {
                                $balance = new ElementBalanceLocation();
                                $balance->element_id = $element->id;
                                $balance->location_id = $detail->location_id;
                                $balance->quantity = $detail->quantity;
                                $balance->quantity_available = $detail->quantity;
                                $balance->quantity_allocated = 0;
                                $balance->save();
                            }

                            foreach ($value['codes'] as $key => $code) 
                            {
                                $product = new ElementBalanceSpecific;
                                $product->hash = $code['value'];
                                $product->code = $code['value'];
                                $product->element_balance_id = $balance->id;
                                $product->location_id = $balance->location_id;
                                $product->save();

                                array_push($elements_sync, $product->id);
                            }
                        }
                        else
                        {
                            $detail->quantity = $value['quantity'];

                            if(!$detail->save())
                                return $this->respondHttp500();

                            if ($balance)
                            {
                                $balance->quantity = $balance->quantity + $detail->quantity;
                                $balance->quantity_available = $balance->quantity_available + $detail->quantity;
                                $balance->save();
                            }
                            else
                            {
                                $balance = new ElementBalanceLocation();
                                $balance->element_id = $element->id;
                                $balance->location_id = $detail->location_id;
                                $balance->quantity = $detail->quantity;
                                $balance->quantity_available = $detail->quantity;
                                $balance->quantity_allocated = 0;
                                $balance->save();
                            }

                            for ($i=1; $i <= $value['quantity']; $i++) { 
                                $hash = Hash::make($element->id . str_random(30));
                                $product = new ElementBalanceSpecific;
                                $product->hash = $hash;
                                $product->code = $hash;
                                $product->element_balance_id = $balance->id;
                                $product->location_id = $balance->location_id;
                                $product->save();

                                array_push($elements_sync, $product->id);
                            }
                        }
                        
                        $detail->specifics()->sync($elements_sync);
                    }
                }
            }

            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el ingreso'
        ]);
    }

    public function deleteData($delete)
    {
        foreach ($delete['elements'] as $id)
        {
            $detail = EppIncomeDetail::find($id);

            if ($detail)
            {
                $balance = ElementBalanceLocation::where('element_id', $detail->element_id)
                ->where('location_id', $detail->location_id)->first();

                $balance->quantity = $balance->quantity - $detail->quantity;
                $balance->quantity_available = $balance->quantity_available - $detail->quantity;
                $balance->save();

                foreach ($detail->specifics as $key => $product) 
                {                    
                    if(!$product->delete())
                        return $this->respondHttp500();
                }

                $detail->delete();
            }                
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function destroy(EppIncome $income)
    {
        foreach ($income->detail as $key => $value) {
            
            $balance = ElementBalanceLocation::where('element_id', $value->element_id)
                ->where('location_id', $value->location_id)->first();

            $balance->quantity = $balance->quantity_available - $value->quantity;
            $balance->quantity_available = $balance->quantity_available - $detail->quantity;

            $balance->save();

            foreach ($value->specifics as $key => $product) 
            {                    
                if(!$product->delete())
                    return $this->respondHttp500();
            }
        }

        if(!$income->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el ingreso'
        ]);
    }

    public function elementInfo(Request $request)
    {
        $ele = Element::find($request->id);
        
        if ($ele->identify_each_element)
        {
            return [
                'type' => 'Identificable',
                'expiration_date' => $ele->expiration_date
            ];
        }
        else
        {
            return [
                'type' => 'No Identificable',
                'expiration_date' => $ele->expiration_date
            ];
        }
    }

    public function multiselectReason(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $tags = TagReason::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->where('company_id', $this->company)
                ->orderBy('name')
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($tags)
            ]);
        }
    }
}
