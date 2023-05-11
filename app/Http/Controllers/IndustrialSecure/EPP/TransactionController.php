<?php

namespace App\Http\Controllers\IndustrialSecure\Epp;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Models\Administrative\Employees\Employee;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\ElementTransactionEmployee;
use App\Http\Requests\IndustrialSecure\Epp\ElementTransactionsRequest;
use App\Http\Requests\IndustrialSecure\Epp\ElementTransactionsReturnsRequest;
use App\Models\IndustrialSecure\Epp\TagsType;
use App\Models\IndustrialSecure\Epp\FileTransactionEmployee;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\HashSelectDeliveryTemporal;
use App\Models\IndustrialSecure\Epp\EppWastes;
use App\Models\IndustrialSecure\Epp\ReturnDelivery;
use App\Models\IndustrialSecure\Epp\ChangeElement;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use App\Models\IndustrialSecure\Epp\TagReason;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\General\Company;
use App\Jobs\IndustrialSecure\Epp\DeliveryExportJob;
use Validator;
use App\Traits\Filtertrait;
use Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
use Hash;
use PDF;
use App\Models\Administrative\Users\User;

class TransactionController extends Controller
{
    use Filtertrait;
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
        $transactions = ElementTransactionEmployee::selectRaw(
            "sau_epp_transactions_employees.*,
            sau_employees.name AS employee,
            sau_employees_positions.name as position,
            GROUP_CONCAT(DISTINCT sau_epp_elements.name ORDER BY sau_epp_elements.name ASC) AS elements"
        )        
        ->join('sau_employees', 'sau_employees.id', 'sau_epp_transactions_employees.employee_id')
        ->join('sau_employees_positions', 'sau_employees_positions.id', 'sau_employees.employee_position_id')
        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.transaction_employee_id', 'sau_epp_transactions_employees.id')
        ->join('sau_epp_elements_balance_specific', 'sau_epp_elements_balance_specific.id', 'sau_epp_transaction_employee_element.element_id')
        ->leftJoin('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
        ->where('sau_epp_transactions_employees.type', 'Entrega')
        ->groupBy('sau_epp_transactions_employees.id');

        $url = '/industrialsecure/epps/transactions';

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (COUNT($filters) > 0)
        {
            $dates_request = explode('/', $filters["dateRange"]);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $transactions->betweenDate($dates);
        }

        return Vuetable::of($transactions)
        ->addColumn('switchStatus', function ($transaction) {
            if ($transaction->state == 'Procesada')
                return false;
            else
                return true;
        })
        ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Epp\ElementRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ElementTransactionsRequest $request)
    {
        if ($request->type == 'Entrega')
        {
            if ($request->inventary == 'SI')
                return $this->storeDelivery($request);
            else
                return $this->storeDeliveryNotInventary($request);
        }
        else
        {
            return $this->storeReturns($request);
        }

    }

    public function storeDelivery(ElementTransactionsRequest $request)
    {                        
        Validator::make($request->all(), [
            "files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg')

                        $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $employee = Employee::findOrFail($request->employee_id);

            $delivery = new ElementTransactionEmployee();
            $delivery->employee_id = $request->employee_id;
            $delivery->position_employee_id = $employee->position->id;
            $delivery->type = 'Entrega';
            $delivery->observations = $request->observations ? $request->observations : NULL;
            $delivery->location_id = $request->location_id;
            $delivery->company_id = $this->company;
            $delivery->class_element = $request->class_element;
            $delivery->edit_firm = $request->edit_firm;
            $delivery->firm_email = $request->firm_email;
            $delivery->email_firm_employee = $request->firm_email == 'Email' ? $request->email_firm_employee : NULL;
            $delivery->user_id = $this->user->id;
            
            if(!$delivery->save())
                return $this->respondHttp500();

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                $element = Element::find($value['id_ele']);

                if ($element)
                {
                    if ($element->identify_each_element)
                    {
                        $disponible = ElementBalanceSpecific::where('hash', $value['code'])->where('location_id', $request->location_id)->first();

                        if ($disponible->state != 'Disponible')
                            return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                        
                        $disponible->state = 'Asignado';
                        $disponible->save();

                        array_push($elements_sync, $disponible->id);

                        $element_balance = ElementBalanceLocation::find($disponible->element_balance_id);

                        $element_balance->quantity_available = $element_balance->quantity_available - 1;

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;

                        $element_balance->save();
                    }
                    else
                    {
                        $element_balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                        $disponible = ElementBalanceSpecific::where('element_balance_id', $element_balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->limit($value['quantity'])->get();

                        if (!$disponible)
                            return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                        else if ($disponible->count() < $value['quantity'])
                            return $this->respondWithError('El elemento ' . $element->name . ' no se tiene disponible suficientes unidades');

                        foreach ($disponible as $key => $value2) {
                            $value2->state = 'Asignado';
                            $value2->save();
                            array_push($elements_sync, $value2->id);
                        }

                        $element_balance->quantity_available = $element_balance->quantity_available - $value['quantity'];

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated + $value['quantity'];

                        $element_balance->save();
                    }
                }
            }

            $delivery->elements()->sync($elements_sync);

            if ($delivery->edit_firm)
            {
                if ($request->firm_employee && $delivery->firm_email == 'Dibujar')
                {
                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $delivery->firm_employee = $imageName;

                    if(!$delivery->update())
                        return $this->respondHttp500();
                }
                else if ($delivery->firm_email == 'Email')
                {
                    $recipient = new User(['email' => $delivery->email_firm_employee]);

                    NotificationMail::
                        subject('Sauce - Elementos de protección personal')
                        //->view('LegalAspects.legalMatrix.notifyUpdateLaws')
                        ->recipients($recipient)
                        ->message("Estimado $employee->name, usted tiene una solicitud de firma de una entrega de elementos de protección personal, para hacerlo ingrese a los links acontinuación: ")
                        ->module('epp')
                        ->buttons([['text'=>'Firmar', 'url'=>action('IndustrialSecure\EPP\TransactionFirmController@index', ['transaction' => $delivery->id, 'employee' => $employee->id])]])
                        //->with(['user' => $employee->name, 'urls'=>$url_email])
                        //->list(['<b>'.$delivery->type.'</b>'], 'ul')
                        ->company($this->company)
                        ->send();
                }
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $delivery->id);
            }

            $this->deletedTemporal();

            DB::commit();

            $this->saveLogActivitySystem('EPP - Entregas', 'Se creo una entrega para el empleado '.$employee->name.' ');


            return $this->respondHttp200([
                'message' => 'Se creo la entrega'
            ]);

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
    }

    public function storeDeliveryNotInventary(ElementTransactionsRequest $request)
    {
        Validator::make($request->all(), [
            "files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg')

                        $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {

            $employee = Employee::findOrFail($request->employee_id);

            $delivery = new ElementTransactionEmployee();
            $delivery->employee_id = $request->employee_id;
            $delivery->position_employee_id = $employee->position->id;
            $delivery->type = 'Entrega';
            $delivery->observations = $request->observations ? $request->observations : NULL;
            $delivery->location_id = $request->location_id;
            $delivery->company_id = $this->company;
            $delivery->edit_firm = $request->edit_firm;
            $delivery->firm_email = $request->firm_email;
            $delivery->email_firm_employee = $request->email_firm_employee;            
            $delivery->user_id = $this->user->id;
            
            if(!$delivery->save())
                return $this->respondHttp500();

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                $element = Element::find($value['id_ele']);

                if ($element)
                {
                    $element_balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                    if (!$element_balance)
                    {
                        $element_balance = new ElementBalanceLocation();
                        $element_balance->element_id = $element->id;
                        $element_balance->location_id = $request->location_id;
                        $element_balance->quantity = 0;
                        $element_balance->quantity_available = 0;
                        $element_balance->quantity_allocated = 0;
                        $element_balance->save();
                    }

                    for ($i=1; $i <= $value['quantity']; $i++) { 
                        $hash = Hash::make($element->id . str_random(30));
                        $product = new ElementBalanceSpecific;
                        $product->hash = $hash;
                        $product->code = $hash;
                        $product->element_balance_id = NULL;
                        $product->location_id = $request->location_id;
                        $product->expiration_date = $element->days_expired ? $element->days_expired : NULL;
                        $product->state = 'Asignado';
                        $product->save();

                        array_push($elements_sync, $product->id);
                    }

                    /*$element_balance->quantity_available = $element_balance->quantity_available - $value['quantity'];

                    $element_balance->quantity_allocated = $element_balance->quantity_allocated + $value['quantity'];

                    $element_balance->save();*/
                }
            }

            $delivery->elements()->sync($elements_sync);

            if ($delivery->edit_firm)
            {
                if ($request->firm_employee && $delivery->firm_email == 'Dibujar')
                {
                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $delivery->firm_employee = $imageName;

                    if(!$delivery->update())
                        return $this->respondHttp500();
                }
                else if ($delivery->firm_email == 'Email')
                {
                    $recipient = new User(['email' => $delivery->email_firm_employee]);

                    NotificationMail::
                        subject('Sauce - Elementos de protección personal')
                        //->view('LegalAspects.legalMatrix.notifyUpdateLaws')
                        ->recipients($recipient)
                        ->message("Estimado $employee->name, usted tiene una solicitud de firma de una entrega de elementos de protección personal, para hacerlo ingrese a los links acontinuación: ")
                        ->module('epp')
                        ->buttons([['text'=>'Firmar', 'url'=>action('IndustrialSecure\EPP\TransactionFirmController@index', ['transaction' => $delivery->id, 'employee' => $employee->id])]])
                        //->with(['user' => $employee->name, 'urls'=>$url_email])
                        //->list(['<b>'.$delivery->type.'</b>'], 'ul')
                        ->company($this->company)
                        ->send();
                }
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $delivery->id);
            }

            $this->deletedTemporal();

            $this->saveLogActivitySystem('EPP - Entregas', 'Se creo una entrega para el empleado '.$employee->name.' ');

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la entrega'
        ]);
    }

