<?php

namespace App\Exports\System\CustomerMonitoring;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use Carbon\Carbon;
use App\Models\General\Company;
use App\Models\LegalAspects\Contracts\FileUpload AS FileContract;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\EvaluationContract;
use App\Models\LegalAspects\Contracts\EvaluationFile;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ContractTemplateExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    public function __construct()
    {
      //
    }

    public function query()
    {
      $now = Carbon::now();

        $contract = ContractLesseeInformation::select(
            "sau_ct_information_contract_lessee.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_information_contract_lessee.created_at) = {$now->year} AND MONTH(sau_ct_information_contract_lessee.created_at) = {$now->month} THEN 1 ELSE 0 END) AS contract_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_information_contract_lessee.created_at) = {$now->year} THEN 1 ELSE 0 END) AS contract_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_ct_information_contract_lessee.company_id');

        $evaluations = EvaluationContract::select(
            "sau_ct_evaluation_contract.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_evaluation_contract.updated_at) = {$now->year} AND MONTH(sau_ct_evaluation_contract.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS eva_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_evaluation_contract.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS eva_anio")
        )
        ->withoutGlobalScopes()
        ->groupBy('sau_ct_evaluation_contract.company_id');

        $qualifications = ItemQualificationContractDetail::select(
            "sau_ct_information_contract_lessee.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_item_qualification_contract.updated_at) = {$now->year} AND MONTH(sau_ct_item_qualification_contract.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS cal_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_item_qualification_contract.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS cal_anio")
        )
        ->withoutGlobalScopes()
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_item_qualification_contract.contract_id')
        ->groupBy('sau_ct_information_contract_lessee.company_id');

        $list_files = FileContract::select(
            "sau_ct_information_contract_lessee.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_file_upload_contracts_leesse.updated_at) = {$now->year} AND MONTH(sau_ct_file_upload_contracts_leesse.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS file_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_file_upload_contracts_leesse.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS file_anio")
        )
        ->withoutGlobalScopes()
        ->join('sau_ct_file_upload_contract', 'sau_ct_file_upload_contract.file_upload_id', 'sau_ct_file_upload_contracts_leesse.id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_file_upload_contract.contract_id')
        ->groupBy('sau_ct_information_contract_lessee.company_id');

        
        $eva_files = EvaluationFile::select(
            "sau_ct_information_contract_lessee.company_id AS company_id",
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_evaluation_item_files.updated_at) = {$now->year} AND MONTH(sau_ct_evaluation_item_files.updated_at) = {$now->month} THEN 1 ELSE 0 END) AS file_e_mes"),
            DB::raw("SUM(CASE WHEN YEAR(sau_ct_evaluation_item_files.updated_at) = {$now->year} THEN 1 ELSE 0 END) AS file_e_anio")
        )
        ->withoutGlobalScopes()
        ->join('sau_ct_evaluation_contract', 'sau_ct_evaluation_contract.id', 'sau_ct_evaluation_item_files.evaluation_id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_evaluation_contract.contract_id')
        ->groupBy('sau_ct_information_contract_lessee.company_id');
        
        $companies = Company::selectRaw('
                sau_companies.id AS id,
                sau_companies.name AS name,
                MAX(sau_licenses.started_at) AS started_at,
                MAX(sau_licenses.ended_at) AS ended_at,
                IFNULL(t.contract_mes, 0) AS contract_mes,
                IFNULL(t.contract_anio, 0) AS contract_anio,
                IFNULL(t2.eva_mes, 0) AS eva_mes,
                IFNULL(t2.eva_anio, 0) AS eva_anio,
                IFNULL(t3.cal_mes, 0) AS cal_mes,
                IFNULL(t3.cal_anio, 0) AS cal_anio,
                IFNULL(t4.file_mes, 0) + IFNULL(t5.file_e_mes, 0) AS file_mes,
                IFNULL(t4.file_anio, 0) + IFNULL(t5.file_e_anio, 0) AS file_anio'
            )
            ->join('sau_licenses', 'sau_licenses.company_id', 'sau_companies.id')
            ->join('sau_license_module', 'sau_license_module.license_id', 'sau_licenses.id')
            ->leftJoin(DB::raw("({$contract->toSql()}) as t"), function ($join) {
                $join->on("t.company_id", "sau_companies.id");
            })
            ->mergeBindings($contract->getQuery())
            ->leftJoin(DB::raw("({$evaluations->toSql()}) as t2"), function ($join) {
                $join->on("t2.company_id", "sau_companies.id");
            })
            ->mergeBindings($evaluations->getQuery())
            ->leftJoin(DB::raw("({$qualifications->toSql()}) as t3"), function ($join) {
                $join->on("t3.company_id", "sau_companies.id");
            })
            ->mergeBindings($qualifications->getQuery())
            ->leftJoin(DB::raw("({$list_files->toSql()}) as t4"), function ($join) {
                $join->on("t4.company_id", "sau_companies.id");
            })
            ->mergeBindings($list_files->getQuery())
            ->leftJoin(DB::raw("({$eva_files->toSql()}) as t5"), function ($join) {
                $join->on("t5.company_id", "sau_companies.id");
            })
            ->mergeBindings($eva_files->getQuery())
            ->whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])
            ->where('sau_license_module.module_id', 14)
            ->groupBy('sau_companies.id');

      return $companies;
    }

    public function map($data): array
    {
      $values = [
        $data->name,
        $data->started_at,
        $data->ended_at,
        $data->contract_mes,
        $data->contract_anio,
        $data->eva_mes,
        $data->eva_anio,
        $data->cal_mes,
        $data->cal_anio,
        $data->file_mes,
        $data->file_anio
      ];

      return $values;
    }

    public function headings(): array
    {
      $columns = [
        'Compañia',
        'Fecha inicio licencia',
        'Fecha fin licencia',
        'Contratistas creados este mes',
        'Contratistas creados este año',
        'Evaluaciones creadas o modificadas este mes',
        'Evaluaciones creadas o modificadas este año',
        'Items de lista de chequeo califiados este mes',
        'Items de lista de chequeo califiados este año',
        'Archivos cargados este mes',
        'Archivos cargados este año'
      ];

      return $columns;
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:R1',
          [
            'alignment' => [
              'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
              'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'font' => [
               'name' => 'Arial', 
               'bold' => true,
            ]
          ]
      );
    }

    /**
     * @return string
    */
    public function title(): string
    {
      return 'Contratistas';
    }
}


