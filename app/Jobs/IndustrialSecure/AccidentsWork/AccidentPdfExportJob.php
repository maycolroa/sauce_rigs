<?php

namespace App\Jobs\IndustrialSecure\AccidentsWork;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\IndustrialSecure\WorkAccidents\Accident;
use App\Facades\ActionPlans\Facades\ActionPlan;
use App\Exports\IndustrialSecure\AccidentsWork\AccidentExcel;
use App\Models\IndustrialSecure\WorkAccidents\FileAccident;
use App\Facades\Mail\Facades\NotificationMail;
use App\Models\General\WorkCenter;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\General\Company;
use App\Models\Administrative\Users\User;
use PDF;
use DB;


class AccidentPdfExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $company_id;
    protected $form;
    protected $accident_id;
    protected $emails;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $accident_id, $emails)
    {
      $this->user = $user;
      $this->company_id = $company_id;
      $this->accident_id = $accident_id;
      $this->emails = $emails;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $recipients = User::where('id', -1)->get();
      
      foreach ($this->emails as $key => $value)
      {
          $recipients->push(new User(['email'=>$value]));
      }

      PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);
      
      $this->form = $this->getDataExportPdf();

      $pdf = PDF::loadView('pdf.formularioAccidents3', ['form' => $this->form]);

      $pdf->setPaper('A3', 'landscape');

      $namePdf = 'reporte_evento_'.date("YmdHis").'.pdf';

      Storage::disk('public')->put('export/1/accidents/reportsEmail/'.$namePdf, $pdf->output());

      $path = Storage::disk('public')->url('export/1/accidents/reportsEmail/'.$namePdf);

      //$pdf->save('export/1/'.$namePdf);
      
      $paramUrl = base64_encode('export/1/accidents/reportsEmail/'.$namePdf);
      
      NotificationMail::
        subject('Exportación de los formularios de accidentes')
        ->recipients($recipients)
        ->message('Se ha generado una exportación de accidentes.')
        ->subcopy('Este link es valido por 24 horas')
        ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
        ->module('dangerousConditions')
        ->event('Job: AccidentsExportJob')
        ->company($this->company_id)
        ->send();
    }

    public function getDataExportPdf()
    {
      $accident = Accident::findOrFail($this->accident_id);

      $accident->info_sede_principal_misma_centro_trabajo = $accident->info_sede_principal_misma_centro_trabajo ? 'SI' : 'NO';
      $accident->tiene_seguro_social = $accident->tiene_seguro_social ? 'SI' : 'NO';
      $accident->estaba_realizando_labor_habitual = $accident->estaba_realizando_labor_habitual ? 'SI' : 'NO';
      $accident->causo_muerte = $accident->causo_muerte ? 'SI' : 'NO';
      $accident->personas_presenciaron_accidente = $accident->personas_presenciaron_accidente ? 'SI' : 'NO';

      $persons = [];

      foreach ($accident->personas as $key => $value) {
          if ($value->rol == 'Presencio Accidente')
              array_push($persons, $value);
      }

      $accident->persons = $persons;

      $accident->dia_accidente = ucfirst(Carbon::parse($accident->fecha_accidente)->locale('es_ES')->dayName);

      $company = Company::select('*')->where('id', $this->company_id)->first();

      $accident->nombre_actividad_economica_sede_principal = $company->nombre_actividad_economica_sede_principal;

      $accident->razon_social = $company->name;

      $accident->tipo_identificacion_sede_principal = $company->tipo_identificacion_sede_principal;

      $accident->identificacion_sede_principal = $company->identificacion_sede_principal;

      $accident->direccion_sede_principal = $company->direccion_sede_principal;

      $accident->email_sede_principal = $company->email_sede_principal;

      $accident->telefono_sede_principal = $company->telefono_sede_principal;

      $accident->departamentSede = $company->departament ? $company->departament->name : NULL;

      $accident->ciudadSede = $company->city ? $company->city->name : NULL;

      $accident->zona_sede_principal = $company->zona_sede_principal;

      if ($accident->info_sede_principal_misma_centro_trabajo == 'NO')
      {
          $centro = WorkCenter::find($accident->centro_trabajo_secundary_id);

          $accident->nombre_actividad_economica_centro_trabajo = $centro->activity_economic;

          $accident->direccion_centro_trabajo = $centro->direction;

          $accident->telefono_centro_trabajo = $centro->telephone;

          $accident->email_centro_trabajo = $centro->email;

          $accident->departamentCentro = $centro->departamentCentro;

          $accident->ciudadCentro = $centro->ciudadCentro;

          $accident->zona_centro_trabajo = $centro->zona;
      }

      $logo = ($company && $company->logo) ? $company->logo : null;

      $accident->logo = $logo;

      return $accident;
    }
}
