<?php

namespace App\Http\Controllers\IndustrialSecure\RiskMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\RiskMatrix\RiskMatrix;
use App\Jobs\IndustrialSecure\RiskMatrix\RiskMatrixReportExportJob;
use App\Traits\RiskMatrixTrait;
use App\Traits\Filtertrait;
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

    public function getDataExportPdf()
    {
        $data = [
            'inherent_report' => $this->inherent_report,
            'residual_report' => $this->residual_report,
            'inherent_report_table' => $this->inherent_report_table,
            'residual_report_table' => $this->residual_report_table,
            'table_report_residual' => $this->table_report_residual
        ];

        return $data;
    }

    public function downloadPdf()
    {
        $report = $this->getDataExportPdf();

        \Log::info($report);

        /*PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif']);

        $pdf = PDF::loadView('pdf.qualificationListCheck', ['report' => $report] );

        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('Reportes matriz de riesgos.pdf');*/
    }
}