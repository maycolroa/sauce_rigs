<?php

namespace App\Imports\LegalAspects;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\LegalAspects\LegalMatrix\Law;
use App\Models\LegalAspects\LegalMatrix\Article;
use App\Models\LegalAspects\LegalMatrix\Entity;
use App\Models\LegalAspects\LegalMatrix\Interest;
use App\Models\LegalAspects\LegalMatrix\SystemApply;
use App\Models\LegalAspects\LegalMatrix\LawType;
use App\Models\LegalAspects\LegalMatrix\RiskAspect;
use App\Models\LegalAspects\LegalMatrix\SstRisk;
use App\Jobs\LegalAspects\LegalMatrix\SyncQualificationsCompaniesImportJob;
use App\Exports\LegalAspects\LegalMatrix\Laws\LawsImportErrorExcel;
use App\Facades\Mail\Facades\NotificationMail;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Validator;
use Exception;
use App\Traits\LegalMatrixTrait;

class LawImport implements ToCollection, WithCalculatedFormulas 
{
    use LegalMatrixTrait;

    private $company_id;
    private $user;
    private $errors = [];
    private $errors_data = [];
    private $sheet = 1;
    private $key_row = 2;
    private $law_exist;
    private $type;
    private $system_apply;
    private $risk_aspect;
    private $entity;
    private $risk_sst;
    private $interest;

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
                            $this->checkLaw($row, $key);
                        }
                    }
                }

                if (COUNT($this->errors) == 0)
                {
                    NotificationMail::
                        subject('Importación de leyes')
                        ->recipients($this->user)
                        ->message('El proceso de importación de todos los registros de las leyes finalizo correctamente')
                        ->module('legalMatrix')
                        ->event('Job: LawImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                else
                {
                    $nameExcel = 'export/1/laws_errors_'.date("YmdHis").'.xlsx';

                    \Log::info($this->errors);                    
                    Excel::store(new LawsImportErrorExcel(collect($this->errors_data), $this->errors, $this->company_id), $nameExcel, 'public',\Maatwebsite\Excel\Excel::XLSX);
                    $paramUrl = base64_encode($nameExcel);
            
                    NotificationMail::
                        subject('Importación de leyes')
                        ->recipients($this->user)
                        ->message('El proceso de importación de leyes finalizo correctamente, pero algunas filas contenian errores. Puede descargar el archivo con el detalle de los errores en el botón de abajo.')
                        ->subcopy('Este link es valido por 24 horas')
                        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                        ->module('legalMatrix')
                        ->event('Job: LawImportJob')
                        ->company($this->company_id)
                        ->send();
                }
                
            } catch (\Exception $e)
            {
                \Log::info($e->getMessage());
                NotificationMail::
                    subject('Importación de leyes')
                    ->recipients($this->user)
                    ->message('Se produjo un error durante el proceso de importación de matríz de peligros. Contacte con el administrador')
                    ->module('legalMatrix')
                    ->event('Job: LawImportJob')
                    ->company($this->company_id)
                    ->send();
            }

            $this->sheet++;
            
            //dd($this->errors);
        }
    }

    private function checkLaw($row, $fila)
    {
        $data = [
            'name' => $row[0],
            'number' => $row[1],
            'type' => strtolower($row[2]),
            'year' => $row[3],
            'system_apply' => $row[4],
            'description' => $row[5],
            'observation' => $row[6],
            'risk_aspect' => strtolower($row[7]),
            'entity' => $row[8],
            'risk_sst' => strtolower($row[9]),
            'repealed' => strtoupper($row[10]),
            'article_description' => $row[11],
            'article_interests' => $row[12],
            'article_repealed' => strtoupper($row[13]),
        ];

        $sst_values = SstRisk::selectRaw("LOWER(name) AS name")->pluck('name')->implode(',');
        $aspects_values = RiskAspect::selectRaw("LOWER(name) AS name")->pluck('name')->implode(',');
        $type_values = LawType::selectRaw("LOWER(name) AS name")->pluck('name')->implode(',');

        $rules = [
            'name' => 'required',
            'number' => 'required',
            'type' => "required|in:$type_values",
            'year' => 'required',
            'system_apply' => 'required',
            'description' => 'required',
            'risk_aspect' => "required|in:$aspects_values",
            'entity' => 'required',
            'risk_sst' => "required|in:$sst_values",
            'repealed' => 'required|in:SI,NO',
            'article_description' => 'required',
            'article_interests' => 'required',
            'article_repealed' => 'nullable|in:SI,NO'         
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
            $this->type = $this->checkType($data['type']);
            $this->system_apply = $this->checkSystem($data['system_apply']);
            $this->risk_aspect = $this->checkRiskAspect($data['risk_aspect']);
            $this->entity = $this->checkEntity($data['entity']);
            $this->risk_sst = $this->checkRiskSst($data['risk_sst']);
            
            $law = $this->checkLawExist($data['name'], $this->type, $data['number'], $data['year'],$data['description'], $data['observation'], $data['repealed']);

            $article = new Article;
            $article->description = $data['article_description'];
            $article->law_id = $law->id;
            $article->repealed = $law->repealed == 'SI' ? 'SI' : ($data['article_repealed'] == 'SI' ? $data['article_repealed'] : 'NO');
            $article->sequence = $law->sequence + 1;
            $article->save();

            $this->interest = $this->checkInterest(explode(",", $data['article_interests']));

            $article->interests()->sync($this->interest);

            SyncQualificationsCompaniesImportJob::dispatch($this->company_id);

            return true;
        }
    }

    private function checkType($name)
    {
        $type = LawType::where('name', $name)->first();

        return $type->id;
    }

     private function checkSystem($name)
    {
        $system_apply = SystemApply::query();
        $system_apply->company_scope = $this->company_id;
        $system_apply = $system_apply->firstOrCreate(
            ['name' => $name, 'company_id' => $this->company_id], 
            ['name' => $name, 'company_id' => $this->company_id]
        );

        return $system_apply->id;
    }

    private function checkLawExist($name, $type, $number, $year, $description, $observations, $repealed)
    {
        $law = Law::query();
        $law->company_scope = $this->company_id;
        $law = $law->firstOrCreate(
            ['name' => $name, 'law_type_id' => $type, 'law_number' => $number, 'law_year' => $year, 'company_id' => $this->company_id], 
            [
                'name' => $name,
                'law_type_id' => $type,
                'law_number' => $number,
                'law_year' => $year,
                'company_id' => $this->company_id,
                'system_apply_id' => $this->system_apply,
                'description' => $description,
                'observations' => $observations,
                'risk_aspect_id' => $this->risk_aspect,
                'entity_id' => $this->entity,
                'sst_risk_id' => $this->risk_sst,
                'repealed' => $repealed
            ]
        );

        $sequence = Article::where('law_id', $law->id)->max('sequence');
        $law->sequence = $sequence;

        return $law;
    }

    private function checkRiskAspect($name)
    {
        $aspect = RiskAspect::where('name', $name)->first();

        return $aspect->id;
    }

    private function checkEntity($name)
    {
        $entity = Entity::query();
        $entity->company_scope = $this->company_id;
        $entity = $entity->firstOrCreate(
            ['name' => $name, 'company_id' => $this->company_id], 
            ['name' => $name, 'company_id' => $this->company_id]
        );

        return $entity->id;
    }

    private function checkRiskSst($name)
    {
        $risk_sst = SstRisk::where('name', $name)->first();

        return $risk_sst->id;
    }

    private function setError($message)
    {
        $this->errors[$this->key_row][] = ucfirst($message);
    }

    private function checkInterest($interests)
    {
        $result = collect([]);

        foreach ($interests as $key => $interest)
        {
            $query = Interest::alls($this->company_id)
            ->where('name', trim($interest))
            ->orderBy('company_id', 'DESC')
            ->first();

            if (!$query)
            {
                $query = Interest::create([
                    'name' => trim($interest),
                    'company_id' => $this->company_id
                ]);
            }

            $result->push($query->id);
        }

        return $result;
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