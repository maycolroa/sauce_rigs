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
use Validator;
use Exception;
use Hash;

use App\Traits\UtilsTrait;

class ElementBalanceInitialImport implements ToCollection, WithCalculatedFormulas
{
    use UtilsTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;
    private $secuence = [];
    private $template_error;
    private $count = [];

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
                foreach ($rows as $key => $row) 
                {  
                    if ($key > 0) //Saltar cabecera
                    {
                        if (isset($row[0]) && $row[0])
                        {
                            $this->checkElementIdent($row);
                        }
                    }
                }

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

                    Excel::store(new ElementIdentImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
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
                    ->message('Se produjo un error durante el proceso de importación de saldos. Contacte con el administrador')
                    ->module('epp')
                    ->event('Job: ElementBalanceInitialNotIdentImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
        }
    }

    private function checkElementIdent($row)
    {
        $data = [
            'codigo' => $row[0],
            'id_elemento' => $row[1],
            'id_ubicacion' => $row[2],
        ];

        $tipo = Element::where('code', $row[1]);
        $tipo->company_scope = $this->company_id;
        $tipo = $tipo->first();

        $location = Location::where('id', $row[2]);
        $location->company_scope = $this->company_id;
        $location = $location->first();

        if ($tipo && $location)
        {
            if (COUNT($this->count) > 0 && isset($this->count[$tipo->id][$location->id]))
            {
                $this->count[$tipo->id][$location->id] = $this->count[$tipo->id][$location->id] + 1;
            }
            else
            {
                $this->count[$tipo->id][$location->id] = 1;
            }
        }

        $hashs = ElementBalanceSpecific::select('hash')->get()->toArray();

        $hashs_validar = [];

        foreach ($hashs as $key => $value) {
            array_push($hashs_validar, $value['hash']);
        }

        $rules = [
            'id_elemento' => 'required',
            'id_ubicacion' => 'required',
            'codigo' => 'required|not_in:'.  implode(',', $hashs_validar)
        ];

        $validator = Validator::make($data, $rules,
        [
            'codigo.required' => 'El campo Código es requerido y debe ser unico, ya existe un elemento registrado con este código'

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
            if ($tipo) 
            {
                if ($location)
                {
                    $log_exist = ElementBalanceInicialLog::where('element_id', $tipo->id)->where('location_id', $data['id_ubicacion'])->exists();

                    $element = ElementBalanceLocation::updateOrCreate(
                        [
                            'element_id' => $tipo->id,
                            'location_id' => $data['id_ubicacion']
                        ],
                        [
                            'element_id' => $tipo->id,
                            'location_id' => $data['id_ubicacion'],
                            'quantity' => $this->count[$tipo->id][$data['id_ubicacion']],
                            'quantity_available' => $this->count[$tipo->id][$data['id_ubicacion']],
                            'quantity_allocated' => 0
                        ]
                    );

                    $product = new ElementBalanceSpecific;
                    $product->hash = $data['codigo'];
                    $product->element_balance_id = $element->id;
                    $product->location_id = $element->location_id;
                    $product->code = $data['codigo'];
                    $product->expiration_date = $tipo->expiration_date ? $data['vencimiento'] : NULL;
                    $product->save();

                    if (!$log_exist)
                    {
                        $log = new ElementBalanceInicialLog;
                        $log->element_id = $tipo->id;
                        $log->location_id = $data['id_ubicacion'];
                        $log->balance_inicial = true;
                        $log->save();
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

    /*private function validateDate($date)
    {
        try
        {
            $d = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date);
        }
        catch (\Exception $e) {
            return $date;
        }

        return $d ? $d : null;
    }*/
}