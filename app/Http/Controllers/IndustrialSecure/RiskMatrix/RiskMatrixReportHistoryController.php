<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Jobs\IndustrialSecure\RiskMatrix\RisksMatrixReportHistoryExportJob;
use App\Traits\RiskMatrixTrait;
use Session;
use App\Models\IndustrialSecure\RiskMatrix\ReportHistory;
use App\Traits\Filtertrait;
use RiskMatrixHistoryManager;
use PDF;
use Illuminate\Support\Facades\Storage;
use App\Facades\Mail\Facades\NotificationMail;

class RiskMatrixReportHistoryController extends Controller
{
    use Filtertrait;
    use RiskMatrixTrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:dangerMatrix_r|dangerMatrix_view_report, {$this->team}");
        $this->middleware("permission:dangerMatrix_export_report, {$this->team}", ['only' => 'reportExport']);*/
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportInherent(Request $request)
    {
        $data = RiskMatrixHistoryManager::reportInherent($request, $request->filters, $this->user->id, $this->company);

        return $this->respondHttp200([
            "data" => $data
        ]);
    }

    public function reportResidual(Request $request)
    {
        $data = RiskMatrixHistoryManager::reportResidual($request, $request->filters, $this->user->id, $this->company);

        return $this->respondHttp200([
            "data" => $data
        ]);
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportTableResidual(Request $request)
    {   
        $data = RiskMatrixHistoryManager::reportTableResidual($request, $request->filters, $this->user->id, $this->company);

        return $this->respondHttp200([
            "data" => $data
        ]);
    }

    public function reportExportExcel(Request $request)
    {
        try
        {
            /** FIltros */
            $filters = [
                "regionals" => $this->getValuesForMultiselect($request->regionals),
                "headquarters" => $this->getValuesForMultiselect($request->headquarters),
                "areas" => $this->getValuesForMultiselect($request->areas),
                "processes" => $this->getValuesForMultiselect($request->processes),
                "macroprocesses" => $this->getValuesForMultiselect($request->macroprocesses),
                "risks" => $this->getValuesForMultiselect($request->risks),
                "filtersType" => $request->filtersType,
                "year" => $request->year,
                "month" => $request->month,
            ];

            RisksMatrixReportHistoryExportJob::dispatch($this->user, $this->company, $filters);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function getDataExportPdf($request)
    {
        $data = [
            'inherent_report' => RiskMatrixHistoryManager::reportInherent($request, $request->filters, $this->user->id, $this->company),
            'residual_report' => RiskMatrixHistoryManager::reportResidual($request, $request->filters, $this->user->id, $this->company),
            'table_report_residual' => RiskMatrixHistoryManager::reportTableResidual($request, $request->filters, $this->user->id, $this->company)
        ];

        return $data;
    }

    public function reportExportPdf(Request $request)
    {
        try
        {
            $report = $this->getDataExportPdf($request);

            PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            $pdf = PDF::loadView('pdf.reportHistoryRiskMatrix', ['report' => $report, 'locationForm' => $this->getLocationFormConfModule()] );

            $pdf->setPaper('A4');
            //PDF::render();
            $pdf = $pdf->output();
        
            $pdfName = 'matriz_de_riesgos_reporte_'.date("YmdHis").'.pdf';
            $nameExcel = 'export/1/matriz_de_riesgos_reporte_'.date("YmdHis").'.pdf';

            Storage::disk('public')->put($nameExcel, $pdf, 'public');

            $paramUrl = base64_encode($nameExcel);

            NotificationMail::
                subject('Exportación de matriz de riesgos - Reportes')
                ->recipients($this->user)
                ->message('Se ha generado una exportación de reportes de matriz de riesgos.')
                ->subcopy('Este link es valido por 24 horas')
                ->buttons([['text'=>'Descargar', 'url'=>url("/export/{$paramUrl}")]])
                ->module('dangerMatrix')
                ->event('Job: RiskMatrixReportExportJob')
                ->company($this->company)
                ->send();

            return $this->respondHttp200();

        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if ($request->has('column') && $request->get('column') != '')
        {
            if($request->has('keyword'))
            {
                $column = $request->column;

                $keyword = "%{$request->keyword}%";
                $data = ReportHistory::selectRaw("DISTINCT $column")
                ->where(function ($query) use ($keyword, $column) {
                    $query->orWhere($column, 'like', $keyword);
                })
                ->orderBy($column);

                if ($request->has('year'))
                    $data->where('year', $request->year);
                
                if ($request->has('month'))
                    $data->where('month', $request->month);

                $data = $data->take(30)->pluck($column, $column);

                if ($column == 'month')
                {
                    $new_data = [];

                    foreach ($data as $value)
                    {
                        $new_data[trans("months.$value")] = $value;
                    }

                    $data = collect($new_data);
                }

                return $this->respondHttp200([
                    'options' => $this->multiSelectFormat($data)
                ]);
            }
            else
            {
                $column = $request->column;

                $keyword = "%{$request->keyword}%";
                $data = ReportHistory::selectRaw("DISTINCT $column")
                ->where($column, "<>", "")
                ->whereNotNull($column);

                if ($request->has('year'))
                    $data->where('year', $request->year);
                
                if ($request->has('month'))
                    $data->where('month', $request->month);

                $data = $data->pluck($column, $column);

                if ($column == 'month')
                {
                    $new_data = [];

                    foreach ($data as $value)
                    {
                        $new_data[trans("months.$value")] = $value;
                    }

                    $data = collect($new_data);
                }

                return $this->multiSelectFormat($data);
            }
        }

        return [];
    }
}