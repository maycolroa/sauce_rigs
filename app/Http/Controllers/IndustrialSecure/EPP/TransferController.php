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
use App\Http\Requests\IndustrialSecure\Epp\ElementTransferRequest;
use Validator;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
use Hash;

class TransferController extends Controller
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
        $transfer = EppTransfer::selectRaw(
            "sau_epp_transfers.*,
            GROUP_CONCAT(DISTINCT sau_epp_elements.name ORDER BY sau_epp_elements.name ASC) AS elements,            
            origin.name as name_location_origin,            
            destiny.name as name_location_destiny"
        )
        ->join('sau_epp_transfer_details', 'sau_epp_transfer_details.transfer_id', 'sau_epp_transfers.id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_transfer_details.element_id')
        ->join('sau_epp_locations as origin', 'origin.id', 'sau_epp_transfers.location_origin_id')
        ->join('sau_epp_locations as destiny', 'destiny.id', 'sau_epp_transfers.location_destiny_id')
        ->groupBy('sau_epp_transfers.id', 'origin.name', 'destiny.name')
        ->orderBy('sau_epp_transfers.id', 'DESC');

        return Vuetable::of($transfer)
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
        DB::beginTransaction();

        try
        {
            $transfer = new EppTransfer;
            $transfer->company_id = $this->company;
            $transfer->user_id = $this->user->id;
            $transfer->location_origin_id = $request->location_origin_id;
            $transfer->location_destiny_id = $request->location_destiny_id;
            $transfer->state = $request->state;
            
            if(!$transfer->save())
                return $this->respondHttp500();

            $reception = new EppReception;
            $reception->company_id = $this->company;
            $reception->user_transfer = $this->user->id;
            $reception->transfer_id = $transfer->id;
            $reception->location_origin_id = $request->location_origin_id;
            $reception->location_destiny_id = $request->location_destiny_id;
            
            if ($transfer->state == 'En transito')
                $reception->state = 'En espera';
            else
            {
                $reception->state = 'Recibido';
                $reception->user_reception = $this->user->id;
            }
            
            $reception->save();

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

                        $detail = new EppTransferDetail;
                        $detail->transfer_id = $transfer->id;
                        $detail->company_id = $this->company;
                        $detail->element_id = $element->id;
                        $detail->location_origin_id = $request->location_origin_id;
                        $detail->location_destiny_id = $request->location_destiny_id;

                        $detail_reception = new EppReceptionDetail;
                        $detail_reception->reception_id = $reception->id;
                        $detail_reception->company_id = $this->company;
                        $detail_reception->element_id = $element->id;
                        $detail_reception->location_origin_id = $request->location_origin_id;
                        $detail_reception->location_destiny_id = $request->location_destiny_id;

                        if ($element->identify_each_element)
                        {
                            $detail->quantity = COUNT($value['codes']);
                            $detail_reception->quantity_transfer = COUNT($value['codes']);

                            if ($transfer->state != 'En transito')
                            {
                                $detail_reception->quantity_reception = COUNT($value['codes']);
                                $detail_reception->reception = 'SI';
                            }

                            if(!$detail->save())
                                return $this->respondHttp500();

                            if(!$detail_reception->save())
                                return $this->respondHttp500();

                            if ($transfer->state != 'En transito')
                            {                                   
                                if ($balance_destiny)
                                {
                                    $balance_destiny->quantity = $balance_destiny->quantity + $detail->quantity;
                                    $balance_destiny->quantity_available = $balance_destiny->quantity_available + $detail->quantity;
                                    $balance_destiny->save();
                                }
                                else
                                {
                                    $balance_destiny = new ElementBalanceLocation();
                                    $balance_destiny->element_id = $element->id;
                                    $balance_destiny->location_id = $detail->location_destiny_id;
                                    $balance_destiny->quantity = $detail->quantity;
                                    $balance_destiny->quantity_available = $detail->quantity;
                                    $balance_destiny->quantity_allocated = 0;
                                    $balance_destiny->save();
                                }

                                $balance_origin->quantity = $balance_origin->quantity - $detail->quantity;
                                $balance_origin->quantity_available = $balance_origin->quantity_available - $detail->quantity;
                                $balance_origin->save();
                            }

                            foreach ($value['codes'] as $key => $code) 
                            {
                                $product = ElementBalanceSpecific::where('hash', $code['value'])->first();

                                if ($product)
                                {
                                    if ($transfer->state == 'En transito')
                                        $product->state = 'No disponible';
                                    else
                                        $product->state = 'Disponible';

                                    $product->location_id = $request->location_destiny_id;
                                    $product->element_balance_id = $balance_destiny->id;
                                    $product->save();
                                }

                                array_push($elements_sync, $product->id);
                                array_push($elements_sync_reception, $product->id);
                            }
                        }
                        else
                        {
                            $detail->quantity = $value['quantity'];

                            $detail_reception->quantity_transfer = $value['quantity'];

                            if ($transfer->state != 'En transito')
                            {
                                $detail_reception->quantity_reception = $value['quantity'];
                                $detail_reception->reception = 'SI';
                            }

                            if(!$detail->save())
                                return $this->respondHttp500();
                            
                            if(!$detail_reception->save())
                                return $this->respondHttp500();

                            $products = ElementBalanceSpecific::where('element_balance_id', $balance_origin->id)->where('location_id', $request->location_origin_id)->where('state', 'Disponible')->limit($detail->quantity)->get();

                            if (!$products)
                                return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación de origen seleccionada');
                            else if ($products->count() < $value['quantity'])
                                return $this->respondWithError('Del elemento ' . $element->name . ' no se tienen disponibles suficientes unidades');

                            if ($balance_destiny)
                            {
                                $balance_destiny->quantity = $balance_destiny->quantity + $detail->quantity;
                                $balance_destiny->quantity_available = $balance_destiny->quantity_available + $detail->quantity;
                                $balance_destiny->save();
                            }
                            else
                            {
                                $balance_destiny = new ElementBalanceLocation();
                                $balance_destiny->element_id = $element->id;
                                $balance_destiny->location_id = $detail->location_destiny_id;
                                $balance_destiny->quantity = $detail->quantity;
                                $balance_destiny->quantity_available = $detail->quantity;
                                $balance_destiny->quantity_allocated = 0;
                                $balance_destiny->save();
                            }

                            foreach ($products as $key => $value2) {
                                if ($transfer->state == 'En transito')
                                    $value2->state = 'No disponible';
                                else
                                    $value2->state = 'Disponible';
                                        
                                $value2->location_id = $request->location_destiny_id;
                                $value2->element_balance_id = $balance_destiny->id;
                                $value2->save();
                                
                                array_push($elements_sync, $value2->id);
                                array_push($elements_sync_reception, $value2->id);
                            }

                            $balance_origin->quantity = $balance_origin->quantity - $detail->quantity;
                            $balance_origin->quantity_available = $balance_origin->quantity_available - $detail->quantity;
                            $balance_origin->save();
                        }
                        
                        $detail->specifics()->sync($elements_sync);
                        $detail_reception->specifics()->sync($elements_sync_reception);

                        if ($transfer->state != 'En transito')
                            $detail_reception->received()->sync($elements_sync_reception);
                    }
                }
            }

            DB::commit();

            $this->saveLogActivitySystem('Epp - Transferencias', 'Se realizo una transferencia de la ubicacion '.$transfer->origin->name.' a la ubicación '.$transfer->destiny->name);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            //DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el traslado'
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
            $transfer = EppTransfer::findOrFail($id);
            $transfer->multiselect_location_origin = $transfer->origin->multiselect();
            $transfer->multiselect_location_destiny = $transfer->destiny->multiselect();

            $elements_id = [];
            $id_saltar = [];
            $codes = [];
            $multiselect = [];
            $element_balance_id = [];
            $options = [];

            foreach ($transfer->detail as $key => $detail) 
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
            ->where('location_id', $transfer->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();

            $disponible = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id', 'sau_epp_elements_balance_specific.element_balance_id')
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('sau_epp_elements_balance_specific.location_id', $transfer->location_id)
            ->where('sau_epp_elements_balance_specific.state', 'Disponible')
            ->whereIn('element_balance_id', $element_balance)
            ->get()
            ->toArray();

            $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible)
            ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
            ->where('location_id', $transfer->location_id)
            ->where('sau_epp_elements.company_id', $this->company)
            ->get()
            ->toArray();


            foreach ($element_disponibles as $key => $value) {
                $ele = Element::find($value['element_id']);

                array_push($multiselect, $ele->multiselect());
            }

            $transfer->elementos = $multiselect;

            $transfer->elements_codes = $elements_id;

            $transfer->elements_id = [];

            $transfer->delete = [
                'files' => [],
                'elements' => []
            ];

            return $this->respondHttp200([
                'data' => $transfer,
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
    public function update(ElementTransferRequest $request, EppTransfer $exit)
    {
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

            $not_element = 'SI';

            if (COUNT($multiselect) > 0)
                $not_element = 'NO';

            $data = [
                'multiselect' => $multiselect,
                'codes' => $codes,
                'not_element' => $not_element
            ];

            return $data;

        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }
}
