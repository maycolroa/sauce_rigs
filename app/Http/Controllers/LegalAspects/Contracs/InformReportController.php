<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\LegalAspects\Contracts\InformContractRequest;
use App\Models\LegalAspects\Contracts\Inform;
use App\Models\LegalAspects\Contracts\InformContract;
use App\Models\LegalAspects\Contracts\InformThemeItem;
use App\Models\LegalAspects\Contracts\InformTheme;
use App\Models\LegalAspects\Contracts\InformContractItem;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Models\General\Module;
use App\Traits\Filtertrait;
use App\Models\General\Company;
use Carbon\Carbon;
use DB;
use Validator;
use PDF;

class InformReportController extends Controller
{
    use Filtertrait;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
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
    public function data(Request $request)
    {
        $inform_contracts = InformContract::select(
                'sau_ct_inform_contract.*',
                'sau_ct_information_contract_lessee.social_reason as social_reason',
                'sau_ct_information_contract_lessee.nit as nit',
                'sau_users.name as name',
                DB::raw("CONCAT(year, ' - ', month) AS periodo")
            )
            ->join('sau_users', 'sau_users.id', 'sau_ct_inform_contract.evaluator_id')
            ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_inform_contract.contract_id')
            ->where('sau_ct_inform_contract.inform_id', '=', $request->get('modelId'));;

        /*$url = "/legalaspects/inform/contracts/".$request->get('modelId');

        $filters = COUNT($request->get('filters')) > 0 ? $request->get('filters') : $this->filterDefaultValues($this->user->id, $url);

        if (isset($filters["dateRange"]) && $filters["dateRange"])
        {
            $dates_request = explode('/', $filters["dateRange"]);
            $dates = [];

            if (COUNT($dates_request) == 2)
            {
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[0]))->format('Y-m-d 00:00:00'));
                array_push($dates, (Carbon::createFromFormat('D M d Y',$dates_request[1]))->format('Y-m-d 23:59:59'));
            }
            
            $inform_contracts->betweenDate($dates);
        }*/

        //return Vuetable::of($inform_contracts)->make();
        return Vuetable::of($inform_contracts)
                    ->make();
    }
    
    public function multiselectMonth()
    {
        $months = [
            "Enero" => "Enero",
            "Febrero" => "Febrero",
            "Marzo" => "Marzo",
            "Abril" => "Abril",
            "Mayo" => "Mayo",
            "Junio" => "Junio",
            "Julio" => "Julio",
            "Agosto" => "Agosto",
            "Septiembre" => "Septiembre",
            "Octubre" => "Octubre",
            "Noviembre" => "Noviembre",
            "Diciembre" => "Diciembre",
        ];

        return $this->multiSelectFormat(collect($months));
    }
    
    public function reportTableTotales(Request $request)
    {
        $themes = collect([]);

        if ($request->theme)
        {
            $inform_themes = InformTheme::find($request->theme);

            $themes->push(['id' => $inform_themes->id, 'name' => $inform_themes->description, 'headings' => collect([]), 'items' => collect([])]);
        }
        else
        {
            $inform_themes = InformTheme::where('inform_id',$request->inform_id)->get();

            foreach ($inform_themes as $key => $value) 
            {
                $themes->push(['id' => $value->id, 'name' => $value->description, 'headings' => collect([]), 'items' => collect([])]);
            }
        }

        $headingsXls = collect([['id' => 'id', 'name' => 'Item']]);

        $months = $this->multiselectMonth();

        foreach ($months as $key => $month) 
        {
            $headingsXls->push(['id' => $month['name'], 'name' => $month['name']]);
        }

        $headingsXls->push(['id' => 'Total', 'name' => 'Total']);

        foreach ($themes as $key => $theme) 
        {
            $theme['headings']->push($headingsXls->pluck('name')->toArray());

            $qualifications = InformContractItem::join('sau_ct_inform_contract', 'sau_ct_inform_contract.id', 'sau_ct_inform_contract_items.inform_id')
            ->join('sau_ct_inform_theme_item', 'sau_ct_inform_theme_item.id', 'sau_ct_inform_contract_items.item_id')
            ->where('sau_ct_inform_theme_item.evaluation_theme_id', $theme['id'])
            ->where('sau_ct_inform_contract.year', $request->year)
            ->where('contract_id', $request->contract_id)
            ->where('sau_ct_inform_contract.inform_id', $request->inform_id)
            ->groupBy('sau_ct_inform_theme_item.description');

            foreach ($months as $key => $month)
            {
                $select[] = "SUM(
                    CASE 
                        WHEN sau_ct_inform_contract.month = '{$month['name']}' THEN value_executed
                        ELSE 0
                    END
                ) AS month{$key}";
            }

            $qualifications = $qualifications->select(
                "sau_ct_inform_theme_item.description AS item",
                DB::raw(implode(",", $select)),
                DB::raw("SUM(value_executed) as total")
            )->get();

            $qualifications_2 = [
                'item' => 'Total',
                'month0' => $qualifications->sum('month0'),
                'month1' => $qualifications->sum('month1'),
                'month2' => $qualifications->sum('month2'),
                'month3' => $qualifications->sum('month3'),
                'month4' => $qualifications->sum('month4'),
                'month5' => $qualifications->sum('month5'),
                'month6' => $qualifications->sum('month6'),
                'month7' => $qualifications->sum('month7'),
                'month8' => $qualifications->sum('month8'),
                'month9' => $qualifications->sum('month9'),
                'month10' => $qualifications->sum('month10'),
                'month11' => $qualifications->sum('month11'),
                'total' => $qualifications->sum('total'),
            ];

            $qualifications_2 = collect($qualifications_2);

            $qualifications->push($qualifications_2);    

            $theme['items']->push($qualifications);
        }

        return $themes;
    }

    public function multiselect(Request $request)
    {
        if ($request->has('column') && $request->get('column') != '')
        {
            if($request->has('keyword'))
            {
                $column = $request->column;

                $keyword = "%{$request->keyword}%";
                $data = InformContract::selectRaw("DISTINCT $column")
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
                $data = InformContract::selectRaw("DISTINCT $column")
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

    public function multiselectThemes(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $data = InformTheme::select("id", "description")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('description', 'like', $keyword);
                })
                ->where('sau_ct_informs_themes.inform_id', $request->inform_id)
                ->take(30)->pluck('id', 'description');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($data)
            ]);
        }
        else
        {
            $data = InformTheme::selectRaw("
                sau_ct_informs_themes.id as id,
                sau_ct_informs_themes.description as description
            ")->pluck('id', 'description');
        
            return $this->multiSelectFormat($data);
        }
    }
}