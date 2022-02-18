<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\EppTransfer;
use App\Models\IndustrialSecure\Epp\EppTransferDetail;
use App\Models\IndustrialSecure\Epp\EppReception;
use App\Models\IndustrialSecure\Epp\EppReceptionDetail;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Http\Requests\IndustrialSecure\Epp\ElementReceptionRequest;
use Validator;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
use Hash;

class ReceptionController extends Controller
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
        $receptions = EppReception::selectRaw(
            "sau_epp_receptions.*,
            GROUP_CONCAT(DISTINCT sau_epp_elements.name ORDER BY sau_epp_elements.name ASC) AS elements,            
            origin.name as name_location_origin,            
            destiny.name as name_location_destiny"
        )
        ->join('sau_epp_receptions_details', 'sau_epp_receptions_details.reception_id', 'sau_epp_receptions.id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_receptions_details.element_id')
        ->join('sau_epp_locations as origin', 'origin.id', 'sau_epp_receptions.location_origin_id')
        ->join('sau_epp_locations as destiny', 'destiny.id', 'sau_epp_receptions.location_destiny_id')
        ->groupBy('sau_epp_receptions.id', 'origin.name', 'destiny.name');

        return Vuetable::of($receptions)
            ->addColumn('industrialsecure-epps-transactions-reception-edit', function ($reception) {
                if ($reception->state == 'Recibido')
                    return false;
                else
                    return true;
            })
        ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Epp\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ElementTransferRequest $request)
    {
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
            $reception = EppReception::findOrFail($id);
            $reception->multiselect_location_origin = $reception->origin->multiselect();
            $reception->multiselect_location_destiny = $reception->destiny->multiselect();

            $elements_id = [];
            $id_saltar = [];
            $codes = [];
            $codes_received = [];
            $multiselect = [];
            $element_balance_id = [];
            $options = [];

            foreach ($reception->detail as $key => $detail) 
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

                foreach ($detail->received as $key => $productR) 
                {                    
                    if ($productR->element->element->identify_each_element)
                    {                        
                        array_push($codes_received, $productR->hash);
                    }
                }

                foreach ($detail->specifics as $key => $product) 
                {
                    $disponible_hash = ElementBalanceSpecific::select('id', 'hash')
                        ->whereIn('hash', $codes)
                        ->orderBy('id')
                        ->pluck('hash', 'hash');

                    $option = $this->multiSelectFormat($disponible_hash);

                    if ($product->element->element->identify_each_element)
                    {                        
                        if (!in_array($product->element_balance_id, $id_saltar))
                        {
                            $content = [
                                'id' => $detail->id,
                                'id_ele' => $product->element->element->id,
                                'quantity_transfer' => '',
                                'quantity_reception' => '',
                                'quantity_complete' => $detail->quantity_complete,
                                'reception' => $detail->reception,
                                'type' => 'Identificable',
                                'codes_transfer' => implode(',', $codes),
                                'codes_reception' => implode(',', $codes_received),
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
                                'quantity_transfer' => $detail->quantity_transfer,
                                'quantity_reception' => $detail->quantity_reception,
                                'quantity_complete' => $detail->quantity_complete,
                                'reception' => $detail->reception,
                                'type' => 'No Identificable',
                                'codes_transfer' => '',
                                'codes_reception' => '',
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
            ->where('location_id', $reception->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();

            $disponible = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id', 'sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('sau_epp_elements_balance_specific.location_id', $reception->location_id)
            ->where('sau_epp_elements_balance_specific.state', 'Disponible')
            ->whereIn('element_balance_id', $element_balance)
            ->get()
            ->toArray();

            $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible)
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $reception->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();


            foreach ($element_disponibles as $key => $value) {
                $ele = Element::find($value['element_id']);

                array_push($multiselect, $ele->multiselect());
            }

            $reception->elementos = $multiselect;

            $reception->elements_codes = $elements_id;

            $reception->elements_id = [];

            $reception->delete = [
                'files' => [],
                'elements' => []
            ];

            return $this->respondHttp200([
                'data' => $reception,
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
    public function update(ElementReceptionRequest $request, EppReception $reception)
    {
        DB::beginTransaction();

        try
        {            
            $reception->state = $request->state;
            $reception->user_reception = $this->user->id;
            
            if(!$reception->update()){
                return $this->respondHttp500();
            }

            if (COUNT($request->elements_id) > 0)
            {
                foreach ($request->elements_id as $key => $value) 
                {
                    $element = Element::find($value['id_ele']);

                    if ($element)
                    {
                        $balance_origin = ElementBalanceLocation::where('element_id', $element->id)
                        ->where('location_id', $request->location_origin_id)->first();

                        $balance_destiny = ElementBalanceLocation::where('element_id', $element->id)
                        ->where('location_id', $request->location_destiny_id)->first();

                        $elements_sync = [];
                        $elements_sync_reception = [];

                        $detail_reception = EppReceptionDetail::where('reception_id', $reception->id)->where('element_id', $element->id)->first();

                        if ($element->identify_each_element)
                        {
                            $detail_reception->quantity_reception = COUNT($value['codes_reception']);
                            $detail_reception->reception = $value['reception'];
                            $detail_reception->quantity_complete = $value['quantity_complete'];

                            if(!$detail_reception->update())
                                return $this->respondHttp500();
                            
                            if ($balance_destiny)
                            {
                                $balance_destiny->quantity = $balance_destiny->quantity + $detail_reception->quantity_reception;
                                $balance_destiny->quantity_available = $balance_destiny->quantity_available + $detail_reception->quantity_reception;
                                $balance_destiny->save();
                            }
                            else
                            {
                                $balance_destiny = new ElementBalanceLocation();
                                $balance_destiny->element_id = $element->id;
                                $balance_destiny->location_id = $detail_reception->location_destiny_id;
                                $balance_destiny->quantity = $detail_reception->quantity_reception;
                                $balance_destiny->quantity_available = $detail->quantity_reception;
                                $balance_destiny->quantity_allocated = 0;
                                $balance_destiny->save();
                            }

                            $balance_origin->quantity = $balance_origin->quantity - $detail_reception->quantity_reception;
                            $balance_origin->quantity_available = $balance_origin->quantity_available - $detail_reception->quantity_reception;
                            $balance_origin->save();

                            foreach ($value['codes_reception'] as $key => $code) 
                            {
                                $product = ElementBalanceSpecific::where('hash', $code['value'])->first();

                                if ($product)
                                {
                                    $product->state = 'Disponible';
                                    $product->location_id = $request->location_destiny_id;
                                    $product->element_balance_id = $balance_destiny->id;
                                    $product->save();
                                }

                                array_push($elements_sync_reception, $product->id);
                            }

                            foreach ($value['codes_transfer'] as $key => $code) 
                            {
                                $product = ElementBalanceSpecific::where('hash', $code['value'])->first();

                                if ($product->state == 'No Disponible')
                                {
                                    $product->state = 'Disponible';
                                    $product->save();
                                }

                                array_push($elements_sync, $product->id);
                            }
                        }
                        else
                        {
                            $detail_reception->quantity_reception = $value ['quantity_reception'];
                            $detail_reception->reception = $value['reception'];
                            $detail_reception->quantity_complete = $value['quantity_complete'];
                            
                            if(!$detail_reception->update())
                                return $this->respondHttp500();

                            $products = $detail_reception->specifics()->where('state', 'No disponible')->limit($detail_reception->quantity_reception)->get();

                            \Log::info($products);
  
                             if ($balance_destiny)
                            {
                                $balance_destiny->quantity = $balance_destiny->quantity + $detail_reception->quantity_reception;
                                $balance_destiny->quantity_available = $balance_destiny->quantity_available + $detail_reception->quantity_reception;
                                $balance_destiny->save();
                            }
                            else
                            {
                                $balance_destiny = new ElementBalanceLocation();
                                $balance_destiny->element_id = $element->id;
                                $balance_destiny->location_id = $detail->location_destiny_id;
                                $balance_destiny->quantity = $detail_reception->quantity_reception;
                                $balance_destiny->quantity_available = $detail_reception->quantity_reception;
                                $balance_destiny->quantity_allocated = 0;
                                $balance_destiny->save();
                            }

                            foreach ($products as $key => $value2) 
                            {
                                $value2->state = 'Disponible';
                                $value2->location_id = $request->location_destiny_id;
                                $value2->element_balance_id = $balance_destiny->id;
                                $value2->save();
                                
                                array_push($elements_sync, $value2->id);
                                array_push($elements_sync_reception, $value2->id);
                            }

                            $products_restantes = $detail_reception->specifics()->where('state', 'No disponible')->get();

                            if ($products_restantes->count() > 0)
                            {
                                $value2->state = 'Disponible';
                                $value2->save();
                                
                                array_push($elements_sync, $value2->id);
                            }

                            $balance_origin->quantity = $balance_origin->quantity - $detail_reception->quantity_reception;
                            $balance_origin->quantity_available = $balance_origin->quantity_available - $detail_reception->quantity_reception;
                            $balance_origin->save();
                        }
                        
                        $detail_reception->specifics()->sync($elements_sync);
                        $detail_reception->received()->sync($elements_sync_reception);
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
            'message' => 'Se actualizo la recepción'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function destroy(EppTransfer $exit)
    {        
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

    public function elementsLocation(Request $request)
    {
        try
        {
            $multiselect = [];
            $codes = [];

            $element_balance = ElementBalanceLocation::select('sau_epp_elements_balance_ubication.id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $request->location_origin_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();

            $disponible = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id', 'sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('sau_epp_elements_balance_specific.location_id', $request->location_origin_id)
            ->where('sau_epp_elements_balance_specific.state', 'Disponible')
            ->whereIn('element_balance_id', $element_balance)
            ->get()
            ->toArray();

            $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible)
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $request->location_origin_id)
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

                $element_balance = ElementBalanceLocation::where('location_id', $request->location_origin_id)
                ->where('element_id', $ele->id)
                ->first();

                $disponible = ElementBalanceSpecific::select('id', 'hash')
                ->where('location_id', $request->location_origin_id)
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
