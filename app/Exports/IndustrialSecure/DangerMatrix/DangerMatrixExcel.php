<?php

namespace App\Exports\IndustrialSecure\DangerMatrix;

use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Models\IndustrialSecure\DangerMatrix\Qualification;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use App\Models\IndustrialSecure\DangerMatrix\QualificationDanger;
use App\Facades\ConfigurationCompany\Facades\ConfigurationsCompany;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\IndustrialSecure\DangerMatrix\AdditionalFields;
use App\Models\IndustrialSecure\DangerMatrix\AdditionalFieldsValues;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;

use App\Traits\LocationFormTrait;
use App\Traits\UtilsTrait;
use App\Traits\DangerMatrixTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class DangerMatrixExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use LocationFormTrait;
    use UtilsTrait;
    use DangerMatrixTrait;

    protected $company_id;
    protected $danger_matrix_id;
    protected $confLocation;
    protected $qualifications;
    protected $qualificationsValues;
    protected $configurations;
    protected $keywords;
    protected $add_fields;
    protected $add_fields_values;
    protected $add_fields_ids;

    public function __construct($company_id, $danger_matrix_id)
    {
        $this->company_id = $company_id;
        $this->danger_matrix_id = $danger_matrix_id;
        $this->confLocation = $this->getLocationFormConfModule($this->company_id);
        $this->keywords = $this->getKeywordQueue($this->company_id);


        $qualifications = QualificationCompany::query();
        $qualifications->company_scope = $this->company_id;
        $qualifications = $qualifications->first();

        if ($qualifications)
            $this->qualifications = $qualifications->qualification->types;
        else
        {
            $qualification = Qualification::where('name', $this->getDefaultCalificationDm())->first();
            $this->qualifications = $qualification->types;
        }

        $qualifications = QualificationDanger::selectRaw('
        CONCAT(sau_dm_qualification_danger.activity_danger_id, "_", sau_dm_qualification_danger.type_id) AS indice,
        sau_dm_qualification_danger.value_id AS value');
        $this->qualificationsValues = $qualifications->pluck('value', 'indice');

        $this->configurations = ConfigurationsCompany::company($this->company_id)->findAll(); //findByKey('show_action_plans');
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $selecs = [
            'sau_dangers_matrix.name AS name',
            'sau_dangers_matrix.participants AS participants',
            'sau_employees_regionals.name AS regional',
            'sau_employees_headquarters.name AS headquarter',
            'sau_employees_processes.name AS process',
            'sau_employees_processes.types AS macroprocess',
            'sau_employees_areas.name AS area',
            'sau_dm_activities.name AS activity',
            'sau_danger_matrix_activity.type_activity AS type_activity',
            'sau_dm_dangers.name AS danger',
            'sau_dm_activity_danger.id AS peligro_id',
            'sau_dm_activity_danger.danger_description',
            'sau_dm_activity_danger.danger_generated',
            'sau_dm_activity_danger.possible_consequences_danger',
            'sau_dm_activity_danger.generating_source',
            'sau_dm_activity_danger.collaborators_quantity',
            'sau_dm_activity_danger.esd_quantity',
            'sau_dm_activity_danger.visitor_quantity',
            'sau_dm_activity_danger.student_quantity',
            'sau_dm_activity_danger.esc_quantity',            
            'sau_dm_activity_danger.observations',
            'sau_dm_activity_danger.existing_controls_engineering_controls',
            'sau_dm_activity_danger.existing_controls_substitution',
            'sau_dm_activity_danger.existing_controls_warning_signage',
            'sau_dm_activity_danger.existing_controls_administrative_controls',
            'sau_dm_activity_danger.existing_controls_epp',
            'sau_dm_activity_danger.legal_requirements',
            'sau_dm_activity_danger.quality_policies',
            'sau_dm_activity_danger.objectives_goals',
            'sau_dm_activity_danger.risk_acceptability',
            'sau_dm_activity_danger.intervention_measures_elimination',
            'sau_dm_activity_danger.intervention_measures_substitution',
            'sau_dm_activity_danger.intervention_measures_engineering_controls',
            'sau_dm_activity_danger.intervention_measures_warning_signage',
            'sau_dm_activity_danger.intervention_measures_administrative_controls',
            'sau_dm_activity_danger.intervention_measures_epp',
            'sau_dm_activity_danger.qualification'
        ];

        $dangerMatrix = DangerMatrix::
              leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_dangers_matrix.employee_regional_id')
            ->leftJoin('sau_employees_headquarters', 'sau_employees_headquarters.id', 'sau_dangers_matrix.employee_headquarter_id')
            ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_dangers_matrix.employee_process_id')
            ->leftJoin('sau_employees_areas', 'sau_employees_areas.id', 'sau_dangers_matrix.employee_area_id')
            ->join('sau_danger_matrix_activity', 'sau_danger_matrix_activity.danger_matrix_id', 'sau_dangers_matrix.id')
            ->join('sau_dm_activities', 'sau_dm_activities.id', 'sau_danger_matrix_activity.activity_id')
            ->join('sau_dm_activity_danger', 'sau_dm_activity_danger.dm_activity_id', 'sau_danger_matrix_activity.id')
            ->join('sau_dm_dangers', 'sau_dm_dangers.id', 'sau_dm_activity_danger.danger_id')
            ->where('sau_dangers_matrix.id', $this->danger_matrix_id);

        if (!isset($this->configurations['show_action_plans']) || ( isset($this->configurations['show_action_plans']) && $this->configurations['show_action_plans'] == 'SI') )
        {
            $selecs = array_merge($selecs, [
                'sau_action_plans_activities.description AS description_plan',
                'sau_action_plans_activities.execution_date AS execution_date ',
                'sau_action_plans_activities.expiration_date AS expiration_date',
                'sau_action_plans_activities.state AS state',
                'sau_action_plans_activities.observation AS observation',
                'sau_users.name AS responsible',
            ]);

            $dangerMatrix
                ->leftJoin('sau_action_plans_activity_module', 'sau_action_plans_activity_module.item_id', 'sau_dm_activity_danger.id')
                ->leftJoin('sau_action_plans_activities', 'sau_action_plans_activities.id', 'sau_action_plans_activity_module.activity_id')
                ->leftJoin('sau_users', 'sau_users.id', 'sau_action_plans_activities.responsible_id');
        }

        $dangerMatrix->selectRaw(implode(",", $selecs));

        $dangerMatrix->company_scope = $this->company_id;

        return $dangerMatrix->get();
    }

    public function map($data): array
    {
        $this->add_fields_ids = AdditionalFields::get();

        $this->add_fields_values = [];

        foreach ($this->add_fields_ids as $key => $value3) 
        {
            $add = AdditionalFieldsValues::select('value')->where('danger_matrix_id',$this->danger_matrix_id)->where('field_id', $value3->id)->get();

            foreach ($add as $key => $value4) 
            {
                array_push($this->add_fields_values, $value4->value);
            }
        }

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
            $data->activity,
            $data->type_activity,
            $data->danger,
            $data->danger_description,
            $data->danger_generated,
            $data->possible_consequences_danger,
            $data->generating_source,
            (String) $data->collaborators_quantity,
            (String) $data->esd_quantity,
            (String) $data->visitor_quantity,
            (String) $data->student_quantity,
            (String) $data->esc_quantity,
            str_replace('=', '', $data->observations),
            $data->existing_controls_engineering_controls,
            $data->existing_controls_substitution,
            $data->existing_controls_warning_signage,
            $data->existing_controls_administrative_controls,
            $data->existing_controls_epp,
            $data->legal_requirements,
            $data->quality_policies,
            $data->objectives_goals,
            $data->risk_acceptability,
            $data->intervention_measures_elimination,
            $data->intervention_measures_substitution,
            $data->intervention_measures_engineering_controls,
            $data->intervention_measures_warning_signage,
            $data->intervention_measures_administrative_controls,
            $data->intervention_measures_epp
        ]);

        $key = $data->peligro_id.'_';

        foreach ($this->qualifications as $qualification)
        {
            $value = isset($this->qualificationsValues[$key.$qualification->id]) ? $this->qualificationsValues[$key.$qualification->id] : '';

            array_push($values, $value);
        }

        COUNT($this->qualifications) > 0 ? array_push($values, $data->qualification) : NULL;

        if (!isset($this->configurations['show_action_plans']) || ( isset($this->configurations['show_action_plans']) && $this->configurations['show_action_plans'] == 'SI') )
        {
            $values = array_merge($values, [
                $data->description_plan,
                $data->responsible,
                $data->expiration_date,
                $data->execution_date,
                $data->state,
                $data->observation
            ]);
        }

        foreach ($this->add_fields_values as $key => $value2) 
        {
            array_push($values, $value2);
        }

        return $values;
    }

    public function headings(): array
    {
        $this->add_fields = AdditionalFields::select('name')->get();
        
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
            'Actividad',
            'Tipo de actividad',
            'Peligro',
            'Descripción del peligro',
            'Peligro Generado',
            'Posibles consecuencias del peligro',
            'Fuente generadora',
            'Expuestos - Colaboradores',
            'Expuestos - Contratistas',
            'Expuestos - Visitantes',
            'Expuestos - Estudiantes',
            'Expuestos - Arrendatarios',
            'Observaciones',
            'Controles Existentes - Controles de ingenieria',
            'Controles Existentes - Sustitución',
            'Controles Existentes - Señalización, Advertencia',
            'Controles Existentes - Controles administrativos',
            'Controles Existentes - EPP',
            'Criterios de riesgo - Cumplimiento requisitos legales',
            'Criterios de riesgo - Alineamiento con las políticas de calidad y de SST',
            'Criterios de riesgo - Alineamiento con los objetivos y metas',
            'Criterios de riesgo - Aceptabilidad del riesgo',
            'Medidas de Intervención - Eliminación',
            'Medidas de Intervención - Sustitución',
            'Medidas de Intervención - Controles de ingenieria',
            'Medidas de Intervención - Señalización, Advertencia',
            'Medidas de Intervención - Controles administrativos',
            'Medidas de Intervención - EPP'
        ]);
        
        foreach ($this->qualifications as $qualification)
        {
            array_push($columns, $qualification->description);
        }

        COUNT($this->qualifications) > 0 ? array_push($columns, 'Calificación') : NULL;

        if (!isset($this->configurations['show_action_plans']) || ( isset($this->configurations['show_action_plans']) && $this->configurations['show_action_plans'] == 'SI') )
        {
            $columns = array_merge($columns, [
                'Plan de acción - Descripción',
                'Responsable',
                'Fecha de vencimiento',
                'Fecha de ejecución',
                'Estado',
                'Observación'
            ]);
        }

      foreach ($this->add_fields as $key => $value) {
        array_push($columns, $value->name);
      }

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
        return 'Matriz de Peligros';
    }
}

