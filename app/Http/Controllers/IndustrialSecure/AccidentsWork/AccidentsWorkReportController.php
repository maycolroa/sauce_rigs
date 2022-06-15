<?php

namespace App\Http\Controllers\IndustrialSecure\AccidentsWork;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\WorkAccidents\Accident;
use App\Models\IndustrialSecure\WorkAccidents\Person;
use App\Models\IndustrialSecure\WorkAccidents\Agent;
use App\Models\IndustrialSecure\WorkAccidents\Mechanism;
use App\Models\IndustrialSecure\WorkAccidents\FileAccident;
use App\Models\Administrative\Employees\Employee;
use App\Http\Requests\IndustrialSecure\AccidentWork\AccidentRequest;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use App\Models\General\Company;
use App\Jobs\IndustrialSecure\AccidentsWork\AccidentsExportJob;
use Carbon\Carbon;
use DB;
use Validator;
use PDF;
use App\Traits\Filtertrait;

class AccidentsWorkReportController extends Controller
{
    use Filtertrait;
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:activities_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:activities_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:activities_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:activities_d, {$this->team}", ['only' => 'destroy']);*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function reportLineNumberAccidents(Request $request)
    { 
        $colors = ['#f0635f','#2f3337', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a', '#a7b61a', '#f3e562', '#ff9800', '#ff5722', '#ff4514', '#647c8a', '#3f51b5', '#2196f3', '#00b862', '#afdf0a'];


        $headingsXls = collect([]);

            $months = $this->multiselectMonth();

            foreach ($months as $key => $month) 
            {
                $headingsXls->push(['id' => $month['value'], 'name' => $month['name']]);
            }

            $headings = $headingsXls->pluck('name')->toArray();

            if ($request->option == "accidents")
            {
                $count = Accident::select(
                    DB::raw("month(fecha_accidente) AS month"),
                    DB::raw("YEAR(fecha_accidente) AS year"),
                    DB::raw("COUNT(sau_aw_form_accidents.id) AS count")
                )
                ->groupBy('year', 'month')
                ->get();

            }
            else if ($request->option == "persons")
            {
                $count = Accident::select(
                    DB::raw("month(fecha_accidente) AS month"),
                    DB::raw("YEAR(fecha_accidente) AS year"),
                    DB::raw("COUNT(DISTINCT sau_aw_form_accidents.employee_id) AS count")
                )
                ->groupBy('year', 'month')
                ->get();
            }

            \Log::info($count->groupBy('year'));

            $count = $count->groupBy('year');
            
            $records = collect([]);

            $index = 0;

            foreach ($count as $key => $year) 
            {
                $answers = collect([]);

                foreach ($headingsXls as $key2 => $item)
                {                
                    $response = $year->where('month', $item['id'])->first();

                    if ($response)
                        $answers->push($response->count);
                    else
                    {
                        $valor = $key == Carbon::now()->year && $item['id'] > Carbon::now()->month ? null : 0;
                        $answers->push($valor);
                    }
                }          

                $content = [
                    'label' => $key,
                    'data' => $answers,
                    'borderWidth' => 1,
                    'backgroundColor' => $colors[$index],
                    'borderColor' => $colors[$index],
                    'fill' => false
                ];

                $records->push($content);

                $index = $index + 1;
            }

            $data = [
                'headings' => $headings,
                'answers' => $records
            ];

            \Log::info($data);
            
        return $data;
    }

    public function multiselectMonth()
    {
        $months = [
            "Enero" => "01",
            "Febrero" => "02",
            "Marzo" => "03",
            "Abril" => "04",
            "Mayo" => "05",
            "Junio" => "06",
            "Julio" => "07",
            "Agosto" => "08",
            "Septiembre" => "09",
            "Octubre" => "10",
            "Noviembre" => "11",
            "Diciembre" => "12",
        ];

        return $this->multiSelectFormat(collect($months));
    }
}
