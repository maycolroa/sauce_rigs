<?php

namespace App\Exports\LegalAspects\LegalMatrix\Reports;

use App\Models\LegalAspects\LegalMatrix\Law;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ReportLawExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;
    protected $user;

    public function __construct($company_id, $user, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->user = $user;
    }

    public function query()
    {
      $laws = Law::selectRaw(
        "sau_lm_articles_fulfillment.id AS id,
         SUBSTRING(sau_lm_articles.description, 1, 20) AS article,
         sau_lm_laws_types.name AS type,
         sau_lm_laws.law_number AS law_number,
         sau_lm_laws.law_year AS law_year,
         sau_lm_system_apply.name AS 'system',
         IF(sau_lm_fulfillment_values.name IS NULL, 'Sin calificar', sau_lm_fulfillment_values.name) AS qualify,
         sau_lm_entities.name AS entity,
         sau_lm_articles_fulfillment.observations AS observations,
         sau_lm_articles_fulfillment.responsible AS responsible"
      )
      ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
      ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
      ->join('sau_lm_entities', 'sau_lm_entities.id', 'sau_lm_laws.entity_id')
      ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
      ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
      ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
      ->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
      ->leftJoin('sau_lm_fulfillment_values','sau_lm_fulfillment_values.id', 'sau_lm_articles_fulfillment.fulfillment_value_id')
      ->where('sau_lm_articles_fulfillment.company_id', $this->company_id)
      ->inLawTypes($this->filters['lawTypes'], $this->filters['filtersType']['lawTypes'])
      ->inRiskAspects($this->filters['riskAspects'], $this->filters['filtersType']['riskAspects'])
      ->inEntities($this->filters['entities'], $this->filters['filtersType']['entities'])
      ->inSstRisks($this->filters['sstRisks'], $this->filters['filtersType']['sstRisks'])
      ->inSystemApply($this->filters['systemApply'], $this->filters['filtersType']['systemApply'])
      ->inLawNumbers($this->filters['lawNumbers'], $this->filters['filtersType']['lawNumbers'])
      ->inLawYears($this->filters['lawYears'], $this->filters['filtersType']['lawYears'])
      ->inRepealed($this->filters['repealed'], $this->filters['filtersType']['repealed'])
      ->inResponsibles($this->filters['responsibles'], $this->filters['filtersType']['responsibles'])
      ->inInterests($this->filters['interests'], $this->filters['filtersType']['interests'])
      ->inState($this->filters['states'], $this->filters['filtersType']['states'])
      ->groupBy('id');

      $laws->company_scope = $this->company_id;
      $laws->user = $this->user->id;

      return $laws;
    }

    public function map($data): array
    {
      return [
        $data->article,
        $data->type,
        $data->law_number,
        $data->law_year,
        $data->system,
        $data->qualify,
        $data->entity,
        $data->observations,
        $data->responsible
      ];
    }

    public function headings(): array
    {
        return [
          'Número de artículo',
          'Tipo de Norma',
          'Número de Norma',
          'Año',
          'Sistema',
          'Cumplimiento',
          'Ente',
          'Observaciones',
          'Responsable'
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $event->sheet->styleCells(
        'A1:I1',
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
        return 'Reporte';
    }
}

