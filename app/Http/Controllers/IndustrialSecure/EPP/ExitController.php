<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\EppExit;
use App\Models\IndustrialSecure\Epp\EppExitDetail;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\TagReason;
use App\Http\Requests\IndustrialSecure\Epp\ElementExitRequest;
use Validator;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
use Hash;

class ExitController extends Controller
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
        $exit = EppExit::selectRaw(
            "sau_epp_exits.*,
            GROUP_CONCAT(DISTINCT sau_epp_elements.name ORDER BY sau_epp_elements.name ASC) AS elements,            
            sau_epp_locations.name as name_location"
        )
        ->join('sau_epp_exits_details', 'sau_epp_exits_details.exit_id', 'sau_epp_exits.id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_exits_details.element_id')
        ->join('sau_epp_locations', 'sau_epp_locations.id', 'sau_epp_exits_details.location_id')
        ->groupBy('sau_epp_exits.id', 'sau_epp_locations.name');

        return Vuetable::of($exit)
        ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Epp\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ElementExitRequest $request)
    {
        DB::beginTransaction();

        try
        {
            $exit = new EppExit;
            $exit->company_id = $this->company;
            $exit->user_id = $this->user->id;
            $exit->location_id = $request->location_id;
            
            if(!$exit->save())
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

                        $detail = new EppExitDetail;
                        $detail->exit_id = $exit->id;
                        $detail->company_id = $this->company;
                        $detail->element_id = $element->id;
                        $detail->location_id = $request->location_id;
                        $detail->reason = $reason->implode(',');

                        if ($element->identify_each_element)
                        {
                            $detail->quantity = COUNT($value['codes']);

                            if(!$detail->save())
                                return $this->respondHttp500();

                            $balance->quantity = $balance->quantity - $detail->quantity;
                            $balance->quantity_available = $balance->quantity_available - $detail->quantity;
                            $balance->save();

                            foreach ($value['codes'] as $key => $code) 
                            {
                                $product = ElementBalanceSpecific::where('hash', $code['value'])->first();

                                if ($product)
                                {
                                    $product->state = 'No disponible o desechado';
                                    $product->save();
                                }

                                array_push($elements_sync, $product->id);
                            }
                        }
                        else
                        {
                            $detail->quantity = $value['quantity'];

                            if(!$detail->save())
                                return $this->respondHttp500();

                            $products = ElementBalanceSpecific::where('element_balance_id', $balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->limit($detail->quantity)->get();

                            if (!$products)
                                return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                            else if ($products->count() < $value['quantity'])
                                return $this->respondWithError('El elemento ' . $element->name . ' no se tiene disponible suficientes unidades');

                            foreach ($products as $key => $value2) {
                                $value2->state = 'No disponible o desechado';
                                $value2->save();
                                array_push($elements_sync, $value2->id);
                            }

                            $balance->quantity = $balance->quantity - $detail->quantity;
                            $balance->quantity_available = $balance->quantity_available - $detail->quantity;
                            $balance->save();
                        }
                        
                        $detail->specifics()->sync($elements_sync);
                    }
                }
            }

            DB::commit();

            $this->saveLogActivitySystem('Epp - Salidas', 'Se realizo una salida en la ubicacion '.$exit->location->name.' ');

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            //DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la salida'
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
            $exit = EppExit::findOrFail($id);
            $exit->multiselect_location = $exit->location->multiselect();

            $elements_id = [];
            $id_saltar = [];
            $codes = [];
            $multiselect = [];
            $element_balance_id = [];
            $options = [];

            foreach ($exit->detail as $key => $detail) 
            {
                foreach ($detail->specifics as $key => $product) 
                {                    
                    if ($product->element->element->identify_each_element)
                    {                        
                        array_push($codes, $product->hash);

                        if (!in_array($product->element_balance_id, $element_balance_id))
                        {
                            array_push($element_balance_id, $product->element_balance_id);
                        }
                    }
                }

                foreach ($detail->specifics as $key => $product) 
                {
                    $disponible_hash = ElementBalanceSpecific::select('id', 'hash')
                        ->where('location_id', $detail->location_id)
                        ->where('state', 'Disponible')
                        ->where('element_balance_id', $product->element_balance_id)
                        ->orderBy('id')
                        ->get();
                    
                    $hash_specific = ElementBalanceSpecific::select('id', 'hash')
                        ->where('hash', $product->hash)
                        ->get(); 

                    $disponible_hash = $disponible_hash->merge($hash_specific);

                    $disponible_hash = $disponible_hash->pluck('hash', 'hash');

                    $option = $this->multiSelectFormat($disponible_hash);

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

                            array_push($elements_id, ['element' => $content, 'options' => $option]);
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

                            array_push($elements_id, ['element' => $content, 'options' => $option]);
                            array_push($id_saltar, $product->element_balance_id);
                        }
                    }
                }
            }

            $element_balance = ElementBalanceLocation::select('sau_epp_elements_balance_ubication.id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $exit->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();

            $disponible = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id', 'sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('sau_epp_elements_balance_specific.location_id', $exit->location_id)
            ->where('sau_epp_elements_balance_specific.state', 'Disponible')
            ->whereIn('element_balance_id', $element_balance)
            ->get()
            ->toArray();

            $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible)
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $exit->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();


            foreach ($element_disponibles as $key => $value) {
                $ele = Element::find($value['element_id']);

                array_push($multiselect, $ele->multiselect());
            }

            $exit->elementos = $multiselect;

            $exit->elements_codes = $elements_id;

            $exit->elements_id = [];

            //$exit->elements_id = $elements_id;

            //$exit->codes = $options;

            $exit->delete = [
                'files' => [],
                'elements' => []
            ];

            return $this->respondHttp200([
                'data' => $exit,
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
    public function update(ElementExitRequest $request, EppExit $exit)
    {
        DB::beginTransaction();

        try
        {
            $exit->user_id = $this->user->id;
            $exit->location_id = $request->location_id;

            if(!$exit->save())
                return $this->respondHttp500();

            foreach ($request->elements_id as $key => $value) 
            {
                if (isset($value['id']))
                {
                    $ids = [];
                    $reason = $this->tagsPrepare($value['reason']);
                    $this->tagsSave($reason, TagReason::class);

                    $detail = EppExitDetail::find($value['id']);
                    $detail->location_id = $request->location_id;
                    $detail->reason = $reason->implode(',');

                    $element = Element::find($detail->element_id);

                    if ($element->identify_each_element)
                    {
                        $codes_new = [];

                        foreach ($value['codes'] as $key => $code) 
                        {
                            $product = ElementBalanceSpecific::where('hash', $code['value'])->whereNotIn('state', ['Asignado', 'Desechado'])->first();

                            if ($product->state == 'No disponible o desechado')
                                array_push($ids, $product->id);
                            else if ($product->state = 'Disponible')
                                array_push($codes_new, $product->hash);
                        }

                        if ($detail)
                        {
                            $balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                            foreach ($detail->specifics as $key => $product) 
                            {
                                if (!in_array($product->id, $ids))
                                {
                                    if ($product->state == 'No disponible o desechado')
                                    {
                                        $product->state = 'Disponible';
                                        $product->save();

                                        $detail->quantity = $detail->quantity - 1;
                                        $detail->save();

                                        $balance->quantity = $balance->quantity + 1;
                                        $balance->quantity_available = $balance->quantity_available + 1;
                                        $balance->save();
                                    }                               
                                }
                            }

                            foreach ($codes_new as $key => $new) 
                            {
                                $product = ElementBalanceSpecific::where('hash', $new)->first();

                                if ($product)
                                {
                                    $product->state = 'No disponible o desechado';
                                    $product->save();
                                }

                                $detail->quantity = $detail->quantity + 1;

                                $balance->quantity = $balance->quantity - 1;
                                $balance->quantity_available = $balance->quantity_available - 1;
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

                            $products = ElementBalanceSpecific::whereIn('hash', $hashs)->where('state', 'No disponible o desechado')->limit($delete)->get();

                            foreach ($products as $key => $value) 
                            {
                                $value->state = 'Disponible';
                                $value->save();

                                $detail->quantity = $detail->quantity - 1;
                                $detail->save();

                                $balance->quantity = $balance->quantity + 1;
                                $balance->quantity_available = $balance->quantity_available + 1;
                                $balance->save();
                            }       
                            
                            $products_restantes = ElementBalanceSpecific::whereIn('hash', $hashs)->where('state', 'No disponible o desechado')->get();

                            foreach ($products_restantes as $key => $product2) 
                            {
                                array_push($ids, $product2->id);
                            }
                        }
                        else if ($detail->quantity < $value['quantity'])
                        {
                            $news = $value['quantity'] - $detail->quantity;
                            $hashs = [];

                            foreach ($detail->specifics as $key => $product) 
                            {
                                array_push($hashs, $product->hash);
                            }

                            $products = ElementBalanceSpecific::whereIn('hash', $hashs)->where('state', 'No disponible o desechado')->get();

                            foreach ($products as $key => $product2) 
                            {
                                array_push($ids, $product2->id);
                            }

                            $balance = ElementBalanceLocation::where('element_id', $element->id)
                            ->where('location_id', $request->location_id)->first(); 

                            $products = ElementBalanceSpecific::where('element_balance_id', $balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->limit($news)->get();

                            if (!$products)
                                return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                            else if ($products->count() < $news)
                                return $this->respondWithError('El elemento ' . $element->name . ' no se tiene disponible suficientes unidades');

                            foreach ($products as $key => $value2) {
                                $value2->state = 'No disponible o desechado';
                                $value2->save();

                                array_push($ids, $value2->id);

                                $detail->quantity = $detail->quantity + 1;
                                $detail->save();

                            }

                            $balance->quantity = $balance->quantity - $news;
                            $balance->quantity_available = $balance->quantity_available - $news;
                            $balance->save();
                        }
                        else
                        {
                            foreach ($detail->specifics as $key => $product) 
                            {
                                array_push($ids, $product->id);
                            }
                        }

                        $detail->save();

                        $detail->specifics()->sync($ids);
                    }
                }
                else
                {
                    $ids = [];
                    $element = Element::find($value['id_ele']);

                    if ($element)
                    {
                        $balance = ElementBalanceLocation::where('element_id', $element->id)
                        ->where('location_id', $request->location_id)->first();

                        $reason = $this->tagsPrepare($value['reason']);
                        $this->tagsSave($reason, TagReason::class);

                        $elements_sync = [];

                        $detail = new EppExitDetail;
                        $detail->exit_id = $exit->id;
                        $detail->company_id = $this->company;
                        $detail->element_id = $element->id;
                        $detail->location_id = $request->location_id;
                        $detail->reason = $reason->implode(',');

                        if ($element->identify_each_element)
                        {
                            $detail->quantity = COUNT($value['codes']);

                            if(!$detail->save())
                                return $this->respondHttp500();

                            $balance->quantity = $balance->quantity - $detail->quantity;
                            $balance->quantity_available = $balance->quantity_available - $detail->quantity;
                            $balance->save();

                            foreach ($value['codes'] as $key => $code) 
                            {
                                $product = ElementBalanceSpecific::where('hash', $code['value'])->first();

                                if ($product)
                                {
                                    $product->state = 'No disponible o desechado';
                                    $product->save();
                                }

                                array_push($elements_sync, $product->id);
                            }
                        }
                        else
                        {
                            $detail->quantity = $value['quantity'];

                            if(!$detail->save())
                                return $this->respondHttp500();

                            $products = ElementBalanceSpecific::where('element_balance_id', $balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->limit($detail->quantity)->get();

                            if (!$products)
                                return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                            else if ($products->count() < $value['quantity'])
                                return $this->respondWithError('El elemento ' . $element->name . ' no se tiene disponible suficientes unidades');

                            foreach ($products as $key => $value2) {
                                $value2->state = 'No disponible o desechado';
                                $value2->save();
                                array_push($elements_sync, $value2->id);
                            }

                            $balance->quantity = $balance->quantity - $detail->quantity;
                            $balance->quantity_available = $balance->quantity_available - $detail->quantity;
                            $balance->save();
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
            'message' => 'Se actualizo la salida'
        ]);
    }

    public function deleteData($delete)
    {
        foreach ($delete['elements'] as $id)
        {
            $detail = EppExitDetail::find($id);

            if ($detail)
            {
                $balance = ElementBalanceLocation::where('element_id', $detail->element_id)
                ->where('location_id', $detail->location_id)->first();

                $balance->quantity = $balance->quantity + $detail->quantity;
                $balance->quantity_available = $balance->quantity_available + $detail->quantity;
                $balance->save();

                foreach ($detail->specifics as $key => $product) 
                {                    
                    $product->state = 'Disponible';
                    $product->save();
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
    public function destroy(EppExit $exit)
    {
        foreach ($exit->detail as $key => $value) {
            
            $balance = ElementBalanceLocation::where('element_id', $value->element_id)
                ->where('location_id', $value->location_id)->first();

            $balance->quantity = $balance->quantity_available - $value->quantity;

            $balance->save();

            foreach ($value->specifics as $key => $product) 
            {                    
                if(!$product->delete())
                    return $this->respondHttp500();
            }
        }

        if(!$exit->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la salida'
        ]);
    }

    public function elementInfo(Request $request)
    {
        $ele = Element::find($request->id);
        
        if ($ele->identify_each_element)
        {
            return [
                'type' => 'Identificable',
            ];
        }
        else
        {
            return [
                'type' => 'No Identificable',
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

    public function elementsLocation(Request $request)
    {
        try
        {
            $multiselect = [];
            $codes = [];

            $element_balance = ElementBalanceLocation::select('sau_epp_elements_balance_ubication.id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $request->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();

            $disponible = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id', 'sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('sau_epp_elements_balance_specific.location_id', $request->location_id)
            ->where('sau_epp_elements_balance_specific.state', 'Disponible')
            ->whereIn('element_balance_id', $element_balance)
            ->get()
            ->toArray();

            $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible)
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $request->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();

            $ids_disponibles = [];

            foreach ($element_disponibles as $key => $value) {
                $ele = Element::find($value['element_id']);

                array_push( $multiselect, $ele->multiselect());
                array_push( $ids_disponibles, $ele->id);
            }

            foreach ($ids_disponibles as $key => $value) {

                $ele = Element::find($value);

                $element_balance = ElementBalanceLocation::where('location_id', $request->location_id)
                ->where('element_id', $ele->id)
                ->first();

                $disponible = ElementBalanceSpecific::select('id', 'hash')
                ->where('location_id', $request->location_id)
                ->where('state', 'Disponible')
                ->where('element_balance_id', $element_balance->id)
                ->orderBy('id')
                ->pluck('hash', 'hash');

                $options = $this->multiSelectFormat($disponible);

                array_push($codes, $options);
            }

            $data = [
                'multiselect' => $multiselect,
                'codes' => $codes
            ];

            return $data;
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }
}
