<?php

namespace App\Imports\IndustrialSecure\Epp;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Facades\Configuration;
use App\Exports\IndustrialSecure\Epp\ElementStockMinimunImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IndustrialSecure\Epp\ElementStockMinimun;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\Location;
use Validator;
use Exception;
use Hash;

use App\Traits\UtilsTrait;

class ElementStockMinimunImport implements ToCollection, WithCalculatedFormulas
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
                foreach ($rows as $key => $row) 
                {  
                    if ($key > 0) //Saltar cabecera
                    {
                        if (isset($row[0]) && $row[0])
                        {
                            $this->checkElementsStockMinimun($row);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de existencias minimas por ubicación de elementos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros finalizo correctamente')
                        ->module('epp')
                        ->event('Job: ElementImportStockMinimunJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/elements_stock_minimo_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);   

                    Excel::store(new ElementStockMinimunImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                        $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de existencias minimas por ubicación de elementos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de existencias minimas por ubicación finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('epp')
                        ->event('Job: ElementImportStockMinimunJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de existencias minimas por ubicación de elementos')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de existencias minimas por ubicación. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
                    ->module('epp')
                    ->event('Job: ElementImportStockMinimunJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
        }
    }

    private function checkElementsStockMinimun($row)
    {
        $data = [
                'id_elemento' => $row[0],
                'id_ubicacion' => $row[1],
                'cantidad' => $row[2],

            ];
            
        $tipo = Element::where('code', $row[0]);
        $tipo->company_scope = $this->company_id;
        $tipo = $tipo->first();
        $tipo->stock_minimun = true;
        $tipo->save();

        $location = Location::where('id', $row[1]);
        $location->company_scope = $this->company_id;
        $location = $location->first();

        $rules = [
            'id_elemento' => 'required|in:'.$tipo->code,
            'id_ubicacion' => 'required',
            'cantidad' => 'required'       
        ];
        $validator = Validator::make($data, $rules,
        [
            'id_elemento.required' => 'El valor ingresado en el campo Código no esta registrado'

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
                    $record = ElementStockMinimun::updateOrCreate(
                        [
                            "element_id" => $tipo->id,
                            "location_id" => $location->id
                        ],
                        [
                            'element_id' => $tipo->id,
                            'location_id' => $location->id,
                            'quantity' => $data['cantidad']
                        ]
                    );
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
}