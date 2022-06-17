<?php

namespace App\Exports\IndustrialSecure\AccidentsWork;

use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Events\AfterSheet;
use \Maatwebsite\Excel\Sheet;
use Carbon\Carbon;
use Datetime;

use App\Models\Administrative\Employees\Employee;
use App\Models\Administrative\Positions\EmployeePosition;
use App\Models\Administrative\Business\EmployeeBusiness;
use App\Models\Administrative\Regionals\EmployeeRegional;
use App\Models\IndustrialSecure\WorkAccidents\Accident;
use App\Traits\UtilsTrait;

Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
  $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
});

class AccidentExcel implements FromCollection, WithHeadings, WithMapping, WithEvents, WithTitle, ShouldAutoSize
{
    use RegistersEventListeners;
    use UtilsTrait;

    protected $company_id;
    protected $keywords;
    protected $filters;

    public function __construct($company_id, $filters)
    {
      $this->company_id = $company_id;
      $this->filters = $filters;
      $this->keywords = $this->getKeywordQueue($this->company_id);
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
      $accidents = Accident::selectRaw(
        "sau_aw_form_accidents.*,
        if(sau_aw_form_accidents.consolidado, 'SI', 'NO') AS consolidado")
        ->where('sau_aw_form_accidents.company_id', $this->company_id);

      if (isset($this->filters["mechanism"]) && $this->filters["mechanism"])
          $accidents->inMechanisms($this->filters["mechanism"], $this->filters['filtersType']['mechanism']);

      if (isset($this->filters["agent"]) && $this->filters["agent"])
          $accidents->inAgents($this->filters["agent"], $this->filters['filtersType']['agent']);

      if (isset($this->filters["cargo"]) && $this->filters["cargo"])
          $accidents->inCargo($this->filters["cargo"], $this->filters['filtersType']['cargo']);

      if (isset($this->filters["activityEconomic"]) && $this->filters["activityEconomic"])
          $accidents->inActivityEconomic($this->filters["activityEconomic"], $this->filters['filtersType']['activityEconomic']);

      if (isset($this->filters["razonSocial"]) && $this->filters["razonSocial"])
          $accidents->inSocialReason($this->filters["razonSocial"], $this->filters['filtersType']['razonSocial']);

      if (isset($this->filters["sexs"]) && $this->filters["sexs"])
          $accidents->inSexs($this->filters["sexs"], $this->filters['filtersType']['sexs']);

      if (isset($this->filters["names"]) && $this->filters["names"])
          $accidents->inName($this->filters["names"], $this->filters['filtersType']['names']);

      if (isset($this->filters["identifications"]) && $this->filters["identifications"])
          $accidents->inIdentification($this->filters["identifications"], $this->filters['filtersType']['identifications']);

      if (isset($this->filters["departament"]) && $this->filters["departament"])
          $accidents->inDepartamentAccident($this->filters["departament"], $this->filters['filtersType']['departament']);

      if (isset($this->filters["city"]) && $this->filters["city"])
          $accidents->inCityAccident($this->filters["city"], $this->filters['filtersType']['city']);

      if (isset($this->filters['causoMuerte']) && COUNT($this->filters['causoMuerte']) > 0)
      {
          if ($this->filters['filtersType']['causoMuerte'] == 'IN')
              $accidents->whereIn('causo_muerte', $this->filters['causoMuerte']);

          else if ($this->filters['filtersType']['causoMuerte'] == 'NOT IN')
              $accidents->whereNotIn('causo_muerte', $this->filters['causoMuerte']);
      }

      if (isset($this->filters["dentroEmpresa"]) && COUNT($this->filters["dentroEmpresa"]) > 0)
      {
          if ($this->filters['filtersType']['dentroEmpresa'] == 'IN')
              $accidents->whereIn('accidente_ocurrio_dentro_empresa', $this->filters["dentroEmpresa"]);

          else if ($this->filters['filtersType']['dentroEmpresa'] == 'NOT IN')
              $accidents->whereNotIn('accidente_ocurrio_dentro_empresa', $this->filters["dentroEmpresa"]);
      }

      return $accidents->get();
    }

    public function map($data): array
    {
      $values = $data->lesionTypes()->pluck('sau_aw_types_lesion.name')->toArray();
      $data->lesions_id = implode(', ', $values);

      $values2 = $data->partsBody()->pluck('sau_aw_parts_body.name')->toArray();
      $data->parts_body = implode(', ', $values2);

      $data->dia_Semana = ucfirst(Carbon::parse($data->fecha_accidente)->locale('es_ES')->dayName);

      return [
        $data->id,
        $data->consolidado,
        $data->tipo_vinculacion_persona,
        $data->tipo_identificacion_persona.' - '.$data->identificacion_persona,
        $data->nombre_persona,
        $data->fecha_nacimiento_persona,
        $data->sexo_persona,
        $data->email_persona,
        $data->telefono_persona,
        $data->cargo_persona,
        $data->tipo_vinculador_laboral,
        $data->direccion_persona,
        $data->departamentPerson->name,
        $data->ciudadPerson->name,
        $data->zona_persona,
        $data->fecha_ingreso_empresa_persona,
        $data->tiempo_ocupacion_habitual_persona,
        $data->salario_persona,
        $data->jornada_trabajo_habitual_persona,
        $data->nombre_actividad_economica_sede_principal,
        $data->razon_social,
        $data->tipo_identificacion_sede_principal.' - '.$data->identificacion_sede_principal,
        $data->direccion_sede_principal,
        $data->email_sede_principal,
        $data->telefono_sede_principal,
        $data->departamentSede->name,
        $data->ciudadSede->name,
        $data->zona_sede_principal,
        $data->info_sede_principal_misma_centro_trabajo  ? 'SI' : 'NO',
        $data->nombre_actividad_economica_centro_trabajo,
        $data->direccion_centro_trabajo,
        $data->email_centro_trabajo,
        $data->telefono_centro_trabajo,
        $data->departamentCentro->name,
        $data->ciudadCentro->name,
        $data->zona_centro_trabajo,
        $data->nivel_accidente,
        $data->fecha_envio_arl,
        $data->fecha_envio_empresa,
        $data->coordinador_delegado,
        $data->cargo,
        $data->employee_eps_id,
        $data->employee_afp_id,
        $data->employee_arl_id,
        $data->tiene_seguro_social  ? 'SI' : 'NO',
        $data->nombre_seguro_social,
        $data->fecha_accidente,
        $data->dia_Semana,
        $data->jornada_accidente,
        $data->estaba_realizando_labor_habitual  ? 'SI' : 'NO',
        $data->otra_labor_habitual,
        $data->total_tiempo_laborado,
        $data->accidente_ocurrio_dentro_empresa,
        $data->tipo_accidente,
        $data->departamentAccident->name,
        $data->ciudadAccident->name,
        $data->zona_accidente,
        $data->causo_muerte  ? 'SI' : 'NO',
        $data->fecha_muerte,
        $data->agent_id,
        $data->mechanism_id,
        $data->site_id,
        $data->parts_body,
        $data->lesions_id,
        $data->fecha_diligenciamiento_informe,
        $data->nombres_apellidos_responsable_informe,
        $data->tipo_identificacion_responsable_informe.' - '.$data->identificacion_responsable_informe,
        $data->cargo_responsable_informe,
        $data->descripcion_accidente,
        $data->personas_presenciaron_accidente  ? 'SI' : 'NO',
        $data->observaciones_empresa
      ];
    }

    public function headings(): array
    {
      return [
        'ID',
        'Consolidado',
        'Tipo de vinculación laboral',
        'Identificación',
        'Nombre',
        'Fecha de nacimiento',
        'Sexo',
        'Email',
        'Teléfono',
        $this->keywords['position'],
        'Tipo de vinculación',
        'Dirección',
        'Departamento del empleado',
        'Ciudad del empleado',
        'Zona del empleado',
        'Fecha de ingreso a la empresa',
        'Tiempo de ocupacion habitual al momento del accidente',
        'Salario',
        'Jornada de trabajo habitual',
        'Nombre de la actividad económica Sede Principal',
        'Nombre o razón social Sede Principal',
        'Identificación Sede Principal',
        'Dirección Sede Principal',
        'Email Sede Principal',
        'Teléfono Sede Principal',
        'Departamento Sede Principal',
        'Ciudad Sede Principal',
        'Zona Sede Principal',
        '¿Son los datos del centro de trabajo los mismos de la sede principal?',
        'Nombre de la actividad económica Centro de trabajo',
        'Dirección Centro de trabajo',
        'Email Centro de trabajo',
        'Teléfono Centro de trabajo',
        'Departamento Centro de trabajo',
        'Ciudad Centro de trabajo',
        'Zona Centro de trabajo',
        'Nivel de accidente',
        'Fecha en que se envía la investigación a la ARL',
        'Fecha en que se envía recomendación a la empresa',
        'Coordinador delegado',
        'Cargo',
        $this->keywords['eps'].' a la que esta afiliado',
        $this->keywords['afp'].' a la que esta afiliado',
        $this->keywords['arl'].' a la que esta afiliado',
        'Seguro Social',
        'Nombre Seguro Social',
        'Fecha del accidente',
        'Dia de la semana en que ocurrio el accidente',
        'Jornada en que sucede',
        '¿Estaba realizando su labor habitual?',
        '¿Qué labor realizaba?',
        'Total tiempo laborado previo al accidente',
        'Lugar donde ocurrió el accidente',
        'Tipo de accidente',
        'Departamento Accidente',
        'Ciudad Accidente',
        'Zona Accidente',
        '¿Causó la muerte del trabajador?',
        'Fecha de la muerte',
        'Agente del accidente (con que se lesionó el trabajador)',
        'Mecanismo o forma del accidente',
        'Sitio donde ocurrió el accidente',
        'Partes del cuerpo aparentemente afectado',
        'Tipo de lesión',
        'Fecha de diligenciamiento del informe',
        'Responsable del Informe',
        'Nombre',
        'Identificación',
        'Cargo',
        'Descripción del accidente',
        '¿Hubo personas que presenciaron el accidente?',
        'Observaciones de la empresa'
      ];
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
        return 'Formularios';
    }

    private function timeDifference($startDate, $endDate = null)
    {
      $start = new DateTime($startDate);
      $end;

      if ($endDate == null)
          $end = new DateTime();
      else
          $start = new DateTime($endDate);

      $interval = $start->diff($end);

      return $interval->format('%y años %m meses y %d dias');
    }
}

