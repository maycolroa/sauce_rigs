<?php

namespace App\Exports\IndustrialSecure\RiskMatrix;

use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use App\Models\IndustrialSecure\RiskMatrix\Indicators;

use App\Traits\LocationFormTrait;
use App\Traits\UtilsTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class RiskMatrixExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use LocationFormTrait;
    use UtilsTrait;

    protected $company_id;
    protected $risk_matrix_id;
    protected $confLocation;
    protected $qualifications;
    protected $qualificationsValues;
    protected $configurations;
    protected $keywords;

    public function __construct($company_id, $risk_matrix_id)
    {
        $this->company_id = $company_id;
        $this->risk_matrix_id = $risk_matrix_id;
        $this->confLocation = $this->getLocationFormConfModule($this->company_id);
        $this->keywords = $this->getKeywordQueue($this->company_id);;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $selecs = [
            'sau_rm_risks_matrix.name AS name',
            'sau_rm_risks_matrix.participants AS participants',
            'sau_employees_regionals.name AS regional',
            'sau_employees_headquarters.name AS headquarter',
            'sau_employees_processes.name AS process',
            'sau_tags_processes.name AS macroprocess',
            'sau_employees_areas.name AS area',
            'sau_rm_sub_processes.name AS subprocess',
            'sau_rm_risk.name AS risk',
            'sau_rm_risk.category AS risk_category',
            'sau_rm_subprocess_risk.id as rm_id',
            'sau_rm_subprocess_risk.rm_subprocess_id',
            'sau_rm_subprocess_risk.risk_id',
            'sau_rm_subprocess_risk.risk_sequence',
            'sau_rm_subprocess_risk.nomenclature AS nom_risk',
            'sau_rm_subprocess_risk.economic',
            'sau_rm_subprocess_risk.quality_care_patient_safety',
            'sau_rm_subprocess_risk.reputational',
            'sau_rm_subprocess_risk.legal_regulatory',
            'sau_rm_subprocess_risk.environmental',
            'sau_rm_subprocess_risk.max_inherent_impact',
            'sau_rm_subprocess_risk.description_inherent_impact',
            'sau_rm_subprocess_risk.max_inherent_frequency',
            'sau_rm_subprocess_risk.description_inherent_frequency',
            'sau_rm_subprocess_risk.inherent_exposition',
            'sau_rm_subprocess_risk.controls_decrease',
            'sau_rm_subprocess_risk.nature',
            'sau_rm_subprocess_risk.evidence',
            'sau_rm_subprocess_risk.coverage',
            'sau_rm_subprocess_risk.documentation',
            'sau_rm_subprocess_risk.segregation',
            'sau_rm_subprocess_risk.control_evaluation',
            'sau_rm_subprocess_risk.percentege_mitigation',
            'sau_rm_subprocess_risk.max_residual_impact',
            'sau_rm_subprocess_risk.description_residual_impact',
            'sau_rm_subprocess_risk.max_residual_frequency',
            'sau_rm_subprocess_risk.description_residual_frequency',
            'sau_rm_subprocess_risk.residual_exposition',
            'sau_rm_subprocess_risk.max_impact_event_risk',
            'sau_rm_causes.cause',
            'sau_rm_cause_controls.controls',
            'sau_rm_cause_controls.number_control',
            'sau_rm_cause_controls.nomenclature AS nom_control',
            //'sau_rm_risk_indicators.indicator'
        ];

        $riskMatrix = RiskMatrix::
              join('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_rm_risks_matrix.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_rm_risks_matrix.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_rm_risks_matrix.employee_area_id')
            ->leftJoin('sau_tags_processes', 'sau_tags_processes.id', 'sau_rm_risks_matrix.macroprocess_id')
            ->join('sau_rm_risk_matrix_subprocess', 'sau_rm_risk_matrix_subprocess.risk_matrix_id', 'sau_rm_risks_matrix.id')
            ->join('sau_rm_sub_processes', 'sau_rm_sub_processes.id', 'sau_rm_risk_matrix_subprocess.sub_process_id')
            ->join('sau_rm_subprocess_risk', 'sau_rm_subprocess_risk.rm_subprocess_id', 'sau_rm_risk_matrix_subprocess.id')
            ->join('sau_rm_risk', 'sau_rm_risk.id', 'sau_rm_subprocess_risk.risk_id')
            ->leftJoin('sau_rm_causes', 'sau_rm_causes.rm_subprocess_risk_id', 'sau_rm_subprocess_risk.id')
            ->leftJoin('sau_rm_cause_controls', 'sau_rm_cause_controls.rm_cause_id', 'sau_rm_causes.id')
            //->leftJoin('sau_rm_risk_indicators', 'sau_rm_risk_indicators.rm_subprocess_risk_id', 'sau_rm_subprocess_risk.id')
            ->where('sau_rm_risks_matrix.id', $this->risk_matrix_id);

        $riskMatrix->selectRaw(implode(",", $selecs));

        $riskMatrix->company_scope = $this->company_id;

        return $riskMatrix->get();
    }

    public function map($data): array
    {
        $indicators = Indicators::select('indicator')->where('rm_subprocess_risk_id', $data->rm_id)->pluck('indicator');

        $values = [$data->name];

        if ($this->confLocation['regional'] == 'SI')
            array_push($values, $data->regional);

        if ($this->confLocation['headquarter'] == 'SI')
            array_push($values, $data->headquarter);

        if ($this->confLocation['process'] == 'SI')
        {
            array_push($values, $data->process);
            array_push($values, $data->macroprocess);
        }

        if ($this->confLocation['area'] == 'SI')
            array_push($values, $data->area);

        $values = array_merge($values, [
            $data->participants,
            $data->subprocess,
            $data->risk_category,
            $data->nom_risk,
            (String) $data->risk_sequence,
            $data->risk,
            $data->cause,
            (String) $data->economic,
            (String) $data->quality_care_patient_safety,
            (String) $data->reputational,
            (String) $data->legal_regulatory,
            (String) $data->environmental,
            (String) $data->max_inherent_impact,
            $data->description_inherent_impact,
            (String) $data->max_inherent_frequency,
            $data->description_inherent_frequency,
            (String) $data->inherent_exposition,
            (String) $data->number_control,
            $data->nom_control,
            $data->controls,
            $data->controls_decrease,
            $data->nature,
            $data->evidence,
            $data->coverage,
            $data->documentation,
            $data->segregation,
            $data->control_evaluation,
            $data->percentege_mitigation,
            (String) $data->max_residual_impact,
            $data->description_residual_impact,
            (String) $data->max_residual_frequency,
            $data->description_residual_frequency,
            (String) $data->residual_exposition,
            $data->max_impact_event_risk,
            $indicators->implode(',')
        ]);

        return $values;
    }

    public function headings(): array
    {        
        $columns = ['Nombre'];

        if ($this->confLocation['regional'] == 'SI')
            array_push($columns, $this->keywords['regional']);

        if ($this->confLocation['headquarter'] == 'SI')
            array_push($columns, $this->keywords['headquarter']);

        if ($this->confLocation['process'] == 'SI')
        {
            array_push($columns, $this->keywords['process']);
            array_push($columns, 'Macroproceso');
        }

        if ($this->confLocation['area'] == 'SI')
            array_push($columns, $this->keywords['area']);

        $columns = array_merge($columns, [
            'Participantes',
            'Sub-Proceso',
            'Categoría del Riesgo',
            'Nomenclatura al Riesgo',
            'Código',
            'Evento de Riesgo',
            'Causas',
            'Económico',
            'Cal. en la atención y seg. del paciente',
            'Reputacional',
            'Legal Regulatorio',
            'Ambiental',
            'Max. Impacto Inherente',
            'Descripción Impacto inherente',
            'Max. Frecuencia Inherente',
            'Descripción Frecuencia Inherente',
            'Exposición Inherente',
            '# Control',
            'Nomenclatura Controles',
            'Controles Actuales',
            'El control apunta a disminuir',
            'Naturaleza',
            'Evidencia',
            'Cobertura',
            'Documentación del control',
            'Segregación',
            'Evaluacion del control',
            '% de Mitigación',
            'Max. Impacto Residual',
            'Descripción Impacto Residual',
            'Max. Frecuencia Residual',
            'Descripción Frecuencia Residual',
            'Exposición Residual',
            'Maximo Impacto Evento Riesgo',
            'Indicadores'
        ]);
        
        return $columns;
    }

    public static function afterSheet(AfterSheet $event)
    {
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
        return 'Matriz de Riesgos';
    }
}

