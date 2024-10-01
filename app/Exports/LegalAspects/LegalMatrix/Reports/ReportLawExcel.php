<?php

namespace App\Exports\LegalAspects\LegalMatrix\Reports;

use App\Models\LegalAspects\LegalMatrix\Law;
use App\Models\LegalAspects\LegalMatrix\QualificationColorDinamic;
use App\Models\LegalAspects\LegalMatrix\LawRiskOpportunity;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use DB;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class ReportLawExcel implements FromQuery, WithMapping, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use RegistersEventListeners;

    protected $company_id;
    protected $filters;
    protected $user;
    protected $colors_company;

    public function __construct($company_id, $user, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->user = $user;
      $this->colors_company = QualificationColorDinamic::where('company_id', $this->company_id)->withoutGlobalScopes()->first();
    }

    public function query()
    {
      $laws = Law::selectRaw(
        "sau_lm_articles_fulfillment.id AS id,
         SUBSTRING(sau_lm_articles.description, 1, 24) AS article,
         sau_lm_laws.id AS law_id,
         sau_lm_laws_types.name AS type,
         sau_lm_laws.law_number AS law_number,
         sau_lm_laws.law_year AS law_year,
         sau_lm_system_apply.name AS 'system',
         IF(sau_lm_fulfillment_values.name IS NULL, 'Sin calificar', sau_lm_fulfillment_values.name) AS qualify,
         sau_lm_entities.name AS entity,
         sau_lm_articles_fulfillment.observations AS observations,
         sau_lm_articles_fulfillment.responsible AS responsible,
         sau_lm_articles_fulfillment.workplace AS workplace,
         sau_lm_articles.repealed AS derogado,
         GROUP_CONCAT(sau_lm_interests.name) AS intereses,
         sau_lm_sst_risks.name AS risk_sst,
         sau_lm_risks_aspects.name AS risk_aspects
         "
      )
      ->join('sau_lm_system_apply', 'sau_lm_system_apply.id', 'sau_lm_laws.system_apply_id')
      ->join('sau_lm_laws_types', 'sau_lm_laws_types.id', 'sau_lm_laws.law_type_id')
      ->join('sau_lm_entities', 'sau_lm_entities.id', 'sau_lm_laws.entity_id')
      ->join('sau_lm_articles', 'sau_lm_articles.law_id', 'sau_lm_laws.id')
      ->join('sau_lm_article_interest', 'sau_lm_article_interest.article_id', 'sau_lm_articles.id')
      ->join('sau_lm_company_interest','sau_lm_company_interest.interest_id', 'sau_lm_article_interest.interest_id')
      ->join('sau_lm_articles_fulfillment','sau_lm_articles_fulfillment.article_id', 'sau_lm_articles.id')
      ->leftJoin('sau_lm_fulfillment_values','sau_lm_fulfillment_values.id', 'sau_lm_articles_fulfillment.fulfillment_value_id')
      ->join('sau_lm_interests', 'sau_lm_interests.id', 'sau_lm_article_interest.interest_id')
      ->join('sau_lm_sst_risks', 'sau_lm_sst_risks.id', 'sau_lm_laws.sst_risk_id')
      ->join('sau_lm_risks_aspects', 'sau_lm_risks_aspects.id', 'sau_lm_laws.risk_aspect_id')
      ->where('sau_lm_articles_fulfillment.company_id', $this->company_id)
      ->where('sau_lm_articles_fulfillment.hide', DB::raw("'NO'"))
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
      ->groupBy('id')
      ->orderBy('law_number');

      $laws->company_scope = $this->company_id;
      $laws->user = $this->user->id;

      $colors = [
        'qualify' => $laws->get(),
        'colors' => $this->colors_company
      ];

      Sheet::macro('getColors', function (Sheet $sheet) use ($colors) {
        return $colors;
      });

      return $laws;
    }

    public function map($data): array
    {
      $law_risk = LawRiskOpportunity::where('company_id', $this->company_id)->where('law_id', $data->law_id)->first();

      $risk = $law_risk && ($law_risk->type == 'Riesgo' || $law_risk->type == 'Riesgo y oportunidad') ? 'SI' : 'NO';
      $oppor = $law_risk && ($law_risk->type == 'Oportunidad' || $law_risk->type == 'Riesgo y oportunidad') ? 'SI' : 'NO';
      $no_apl = $law_risk && $law_risk->type == 'No aplica' ? 'SI' : 'NO';

      return [
        $data->article,
        $data->type,
        $data->law_number,
        $data->law_year,
        $data->derogado,
        $data->system,
        $data->intereses,
        $data->risk_sst,
        $data->risk_aspects,
        $data->qualify,
        $data->entity,
        $data->observations,
        $data->responsible,
        $data->workplace,
        $risk,
        $oppor,
        $no_apl,
        $law_risk ? $law_risk->description : NULL
      ];
    }

    public function headings(): array
    {
        return [
          'Número de artículo',
          'Tipo de Norma',
          'Número de Norma',
          'Año',
          'Derogado',
          'Sistema',
          'Intereses',
          'Tema SST',
          'Tema Ambiental',
          'Cumplimiento',
          'Ente',
          'Observaciones',
          'Responsable',
          'Centro de trabajo',
          'Riesgo',
          'Oportunidad',
          'No aplica',
          'Descripcion'
        ];
    }

    public static function afterSheet(AfterSheet $event)
    {
      $report_colors = $event->sheet->getColors();

      $colors = [];

      if ($report_colors['colors'])
      {
        foreach ($report_colors['qualify'] as $index => $color)
        {

          if ($color->qualify == 'En Transición')
            $color->qualify = 'En Transicion';
          else if ($color->qualify == 'Pendiente reglamentación')
            $color->qualify = 'Pendiente reglamentacion';


          $columna = str_replace(" ", "_", strtolower($color->qualify));
          $number = $index + 2;

          $colors['J'.$number] = $report_colors['colors']["$columna"];
        }

        foreach ($colors as $cols => $color)
        {
          $event->sheet->styleCells(
            $cols,
              [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                    'rotation' => 90,
                    'startColor' => [
                        'rgb' => $color
                    ],
                    'endColor' => [
                        'rgb' => $color
                    ],
                  ],
              ]
          );
        }
      }

      $event->sheet->styleCells(
        'A1:AZ1',
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

