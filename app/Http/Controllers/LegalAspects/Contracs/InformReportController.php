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
                });
            
            if ($request->has('theme_id') && $request->theme_id)                
                $data->where('sau_ct_inform_theme_item.evaluation_theme_id', $request->theme_id);
            if ($request->has('compliance') && $request->compliance) 
                $data->where('show_program_value', DB::raw("'SI'"));
            
            $data = $data->where('sau_ct_informs_themes.inform_id', $request->inform_id)
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
            ->where('sau_ct_inform_theme_item.show_program_value', DB::raw("'SI'"))
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
            ->where('sau_ct_inform_theme_item.show_program_value', DB::raw("'SI'"))
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
                'month0' => $qualifications->sum('month0') > 0 ? (round($qualifications->sum('month0')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month1' => $qualifications->sum('month1') > 0 ? (round($qualifications->sum('month1')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month2' => $qualifications->sum('month2') > 0 ? (round($qualifications->sum('month2')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month3' => $qualifications->sum('month3') > 0 ? (round($qualifications->sum('month3')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month4' => $qualifications->sum('month4') > 0 ? (round($qualifications->sum('month4')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month5' => $qualifications->sum('month5') > 0 ? (round($qualifications->sum('month5')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month6' => $qualifications->sum('month6') > 0 ? (round($qualifications->sum('month6')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month7' => $qualifications->sum('month7') > 0 ? (round($qualifications->sum('month7')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month8' => $qualifications->sum('month8') > 0 ? (round($qualifications->sum('month8')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month9' => $qualifications->sum('month9') > 0 ? (round($qualifications->sum('month9')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month10' => $qualifications->sum('month10') > 0 ? (round($qualifications->sum('month10')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'month11' => $qualifications->sum('month11') > 0 ? (round($qualifications->sum('month11')/($qualifications->count() > 0 ? $qualifications->count() : 1), 2)) : 0,
                'total' => $qualifications->sum('total') > 0 ? (round($qualifications->sum('total')/$qualifications->count(), 1)) : 0,
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
            ->where('sau_ct_inform_theme_item.show_program_value', DB::raw("'SI'"))
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

    public function reportTableTotalesContractsPorcentage(Request $request)
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
            //->where('contract_id', $request->contract_id)
            ->where('sau_ct_inform_contract.inform_id', $request->inform_id)
            ->where('sau_ct_inform_theme_item.show_program_value', DB::raw("'SI'"))
            ->groupBy('sau_ct_inform_theme_item.description', 'sau_ct_inform_contract.contract_id');

            $total = InformContractItem::selectRaw('
                COUNT(DISTINCT sau_ct_inform_contract.month) AS total,
                sau_ct_inform_contract.contract_id AS contract
            ')
            ->join('sau_ct_inform_contract', 'sau_ct_inform_contract.id', 'sau_ct_inform_contract_items.inform_id')
            ->join('sau_ct_inform_theme_item', 'sau_ct_inform_theme_item.id', 'sau_ct_inform_contract_items.item_id')
            ->where('year', $request->year)
            ->where('sau_ct_inform_theme_item.evaluation_theme_id', $theme['id'])
            ->where('sau_ct_inform_contract.inform_id', $request->inform_id)
            //->where('contract_id', $request->contract_id)
            ->where('sau_ct_inform_theme_item.show_program_value', DB::raw("'SI'"))
            ->groupBy('contract');

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
                DB::raw("round(SUM(compliance)/total_qualification.total, 1) as total"),
                "total_qualification.contract"
            )
            ->joinSub($total, 'total_qualification', function ($join) {
                $join->on('sau_ct_inform_contract.contract_id', 'total_qualification.contract');
            })
            ->groupBy('total_qualification.contract')->get();

            $qualifications = $qualifications->groupBy("item");

            $qualification_global = collect([]);

            foreach ($qualifications as $key => $value) 
            {
                $content['item'] = $key;
                $total = 0;
                $count = 0;

                for ($i=0; $i < 12; $i++)
                { 
                    $content["month{$i}"] = $value->sum("month{$i}") / $value->count();
                    $total += $content["month{$i}"];
                    $count += $content["month{$i}"] > 0 ? 1 : 0;
                }

                $content['total'] = $count > 0 ? round($total / $count, 2) : 0;

                $qualification_global->push($content);
            }

            $qualifications_2 = [
                'item' => 'Total',
                'month0' => $qualification_global->sum('month0') > 0 ? (round($qualification_global->sum('month0')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month1' => $qualification_global->sum('month1') > 0 ? (round($qualification_global->sum('month1')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month2' => $qualification_global->sum('month2') > 0 ? (round($qualification_global->sum('month2')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month3' => $qualification_global->sum('month3') > 0 ? (round($qualification_global->sum('month3')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month4' => $qualification_global->sum('month4') > 0 ? (round($qualification_global->sum('month4')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month5' => $qualification_global->sum('month5') > 0 ? (round($qualification_global->sum('month5')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month6' => $qualification_global->sum('month6') > 0 ? (round($qualification_global->sum('month6')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month7' => $qualification_global->sum('month7') > 0 ? (round($qualification_global->sum('month7')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month8' => $qualification_global->sum('month8') > 0 ? (round($qualification_global->sum('month8')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month9' => $qualification_global->sum('month9') > 0 ? (round($qualification_global->sum('month9')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month10' => $qualification_global->sum('month10') > 0 ? (round($qualification_global->sum('month10')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'month11' => $qualification_global->sum('month11') > 0 ? (round($qualification_global->sum('month11')/($qualification_global->count() > 0 ? $qualification_global->count() : 1), 2)) : 0,
                'total' => $qualification_global->sum('total') > 0 ? (round($qualification_global->sum('total')/$qualification_global->count(), 1)) : 0,
            ];

            $qualifications_2 = collect($qualifications_2);

            $qualification_global->push($qualifications_2);    

            $theme['items']->push($qualification_global);
        }

        $themes = $themes->reject(function ($value, $key) {
            return COUNT($value['items'][0]) < 2;
        });

        return $themes;
    }

    public function detailContractGlobal(Request $request)
    {
        $month = '';

        if ($request->month == 'month0')
            $month = 'Enero';
        if ($request->month == 'month1')
            $month = 'Febrero';
        if ($request->month == 'month2')
            $month = 'Marzo';
        if ($request->month == 'month3')
            $month = 'Abril';
        if ($request->month == 'month4')
            $month = 'Mayo';
        if ($request->month == 'month5')
            $month = 'Junio';
        if ($request->month == 'month6')
            $month = 'Julio';
        if ($request->month == 'month7')
            $month = 'Agosto';
        if ($request->month == 'month8')
            $month = 'Septiembre';
        if ($request->month == 'month9')
            $month = 'Octubre';
        if ($request->month == 'month10')
            $month = 'Noviembre';
        if ($request->month == 'month11')
            $month = 'Diciembre';
        if ($request->month == 'total')
            $month = 'total';
        
        $item = collect([]);

        $themes = InformTheme::find($request->theme);

        $qualifications = InformContractItem::join('sau_ct_inform_contract', 'sau_ct_inform_contract.id', 'sau_ct_inform_contract_items.inform_id')
        ->join('sau_ct_inform_theme_item', 'sau_ct_inform_theme_item.id', 'sau_ct_inform_contract_items.item_id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_inform_contract.contract_id')
        ->where('sau_ct_inform_theme_item.evaluation_theme_id', $themes->id)
        ->where('sau_ct_inform_contract.year', $request->year)
        ->where('sau_ct_inform_contract.inform_id', $request->inform_id)
        ->where('sau_ct_inform_theme_item.show_program_value', DB::raw("'SI'"))
        ->where('sau_ct_inform_theme_item.description', "$request->item")
        ->where('sau_ct_inform_contract.month', $month)
        ->groupBy('sau_ct_inform_theme_item.description', 'sau_ct_inform_contract.contract_id');

        $total = InformContractItem::selectRaw('
            COUNT(DISTINCT sau_ct_inform_contract.month) AS total,
            sau_ct_inform_contract.contract_id AS contract,
            sau_ct_information_contract_lessee.social_reason as name
        ')
        ->join('sau_ct_inform_contract', 'sau_ct_inform_contract.id', 'sau_ct_inform_contract_items.inform_id')
        ->join('sau_ct_inform_theme_item', 'sau_ct_inform_theme_item.id', 'sau_ct_inform_contract_items.item_id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_inform_contract.contract_id')
        ->where('year', $request->year)
        ->where('sau_ct_inform_theme_item.evaluation_theme_id', $themes->id)
        ->where('sau_ct_inform_contract.inform_id', $request->inform_id)
        ->where('sau_ct_inform_contract.month', $month)
        ->where('sau_ct_inform_theme_item.show_program_value', DB::raw("'SI'"))
        ->groupBy('contract');

        if ($month != 'Total')
            $select[] = "SUM(
                CASE 
                    WHEN sau_ct_inform_contract.month = '{$month}' THEN 
                    CASE
                        WHEN compliance IS NOT NULL 
                        THEN compliance ELSE 0 END
                    ELSE 0
                END
            ) AS {$month}";

        $qualifications = $qualifications->select(
            "sau_ct_inform_theme_item.description AS item",
            DB::raw(implode(",", $select)),
            DB::raw("round(SUM(compliance)/total_qualification.total, 1) as total"),
            "total_qualification.contract",
            "total_qualification.name",
            DB::raw("'$month' as month_name")
        )
        ->joinSub($total, 'total_qualification', function ($join) {
            $join->on('sau_ct_inform_contract.contract_id', 'total_qualification.contract');
        })
        ->groupBy('total_qualification.contract')->get();

        $qualifications = $qualifications->groupBy("item");

        $qualification_global = collect([]);

        foreach ($qualifications as $key => $value) 
        {
            for ($i=0; $i < 12; $i++)
            { 
                if ($request->month == "month{$i}")
                    $qualification_global->push($value);
            }
        }
        
        $item = $qualification_global;

        if (count($item) > 0)
            return $item[0];
        else
            return [];
    }
}