    public function storeReturns(ElementTransactionsRequest $request)
    {
        Validator::make($request->all(), [
            "files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg')

                        $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $employee = Employee::findOrFail($request->employee_id);

            $returns = new ElementTransactionEmployee();
            $returns->employee_id = $request->employee_id;
            $returns->position_employee_id = $employee->position->id;
            $returns->type = 'Devolucion';
            $returns->observations = $request->observations ? $request->observations : NULL;
            $returns->location_id = $request->location_id;
            $returns->company_id = $this->company;
            $returns->user_id = $this->user->id;
            
            if(!$returns->save())
                return $this->respondHttp500();

            $elements_sync = [];
            $elements_sync_rechange = [];
            $elements_change = [];

            foreach ($request->elements_id as $key => $value) 
            {
                $element = Element::find($value['id_ele']);
                $element_balance = ElementBalanceLocation::where('element_id', $value['id_ele'])->where('location_id', $request->location_id)->first();

                if ($element) 
                {
                    if ($element->identify_each_element)
                    {
                        $disponible = ElementBalanceSpecific::where('hash', $value['code'])->first();

                        $balance_origen = ElementBalanceLocation::find($disponible->element_balance_id);
                
                        if ($value['rechange'] == 'SI')
                        {
                            $reason = $this->tagsPrepare($value['reason']);
                            $this->tagsSave($reason, TagReason::class);
                            
                            if ($value['waste'] == 'SI')
                            {
                                $disponible->state = 'Desechado';
                                $disponible->location_id = $request->location_id;
                                $disponible->element_balance_id = $element_balance->id;
                                $disponible->save();

                                $desecho = new EppWastes;
                                $desecho->company_id = $this->company;
                                $desecho->employee_id = $request->employee_id;
                                $desecho->element_id = $disponible->id;
                                $desecho->location_id = $request->location_id;
                                $desecho->code_element = $disponible->hash;
                                $desecho->save();

                                $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;
                            }
                            else
                            {
                                $disponible->state = 'Disponible';
                                $disponible->element_balance_id = $element_balance->id;
                                $disponible->location_id = $request->location_id;
                                $disponible->save();

                                $element_balance->quantity_available = $element_balance->quantity_available + 1;
                                $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;
                            }

                            array_push($elements_sync, $disponible->id);

                            $new_product = ElementBalanceSpecific::where('hash', $value['code_new'])->first();

                            if ($new_product->state = 'Disponible')
                            {
                                $new_product->state = 'Asignado';
                                $new_product->location_id = $request->location_id;
                                $new_product->element_balance_id = $element_balance->id;
                                $new_product->save();

                                $change = [
                                    'code_new' => $new_product->id,
                                    'code_old' => $disponible->id,
                                    'element_id' => $element->id,
                                    'reason' => $reason->implode(',')
                                ];

                                array_push($elements_change, $change);

                                $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;
                                $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;

                                array_push($elements_sync_rechange, $new_product->id);
                            }
                        }
                        else
                        {
                            $disponible->state = 'Disponible';
                            $disponible->location_id = $request->location_id;
                            $disponible->element_balance_id = $element_balance->id;
                            $disponible->save();

                            $element_balance->quantity_available = $element_balance->quantity_available + 1;
                            $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                            $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;

                            array_push($elements_sync, $disponible->id);
                        }
                    }
                    else
                    {
                        $codigos = explode(',' , $value['code']);

                        $count_codes = COUNT($codigos);

                        $quantity_return = $value['quantity_return'];
                        \Log::info($value['quantity_return']);

                        if ($quantity_return > $value['quantity'])
                        {
                            return $this->respondWithError('No puede regresar una cantidad superior a la asignada del elemento ' . $element->name);
                        }
                        else if ($value['quantity_return'] == '' || $value['quantity_return'] == 0)
                        {
                            return $this->respondWithError('Debe colocar una cantidad valida en el elemento ' . $element->name);
                        }

                        $codes_returns = [];

                        foreach ($codigos as $key => $code) 
                        {
                            $key = $key + 1;

                            if ($key <= $quantity_return)
                                array_push($codes_returns, $code);
                        }
                        
                        $count_codes_returns = COUNT($codes_returns);

                        $balance_id = ElementBalanceSpecific::whereIn('hash', $codes_returns)->where('state', 'Asignado')->first();

                        $balance_origen = ElementBalanceLocation::find($balance_id->element_balance_id);

                        if ($value['waste'] == 'SI')
                        {
                            if ($value['quantity_waste'] <= $count_codes_returns)
                            {
                                for ($i=1; $i <= $value['quantity_waste']; $i++) 
                                { 
                                    $new_product = ElementBalanceSpecific::whereIn('hash', $codes_returns)->where('state', 'Asignado')->first();

                                    $new_product->state = 'Desechado';
                                    $new_product->save();

                                    $desecho = new EppWastes;
                                    $desecho->company_id = $this->company;
                                    $desecho->employee_id = $request->employee_id;
                                    $desecho->element_id = $new_product->id;
                                    $desecho->location_id = $request->location_id;
                                    $desecho->code_element = $new_product->hash;
                                    $desecho->save();

                                    $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                    $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;

                                    array_push($elements_sync, $new_product->id);
                                }
                            }
                            else
                            {
                                return $this->respondWithError('No puede desechar una cantidad superior a la asignada del elemento ' . $element->name);
                            }
                        }

                        if ($value['rechange'] == 'SI')
                        {
                            $reason = $this->tagsPrepare($value['reason']);
                            $this->tagsSave($reason, TagReason::class);

                            if ($value['quantity_rechange'] > 0)
                            {
                                if ($value['quantity_rechange'] <= $count_codes_returns)
                                {
                                    for ($i=1; $i <= $value['quantity_rechange']; $i++) 
                                    { 
                                        $new_product = ElementBalanceSpecific::where('element_balance_id', $element_balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->first();

                                        if ($new_product)
                                        {
                                            $new_product->state = 'Asignado';
                                            $new_product->save();

                                            $element_balance->quantity_available = $element_balance->quantity_available - 1;
                                            $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;
                                        }
                                        else
                                        {
                                            if ($request->inventary == 'NO')
                                            {
                                                $hash = Hash::make($element_balance->element_id . str_random(30));
                                                $product = new ElementBalanceSpecific;
                                                $product->hash = $hash;
                                                $product->code = $hash;
                                                $product->element_balance_id = $element_balance->id;
                                                $product->location_id = $element_balance->location_id;
                                                $product->state = 'Asignado';
                                                $product->save();
                                            }
                                            else
                                                return $this->respondWithError('No existen unidades disponibles del elemento ' . $element->name);
                                        }


                                        $old_product = ElementBalanceSpecific::whereIn('hash', $codigos)->first();

                                        if ($old_product->state != 'Desechado')
                                        {
                                            $old_product->state = 'Disponible';
                                            $old_product->location_id = $request->location_id;
                                            $old_product->element_balance_id = $element_balance->id;
                                            $old_product->save();

                                            $element_balance->quantity_available = $element_balance->quantity_available + 1;
                                            $balance_origen->quantity_available = $balance_origen->quantity_available - 1;
                                        }                                        

                                        $change = [
                                            'code_new' => $new_product->id,
                                            'code_old' => $old_product->id,
                                            'element_id' => $element->id,
                                            'reason' => $reason->implode(',')
                                        ];

                                        array_push($elements_change, $change);

                                        array_push($elements_sync_rechange, $new_product->id);
                                    }

                                    $old_products = ElementBalanceSpecific::whereIn('hash', $codigos)->get();

                                    foreach ($old_products as $key => $old) 
                                    {
                                        if (!$old->state = 'Desechado')
                                        {
                                            $old->state = 'Disponible';
                                            $old->location_id = $request->location_id;
                                            $old->element_balance_id = $element_balance->id;
                                            $old->save();
                                        }

                                        array_push($elements_sync, $old->id);
                                    }
                                }
                                else
                                {
                                    return $this->respondWithError('No puede hacer un recambio de una cantidad superior a la asignada del elemento ' . $element->name);
                                }
                            }
                        }

                        if ($value['waste'] == 'NO' && $value['rechange'] == 'NO')
                        {
                            $old_products = ElementBalanceSpecific::whereIn('hash', $codes_returns)->get();

                            foreach ($old_products as $key => $old) 
                            {
                                $old->state = 'Disponible';
                                $old->location_id = $request->location_id;
                                $old->element_balance_id = $element_balance->id;
                                $old->save();

                                array_push($elements_sync, $old->id);
                            }

                            $element_balance->quantity_available = $element_balance->quantity_available + $value['quantity_return'];
                            $balance_origen->quantity_available = $balance_origen->quantity_available - $value['quantity_return'];
                        }
                    }
                }
            }

            $element_balance->save();
            $balance_origen->save();

            if (COUNT($elements_sync_rechange) > 0)
            {
                $new_delivery = new ElementTransactionEmployee;
                $new_delivery->employee_id = $request->employee_id;
                $new_delivery->position_employee_id = $employee->position->id;
                $new_delivery->type = 'Entrega';
                $new_delivery->observations = NULL;
                $new_delivery->location_id = $request->location_id;
                $new_delivery->company_id = $this->company;
                $new_delivery->user_id = $this->user->id;
                $new_delivery->save();

                $new_delivery->elements()->sync($elements_sync_rechange);
            }

            if (COUNT($elements_change) > 0)
            {
                foreach ($elements_change as $key => $change) 
                {
                    $rechange = new ChangeElement;
                    $rechange->transaction_employee_id = $returns->id;
                    $rechange->transaction_delivery_id = $new_delivery->id;
                    $rechange->element_id = $change['element_id'];
                    $rechange->element_specific_old_id = $change['code_old'];
                    $rechange->element_specific_new_id = $change['code_new'];
                    $rechange->company_id = $this->company;
                    $rechange->user_id = $this->user->id;
                    $rechange->reason = $change['reason'];
                    $rechange->save();
                }
            }

            $returns->elements()->sync($elements_sync);

            if ($request->firm_employee)
            {
                $image_64 = $request->firm_employee;
        
                $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
        
                $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
        
                $image = str_replace($replace, '', $image_64); 
        
                $image = str_replace(' ', '+', $image); 
        
                $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                $file = base64_decode($image);

                Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

                $returns->firm_employee = $imageName;


                if(!$returns->update())
                    return $this->respondHttp500();
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $returns->id);
            }

            foreach ($request->ids_transactions as $key => $value) 
            {
                $delivery = ElementTransactionEmployee::find($value);

                $delivery_disponibles = $delivery->elements()->where('state', 'Asignado')->get();

                if ($delivery && $delivery_disponibles->count() == 0)
                {
                    $returnDelivery = new ReturnDelivery;
                    $returnDelivery->transaction_employee_id = $returns->id;
                    $returnDelivery->delivery_id = $value;
                    $returnDelivery->save();

                    $delivery->state = 'Procesada';
                    $delivery->save();
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            //DB::rollback();
            return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('EPP - Devoluviones', 'Se creo una devolucion para el empleado '.$employee->name.' ');

        return $this->respondHttp200([
            'message' => 'Se creo la devolución'
        ]);
    }

    public function processFiles($files, $transaction_id)
    {
        $files_names_delete = [];

        foreach ($files as $keyF => $file) 
        {
            $create_file = true;

            if (isset($file['id']))
            {
                $fileUpload = FileTransactionEmployee::findOrFail($file['id']);

                if (isset($file['old_name']) && $file['old_name'] == $file['file'])
                    $create_file = false;
                else
                    array_push($files_names_delete, $file['old_name']);
            }
            else
            {
                $fileUpload = new FileTransactionEmployee();
                $fileUpload->transaction_employee_id = $transaction_id;
            }

            if ($create_file)
            {
                $file_tmp = $file['file'];
                $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                $file_tmp->storeAs($fileUpload->path_client(false), $nameFile, 's3');
                $fileUpload->file = $nameFile;
            }

            if (!$fileUpload->save())
                return $this->respondHttp500();
        }

         //Borrar archivos reemplazados
         foreach ($files_names_delete as $keyf => $file)
         {
             Storage::disk('s3')->delete($fileUpload->path_client(false)."/".$file);
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
            $transaction = ElementTransactionEmployee::findOrFail($id);

            $transaction->multiselect_employee = $transaction->employee->multiselect();
            $transaction->position_employee = $transaction->position->name;
            $transaction->multiselect_location = $transaction->location->multiselect();

            $multiselect = [];
            $elements_id = [];
            $elements = [];

            /*if ($transaction->type == 'Entrega')
            {*/
                $info = $this->employeeInfo($transaction->employee_id);

                $element_balance_id = [];
                
                foreach ($transaction->elements as $key => $value) 
                {
                    if (!in_array($value->element_balance_id, $element_balance_id))
                    {
                        array_push($element_balance_id, $value->element_balance_id);
                    }                    
                }

                $element_balance = ElementBalanceLocation::select('sau_epp_elements_balance_ubication.id')
                ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
                ->where('location_id', $transaction->location_id)
                ->where('sau_epp_elements.company_id', $this->company)
                ->get()
                ->toArray();

                $disponible = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.element_balance_id')
                ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id', 'sau_epp_elements_balance_specific.element_balance_id')
                ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
                ->where('sau_epp_elements_balance_specific.location_id', $transaction->location_id)
                ->where('sau_epp_elements_balance_specific.state', 'Disponible')
                ->whereIn('element_balance_id', $element_balance)
                ->get()
                ->toArray();

                $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible)
                ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
                ->where('location_id', $transaction->location_id)
                ->where('sau_epp_elements.company_id', $this->company)
                ->get()
                ->toArray();


                foreach ($element_disponibles as $key => $value) {
                    $ele = Element::find($value['element_id']);

                    array_push( $multiselect, $ele->multiselect());
                }

                $ids_balance_saltar = [];

                foreach ($element_balance_id as $key => $value) {
                    $element = $transaction->elements()->where('element_balance_id', $value)->get();

                    $codes = [];

                    foreach ($element as $key => $product) 
                    {                     
                        array_push($codes, $product->hash);
                    }

                    foreach ($element as $key => $e) 
                    {
                        $disponible_hash = ElementBalanceSpecific::select('id', 'hash')
                        ->where('location_id', $transaction->location_id)
                        ->where('state', 'Disponible')
                        ->where('element_balance_id', $e->element_balance_id)
                        ->orderBy('id')
                        ->pluck('hash', 'hash');

                        $options = $this->multiSelectFormat($disponible_hash);

                        if ($e->element->element->identify_each_element)
                        {
                            $rechange = ChangeElement::where('transaction_employee_id', $transaction->id)->where('element_specific_old_id', $e->id)->first();

                            if ($rechange)
                                $rechange_hash_new = ElementBalanceSpecific::find($rechange->element_specific_new_id);

                            $content = [
                                'id' => $e->id,
                                'id_ele' => $e->element->element->id,
                                'quantity' => '',
                                'type' => 'Identificable',
                                'code' => $e->hash,
                                'entry' => 'Manualmente',
                                'multiselect_element' => $e->element->element->multiselect(),
                                'wastes' => $e->state == 'Desechado' ? 'SI' : 'NO',
                                'rechange' => $rechange ? 'SI' : 'NO',
                                'code_new' => $rechange ? $rechange_hash_new->hash : '',
                                'reason' => $rechange ? $rechange->reason : ''
                            ];

                            array_push($elements, ['element' => $content, 'options' => $options]);
                        }
                        else
                        {
                            $rechange = ChangeElement::select('element_specific_old_id')->where('transaction_employee_id', $transaction->id)->get();

                            $id_rechange = [];

                            foreach ($rechange as $key => $re) 
                            {
                                array_push($id_rechange, $re->element_specific_old_id);
                            }
                                     
                            if (!in_array($e->element_balance_id, $ids_balance_saltar))
                            {
                                if (in_array($e->id, $id_rechange))
                                {
                                    $rechange_ele = ChangeElement::where('transaction_employee_id', $transaction->id)->where('element_specific_old_id', $e->id)->first();

                                    $content = [
                                        'id' => $e->id,
                                        'id_ele' => $e->element->element->id,
                                        'quantity' => $element->count(),
                                        'type' => 'No Identificable',
                                        'code' => implode(',', $codes),
                                        'entry' => 'Manualmente',
                                        'multiselect_element' => $e->element->element->multiselect(),
                                        'wastes' => $e->state == 'Desechado' ? 'SI' : 'NO',
                                        'rechange' => 'SI',
                                        'reason' => $rechange_ele ? $rechange_ele->reason : ''
                                    ];
                                }
                                else
                                {
                                    $content = [
                                        'id' => $e->id,
                                        'id_ele' => $e->element->element->id,
                                        'quantity' => $element->count(),
                                        'type' => 'No Identificable',
                                        'code' => implode(',', $codes),
                                        'entry' => 'Manualmente',
                                        'multiselect_element' => $e->element->element->multiselect(),
                                        'wastes' => $e->state == 'Desechado' ? 'SI' : 'NO',
                                        'rechange' => 'NO'
                                    ];
                                }

                                array_push($elements, ['element' => $content, 'options' => $options]);
                                array_push($ids_balance_saltar, $e->element_balance_id);
                            }
                        }
                        
                    }
                }
            /*}
            else
            {
                foreach ($transaction->elements as $key => $value)
                {   \Log::info($value);
                    $element = ElementBalanceSpecific::select('state')
                    ->where('location_id', $transaction->location_id)
                    ->where('id', $value->id)
                    ->where('element_balance_id', $value->element_balance_id)
                    ->first();

                    if ($value->element->element->identify_each_element)
                    {
                        $content = [
                            'id' => $value->id,
                            'id_ele' => $value->element->element->id,
                            'type' => 'Identificable',
                            'quantity' => 1,
                            'code' => $value->hash,
                            'multiselect_element' => $value->element->element->multiselect(),
                            'key' => (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20),
                            'wastes' => $element->state == 'Desechado' ? 'SI' : 'NO'
                        ];

                        array_push($elements, $content);
                        array_push( $multiselect, $value->element->element->multiselect());
                    }
                    else
                    {
                        $content = [
                            'id' => $value->id,
                            'id_ele' => $value->element->element->id,
                            'quantity' => 1,
                            'type' => 'No Identificable',
                            'code' => $value->hash,
                            'multiselect_element' => $value->element->element->multiselect(),
                            'key' => (rand(1,20000) + Carbon::now()->timestamp + rand(1,10000) + Carbon::now()->timestamp) * rand(1,20),
                            'wastes' => $element->state == 'Desechado' ? 'SI' : 'NO'
                        ]; 

                        array_push($elements, $content);
                        array_push( $multiselect, $value->element->element->multiselect());
                    }                                
                }
            }*/

            $transaction->firm_image = $transaction->path_image();
            $transaction->old_firm = $transaction->firm_employee;

            $transaction->elements_codes = $elements;

            $transaction->elements_id = [];
            $transaction->elementos = $multiselect;

            $get_deliverys = ReturnDelivery::select('delivery_id')->where('transaction_employee_id', $transaction->id)->get();
            
            $id_delivery_returns = [];

            if ($get_deliverys->count() > 0)
            {
                foreach ($get_deliverys as $key => $value) {
                    array_push($id_delivery_returns, $value->delivery_id);
                }
            }

            $transaction->ids_transactions = $id_delivery_returns;

            $transaction->element = [
                'multiselect' => $multiselect,
                'element' => $elements
            ];

            $transaction->inventary = '';

            $transaction->files = $this->getFiles($transaction->id);

            $transaction->delete = [
                'files' => [],
                'elements' => []
            ];

            return $this->respondHttp200([
                'data' => $transaction,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function getFiles($transaction)
    {
        $get_files = FileTransactionEmployee::where('transaction_employee_id', $transaction)->get();

        $files = [];

        if ($get_files->count() > 0)
        {               
            $get_files->transform(function($get_file, $index) {
                $get_file->key = Carbon::now()->timestamp + rand(1,10000);
                $get_file->id = $get_file->id;
                $get_file->name = $get_file->file;
                $get_file->old_name = $get_file->file;
                $get_file->path = $get_file->path_image();

                return $get_file;
            });

            $files = $get_files;
        }

        return $files;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\elements\ElementTransactionsRequest  $request
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function update(ElementTransactionsRequest $request, ElementTransactionEmployee $transaction)
    {
        Validator::make($request->all(), [
            "files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value) && 
                        $value->getClientMimeType() != 'image/png' && 
                        $value->getClientMimeType() != 'image/jpg' &&
                        $value->getClientMimeType() != 'image/jpeg')

                        $fail('Imagen debe ser PNG ó JPG ó JPEG');
                },
            ]
        ])->validate();

        if ($transaction->type == 'Entrega')
        {
            if ($request->inventary == 'SI')
                return $this->updateDelivery($request, $transaction);
            else
                return $this->updateDeliveryNotInventary($request, $transaction);
        }
        else
        {
            return $this->updateReturn($request, $transaction);
        }

    }

    public function updateDelivery(ElementTransactionsRequest $request, ElementTransactionEmployee $transaction)
    { 
        DB::beginTransaction();

        try
        {
            $employee = Employee::findOrFail($request->employee_id);

            $transaction->employee_id = $request->employee_id;
            $transaction->position_employee_id = $employee->position->id;
            $transaction->type = 'Entrega';
            $transaction->observations = $request->observations ? $request->observations : NULL;
            $transaction->location_id = $request->location_id;
            $transaction->edit_firm = $request->edit_firm;
            $transaction->firm_email = $request->firm_email;
            
            if(!$transaction->update()){
                return $this->respondHttp500();
            }

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                if (isset($value['id']))
                {
                    $disponible = ElementBalanceSpecific::find($value['id']);
                    $element = Element::find($value['id_ele']);

                    if ($value['type'] == 'Identificable')
                    {
                        if ($disponible->hash != $value['code'])
                        {
                            $disponible->state = 'Disponible';
                            $disponible->save();
                            $new = ElementBalanceSpecific::where('hash', $value['code'])->first();
                            $new->state = 'Asignado';
                            $new->save();

                            array_push($elements_sync, $new->id);
                        }
                        else
                            array_push($elements_sync, $disponible->id);
                    }
                    else
                    {

                        $trans = ElementBalanceSpecific::join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.element_id', 'sau_epp_elements_balance_specific.id')
                        ->join('sau_epp_transactions_employees', 'sau_epp_transactions_employees.id', 'sau_epp_transaction_employee_element.transaction_employee_id')
                        ->where('sau_epp_elements_balance_specific.id', $value['id'])
                        ->first();
                            
                        $transac_id = $trans->transaction_employee_id;
                        $id_balance = $trans->element_balance_id;

                        $elements = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.*')
                        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.element_id', 'sau_epp_elements_balance_specific.id')
                        ->join('sau_epp_transactions_employees', 'sau_epp_transactions_employees.id', 'sau_epp_transaction_employee_element.transaction_employee_id')
                        ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
                        ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
                        ->where('sau_epp_transactions_employees.id', $transac_id)
                        ->where('sau_epp_elements_balance_specific.element_balance_id', $id_balance)
                        ->where('sau_epp_elements.identify_each_element', false)
                        ->get();

                        if ($value['quantity'] > $elements->count())
                        {
                            $count = $value['quantity'] - $elements->count();

                            $elements_news = ElementBalanceSpecific::where('element_balance_id', $disponible->element_balance_id)
                            ->where('state', 'Disponible')
                            ->where('location_id', $transaction->location_id)
                            ->limit($count)->get();

                            if ($elements_news->count() < $count)
                                return $this->respondWithError('El elemento ' . $element->name . ' no cuenta con suficientes unidades disponibles');

                            foreach ($elements_news as $key => $value2) {
                                $value2->state = 'Asignado';
                                $value2->save();
                                array_push($elements_sync, $value2->id);
                            }

                            foreach ($elements as $key => $value) {
                                array_push($elements_sync, $value->id);
                            }

                            $element_balance = ElementBalanceLocation::find($id_balance);

                            $element_balance->quantity_available = $element_balance->quantity_available - $count;

                            $element_balance->quantity_allocated = $element_balance->quantity_allocated + $count;
                            
                            $element_balance->save();

                        }
                        else if ($value['quantity'] < $elements->count())
                        {
                            $count = $elements->count() - $value['quantity'];

                            $elements_delete = $elements->take($count)->all();

                            foreach ($elements_delete as $key => $value2) {
                                $value2->state = 'Disponible';
                                $value2->save();
                            }

                            $elements_restantes = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.*')
                            ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.element_id', 'sau_epp_elements_balance_specific.id')
                            ->join('sau_epp_transactions_employees', 'sau_epp_transactions_employees.id', 'sau_epp_transaction_employee_element.transaction_employee_id')
                            ->where('sau_epp_transactions_employees.id', $transac_id)
                            ->where('sau_epp_elements_balance_specific.state', 'Asignado')
                            ->get();

                            foreach ($elements_restantes as $key => $value) {
                                array_push($elements_sync, $value->id);
                            }

                            $element_balance = ElementBalanceLocation::find($id_balance);

                            $element_balance->quantity_available = $element_balance->quantity_available + $count;

                            $element_balance->quantity_allocated = $element_balance->quantity_allocated - $count;

                            $element_balance->save();
                        }
                        else
                        {
                            foreach ($elements as $key => $value) {
                                array_push($elements_sync, $value->id);
                            }
                        }
                    }
                }
                else
                {
                    $element = Element::find($value['id_ele']);

                    if ($element)
                    {
                        if ($element->identify_each_element)
                        {
                            $disponible = ElementBalanceSpecific::where('hash', $value['code'])->where('location_id', $request->location_id)->first();

                            if ($disponible->state != 'Disponible')
                                return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                            
                            $disponible->state = 'Asignado';
                            $disponible->save();

                            array_push($elements_sync, $disponible->id);

                            $element_balance = ElementBalanceLocation::find($disponible->element_balance_id);

                            $element_balance->quantity_available = $element_balance->quantity_available - 1;

                            $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;

                            $element_balance->save();
                        }
                        else
                        {
                            $element_balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                            $disponible = ElementBalanceSpecific::where('element_balance_id', $element_balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->limit($value['quantity'])->get();

                            if (!$disponible)
                                return $this->respondWithError('El elemento ' . $element->name . ' no se encuentra disponible en la ubicación seleccionada');
                            else if ($disponible->count() < $value['quantity'])
                                return $this->respondWithError('El elemento ' . $element->name . '  no cuenta con suficientes unidades disponibles');

                            foreach ($disponible as $key => $value2) {
                                $value2->state = 'Asignado';
                                $value2->save();
                                array_push($elements_sync, $value2->id);
                            }

                            $element_balance->quantity_available = $element_balance->quantity_available - $value['quantity'];

                            $element_balance->quantity_allocated = $element_balance->quantity_allocated + $value['quantity'];

                            $element_balance->save();
                        }
                    }
                }
            }

            $transaction->elements()->sync($elements_sync);

            if (isset($request->edit_firm) && $request->edit_firm == 'SI')
            {
                if ($request->firm_employee && $transaction->firm_email == 'Dibujar')
                {
                    if ($request->firm_employee != $transaction->firm_employee)
                    {
                        $image_64 = $request->firm_employee;
                
                        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
                
                        $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
                
                        $image = str_replace($replace, '', $image_64); 
                
                        $image = str_replace(' ', '+', $image); 
                
                        $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                        $file = base64_decode($image);

                        Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

                        $transaction->firm_employee = $imageName;

                        if(!$transaction->update())
                            return $this->respondHttp500();
                    }
                }
                else if ($request->firm_email == 'Email')
                {
                    if ($request->email_firm_employee != $transaction->email_firm_employee)
                    {
                        $transaction->email_firm_employee = $request->email_firm_employee;

                        if(!$transaction->update())
                            return $this->respondHttp500();

                        $recipient = new User(['email' => $transaction->email_firm_employee]);

                        NotificationMail::
                            subject('Sauce - Elementos de protección personal')
                            //->view('LegalAspects.legalMatrix.notifyUpdateLaws')
                            ->recipients($recipient)
                            ->message("Estimado $employee->name, usted tiene una solicitud de firma de una entrega de elementos de protección personal, para hacerlo ingrese a los links acontinuación: ")
                            ->module('epp')
                            ->buttons([['text'=>'Firmar', 'url'=>action('IndustrialSecure\EPP\TransactionFirmController@index', ['transaction' => $transaction->id, 'employee' => $employee->id])]])
                            //->with(['user' => $employee->name, 'urls'=>$url_email])
                            //->list(['<b>'.$delivery->type.'</b>'], 'ul')
                            ->company($this->company)
                            ->send();
                    }
                }
                /*if ($request->firm_employee != $transaction->firm_employee)
                {
                    Storage::disk('s3')->delete('industrialSecure/epp/transaction/files/'.$this->company.'/' . $transaction->firm_employee);

                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $transaction->firm_employee = $imageName;

                    if(!$transaction->update())
                        return $this->respondHttp500();
                }*/
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $transaction->id);
            }

            $this->deletedTemporal();
            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la transacción'
        ]);
    }

    public function updateDeliveryNotInventary(ElementTransactionsRequest $request, ElementTransactionEmployee $transaction)
    { 
        DB::beginTransaction();

        try
        {
            $employee = Employee::findOrFail($request->employee_id);

            $transaction->employee_id = $request->employee_id;
            $transaction->position_employee_id = $employee->position->id;
            $transaction->type = 'Entrega';
            $transaction->observations = $request->observations ? $request->observations : NULL;
            $transaction->location_id = $request->location_id;
            $transaction->edit_firm = $request->edit_firm;
            $transaction->firm_email = $request->firm_email;
            
            if(!$transaction->update()){
                return $this->respondHttp500();
            }

            $elements_sync = [];

            foreach ($request->elements_id as $key => $value) 
            {
                if (isset($value['id']))
                {
                    $disponible = ElementBalanceSpecific::find($value['id']);
                    $element = Element::find($value['id_ele']);

                    $trans = ElementBalanceSpecific::join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.element_id', 'sau_epp_elements_balance_specific.id')
                    ->join('sau_epp_transactions_employees', 'sau_epp_transactions_employees.id', 'sau_epp_transaction_employee_element.transaction_employee_id')
                    ->where('sau_epp_elements_balance_specific.id', $value['id'])
                    ->first();
                        
                    $transac_id = $trans->transaction_employee_id;
                    $id_balance = $trans->element_balance_id;

                    $elements = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.*')
                    ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.element_id', 'sau_epp_elements_balance_specific.id')
                    ->join('sau_epp_transactions_employees', 'sau_epp_transactions_employees.id', 'sau_epp_transaction_employee_element.transaction_employee_id')
                    ->join('sau_epp_elements_balance_ubication', 'sau_epp_elements_balance_ubication.id','sau_epp_elements_balance_specific.element_balance_id')
                    ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
                    ->where('sau_epp_transactions_employees.id', $transac_id)
                    ->where('sau_epp_elements_balance_specific.element_balance_id', $id_balance)
                    ->where('sau_epp_elements.identify_each_element', false)
                    ->get();

                    if ($value['quantity'] > $elements->count())
                    {
                        $count = $value['quantity'] - $elements->count();

                        for ($i=1; $i <= $count; $i++) { 
                            $hash = Hash::make($id_balance . str_random(30));
                            $product = new ElementBalanceSpecific;
                            $product->hash = $hash;
                            $product->code = $hash;
                            $product->element_balance_id = $id_balance;
                            $product->location_id = $request->location_id;
                            $product->state = 'Asignado';
                            $product->save();
    
                            array_push($elements_sync, $product->id);
                        }

                        foreach ($elements as $key => $value) {
                            array_push($elements_sync, $value->id);
                        }

                        $element_balance = ElementBalanceLocation::find($id_balance);

                        $element_balance->quantity_available = $element_balance->quantity_available - $count;

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated + $count;
                        
                        $element_balance->save();

                    }
                    else if ($value['quantity'] < $elements->count())
                    {
                        $count = $elements->count() - $value['quantity'];

                        $elements_delete = $elements->take($count)->all();

                        foreach ($elements_delete as $key => $value2) {
                            $value2->state = 'Disponible';
                            $value2->save();
                        }

                        $elements_restantes = ElementBalanceSpecific::select('sau_epp_elements_balance_specific.*')
                        ->join('sau_epp_transaction_employee_element', 'sau_epp_transaction_employee_element.element_id', 'sau_epp_elements_balance_specific.id')
                        ->join('sau_epp_transactions_employees', 'sau_epp_transactions_employees.id', 'sau_epp_transaction_employee_element.transaction_employee_id')
                        ->where('sau_epp_transactions_employees.id', $transac_id)
                        ->where('sau_epp_elements_balance_specific.state', 'Asignado')
                        ->get();

                        foreach ($elements_restantes as $key => $value) {
                            array_push($elements_sync, $value->id);
                        }

                        $element_balance = ElementBalanceLocation::find($id_balance);

                        $element_balance->quantity_available = $element_balance->quantity_available + $count;

                        $element_balance->quantity_allocated = $element_balance->quantity_allocated - $count;

                        $element_balance->save();
                    }
                    else
                    {
                        foreach ($elements as $key => $value) {
                            array_push($elements_sync, $value->id);
                        }
                    }
                }
                else
                {
                    $element = Element::find($value['id_ele']);

                    if ($element)
                    {
                        $element_balance = ElementBalanceLocation::where('element_id', $element->id)->where('location_id', $request->location_id)->first();

                    for ($i=1; $i <= $value['quantity']; $i++) { 
                        $hash = Hash::make($element_balance->element_id . str_random(30));
                        $product = new ElementBalanceSpecific;
                        $product->hash = $hash;
                        $product->code = $hash;
                        $product->element_balance_id = $element_balance->id;
                        $product->location_id = $element_balance->location_id;
                        $product->state = 'Asignado';
                        $product->save();

                        array_push($elements_sync, $product->id);
                    }

                    $element_balance->quantity_available = $element_balance->quantity_available - $value['quantity'];

                    $element_balance->quantity_allocated = $element_balance->quantity_allocated + $value['quantity'];

                    $element_balance->save();
                    }
                }
            }

            $transaction->elements()->sync($elements_sync);

            if (isset($request->edit_firm) && $request->edit_firm == 'SI')
            {
                if ($request->firm_employee && $transaction->firm_email == 'Dibujar')
                {
                    if ($request->firm_employee != $transaction->firm_employee)
                    {
                        $image_64 = $request->firm_employee;
                
                        $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
                
                        $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
                
                        $image = str_replace($replace, '', $image_64); 
                
                        $image = str_replace(' ', '+', $image); 
                
                        $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                        $file = base64_decode($image);

                        Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

                        $transaction->firm_employee = $imageName;

                        if(!$transaction->update())
                            return $this->respondHttp500();
                    }
                }
                else if ($request->firm_email == 'Email')
                {
                    if ($request->email_firm_employee != $transaction->email_firm_employee)
                    {
                        $transaction->email_firm_employee = $request->email_firm_employee;

                        if(!$transaction->update())
                            return $this->respondHttp500();

                        $recipient = new User(['email' => $transaction->email_firm_employee]);

                        NotificationMail::
                            subject('Sauce - Elementos de protección personal')
                            //->view('LegalAspects.legalMatrix.notifyUpdateLaws')
                            ->recipients($recipient)
                            ->message("Estimado $employee->name, usted tiene una solicitud de firma de una entrega de elementos de protección personal, para hacerlo ingrese a los links acontinuación: ")
                            ->module('epp')
                            ->buttons([['text'=>'Firmar', 'url'=>action('IndustrialSecure\EPP\TransactionFirmController@index', ['transaction' => $transaction->id, 'employee' => $employee->id])]])
                            //->with(['user' => $employee->name, 'urls'=>$url_email])
                            //->list(['<b>'.$delivery->type.'</b>'], 'ul')
                            ->company($this->company)
                            ->send();
                    }
                }
                /*if ($request->firm_employee != $transaction->firm_employee)
                {
                    Storage::disk('s3')->delete('industrialSecure/epp/transaction/files/'.$this->company.'/' . $transaction->firm_employee);

                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $transaction->firm_employee = $imageName;

                    if(!$transaction->update())
                        return $this->respondHttp500();
                }*/
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $transaction->id);
            }

            $this->deletedTemporal();
            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la transacción'
        ]);
    }

    public function updateReturn(ElementTransactionsRequest $request, ElementTransactionEmployee $transaction)
    {
        DB::beginTransaction();

        try
        {
            $employee = Employee::findOrFail($request->employee_id);

            $transaction->employee_id = $request->employee_id;
            $transaction->position_employee_id = $employee->position->id;
            $transaction->type = 'Devolucion';
            $transaction->observations = $request->observations ? $request->observations : NULL;
            $transaction->location_id = $request->location_id;
            
            if(!$transaction->update()){
                return $this->respondHttp500();
            }

            $elements_sync_rechange = [];

            $elements_sync = [];

            $elements_change = [];

            foreach ($request->elements_id as $key => $value) 
            {
                $disponible = ElementBalanceSpecific::find($value['id']);
                $element = Element::find($value['id_ele']);
                $element_balance = ElementBalanceLocation::find($disponible->element_balance_id);

                if ($element->identify_each_element)
                {
                    if ($disponible->hash == $value['code'])
                    {
                        if ($value['rechange'] == 'SI')
                        {
                            $reason = $this->tagsPrepare($value['reason']);
                            $this->tagsSave($reason, TagReason::class);

                            if ($value['waste'] == 'SI')
                            {
                                $disponible->state = 'Desechado';
                                $disponible->save();

                                $desecho = new EppWastes;
                                $desecho->company_id = $this->company;
                                $desecho->employee_id = $request->employee_id;
                                $desecho->element_id = $disponible->id;
                                $desecho->location_id = $request->location_id;
                                $desecho->code_element = $value['code'];
                                $desecho->save();

                                $element_balance->quantity_available = $element_balance->quantity_available - 1;
                                $element_balance->save();
                            }
                            else
                            {
                                $disponible->state = 'Disponible';
                                $disponible->save();
                            }

                            array_push($elements_sync, $disponible->id);

                            $new_product = ElementBalanceSpecific::where('hash', $value['code_new'])->first();

                            if ($new_product->state = 'Disponible')
                            {
                                $new_product->state = 'Asignado';
                                $new_product->save();

                                $change = [
                                    'code_new' => $new_product->id,
                                    'code_old' => $disponible->id,
                                    'element_id' => $element->id,
                                    'reason' => $reason->implode(',')
                                ];

                                array_push($elements_change, $change);

                                array_push($elements_sync_rechange, $new_product->id);
                            }
                        }
                        else
                        {
                            if ($disponible->state == 'Desechado')
                            {
                                if ($value['waste'] == 'NO')
                                {
                                    $disponible->state = 'Disponible';
                                    $disponible->save();

                                    $desecho = EppWastes::where('code_element', $value['code'])
                                    ->where('employee_id', $request->employee_id)->first();

                                    if ($desecho)
                                        $desecho->delete();

                                    $element_balance->quantity_available = $element_balance->quantity_available + 1;
                                    $element_balance->save();
                                }
                            }
                            else
                            {
                                if ($value['waste'] == 'SI')
                                {
                                    $disponible->state = 'Desechado';
                                    $disponible->save();

                                    $desecho = new EppWastes;
                                    $desecho->company_id = $this->company;
                                    $desecho->employee_id = $request->employee_id;
                                    $desecho->element_id = $disponible->id;
                                    $desecho->location_id = $request->location_id;
                                    $desecho->code_element = $value['code'];
                                    $desecho->save();

                                    $element_balance->quantity_available = $element_balance->quantity_available - 1;
                                    $element_balance->save();
                                }
                            }
                            
                            array_push($elements_sync, $disponible->id);
                        }
                    }
                }
                else
                {
                    $codigos = explode(',' , $value['code']);

                    $count_codes = COUNT($codigos);

                    if ($value['waste'] == 'SI')
                    {
                        if ($value['quantity_waste'] <= $count_codes)
                        {
                            for ($i=1; $i <= $value['quantity_waste']; $i++) 
                            { 
                                $new_product = ElementBalanceSpecific::whereIn('hash', $codigos)->where('location_id', $request->location_id)->where('state', 'Asignado')->limit($value['quantity_waste'])->get();

                                $new_product->state = 'Desechado';
                                $new_product->save();

                                $desecho = new EppWastes;
                                $desecho->company_id = $this->company;
                                $desecho->employee_id = $request->employee_id;
                                $desecho->element_id = $new_product->id;
                                $desecho->location_id = $request->location_id;
                                $desecho->code_element = $new_product->hash;
                                $desecho->save();

                                $element_balance->quantity_available = $element_balance->quantity_available - 1;
                                $element_balance->save();

                                array_push($elements_sync, $new_product->id);
                            }
                        }
                        else
                        {
                            return $this->respondWithError('No puede desechar una cantidad superior a la asignada del elemento ' . $element->name);
                        }
                    }
                    else
                    {
                        $old_products = ElementBalanceSpecific::whereIn('hash', $codigos)->get();

                        foreach ($old_products as $key => $old) 
                        {
                            $old->state = 'Disponible';
                            $old->save();

                            array_push($elements_sync, $old->id);
                        }
                    }

                    if ($value['rechange'] == 'SI')
                    {
                        if ($value['quantity_rechange'] > 0)
                        {
                            if ($value['quantity_rechange'] <= $count_codes)
                            {
                                $reason = $this->tagsPrepare($value['reason']);
                                $this->tagsSave($reason, TagReason::class);

                                for ($i=1; $i <= $value['quantity_rechange']; $i++) 
                                {
                                    $new_product = ElementBalanceSpecific::where('element_balance_id', $element_balance->id)->where('location_id', $request->location_id)->where('state', 'Disponible')->first();

                                    $new_product->state = 'Asignado';
                                    $new_product->save();

                                    $old_product = ElementBalanceSpecific::whereIn('hash', $codigos)->first();

                                    if ($old->state != 'Desechado')
                                    {
                                        $old->state = 'Disponible';
                                        $old->save();
                                    }

                                    $change = [
                                        'code_new' => $new_product->id,
                                        'code_old' => $old_product->id,
                                        'element_id' => $element->id,
                                        'reason' => $reason->implode(',')
                                    ];
    
                                    array_push($elements_change, $change);
                                    array_push($elements_sync_rechange, $new_product->id);
                                }

                                $old_products = ElementBalanceSpecific::whereIn('hash', $codigos)->get();

                                foreach ($old_products as $key => $old) 
                                {
                                    if ($old->state != 'Desechado')
                                    {
                                        $old->state = 'Disponible';
                                        $old->save();

                                        array_push($elements_sync, $old->id);
                                    }
                                }
                            }
                            else
                            {
                                return $this->respondWithError('No puede hacer un recambio de una cantidad superior a la asignada del elemento ' . $element->name);
                            }
                        }
                    }

                    if ($value['waste'] == 'NO' && $value['rechange'] == 'NO')
                    {
                        $old_products = ElementBalanceSpecific::whereIn('hash', $codigos)->get();

                        foreach ($old_products as $key => $old) 
                        {
                            $old->state = 'Disponible';
                            $old->save();

                            array_push($elements_sync, $old->id);
                        }
                    }
                }
            }            

            if (COUNT($elements_sync_rechange) > 0)
            {
                $new_delivery = new ElementTransactionEmployee;
                $new_delivery->employee_id = $request->employee_id;
                $new_delivery->position_employee_id = $employee->position->id;
                $new_delivery->type = 'Entrega';
                $new_delivery->observations = NULL;
                $new_delivery->location_id = $request->location_id;
                $new_delivery->company_id = $this->company;
                $new_delivery->user_id = $this->user->id;
                $new_delivery->save();

                $new_delivery->elements()->sync($elements_sync_rechange);
            }

            if (COUNT($elements_change) > 0)
            {
                foreach ($elements_change as $key => $change) 
                {
                    $rechange = new ChangeElement;
                    $rechange->transaction_employee_id = $transaction->id;
                    $rechange->transaction_delivery_id = $new_delivery->id;
                    $rechange->element_id = $change['element_id'];
                    $rechange->element_specific_old_id = $change['code_old'];
                    $rechange->element_specific_new_id = $change['code_new'];
                    $rechange->company_id = $this->company;
                    $rechange->user_id = $this->user->id;
                    $rechange->reason = $change['reason'];
                    $rechange->save();
                }
            }

            $transaction->elements()->sync($elements_sync);

            if (isset($request->edit_firm) && $request->edit_firm == 'SI')
            {
                if ($request->firm_employee != $transaction->firm_employee)
                {
                    Storage::disk('s3')->delete('industrialSecure/epp/transaction/returns/files/'.$this->company.'/' . $transaction->firm_employee);

                    $image_64 = $request->firm_employee;
            
                    $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
            
                    $replace = substr($image_64, 0, strpos($image_64, ',')+1); 
            
                    $image = str_replace($replace, '', $image_64); 
            
                    $image = str_replace(' ', '+', $image); 
            
                    $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

                    $file = base64_decode($image);

                    Storage::disk('s3')->put('industrialSecure/epp/transaction/returns/files/'.$this->company.'/' . $imageName, $file, 'public');

                    $transaction->firm_employee = $imageName;

                    if(!$transaction->update())
                        return $this->respondHttp500();
                }
            }

            if (count($request->files) > 0)
            {
                $this->processFiles($request->get('files'), $transaction->id);
            }

            $this->deleteData($request->get('delete'));

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la devolución'
        ]);
    }

