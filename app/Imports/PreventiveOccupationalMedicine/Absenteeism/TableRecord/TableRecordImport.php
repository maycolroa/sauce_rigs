<?php

namespace App\Imports\PreventiveOccupationalMedicine\Absenteeism\TableRecord;

use Illuminate\Support\Collection;
use App\Models\PreventiveOccupationalMedicine\Absenteeism\Table;
use App\Exports\PreventiveOccupationalMedicine\Absenteeism\TableRecordImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Administrative\Users\User;
use App\Models\General\Company;
use Exception;
use DB;

class TableRecordImport implements ToCollection, WithChunkReading, WithHeadingRow, WithMultipleSheets
{
    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $key_row = 2;
    private $table;
    private $columns = [];

    public function __construct($company_id, $user, $table)
    {
        $this->user = $user;
        $this->company_id = $company_id;

        $this->table = Table::where('id', $table);
        $this->table->company_scope = $this->company_id;
        $this->table = $this->table->first();
        $this->columns = $this->table->columns->get('columns');
        
        DB::table($this->table->table_name)->truncate();
    }

    public function chunkSize(): int
    {
        return 2000;
    }

    public function sheets(): array
    {
        return [
            0 => $this
        ];
    }

    public function collection(Collection $rows)
    {
        try
        {
            $inserts = [];
            
            foreach ($rows as $key => $row) 
            {
                $data = [];

                foreach ($this->columns as $column)
                {
                    if (isset($row[$column]) && $row[$column])
                        $data[$column] = $row[$column];
                }

                if (COUNT($data) > 0)
                {
                    //$inserts[] = $data;
                    try
                    {
                        
                        DB::table($this->table->table_name)->insert($data);

                    } catch (Exception $e) {
                        $this->setError($e->getMessage());
                        $this->setErrorData($row);
                    }
                }
            }

            /*if (COUNT($inserts) > 0)
            {
                foreach (array_chunk($inserts, 2) as $t)
                {
                    DB::table($this->table->table_name)->insert($t);
                }
            }*/

            if (COUNT($this->errors) == 0)
            {
                NotificationMail::
                    subject('Importación de Ausentismo - Datos')
                    ->recipients($this->user)
                    ->message('El proceso de importación de todos los registros finalizo correctamente')
                    ->module('absenteeism')
                    ->event('Job: TableRecordImportJob')
                    ->company($this->company_id)
                    ->send();

                $superadmin_notify = (new User(['email'=> 'mroat0@gmail.com']));     
                $company = Company::find($this->company_id);     
                
                if ($superadmin_notify && $company)
                {
                    NotificationMail::
                        subject('Carga de información en tabla ausentismo')
                        ->message("Se han agregado o modificado registros en la tabla {$this->table->name}, perteneciente a la compañia {$company->name} por el usuario {$this->user->name} - {$this->user->email}")
                        ->recipients($superadmin_notify)
                        ->module('absenteeism')
                        ->company($this->company_id)
                        ->send();
                }
            }
            else
            {
                $nameExcel = 'export/1/users_errors_'.date("YmdHis").'.xlsx';

                \Log::info($this->errors);                    
                Excel::store(new TableRecordImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id, $this->table->id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                $paramUrl = base64_encode($nameExcel);
        
                NotificationMail::
                    subject('Importación de Ausentismo - Datos')
                    ->recipients($this->user)
                    ->message('El proceso de importación de ausentismo - datos finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                    ->subcopy('Este link es valido por 24 horas')
                    ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                    ->module('absenteeism')
                    ->event('Job: TableRecordImportJob')
                    ->company($this->company_id)
                    ->send();
            }
            
        } catch (\Exception $e)
        {
            DB::rollback();
            \Log::info($e->getMessage());
            NotificationMail::
                subject('Importación de Ausentismo - Datos')
                ->recipients($this->user)
                ->message('Se produjo un error durante el proceso de importación de ausentismo - datos. Por favor revise la estructura del archivo que coincida con la plantilla emitida por SAUCE y que la información suministrada este plasmada de forma correcta, siguiendo los estandares establecidos en esta, de estar bien todo lo anteriormente explicado por favor contacte con el administrador')
                ->module('absenteeism')
                ->event('Job: TableRecordImportJob')
                ->company($this->company_id)
                ->send();
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