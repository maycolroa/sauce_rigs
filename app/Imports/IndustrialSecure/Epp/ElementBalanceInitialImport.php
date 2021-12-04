<?php

namespace App\Imports\IndustrialSecure\Epp;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Facades\Configuration;
use App\Exports\IndustrialSecure\Epp\ElementImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IndustrialSecure\Epp\ElementBalanceLocation;
use App\Models\IndustrialSecure\Epp\ElementBalanceInicialLog;
use App\Models\IndustrialSecure\Epp\ElementBalanceSpecific;
use App\Models\IndustrialSecure\Epp\Element;
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
                            $tipo = Element::find($row[0]);

                            if (!$tipo->identify_each_element)
                                $this->checkElementNotIdent($row);
                            else
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
                    Excel::store(new ElementImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
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

    private function checkElementNotIdent($row)
    {
        $data = [
                'id_elemento' => $row[0],
                'id_ubicacion' => $row[1],
                'cantidad' => $row[2],

            ];

        $rules = [
            'id_elemento' => 'required',
            'id_ubicacion' => 'required',
            'cantidad' => 'required'       
        ];

        $validator = Validator::make($data, $rules);

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

            $element = new ElementBalanceLocation();
            $element->element_id = $data['id_elemento'];
            $element->location_id = $data['id_ubicacion'];
            $element->quantity = $data['cantidad'];
            $element->quantity_available = $data['cantidad'];
            $element->quantity_allocated = 0;
            $element->save();

            

            for ($i=1; $i <= $data['cantidad']; $i++) { 
                \Log::info('entro' . $i);
                $product = new ElementBalanceSpecific;
                $product->hash = Hash::make($element->element_id . str_random(10));
                $product->element_balance_id = $element->id;
                $product->location_id = $element->location_id;
                $product->code = $element->id . $element->location_id . rand(1,10000);
                $product->save();
            }

            $log = new ElementBalanceInicialLog;
            $log->element_id = $data['id_elemento'];
            $log->balance_inicial = true;
            $log->save();

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
}