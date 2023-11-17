<?php

namespace App\Imports\IndustrialSecure\Epp;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Facades\Configuration;
use App\Exports\IndustrialSecure\Epp\ElementImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Models\IndustrialSecure\Epp\Element;
use App\Models\IndustrialSecure\Epp\TagsMark;
use App\Models\IndustrialSecure\Epp\TagsType;
use Validator;
use Exception;

use App\Traits\UtilsTrait;

class ElementImport implements ToCollection, WithCalculatedFormulas
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
                            $this->checkElement($row);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de elementos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros finalizo correctamente')
                        ->module('epp')
                        ->event('Job: ElementImportJob')
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
                        subject('Importación de elementos')
                        ->recipients($this->user)
                        ->message('El proceso de importación de elementos finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('epp')
                        ->event('Job: ElementImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de elementos')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de elementos. Contacte con el administrador')
                    ->module('epp')
                    ->event('Job: ElementImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
        }
    }

    private function checkElement($row)
    {
        $data = [
            'codigo' => $row[0],
            'nombre' => $row[1],
            'tipo' => $row[2],
            'marca' => $row[3],
            'descripcion' => $row[4],
            'norma' => $row[5],
            'observaciones' => $row[6],
            'instrucciones' => $row[7],
            'estado' => $row[8],
            'reutilizable' => strtoupper($row[9]),
            //'identificar' => strtoupper($row[10]),
            'vencimiento' => strtoupper($row[10]),
            'dias_vencimiento' => $row[8]

        ];

        $codes_bbdd = Element::select('code')
        ->where('company_id', $this->company_id)
        ->get()
        ->toArray();

        $codes = [];

        foreach ($codes_bbdd as $key => $value) {
            array_push($codes, $value['code']);
        }

        $rules = [
            'codigo' => 'required|not_in:'.  implode(',', $codes),
            'nombre' => 'required',
            'tipo' => 'nullable',
            'marca' => 'nullable',
            'descripcion' => 'nullable',
            'norma' => 'nullable',
            'observaciones' => 'nullable',
            'instrucciones' => 'nullable',
            'estado' => 'required|in:Activo,Inactivo',
            'reutilizable' => 'required|in:SI,NO',
            //'identificar' => 'required|in:SI,NO',
            'vencimiento' => 'nullable|in:SI,NO',
            'dias_vencimiento' => 'required_if:vencimiento,SI'       
        ];

        $validator = Validator::make($data, $rules, [
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
            //TAGS
                $types = $this->tagsPrepareImport($data['tipo']);
                $mark = $this->tagsPrepareImport($data['marca']);

                $this->tagsSave($types, TagsType::class, $this->company_id);
                $this->tagsSave($mark, TagsMark::class, $this->company_id);
            //

                $element = new Element();
                $element->name = $data['nombre'];
                $element->code = $data['codigo'];
                $element->description = $data['descripcion'];
                $element->observations = $data['observaciones'];
                $element->operating_instructions = $data['instrucciones'];
                $element->applicable_standard = $data['norma'];
                $element->state = $data['estado'] == "Activo" ? true : false;
                $element->reusable = $data['reutilizable'] == "SI" ? true : false;
                $element->identify_each_element = false;
                $element->expiration_date = $data['vencimiento'] == "SI" ? true : false;
                $element->days_expired = $data['vencimiento'] == "SI" ? $data['dias_vencimiento'] : NULL;
                $element->company_id = $this->company_id;
                $element->type = $types->implode(',');
                $element->mark = $mark->implode(',');
                $element->save();

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