    public function deleteData($delete)
    {
        foreach ($delete['files'] as $id)
        {
            $file_delete = FileTransactionEmployee::find($id);

            if ($file_delete)
            {
                $path = $file_delete->file;
                $file_delete->delete();
                Storage::disk('s3')->delete('industrialSecure/epp/transaction/files/' . $this->company . '/' . $path);
            }
        }

        foreach ($delete['elements'] as $id)
        {
            $element = ElementBalanceSpecific::find($id);

            if ($element)
            {
                $element->state = 'Disponible';
                $element->save();
            }
                
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Element  $element
     * @return \Illuminate\Http\Response
     */
    public function destroy(ElementTransactionEmployee $transaction)
    {
        Storage::disk('s3')->delete($transaction->path_client(false)."/".$transaction->firm_employee);

        $get_files = FileTransactionEmployee::where('transaction_employee_id', $transaction->id)->get();

        foreach ($get_files as $key => $value) {
            Storage::disk('s3')->delete($value->path_client(false)."/".$value->file);
        }

        if ($transaction->type == 'Entrega')
        {
            foreach ($transaction->elements as $key => $value) 
            {
                if ($value->state == 'Desechado')
                    continue;

                $value->state = 'Disponible';
                $value->save();

                $element_balance = ElementBalanceLocation::find($value->element_balance_id);

                $element_balance->quantity_available = $element_balance->quantity_available + 1;

                $element_balance->quantity_allocated = $element_balance->quantity_allocated - 1;

                $element_balance->save();
                
            }
        }
        else
        {
            foreach ($transaction->elements as $key => $value) 
            {
                if ($value->state == 'Desechado')
                    continue;

                $value->state = 'Asignado';
                $value->save();

                /*$desecho = EppWastes::where('code_element', $value->hash)
                ->where('employee_id', $value->employee_id)->first();

                if ($desecho)
                    $desecho->delete();*/

                $element_balance = ElementBalanceLocation::find($value->element_balance_id);

                $element_balance->quantity_available = $element_balance->quantity_available - 1;

                $element_balance->quantity_allocated = $element_balance->quantity_allocated + 1;

                $element_balance->save();
                
            }
        }

        if(!$transaction->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino la transacción'
        ]);
    }

    public function download(FileTransactionEmployee $file)
    {
      return Storage::disk('s3')->download($file->path_donwload());
    }

    public function employeeInfo($id)
    {
        try
        {
            $employee = Employee::findOrFail($id);
            $employee->position_employee = $employee->position->name;
            $employee->email = $employee->email;

            $elements = [];

            foreach ($employee->position->elements as $key => $value)
            {   $content = [
                    'id_ele' => $value->id,
                    'quantity' => '',
                    'type' => $value->identify_each_element ? 'Identificable' : 'No Identificable',
                ];

                array_push($elements, $content);
            }

            $employee->elements = $elements;

            return $this->respondHttp200([
                'data' => $employee,
            ]);
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function elementsLocation(Request $request)
    {
        try
        {
            $multiselect = [];
            $elements_id = [];
            $elements_no_disponibles = [];


            if ($request->inventary == 'SI')
            {
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
                ->where('sau_epp_elements.class_element', $request->class_element)
                ->get()
                ->toArray();

                $element_disponibles = ElementBalanceLocation::select('element_id')->whereIn('sau_epp_elements_balance_ubication.id', $disponible)
                ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
                ->where('location_id', $request->location_id)
                ->where('sau_epp_elements.company_id', $this->company)
                ->where('sau_epp_elements.class_element', $request->class_element)
                ->get()
                ->toArray();

                $ids_disponibles = [];

                foreach ($element_disponibles as $key => $value) {
                    $ele = Element::find($value['element_id']);

                    array_push( $multiselect, $ele->multiselect());
                    array_push( $ids_disponibles, $ele->id);
                }

                foreach ($request->position_elements as $key => $value) {

                    if (in_array($value['id_ele'], $ids_disponibles))
                    {
                        $ele = Element::find($value['id_ele']);

                        $element_balance = ElementBalanceLocation::where('location_id', $request->location_id)
                        ->where('element_id', $ele->id)
                        ->first();

                        $disponible = ElementBalanceSpecific::select('id', 'hash')
                        ->where('location_id', $request->location_id)
                        ->where('state', 'Disponible')
                        ->where('element_balance_id', $element_balance->id)
                        ->orderBy('id')
                        ->pluck('hash', 'hash');

                        $content = [
                            'id_ele' => $ele->id,
                            'quantity' => '',
                            'type' => $ele->identify_each_element ? 'Identificable' : 'No Identificable',
                            'code' => ''
                        ];

                        $options = $this->multiSelectFormat($disponible);

                        array_push( $elements_id, ['element' => $content, 'options' => $options]);
                    }
                    else
                    {
                        $ele = Element::find($value['id_ele']);
                        array_push($elements_no_disponibles, $ele->name);
                    }
                }
            }
            else
            {
                /*$element_disponibles = ElementBalanceLocation::select('element_id')
                ->join('sau_epp_elements', 'sau_epp_elements.id', 'sau_epp_elements_balance_ubication.element_id')
                ->where('location_id', $request->location_id)
                ->where('sau_epp_elements.company_id', $this->company)
                ->where('sau_epp_elements.class_element', $request->class_element)
                ->get()
                ->toArray();

                $ids_disponibles = [];

                foreach ($element_disponibles as $key => $value) {
                    $ele = Element::find($value['element_id']);

                    array_push( $multiselect, $ele->multiselect());
                    array_push( $ids_disponibles, $ele->id);
                }*/

                foreach ($request->position_elements as $key => $value) {

                    /*if (in_array($value['id_ele'], $ids_disponibles))
                    {*/
                        $ele = Element::find($value['id_ele']);
                        array_push( $multiselect, $ele->multiselect());

                        /*$element_balance = ElementBalanceLocation::where('location_id', $request->location_id)
                        ->where('element_id', $ele->id)
                        ->first();*/

                        $content = [
                            'id_ele' => $ele->id,
                            'quantity' => '',
                            'type' => $ele->identify_each_element ? 'Identificable' : 'No Identificable',
                            'code' => ''
                        ];

                        array_push( $elements_id, ['element' => $content, 'options' => []]);
                    //}
                }

                $eles = Element::where('company_id', $this->company)->get();

                foreach ($eles as $key => $value) {
                    array_push( $multiselect, $value->multiselect());
                }
                        
            }

            $data = [
                'multiselect' => $multiselect,
                'elements' => $elements_id,
                'elements_no_disponibles' => $elements_no_disponibles
            ];

            return $data;
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function elementInfo(Request $request)
    {
        $ele = Element::find($request->id);

        $element_balance = ElementBalanceLocation::where('location_id', $request->location_id)
        ->where('element_id', $request->id)
        ->first();

        if ($ele->identify_each_element)
        {
            $disponible = ElementBalanceSpecific::select('hash', 'hash')
            ->where('location_id', $request->location_id)
            ->where('state', 'Disponible')
            ->where('element_balance_id', $element_balance->id)
            ->get();

            foreach ($disponible as $key => $value) {
                $select = HashSelectDeliveryTemporal::where('hash', $value->hash)->exists();

                if ($select)
                {
                    $disponible = $disponible->reject(function ($value2, $key) use ($value) {
                        return $value2->hash == $value->hash;
                    });        
                }
            }

            $disponible = $disponible->pluck('hash', 'hash');
        }
        
        if ($ele->identify_each_element)
        {
            return [
                'type' => 'Identificable',
                'options' => $this->multiSelectFormat($disponible)
            ];
        }
        else
        {
            return [
                'type' => 'No Identificable',
                'options' => []
            ];
        }
    }

    public function hashSelected(Request $request)
    {
        try
        { 
            $disponible = ElementBalanceSpecific::where('location_id', $request->location_id)
            ->where('state', 'Disponible')
            ->where('hash', $request->select_hash)
            ->first();

            if ($disponible)
            {
                $selected = new HashSelectDeliveryTemporal;
                $selected->element_id = $request->id;
                $selected->location_id = $request->location_id;
                $selected->hash = $disponible->hash;
                $selected->user_id = $this->user->id;
                $selected->save();
            }
            else
            {
                return $this->respondWithError('El código seleccionado no existe o no se encuentra disponible');
            }
        } catch(Exception $e){
            \Log::info($e->getMessage());
            $this->respondHttp500();
        }
    }

    public function deletedTemporal()
    {
        $delete = HashSelectDeliveryTemporal::where('user_id', $this->user->id)->delete();
    }

    public function downloadPdf($id)
    {
        $delivery = $this->getDataExportPdf($id);

        PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.letterDeliveryEpp', ['delivery' => $delivery] );

        $pdf->setPaper('A4');

        return $pdf->download('Entrega EPP.pdf');
    }

    public function getDataExportPdf($id)
    {
        $typeElement = '';

        $delivery = ElementTransactionEmployee::findOrFail($id);

        $delivery->employee_name = $delivery->employee->name;
        $delivery->employee_identification = $delivery->employee->identification;

        $element_balance_id = [];
        $elements = [];
            
        foreach ($delivery->elements as $key => $value) 
        {
            if (!in_array($value->element_balance_id, $element_balance_id))
            {
                array_push($element_balance_id, $value->element_balance_id);
            }                    
        }

        $ids_balance_saltar = [];

        foreach ($element_balance_id as $key => $value) 
        {
            $element = $delivery->elements()->where('element_balance_id', $value)->get();

            foreach ($element as $key => $e) 
            {
                if ($key == 0)
                    $typeElement = $e->element->element->class_element;

                if ($e->element->element->identify_each_element)
                {
                    $content = [
                        'quantity' => 1,
                        'name' => $e->element->element->name
                    ];

                    array_push($elements, $content);
                }
                else
                {
                    if (!in_array($e->element_balance_id, $ids_balance_saltar))
                    {
                        $content = [
                            'quantity' => $element->count(),
                            'name' => $e->element->element->name
                        ];

                        array_push($elements, $content);
                        array_push($ids_balance_saltar, $e->element_balance_id);
                    }
                }
                
            }
        }

        if ($delivery->firm_employee)
            $delivery->firm = $delivery->path_image();
        else
            $delivery->firm = NULL;

        $delivery->elements = $elements;
        $delivery->user_name = $delivery->user_id ? $delivery->user->name : '';

        $company = Company::select('logo', 'name')->where('id', $this->company)->first();

        $logo = ($company && $company->logo) ? $company->logo : null;

        $delivery->logo = $logo;

        $delivery->text_company = $this->getTextLetterEpp($company->name, $typeElement);

        return $delivery;
    }

    public function getTextLetterEpp($company, $typeElement)
    {
        /*$text = ConfigurationCompany::select('value')->where('key', 'text_letter_epp')->first();

        $text_default = '<p>Yo, empleado (a) de '.$company .' hago constar que he recibido lo aquí relacionado y firmado por mí.  Doy fé además que he sido informado y capacitado en cuanto al uso de los elementos de protección personal y  frente a los riesgos que me protegen, las actividades y ocasiones en las cuales debo utilizarlos.  He sido informado sobre el procedimiento para su cambio y reposición en caso que sea necesario.

        *Me comprometo a hacer buen uso de todo lo recibido y a realizar el mantenimiento adecuado de los mismos.   Me comprometo a utilizarlos y cuidarlos conforme a las instrucciones recibidas y a la normativa legal vigente; así mismo me comprometo a informar a mi jefe inmediato cualquier defecto, anomalía o daño del elemento de protección personal (EPP) que pueda afectar o disminuir la efectividad de la protección.</p>';

        if (!$text)
            return $text_default;
        else
            return $text->value;*/

        if ($typeElement == 'Elemento de protección personal')
            $text = ConfigurationCompany::select('value')->where('key', 'text_letter_epp')->first();
        else if ($typeElement == 'Dotación')
            $text = ConfigurationCompany::select('value')->where('key', 'text_letter_dotation')->first();

        $text_default_epp = '<p>Yo, empleado (a) de '.$company .' hago constar que he recibido lo aquí relacionado y firmado por mí.  Doy fé además que he sido informado y capacitado en cuanto al uso de los elementos de protección personal y  frente a los riesgos que me protegen, las actividades y ocasiones en las cuales debo utilizarlos.  He sido informado sobre el procedimiento para su cambio y reposición en caso que sea necesario.</p>

        <p>*Me comprometo a hacer buen uso de todo lo recibido y a realizar el mantenimiento adecuado de los mismos. Me comprometo a utilizarlos y cuidarlos conforme a las instrucciones recibidas y a la normativa legal vigente; así mismo me comprometo a informar a mi jefe inmediato cualquier defecto, anomalía o daño del elemento de protección personal (EPP) que pueda afectar o disminuir la efectividad de la protección.</p>';

        $text_default_dotation = '<p>Yo, empleado (a) de '.$company .' hago constar que he recibido lo aquí relacionado y firmado por mí.  Doy fé además que he sido informado y capacitado en cuanto al uso de los elementos de dotación y  frente a los riesgos que me protegen, las actividades y ocasiones en las cuales debo utilizarlos.  He sido informado sobre el procedimiento para su cambio y reposición en caso que sea necesario.</p>

        <p>*Me comprometo a hacer buen uso de todo lo recibido y a realizar el mantenimiento adecuado de los mismos. Me comprometo a utilizarlos y cuidarlos conforme a las instrucciones recibidas y a la normativa legal vigente; así mismo me comprometo a informar a mi jefe inmediato cualquier defecto, anomalía o daño del elemento de dotación que pueda afectar o disminuir la efectividad del mismo.</p>';

        if (!$text)
        {
            if ($typeElement == 'Elemento de protección personal')                
                return $text_default_epp;
            else if ($typeElement == 'Dotación')
                return $text_default_dotation;
        }
        else
            return $text->value;
    }

    public function returnDelivery(ElementTransactionEmployee $transaction)
    {
        $element_returns = [];

        $returns = new ElementTransactionEmployee();
        $returns->employee_id = $transaction->employee_id;
        $returns->position_employee_id = $transaction->position_employee_id;
        $returns->type = 'Devolucion';
        $returns->observations = NULL;
        $returns->location_id = $transaction->location_id;
        $returns->company_id = $this->company;
        $returns->user_id = $this->user->id;
        
        if(!$returns->save())
            return $this->respondHttp500();

        foreach ($transaction->elements as $key => $value) 
        {
            $balance_origen = ElementBalanceLocation::find($value->element_balance_id);
            
            $value->state = 'Disponible';
            $value->save();

            $balance_origen->quantity_available = $balance_origen->quantity_available + 1;
            $balance_origen->quantity_allocated = $balance_origen->quantity_allocated - 1;

            $balance_origen->save();

            array_push($element_returns, $value->id);
        }

        $returns->elements()->sync($element_returns);

        $transaction->state = 'Procesada';
        $transaction->save();

        $returnDelivery = new ReturnDelivery;
        $returnDelivery->transaction_employee_id = $returns->id;
        $returnDelivery->delivery_id = $transaction->id;
        $returnDelivery->save();
    }

    public function export(Request $request)
    {
        try
        {
            $dates = [];

            if ($request->has('dateRange'))
            {
                $dates_request = explode('/', $request->dateRange);

                $date1 = Carbon::createFromFormat('D M d Y', $dates_request[0]);
                $date2 = Carbon::createFromFormat('D M d Y', $dates_request[1]);

                if ($dates_request && COUNT($dates_request) == 2)
                {
                    array_push($dates, $date1->format('Y-m-d 00:00:00'));
                    array_push($dates, $date2->format('Y-m-d 23:59:59'));
                }
            }

            $filtersType = $request->has('filtersType') ? $request->filtersType : [];
            
            $filters = [
                'dates' => $dates,
                'filtersType' => $filtersType
            ];

            DeliveryExportJob::dispatch($this->user, $this->company, $filters );
          
            return $this->respondHttp200();

        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }
}
