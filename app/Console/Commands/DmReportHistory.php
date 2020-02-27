<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\IndustrialSecure\DangerMatrix\DangerMatrix;
use App\Traits\DangerMatrixTrait;
use App\Models\IndustrialSecure\DangerMatrix\QualificationHistory;
use App\Models\IndustrialSecure\DangerMatrix\ReportHistory;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use App\Models\IndustrialSecure\Dangers\Danger;
use Carbon\Carbon;

class DmReportHistory extends Command
{
    use DangerMatrixTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dm-report-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se encarga de alamcenar la información de las matrices de peligros de los ultimos 3 meses';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $now = new Carbon('first day of last month');
        $month = $now->month;
        $year = $now->year;

        DB::beginTransaction();

        try
        {
            $companies = DangerMatrix::select(DB::raw('DISTINCT company_id'))
            ->withoutGlobalScopes()
            ->pluck('company_id');

            $all_matriz = $this->getAllMatrixCalification();

            foreach ($all_matriz as $key => $value)
            {
                $qualificationHistory = new QualificationHistory();
                $qualificationHistory->year = $year;
                $qualificationHistory->month = $month;
                $qualificationHistory->type_configuration = $key;
                $qualificationHistory->value = json_encode($value);
                $qualificationHistory->save();        
            }

            foreach ($companies as $company)
            {
                $data = [];

                $conf = QualificationCompany::select('qualification_id');
                $conf->company_scope = $company;
                $conf = $conf->first();

                if ($conf && $conf->qualification)
                    $conf = $conf->qualification->name;
                else
                    $conf = $this->getDefaultCalificationDm();

                if ($conf)
                {
                    $matriz_calification = $this->getMatrixCalification($conf);
                    $data = $matriz_calification;

                    $dangersMatrix = DangerMatrix::select(
                        'sau_dangers_matrix.*',
                        'sau_employees_regionals.name as regionalName'
                    )
                    ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_dangers_matrix.employee_regional_id')
                    ->leftJoin('sau_employees_processes', 'sau_employees_processes.id', 'sau_dangers_matrix.employee_process_id'); 
                    $dangersMatrix->company_scope = $company;
                    $dangersMatrix = $dangersMatrix->get();

                    foreach ($dangersMatrix as $keyMatrix => $itemMatrix)
                    {
                        foreach ($itemMatrix->activities as $keyActivity => $itemActivity)
                        {
                            foreach ($itemActivity->dangers as $keyDanger => $itemDanger)
                            {
                                $nri = -1;
                                $ndp = -1;
                                $qualification = collect([]);

                                foreach ($itemDanger->qualifications as $keyQ => $itemQ)
                                {
                                    $qualification->push([
                                        "name" => $itemQ->typeQualification->description,
                                        "value" => $itemQ->value_id
                                    ]);

                                    if ($conf == 'Tipo 1')
                                    {
                                        if ($itemQ->typeQualification->description == 'NRI')
                                            $nri = $itemQ->value_id;

                                        if ($itemQ->typeQualification->description == 'Nivel de Probabilidad')
                                            $ndp = $itemQ->value_id;
                                    }
                                }

                                if ($conf == 'Tipo 1')
                                    if (isset($data[$ndp]) && isset($data[$ndp][$nri]))
                                    {
                                        $qualification->push([
                                            "name" => "Calficación",
                                            "value" => $data[$ndp][$nri]['label']
                                        ]);
                                    }

                                $danger = Danger::where("id", $itemDanger->danger_id);
                                $danger->company_scope = $company;
                                $danger = $danger->first();

                                $reportHistory = new ReportHistory();
                                $reportHistory->company_id = $company;
                                $reportHistory->year = $year;
                                $reportHistory->month = $month;
                                $reportHistory->regional = $itemMatrix->regionalName;
                                $reportHistory->area = $itemMatrix->area ? $itemMatrix->area->name : null;
                                $reportHistory->headquarter = $itemMatrix->headquarter ? $itemMatrix->headquarter->name : null;
                                $reportHistory->process = $itemMatrix->process ? $itemMatrix->process->name : null;
                                $reportHistory->macroprocess = $itemMatrix->process ? $itemMatrix->process->types : null;
                                $reportHistory->qualification = json_encode($qualification->toArray());
                                $reportHistory->type_configuration = $conf;
                                $reportHistory->danger = $danger->name;
                                $reportHistory->danger_description = $itemDanger->danger_description;
                                $reportHistory->save();
                            }
                        }
                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info("Ocurrio un error durante el proceso de la tarea programada: DmReportHistory");
        }
    }
}
