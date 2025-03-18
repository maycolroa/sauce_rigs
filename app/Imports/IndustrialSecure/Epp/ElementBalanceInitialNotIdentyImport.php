<?php

namespace App\Imports\IndustrialSecure\Epp;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Facades\Configuration;
use App\Exports\IndustrialSecure\Epp\ElementIdentImportErrorExcel;
use App\Exports\IndustrialSecure\Epp\ElementNotImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\ElementBalanceInicialLog;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\Location;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Validator;
use Exception;
use Hash;

use App\Traits\UtilsTrait;

class ElementBalanceInitialNotIdentyImport implements ToCollection, WithCalculatedFormulas
{
    use UtilsTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;
    private $template_error;

    public function __construct($company_id, $user)
    {        
      $this->user = $user;
      $this->company_id = $company_id;
    }

    public function collection(Collection $rows)
    {
        if ($this->sheet == 1)
        {            
            try
            {
                \Log::info('comenzo la importacion de saldos');
                foreach ($rows as $key => $row) 
                {  
                    if ($key > 0) //Saltar cabecera
                    {
                        if (isset($row[0]) && $row[0])
                        {
                            $this->checkElementNotIdent($row);
                        }
                    }
                }

                \Log::info('termino la importacion de saldos');

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de saldos iniciales de elementos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de los saldos finalizo correctamente')
                        ->module('epp')
                        ->event('Job: ElementBalanceInitialNotIdentImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/elements_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);   

                    Excel::store(new ElementNotImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                        $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de saldos iniciales de elementos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de saldos finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('epp')
                        ->event('Job: ElementBalanceInitialNotIdentImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de saldos iniciales de elementos')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de saldos. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
                    ->module('epp')
                    ->event('Job: ElementBalanceInitialNotIdentImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
        }
    }

    private function checkElementNotIdent($row)
    {
        $detalles = [];
        $data = [
                'code' => $row[0],
                'id_ubicacion' => $row[1],
                'id' => $row[1],
                'cantidad' => $row[2],
        ];

        $rules = [
            'code' => [
                'required',
                Rule::exists('sau_epp_elements')->where(function ($query) use ($row) {
                    $query/*->where('code', $row[0])*/
                          ->where('company_id', $this->company_id);
                })
            ],
            'id' => [
                'required',
                Rule::exists('sau_epp_locations')->where(function ($query) use ($row) {
                    $query/*->where('id', $row[1])*/
                          ->where('company_id', $this->company_id);
                })
            ],
            'cantidad' => 'required'       
        ];
        $validator = Validator::make($data, $rules,
        [
            'code.required' => 'El valor ingresado en el campo Código no esta registrado'

        ]);

        if ($validator->fails())
        {
            foreach ($validator->messages()->all() as $value)
            {
                $this->setError($value);
            }

            $this->setErrorData($row);
            return null;
        }
        else 
        {
            $tipo = Element::where('code', $row[0]);
            $tipo->company_scope = $this->company_id;
            $tipo = $tipo->first();

            $location = Location::where('id', $row[1]);
            $location->company_scope = $this->company_id;
            $location = $location->first();

            if ($tipo) 
            {
                if ($location)
                {
                    $log_exist = ElementBalanceInicialLog::where('element_id', $tipo->id)
                        ->where('location_id', $data['id_ubicacion'])->exists();

                    if (!$log_exist)
                    {
                        $element = new ElementBalanceLocation();
                        $element->element_id = $tipo->id;
                        $element->location_id = $data['id_ubicacion'];
                        $element->quantity = $data['cantidad'];
                        $element->quantity_available = $data['cantidad'];
                        $element->quantity_allocated = 0;
                        $element->save();

                        for ($i=1; $i <= $data['cantidad']; $i++) {                             
                            //$hash = Hash::make($element->element_id . str_random(30));
                            $hash = $element->element_id.str_random(30);
                            array_push($detalles, [
                                'hash' => $hash,
                                'code' => $hash,
                                'element_balance_id' => $element->id,
                                'location_id' => $element->location_id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                            /*$product = new ElementBalanceSpecific;
                            $product->hash = $hash;
                            $product->code = $hash;
                            $product->element_balance_id = $element->id;
                            $product->location_id = $element->location_id;
                            $product->save();*/
                        }

                        $log = new ElementBalanceInicialLog;
                        $log->element_id = $tipo->id;
                        $log->location_id = $data['id_ubicacion'];
                        $log->balance_inicial = true;
                        $log->save();
                    }
                    else
                    {
                        $element = ElementBalanceLocation::where('element_id', $tipo->id)
                        ->where('location_id', $data['id_ubicacion'])->first();

                        if ($element)
                        {
                            $element->quantity = $element->quantity + $data['cantidad'];
                            $element->quantity_available = $element->quantity_available + $data['cantidad'];
                            $element->save();
                        }
                        else
                        {
                            $element = new ElementBalanceLocation();
                            $element->element_id = $tipo->id;
                            $element->location_id = $data['id_ubicacion'];
                            $element->quantity = $data['cantidad'];
                            $element->quantity_available = $data['cantidad'];
                            $element->quantity_allocated = 0;
                            $element->save();                    
                        }

                        for ($i=1; $i <= $data['cantidad']; $i++) { 
                            //$hash = Hash::make($element->element_id . str_random(30));
                            $hash = $element->element_id.str_random(30);
                            array_push($detalles, [
                                'hash' => $hash,
                                'code' => $hash,
                                'element_balance_id' => $element->id,
                                'location_id' => $element->location_id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]);
                            /*$hash = Hash::make($element->element_id . str_random(30));
                            $product = new ElementBalanceSpecific;
                            $product->hash = $hash;
                            $product->code = $hash;
                            $product->element_balance_id = $element->id;
                            $product->location_id = $element->location_id;
                            $product->save();*/
                        }

                        $log = new ElementBalanceInicialLog;
                        $log->element_id = $tipo->id;
                        $log->location_id = $data['id_ubicacion'];
                        $log->balance_inicial = true;
                        $log->save();
                    }

                    foreach (array_chunk($detalles, 2000) as $t)
                    {
                        \Log::info(2);
                        ElementBalanceSpecific::insert($t);
                    }
                }
                else
                {
                    $this->setError('El código de ubicación ingresado no existe en nuestra Base de datos');
                    $this->setErrorData($row);
    
                    return null;
                }
            }
            else
            {
                $this->setError('El código de elemento ingresado no existe en nuestra Base de datos');
                $this->setErrorData($row);

                return null;
            }


            return true;
        }
    }

    private function setError($message)
    {
        $this->errors[$this->key_row][] = ucfirst($message);
    }

    private function setErrorData($row)
    {
        $this->errors_data[] = $row;
        $this->key_row++;
    }

    private function validateDate($date)
    {
        try
        {
            $d = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
        }
        catch (\Exception $e) {
            return $date;
        }

        return $d ? $d : null;
    }
}