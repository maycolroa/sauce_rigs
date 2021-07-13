<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use App\Jobs\IndustrialSecure\RiskMatrix\RiskMatrixReportExportJob;
use App\Traits\RiskMatrixTrait;
use App\Traits\Filtertrait;
use Illuminate\Support\Facades\Storage;
use App\Facades\Mail\Facades\NotificationMail;
use PDF;
use RiskMatrixReportManager;

class RiskMatrixReportController extends Controller
{
    use RiskMatrixTrait;
    use Filtertrait;

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
        $data = RiskMatrixReportManager::reportInherent($request, $request->filters, $this->user->id);

        return $this->respondHttp200([
            "data" => $data
        ]);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function reportRiskInherentTable(Request $request)
    {
        return Vuetable::of(RiskMatrixReportManager::reportRiskInherentTable($request, $request->filters, $this->user->id))->make();
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportResidual(Request $request)
    {
        $data = RiskMatrixReportManager::reportResidual($request, $request->filters, $this->user->id);

        return $this->respondHttp200([
            "data" => $data
        ]);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function reportRiskResidualTable(Request $request)
    {
        return Vuetable::of(RiskMatrixReportManager::reportRiskResidualTable($request, $request->filters, $this->user->id))->make();
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function reportTableResidual(Request $request)
    {
        $data = RiskMatrixReportManager::reportTableResidual($request, $request->filters, $this->user->id);

        return $this->respondHttp200([
            "data" => $data
        ]);
    }

    public function reportExporteExcel(Request $request)
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
                "rowI" => $request->filtersTable['rowI'],
                "colI" => $request->filtersTable['colI'],
                "rowR" => $request->filtersTable['rowR'],
                "colR" => $request->filtersTable['colR'],
                "filtersType" => $request->filtersType
            ];

            RiskMatrixReportExportJob::dispatch($this->user, $this->company, $filters);

            return $this->respondHttp200();
        } catch(Exception $e) {
            return $this->respondHttp500();
        }
    }

    public function getDataExportPdf($request)
    {
        $data = [
            'inherent_report' => RiskMatrixReportManager::reportInherent($request, $request->filters, $this->user->id),
            'residual_report' => RiskMatrixReportManager::reportResidual($request, $request->filters, $this->user->id),
            'inherent_report_table' => RiskMatrixReportManager::reportRiskInherentTablePdf($request, $request->filters, $this->user->id)->get(),
            'residual_report_table' => RiskMatrixReportManager::reportRiskResidualTablePdf($request, $request->filters, $this->user->id)->get(),
            'table_report_residual' => RiskMatrixReportManager::reportTableResidual($request, $request->filters, $this->user->id)
        ];

        return $data;
    }

    public function reportExportPdf(Request $request)
    {
        try
        {
            $report = $this->getDataExportPdf($request);

            PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

            $pdf = PDF::loadView('pdf.reportRiskMatrix', ['report' => $report, 'locationForm' => $this->getLocationFormConfModule()] );

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
}