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

    public function multiselectItems(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $data = InformThemeItem::select("sau_ct_inform_theme_item.id", "sau_ct_inform_theme_item.description")
                ->join('sau_ct_informs_themes', 'sau_ct_informs_themes.id', 'sau_ct_inform_theme_item.evaluation_theme_id')
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('sau_ct_inform_theme_item.description', 'like', $keyword);
                })
                ->where('sau_ct_informs_themes.inform_id', $request->inform_id)
                ->take(30)->pluck('sau_ct_inform_theme_item.id', 'sau_ct_inform_theme_item.description');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($data)
            ]);
        }
        else
        {
            $data = InformThemeItem::selectRaw("
                sau_ct_inform_theme_item.id as id,
                sau_ct_inform_theme_item.description as description
            ")
            ->join('sau_ct_informs_themes', 'sau_ct_informs_themes.id', 'sau_ct_inform_theme_item.evaluation_theme_id')
            ->pluck('id', 'description');
        
            return $this->multiSelectFormat($data);
        }
    }

    public function reportLineItemQualification(Request $request)
    { 
        if ($request->item_id)
        {
            $headingsXls = collect([]);

            $months = $this->multiselectMonth();

            foreach ($months as $key => $month) 
            {
                $headingsXls->push(['id' => $month['name'], 'name' => $month['name']]);
            }

            $headings = $headingsXls->pluck('name')->toArray();

            $qualifications = InformContractItem::select(
                'sau_ct_inform_contract_items.*',
                'sau_ct_inform_contract.*',
                'sau_ct_inform_theme_item.description'
            )
            ->join('sau_ct_inform_theme_item', 'sau_ct_inform_theme_item.id', 'sau_ct_inform_contract_items.item_id')
            ->join('sau_ct_inform_contract', 'sau_ct_inform_contract.id', 'sau_ct_inform_contract_items.inform_id')
            ->where('sau_ct_inform_contract_items.item_id', $request->item_id)
            ->where('sau_ct_inform_contract.year', $request->year)
            ->where('sau_ct_inform_contract.inform_id', $request->inform_id)
            ->where('contract_id', $request->contract_id)
            ->groupBy('sau_ct_inform_theme_item.description', 'sau_ct_inform_contract_items.id')
            ->get();

            $answers = collect([]);

            $item_des = '';

            foreach ($qualifications as $key => $value) {
                if ($key == 0)
                    $item_des = $value->description;
            }

            foreach ($headingsXls as $key => $item)
            {
                $response = $qualifications->where('month', $item['id'])->first();

                if ($response)
                $answers->push($response->value_executed);
                else
                $answers->push(0);
            }

            $data = [
                'item' => $item_des,
                'headings' => $headings,
                'answers' => $answers
            ];
        }
        else
            $data = [];

        return $data;
    }

    public function reportTableTotalesPorcentage(Request $request)
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

            $total = InformContractItem::selectRaw('
                COUNT(DISTINCT sau_ct_inform_contract.month) AS total
            ')
            ->join('sau_ct_inform_contract', 'sau_ct_inform_contract.id', 'sau_ct_inform_contract_items.inform_id')
            ->join('sau_ct_inform_theme_item', 'sau_ct_inform_theme_item.id', 'sau_ct_inform_contract_items.item_id')
            ->where('year', $request->year)
            ->where('sau_ct_inform_theme_item.evaluation_theme_id', $theme['id'])
            ->where('sau_ct_inform_contract.inform_id', $request->inform_id)
            ->where('contract_id', $request->contract_id)
            ->get()->toArray();

            foreach ($total as $key => $value) {
                $total = $value['total'];
            }

            foreach ($months as $key => $month)
            {
                $select[] = "SUM(
                        CASE 
                            WHEN sau_ct_inform_contract.month = '{$month['name']}' THEN 
                            CASE
                                WHEN compliance IS NOT NULL 
                                THEN compliance ELSE 0 END
                            ELSE 0
                        END
                    ) AS month{$key}";
            }

            $qualifications = $qualifications->select(
                "sau_ct_inform_theme_item.description AS item",
                DB::raw(implode(",", $select)),
                DB::raw("round(SUM(compliance)/{$total}, 1) as total")
            )->get();

            $qualifications_2 = [
                'item' => 'Total',
                'month0' => round($qualifications->sum('month0')/$qualifications->count(), 2),
                'month1' => round($qualifications->sum('month1')/$qualifications->count(), 2),
                'month2' => round($qualifications->sum('month2')/$qualifications->count(), 2),
                'month3' => round($qualifications->sum('month3')/$qualifications->count(), 2),
                'month4' => round($qualifications->sum('month4')/$qualifications->count(), 2),
                'month5' => round($qualifications->sum('month5')/$qualifications->count(), 2),
                'month6' => round($qualifications->sum('month6')/$qualifications->count(), 2),
                'month7' => round($qualifications->sum('month7')/$qualifications->count(), 2),
                'month8' => round($qualifications->sum('month8')/$qualifications->count(), 2),
                'month9' => round($qualifications->sum('month9')/$qualifications->count(), 2),
                'month10' => round($qualifications->sum('month10')/$qualifications->count(), 2),
                'month11' => round($qualifications->sum('month11')/$qualifications->count(), 2),
                'total' => round($qualifications->sum('total')/$qualifications->count(), 1),
            ];

            $qualifications_2 = collect($qualifications_2);

            $qualifications->push($qualifications_2);    

            $theme['items']->push($qualifications);
        }

        return $themes;
    }

    public function reportLineItemPorcentage(Request $request)
    { 
        if ($request->item_id)
        {
            $headingsXls = collect([]);

            $months = $this->multiselectMonth();

            foreach ($months as $key => $month) 
            {
                $headingsXls->push(['id' => $month['name'], 'name' => $month['name']]);
            }

            $headings = $headingsXls->pluck('name')->toArray();

            $qualifications = InformContractItem::select(
                'sau_ct_inform_contract_items.*',
                'sau_ct_inform_contract.*',
                'sau_ct_inform_theme_item.description'
            )
            ->join('sau_ct_inform_theme_item', 'sau_ct_inform_theme_item.id', 'sau_ct_inform_contract_items.item_id')
            ->join('sau_ct_inform_contract', 'sau_ct_inform_contract.id', 'sau_ct_inform_contract_items.inform_id')
            ->where('sau_ct_inform_contract_items.item_id', $request->item_id)
            ->where('sau_ct_inform_contract.year', $request->year)
            ->where('sau_ct_inform_contract.inform_id', $request->inform_id)
            ->where('contract_id', $request->contract_id)
            ->groupBy('sau_ct_inform_theme_item.description', 'sau_ct_inform_contract_items.id')
            ->get();

            $answers = collect([]);

            $item_des = '';

            foreach ($qualifications as $key => $value) {
                if ($key == 0)
                    $item_des = $value->description;
            }

            foreach ($headingsXls as $key => $item)
            {
                $response = $qualifications->where('month', $item['id'])->first();

                if ($response)
                $answers->push($response->compliance);
                else
                $answers->push(0);
            }

            $data = [
                'item' => $item_des,
                'headings' => $headings,
                'answers' => $answers
            ];
        }
        else
            $data = [];

        return $data;
    }
}