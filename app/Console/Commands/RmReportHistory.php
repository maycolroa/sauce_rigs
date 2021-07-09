<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use App\Traits\RiskMatrixTrait;
use App\Models\IndustrialSecure\RiskMatrix\ReportHistory;
use App\Models\IndustrialSecure\RiskMatrix\Risk;
use App\Models\Administrative\Processes\TagsProcess;
use Carbon\Carbon;

class RmReportHistory extends Command
{
    use RiskMatrixTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rm-report-history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se encarga de alamcenar la información de las matrices de riesgos de los ultimos 3 meses';

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
            $companies = RiskMatrix::select(DB::raw('DISTINCT company_id'))
            ->withoutGlobalScopes()
            ->pluck('company_id');

            foreach ($companies as $company)
            {
                $data = [];
                $matriz_calification = $this->getMatrixReport();
                $data = $matriz_calification;

                $risksMatrix = RiskMatrix::select(
                    'sau_rm_risks_matrix.*',
                    'sau_employees_regionals.name as regionalName',
                    'sau_tags_processes.name as macroprocess'
                )
                ->leftJoin('sau_employees_regionals', 'sau_employees_regionals.id', 'sau_rm_risks_matrix.employee_regional_id')
                ->leftJoin('sau_tags_processes', 'sau_tags_processes.id', 'sau_rm_risks_matrix.macroprocess_id'); 
                $risksMatrix->company_scope = $company;
                $risksMatrix = $risksMatrix->get();

                foreach ($risksMatrix as $keyMatrix => $itemMatrix)
                {
                    $macroprocess = TagsProcess::where('id', $itemMatrix->macroprocess_id);

                    foreach ($itemMatrix->subprocesses as $keySub => $itemSub)
                    {
                        foreach ($itemSub->risks as $keyRisk => $itemRisk)
                        {
                            $qualification = collect([]);
                            
                            $qualification->push([
                                "name" => 'Frecuencia Inherente',
                                "value" => $itemRisk->description_inherent_frequency
                            ]);

                            $qualification->push([
                                "name" => 'Impacto Inherente',
                                "value" => $itemRisk->description_inherent_impact
                            ]);

                            $qualification->push([
                                "name" => 'Impacto Residual',
                                "value" => $itemRisk->description_residual_impact
                            ]);

                            $qualification->push([
                                "name" => 'Frecuencia Residual',
                                "value" => $itemRisk->description_residual_frequency
                            ]);


                            $risk = Risk::where("id", $itemRisk->risk_id);
                            $risk->company_scope = $company;
                            $risk = $risk->first();

                            $reportHistory = new ReportHistory();
                            $reportHistory->company_id = $company;
                            $reportHistory->year = $year;
                            $reportHistory->month = $month;
                            $reportHistory->regional = $itemMatrix->regionalName;
                            $reportHistory->area = $itemMatrix->area ? $itemMatrix->area->name : null;
                            $reportHistory->headquarter = $itemMatrix->headquarter ? $itemMatrix->headquarter->name : null;
                            $reportHistory->process = $itemMatrix->process ? $itemMatrix->process->name : null;
                            $reportHistory->macroprocess = $itemMatrix->macroprocess ? $itemMatrix->macroprocess : null;
                            $reportHistory->qualification = json_encode($qualification->toArray());
                            $reportHistory->risk = $risk->name;
                            $reportHistory->risk_sequence = $itemRisk->risk_sequence;
                            $reportHistory->save();
                        }
                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            \Log::info($e->getMessage());
            \Log::info("Ocurrio un error durante el proceso de la tarea programada: RmReportHistory");
        }
    }
}
