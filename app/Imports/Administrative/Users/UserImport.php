<?php

namespace App\Imports\Administrative\Users;

use Illuminate\Support\Collection;
use App\Models\Administrative\Roles\Role;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Users\GeneratePasswordUser;
use App\Models\General\Team;
use App\Traits\UserTrait;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Facades\Configuration;
use App\Models\General\Company;
use App\Exports\LegalAspects\Contracts\Contractor\UsersImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use App\Traits\ConfigurableFormTrait;
use Validator;
use Exception;

class UserImport implements ToCollection, WithCalculatedFormulas
{
    use UserTrait;
    use ConfigurableFormTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;
    private $role_id;
    private $formModel;

    public function __construct($company_id, $user, $role_id)
    {
      $this->user = $user;
      $this->role_id = $role_id;
      $this->company_id = $company_id;
    }

    public function collection(Collection $rows)
    {
        if ($this->sheet == 1)
        {
            
            try
            {
                $this->formModel = $this->getFormModel('form_employee', $this->company_id);

                foreach ($rows as $key => $row) 
                { 
                    if ($key > 0) //Saltar cabecera
                    {
                        if (isset($row[0]) && $row[0])
                        {
                            $this->checkUsers($row);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de usuarios')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de los usuarios finalizo correctamente')
                        ->module('users')
                        ->event('Job: UserImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/users_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    Excel::store(new UsersImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de usuarios')
                        ->recipients($this->user)
                        ->message('El proceso de importación de los usuarios finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('users')
                        ->event('Job: UserImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de usuarios')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de usuarios. Contacte con el administrador')
                    ->module('users')
                    ->event('Job: UserImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
        }
    }

    private function checkUsers($row)
    {
        $data = [
            'documento_usuario' => $row[0],
            'email_usuario' => $row[1],
            'nombre_usuario' => $row[2],
            'registro_medico' => ($this->formModel == 'hptu') ? $row[3] : null,
            'licencia_sst' => ($this->formModel == 'hptu') ? $row[4] : null
        ];

        $rules = [
            'nombre_usuario' => 'required|string',
            'documento_usuario' => 'required',
            'email_usuario' => 'required|email'     
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
            ///////////////////Creacion Usiario//////////////////

            $user = User::where('email', trim(strtolower($data['email_usuario'])))->first();

            $team = Team::where('name', $this->company_id)->first()->id;

            if (!$user)
            {
                $user = new User();
                $user->name = $data['nombre_usuario'];
                $user->email = $data['email_usuario'];
                $user->document = $data['documento_usuario'];
                $user->medical_record = $data['registro_medico'];
                $user->sst_license = $data['licencia_sst'];
                $user->api_token = Hash::make($data['documento_usuario'] . str_random(10));
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

                $user->attachRole($this->getIdRole($this->role_id), $team);
            }
            else
            {
                $user->companies()->attach($this->company_id);

                $company = Company::find($this->company_id);

                NotificationMail::
                    subject('Creación de usuario en sauce')
                    ->message("Estimado usuario, usted acaba de ser agregado como usuario en la empresa <b>{$company->name}</b>")
                    ->recipients($user)
                    ->module('users')
                    ->buttons([['text'=>'Ir a Sauce', 'url'=>url("/")]])
                    ->company($this->company_id)
                    ->send();

                $user->attachRole($this->getIdRole($this->role_id), $team);
            }

            return true;

        }
    }

    private function getIdRole($role)
    {
        $role = Role::find($role);

        if ($role)
            $role = $role->id;

        return $role;
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