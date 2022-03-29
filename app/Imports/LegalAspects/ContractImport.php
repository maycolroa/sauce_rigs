<?php

namespace App\Imports\LegalAspects;

use Illuminate\Support\Collection;
use App\Models\Administrative\Roles\Role;
use App\Models\LegalAspects\Contracts\CompanyLimitCreated;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\HighRiskType;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Users\GeneratePasswordUser;
use App\Models\General\Team;
use App\Traits\ContractTrait;
use App\Traits\UserTrait;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Facades\Configuration;
use App\Models\General\Company;
use App\Exports\LegalAspects\Contracts\Contractor\ContractsImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Validator;
use Exception;
use DB;

class ContractImport implements ToCollection, WithCalculatedFormulas
{
    use ContractTrait;
    use UserTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;
    private $keywords;
    const CLASS_RISK = [
        'clase de riesgo i' => 'Clase de riesgo I',
        'clase de riesgo ii' => 'Clase de riesgo II',
        'clase de riesgo iii' => 'Clase de riesgo III',
        'clase de riesgo iv' => 'Clase de riesgo IV',
        'clase de riesgo v' => 'Clase de riesgo V'
    ];

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
                            $this->checkContract($row);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de contratistas')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de los contratistas finalizo correctamente')
                        ->module('contracts')
                        ->event('Job: ContractImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/contracts_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    Excel::store(new ContractsImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de contratistas')
                        ->recipients($this->user)
                        ->message('El proceso de importación de los contratistas finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('contracts')
                        ->event('Job: ContractImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de contratistas')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de contratistas. Contacte con el administrador')
                    //->message($e->getMessage())
                    ->module('contracts')
                    ->event('Job: ContractImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkContract($row)
    {
        $data = [
            'nombre_usuario' => $row[0],
            'documento_usuario' => trim($row[1]),
            'email_usuario' => trim($row[2]),
            'tipo_de_empresa' => strtolower($row[3]),
            'clasificacion' => strtolower($row[4]),
            'nombre_empresa' => $row[5],
            'nit' => $row[6],
            'razon_social' => $row[7],
            'trabajo_alto_riesgo' => strtolower($row[8]),
            'tipo_trabajo_alto_riesgo' => array_map('trim', explode(",", strtolower($row[9]))),            
            'direccion' => $row[10],
            'telefono' => $row[11],
            'nombre_representante_legal' => $row[12],
            'nombre_encargado_sst' => $row[13],
            'nombre_encargado_ambiental' => $row[14],
            'actividad_economica_empresa' => $row[15],
            'arl' => $row[16],
            'numero_trabajadores' => $row[17],
            'clase_riesgo' => strtolower($row[18])
        ];

        $highRisk = HighRiskType::selectRaw("LOWER(name) AS name")->pluck('name')->implode(',');

        $rules = [
            'nombre_usuario' => 'required|string',
            'documento_usuario' => 'required',
            'email_usuario' => 'required|email',
            'tipo_de_empresa' => 'required|string|in:contratista,arrendatario',
            'nombre_empresa' => 'required',
            'nit' => 'required|numeric',
            'razon_social' => 'required|string',
            'trabajo_alto_riesgo' => 'required'     
        ];

        if ($data["tipo_de_empresa"] == 'contratista')
        {
            $rules = array_merge($rules,
                [
                    'clasificacion' => 'required|string|in:empresa,unidad de produccion agropecuaria,unidad de producción agropecuaria'
                ]);
        }

        if ($data["trabajo_alto_riesgo"] == 'si')
        {
            $rules = array_merge($rules,
                [
                    'tipo_trabajo_alto_riesgo' => "required|array|min:1",
                    "tipo_trabajo_alto_riesgo.*" => "nullable|in:$highRisk",
                ]);
        }

        if ($data["clase_riesgo"])
        {
            $rules = array_merge($rules,
                [
                    'clase_riesgo' => "nullable|string|in:clase de riesgo i,clase de riesgo ii,clase de riesgo iii,clase de riesgo iv,clase de riesgo v"
                ]);
        }


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
            if (!$this->checkLimit())
                $this->setError('Límite alcanzado..!! No puede crear más contratistas o arrendatarios hasta que inhabilite alguno de ellos.');
            else
            {
                //////////////////////////Creacion contratista/////////////////

                if ($data["clasificacion"] == "unidad de produccion agropecuaria" || $data["clasificacion"] == "unidad de producción agropecuaria")
                {
                    $data["clasificacion"] = "UPA";
                }
                else
                    $data["clasificacion"] = "Empresa";

                $contracts = new ContractLesseeInformation();
                $contracts->company_id = $this->company_id;
                $contracts->nit = $data['nit'];
                $contracts->type = ucfirst($data['tipo_de_empresa']);
                $contracts->classification = $data['clasificacion'];
                $contracts->business_name = $data['nombre_empresa'];
                $contracts->phone = $data['telefono'];
                $contracts->address = $data['direccion'];
                $contracts->legal_representative_name = $data['nombre_representante_legal'];
                $contracts->environmental_management_name = $data['nombre_encargado_ambiental'];
                $contracts->economic_activity_of_company = $data['actividad_economica_empresa'];
                $contracts->arl = $data['arl'];
                $contracts->SG_SST_name = $data['nombre_encargado_sst'];
                $contracts->risk_class = isset($this::CLASS_RISK[strtolower($data['clase_riesgo'])]) ? $this::CLASS_RISK[strtolower($data['clase_riesgo'])] : NULL;
                $contracts->number_workers = $data['numero_trabajadores'];
                $contracts->high_risk_work = ucfirst($data['trabajo_alto_riesgo']);
                $contracts->social_reason = $data['razon_social'];
                $contracts->save();

                $risks = $this->checkHighRiskWork($data['tipo_trabajo_alto_riesgo']);

                $contracts->highRiskType()->sync($risks);

                ///////////////////Creacion Usiario//////////////////

                $user = User::where('email', trim(strtolower($data['email_usuario'])))->first();

                $team = Team::where('name', $this->company_id)->first()->id;

                if (!$user)
                {
                    $user = new User();
                    $user->name = $data['nombre_usuario'];
                    $user->email = $data['email_usuario'];
                    $user->document = $data['documento_usuario'];
                    $user->save();

                    $user->companies()->sync($this->company_id);

                    $generatePasswordUser = new GeneratePasswordUser();

                    $generatePasswordUser->user_id = $user->id;
                    $generatePasswordUser->token = bcrypt($user->email.$user->document);
                    $generatePasswordUser->save();

                    NotificationMail::
                        subject('Creación de usuario en sauce')
                        ->message('Te damos la bienvenida a la plataforma, se ha generado un nuevo usuario para este correo, para establecer tu nueva contraseña, por favor dar click al siguiente enlace.')
                        ->recipients($user)
                        ->buttons([['text'=>'Establecer contraseña', 'url'=>url("/password/generate/".base64_encode($generatePasswordUser->token))]])
                        ->module('users')
                        ->subcopy('Este link sólo se puede utilizar una vez')
                        ->company($this->company_id)
                        ->send();

                    $user->attachRole($this->getIdRole($contracts->type), $team);
                }
                else
                {
                    $user->companies()->attach($this->company_id);

                    $company = Company::find($this->company_id);

                    NotificationMail::
                        subject('Creación de contratista en sauce')
                        ->message("Usted acaba de ser creado como contratista en la empresa <b>{$company->name}</b>, por favor ingrese a Sauce y seleccione esta empresa para que pueda ingresar su información.")
                        ->recipients($user)
                        ->module('contracts')
                        ->buttons([['text'=>'Ir a Sauce', 'url'=>url("/")]])
                        ->company($this->company_id)
                        ->send();

                    $user->attachRole($this->getIdRole($contracts->type), $team);
                }

                $contracts->users()->sync($user);

                return true;

            }

        }
    }

    private function getIdRole($role)
    {
        $role = Role::defined()
            ->where('name', $role)
            ->first();

        if ($role)
            $role = $role->id;

        return $role;
    }

    private function checkHighRiskWork($data)
    {
        $risks = collect($data)->map(function ($item, $key)
            {
                return ucfirst($item);
            })->toArray();

        $ids = HighRiskType::select('id')->whereIn('name', $risks)->get();

        return $ids;
    }

    private function checkLimit()
    {
        $limit = CompanyLimitCreated::select('value');
        $limit->company_scope = $this->company_id;
        $limit = $limit->first();

        if ($limit)
            $limit = $limit->value;
        else 
            $limit = 1000;

        $count_contracts = ContractLesseeInformation::where('active', 'SI');
        $count_contracts->company_scope = $this->company_id;
        $count_contracts = $count_contracts->count();

        if ($count_contracts < $limit)
            return true;

        return false;
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