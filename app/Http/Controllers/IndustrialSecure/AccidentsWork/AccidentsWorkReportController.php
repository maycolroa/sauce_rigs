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

    const INFORMS = [
        'accidents',
        'persons'
    ];

    const GROUPING_COLUMNS = [
        ['sau_departaments.name', 'departament'],        
        ['sau_municipalities.name', 'city'],
        ['sau_aw_parts_body.name', 'part_body'],
        ['sau_aw_types_lesion.name', 'lesion_type'],
        ['sau_aw_form_accidents.sexo_persona', 'sexo'], 
        ['sau_aw_form_accidents.cargo_persona', 'cargo'],
        ['sau_aw_form_accidents.causo_muerte', 'causo_muerte'],
        ['sau_aw_mechanisms.name', 'mecanismo'],
        ['sau_aw_sites.name', 'sitio'],
        ['sau_aw_agents.name', 'agente']
    ];
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
                ->where('sau_aw_form_accidents.company_id', $this->company)
                ->groupBy('year', 'month');

                if (isset($request->filters['dateRange']) && $request->filters['dateRange'])
                {
                    $dates_request = explode('/', $request->filters['dateRange']);

                    $dates = [];

                    if (COUNT($dates_request) == 2)
                    {
                        array_push($dates, $this->formatDateToSave($dates_request[0]));
                        array_push($dates, $this->formatDateToSave($dates_request[1]));
                    }
                        
                    $count->whereBetween('sau_aw_form_accidents.fecha_accidente', $dates);
                }

            }
            else if ($request->option == "persons")
            {
                $count = Accident::select(
                    DB::raw("month(fecha_accidente) AS month"),
                    DB::raw("YEAR(fecha_accidente) AS year"),
                    DB::raw("COUNT(DISTINCT sau_aw_form_accidents.employee_id) AS count")
                )
                ->where('sau_aw_form_accidents.company_id', $this->company)
                ->groupBy('year', 'month');

                if (isset($request->filters['dateRange']) && $request->filters['dateRange'])
                {
                    $dates_request = explode('/', $request->filters['dateRange']);

                    $dates = [];

                    if (COUNT($dates_request) == 2)
                    {
                        array_push($dates, $this->formatDateToSave($dates_request[0]));
                        array_push($dates, $this->formatDateToSave($dates_request[1]));
                    }
                        
                    $count->whereBetween('sau_aw_form_accidents.fecha_accidente', $dates);
                }
            }

            $count = $count->get();
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
            
        return $data;
    }

    public function getInformDataDinamic(Request $request, $components = [])
    {
        if (!$components) {
            $components = $this::INFORMS;
        }
        $informData = collect([]);
        foreach ($components as $component) {
            $informData->put($component, $this->$component($request));
        }

        return $informData->toArray();
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

    public function accidents($request)
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->accidentsBar($column[0], $request));
        }
    
        return $informData->toArray();
    }

    private function accidentsBar($column, $request)
    {
        if ($column == 'sau_aw_form_accidents.causo_muerte')
        {
            $column = "if(sau_aw_form_accidents.causo_muerte, 'SI', 'NO')";

            $consultas = Accident::select( 
                DB::raw("if(sau_aw_form_accidents.causo_muerte, 'SI', 'NO') as category"), 
                DB::raw('COUNT(DISTINCT sau_aw_form_accidents.id) AS count')
            );
        }
        else
        {
            $consultas = Accident::select("$column as category", 
              DB::raw('COUNT(DISTINCT sau_aw_form_accidents.id) AS count')
            );
        }

        $consultas->join('sau_aw_agents','sau_aw_form_accidents.agent_id', 'sau_aw_agents.id')
            ->join('sau_aw_sites','sau_aw_form_accidents.site_id', 'sau_aw_sites.id')
            ->join('sau_departaments','sau_aw_form_accidents.departamento_accidente', 'sau_departaments.id')
            ->join('sau_aw_mechanisms','sau_aw_form_accidents.mechanism_id', 'sau_aw_mechanisms.id')
            ->join('sau_municipalities','sau_aw_form_accidents.ciudad_accidente', 'sau_municipalities.id')
            ->join('sau_aw_form_accidents_parts_body','sau_aw_form_accidents.id', 'sau_aw_form_accidents_parts_body.form_accident_id')
            ->join('sau_aw_parts_body','sau_aw_form_accidents_parts_body.part_body_id', 'sau_aw_parts_body.id')
            ->join('sau_aw_form_accidents_types_lesion','sau_aw_form_accidents.id', 'sau_aw_form_accidents_types_lesion.form_accident_id')
            ->join('sau_aw_types_lesion','sau_aw_form_accidents_types_lesion.type_lesion_id', 'sau_aw_types_lesion.id')
            ->where('sau_aw_form_accidents.company_id', $this->company)
            ->groupBy('sau_aw_form_accidents.id', 'category');

        if (isset($request->filters['dateRange']) && $request->filters['dateRange'])
        {
            $dates_request = explode('/', $request->filters['dateRange']);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $consultas->betweenDate($dates);
        }

        $consultas = DB::table(DB::raw("({$consultas->toSql()}) AS t"))
        ->selectRaw("
             t.category AS category,
             SUM(t.count) AS total
        ")
        ->mergeBindings($consultas->getQuery())
        ->groupBy('t.category')
        ->pluck('total', 'category');

        return $this->buildDataChart($consultas);
    }

    public function persons($request)
    {
        $columns = $this::GROUPING_COLUMNS;
        $informData = collect([]);

        foreach ($columns as $column) {
            $informData->put($column[1], $this->personsBar($column[0], $request));
        }
    
        return $informData->toArray();
    }

    private function personsBar($column, $request)
    {
        if ($column == 'sau_aw_form_accidents.causo_muerte')
        {
            $column = "if(sau_aw_form_accidents.causo_muerte, 'SI', 'NO')";

            $consultas = Accident::select( 
                DB::raw("if(sau_aw_form_accidents.causo_muerte, 'SI', 'NO') as category"), 
                DB::raw('COUNT(DISTINCT sau_aw_form_accidents.employee_id) AS count')
            );
        }
        else
        {
            $consultas = Accident::select("$column as category", 
              DB::raw('COUNT(DISTINCT sau_aw_form_accidents.employee_id) AS count')
            );
        }

        $consultas->join('sau_aw_agents','sau_aw_form_accidents.agent_id', 'sau_aw_agents.id')
        ->join('sau_aw_sites','sau_aw_form_accidents.site_id', 'sau_aw_sites.id')
        ->join('sau_departaments','sau_aw_form_accidents.departamento_accidente', 'sau_departaments.id')
        ->join('sau_aw_mechanisms','sau_aw_form_accidents.mechanism_id', 'sau_aw_mechanisms.id')
        ->join('sau_municipalities','sau_aw_form_accidents.ciudad_accidente', 'sau_municipalities.id')
        ->join('sau_aw_form_accidents_parts_body','sau_aw_form_accidents.id', 'sau_aw_form_accidents_parts_body.form_accident_id')
        ->join('sau_aw_parts_body','sau_aw_form_accidents_parts_body.part_body_id', 'sau_aw_parts_body.id')
        ->join('sau_aw_form_accidents_types_lesion','sau_aw_form_accidents.id', 'sau_aw_form_accidents_types_lesion.form_accident_id')
        ->join('sau_aw_types_lesion','sau_aw_form_accidents_types_lesion.type_lesion_id', 'sau_aw_types_lesion.id')
        ->where('sau_aw_form_accidents.company_id', $this->company)
        ->groupBy('sau_aw_form_accidents.employee_id', 'category');

        if (isset($request->filters['dateRange']) && $request->filters['dateRange'])
        {
            $dates_request = explode('/', $request->filters['dateRange']);

            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, $this->formatDateToSave($dates_request[0]));
                array_push($dates, $this->formatDateToSave($dates_request[1]));
            }
                
            $consultas->betweenDate($dates);
        }

        $consultas = DB::table(DB::raw("({$consultas->toSql()}) AS t"))
        ->selectRaw("
             t.category AS category,
             SUM(t.count) AS total
        ")
        ->mergeBindings($consultas->getQuery())
        ->groupBy('t.category')
        ->pluck('total', 'category');

        return $this->buildDataChart($consultas);
    }

    protected function buildDataChart($rawData)
    {
        $labels = [];
        $data = [];
        $total = 0;
        foreach ($rawData as $label => $count) {
            $label2 = strlen($label) > 50 ? substr($this->sanear_string($label), 0, 50).'...' : $label;
            array_push($labels, $label2);
            array_push($data, ['name' => $label, 'value' => $count]);
            $total += $count;
        }

        return collect([
            'labels' => $labels,
            'datasets' => [
                'data' => $data,
                'count' => $total
            ]
        ]);
    }

    public function multiselectBar()
    {
      $select = [
        'Departamento' =>  'departament',        
        'Ciudad' =>  'city',
        'Parte del cuerpo' =>  'part_body',
        'Tipo de lesión' =>  'lesion_type',
        'Sexo' =>  'sexo', 
        'Cargo' =>  'cargo',
        'Causo Muerte' =>  'causo_muerte',
        'Mecanismo' =>  'mecanismo',
        'Sitio' =>  'sitio',
        'Agente' =>  'agente'
      ];
  
      return $this->multiSelectFormat(collect($select));
    }
}
